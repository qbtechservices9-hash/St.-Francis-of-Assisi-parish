<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = static::cachedValues();

        return $settings[$key] ?? $default;
    }

    public static function getMany(array $keys, array $defaults = []): array
    {
        $settings = static::cachedValues();
        $values = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $settings)) {
                $values[$key] = $settings[$key];
            }
        }

        return array_merge($defaults, $values);
    }

    public static function cachedValues(): array
    {
        return Cache::remember('site_settings', now()->addHours(6), function (): array {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }
}
