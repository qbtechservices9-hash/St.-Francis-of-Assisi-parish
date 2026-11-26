<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class MediaStorage
{
    public static function diskName(): string
    {
        return (string) config('filesystems.media_disk', 'public');
    }

    public static function disk()
    {
        return Storage::disk(static::diskName());
    }

    public static function isLocalPublicDisk(?string $disk = null): bool
    {
        return ($disk ?? static::diskName()) === 'public';
    }

    public static function normalizePath(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (str_starts_with($value, '/storage/')) {
            return ltrim(substr($value, strlen('/storage/')), '/');
        }

        if (str_starts_with($value, 'storage/')) {
            return ltrim(substr($value, strlen('storage/')), '/');
        }

        if (str_starts_with($value, '/')) {
            return ltrim($value, '/');
        }

        return ltrim($value, '/');
    }

    public static function url(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        $path = static::normalizePath($value);

        if (! $path) {
            return null;
        }

        if (static::isLocalPublicDisk()) {
            return Storage::disk('public')->url($path);
        }

        return static::disk()->url($path);
    }

    public static function storeUploadedFile($file, string $directory): string
    {
        return $file->store($directory, static::diskName());
    }

    public static function delete(?string $value): void
    {
        $path = static::normalizePath($value);

        if (! $path || str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }

        static::disk()->delete($path);
    }
}
