<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{
    protected $fillable = [
        'title',
        'preacher',
        'date',
        'summary',
        'video_url',
        'audio_url',
        'notes_url',
        'verses',
        'is_published',
    ];

    protected $casts = [
        'date' => 'date',
        'verses' => 'array',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->orderByDesc('date');
    }
}
