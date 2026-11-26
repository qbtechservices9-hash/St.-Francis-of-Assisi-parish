<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE notifications
            ALTER COLUMN data TYPE jsonb
            USING CASE
                WHEN data IS NULL OR btrim(data) = '' THEN '{}'::jsonb
                ELSE data::jsonb
            END
        SQL);
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE notifications
            ALTER COLUMN data TYPE text
            USING data::text
        SQL);
    }
};
