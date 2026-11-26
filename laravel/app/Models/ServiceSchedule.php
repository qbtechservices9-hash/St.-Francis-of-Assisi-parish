<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ServiceSchedule extends Model
{
    protected $fillable = [
        'title',
        'day_of_week',
        'time',
        'location',
        'description',
        'calendar_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('home_service_schedules');
        });

        static::deleted(function (): void {
            Cache::forget('home_service_schedules');
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('day_of_week');
    }
}
