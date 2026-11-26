<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DailyVerse extends Model
{
    protected $fillable = [
        'reference',
        'text',
        'verse_date',
        'is_published',
    ];

    protected $casts = [
        'verse_date' => 'date',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForDate(Builder $query, Carbon $date): Builder
    {
        return $query->where('verse_date', '<=', $date->toDateString());
    }
}
