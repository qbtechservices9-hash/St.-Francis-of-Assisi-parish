<?php

use App\Support\MediaStorage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Models\Page;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pages:normalize-images {--dry-run}', function () {
    $normalize = function ($value) {
        if (is_array($value)) {
            $direct = $value['path'] ?? $value['file'] ?? $value['url'] ?? null;
            if (is_string($direct) && $direct !== '') {
                return $direct;
            }
            foreach ($value as $item) {
                if (is_string($item) && $item !== '') {
                    return $item;
                }
                if (is_array($item)) {
                    $nested = $item['path'] ?? $item['file'] ?? $item['url'] ?? null;
                    if (is_string($nested) && $nested !== '') {
                        return $nested;
                    }
                }
            }
            return null;
        }
        return is_string($value) ? $value : null;
    };

    $map = [
        'home' => ['hero.background_image', 'welcome.image'],
        'about' => ['hero.background_image', 'story.image'],
    ];

    $updated = 0;

    foreach ($map as $slug => $paths) {
        $page = Page::query()->where('slug', $slug)->first();
        if (! $page) {
            $this->warn("Missing page: {$slug}");
            continue;
        }

        $sections = is_array($page->sections) ? $page->sections : [];
        $changed = false;

        foreach ($paths as $path) {
            $value = data_get($sections, $path);
            $normalized = $normalize($value);

            if (is_array($value) && $normalized !== null) {
                data_set($sections, $path, $normalized);
                $changed = true;
                $this->line("{$slug}: {$path} -> {$normalized}");
            }
        }

        if ($changed && ! $this->option('dry-run')) {
            $page->sections = $sections;
            $page->save();
            $updated++;
        }
    }

    $this->info($this->option('dry-run') ? 'Dry run complete.' : "Updated {$updated} page(s).");
})->purpose('Normalize image fields in page sections');

Artisan::command('db:sync-sqlite-to-pgsql {--include-runtime : Include sessions, cache, jobs, and other runtime tables}', function () {
    $source = 'sqlite';
    $target = 'pgsql';

    if (config('database.default') !== $target) {
        $this->warn("Current default database is [" . config('database.default') . "]. That's okay, but the target sync connection is [{$target}].");
    }

    $orderedTables = [
        'users',
        'permissions',
        'roles',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
        'ministries',
        'ministry_user',
        'pages',
        'site_settings',
        'contact_messages',
        'events',
        'gallery_items',
        'societies',
        'friars',
        'pastoral_council_chairpersons',
        'service_schedules',
        'daily_verses',
        'sermons',
        'audit_logs',
        'notifications',
    ];

    $tables = collect(Schema::connection($source)->getTableListing())
        ->map(function (string $table) {
            return str_contains($table, '.') ? explode('.', $table, 2)[1] : $table;
        })
        ->unique()
        ->reject(function (string $table) {
            $runtimeTables = ['cache', 'cache_locks', 'failed_jobs', 'job_batches', 'jobs', 'migrations', 'sessions'];

            if ($this->option('include-runtime')) {
                return false;
            }

            return in_array($table, $runtimeTables, true);
        })
        ->sortBy(function (string $table) use ($orderedTables) {
            $position = array_search($table, $orderedTables, true);

            return $position === false ? 999 : $position;
        })
        ->values();

    if ($tables->isEmpty()) {
        $this->warn('No tables found to sync.');
        return;
    }

    $this->info('Preparing PostgreSQL target...');

    Schema::connection($target)->disableForeignKeyConstraints();

    foreach ($tables as $table) {
        DB::connection($target)->statement(sprintf('TRUNCATE TABLE "%s" RESTART IDENTITY CASCADE', str_replace('"', '""', $table)));
    }

    foreach ($tables as $table) {
        $rows = DB::connection($source)->table($table)->get()->map(fn ($row) => (array) $row);
        $count = $rows->count();

        if ($count === 0) {
            $this->line("Skipped {$table} (empty)");
            continue;
        }

        foreach ($rows->chunk(200) as $chunk) {
            DB::connection($target)->table($table)->insert($chunk->all());
        }

        $columns = Schema::connection($target)->getColumnListing($table);
        if (in_array('id', $columns, true)) {
            DB::connection($target)->statement(
                sprintf(
                    "SELECT setval(pg_get_serial_sequence('%s', 'id'), COALESCE(MAX(id), 1), MAX(id) IS NOT NULL) FROM \"%s\"",
                    str_replace("'", "''", $table),
                    str_replace('"', '""', $table),
                )
            );
        }

        $this->info("Synced {$table} ({$count} rows)");
    }

    Schema::connection($target)->enableForeignKeyConstraints();

    $this->newLine();
    $this->info('SQLite to PostgreSQL sync complete.');
    $this->line('Make sure your .env uses DB_CONNECTION=pgsql before serving the app with Supabase.');
})->purpose('Copy application data from the local SQLite database to PostgreSQL / Supabase');

Artisan::command('media:create-supabase-bucket {name=media} {--public=1}', function (string $name) {
    $isPublic = filter_var($this->option('public'), FILTER_VALIDATE_BOOL);

    DB::connection('pgsql')->table('storage.buckets')->updateOrInsert(
        ['id' => $name],
        [
            'name' => $name,
            'public' => $isPublic,
            'file_size_limit' => null,
            'allowed_mime_types' => null,
        ]
    );

    $this->info("Supabase bucket [{$name}] is ready.");
})->purpose('Create or update a public Supabase Storage bucket');

Artisan::command('media:sync-to-cloud {--disk=} {--dry-run}', function () {
    $sourceDisk = 'public';
    $targetDisk = (string) ($this->option('disk') ?: config('filesystems.media_disk', 'public'));

    if ($targetDisk === $sourceDisk) {
        $this->warn('The configured media disk is still [public]. Set MEDIA_DISK to your cloud disk first.');
        return;
    }

    $files = Storage::disk($sourceDisk)->allFiles();

    if (empty($files)) {
        $this->warn('No local media files were found.');
        return;
    }

    $this->info("Syncing media from [{$sourceDisk}] to [{$targetDisk}]...");

    $copied = 0;

    foreach ($files as $file) {
        if (str_starts_with(basename($file), '.')) {
            $this->line("Skipped {$file} (hidden file)");
            continue;
        }

        if ($this->option('dry-run')) {
            $this->line("Would copy {$file}");
            continue;
        }

        if (Storage::disk($targetDisk)->exists($file)) {
            $this->line("Skipped {$file} (already exists)");
            continue;
        }

        $stream = Storage::disk($sourceDisk)->readStream($file);
        Storage::disk($targetDisk)->writeStream($file, $stream, ['visibility' => 'public']);

        if (is_resource($stream)) {
            fclose($stream);
        }

        $copied++;
        $this->info("Copied {$file}");
    }

    $this->newLine();
    $this->info($this->option('dry-run') ? 'Dry run complete.' : "Media sync complete. {$copied} file(s) copied.");
    $this->line('Once the files are in the cloud bucket, MEDIA_DISK can stay pointed at the cloud disk and your existing DB paths will resolve there automatically.');
})->purpose('Copy local public media files to the configured cloud disk');



