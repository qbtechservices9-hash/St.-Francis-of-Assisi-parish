<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicUploads;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    use ResolvesPublicUploads;

    protected $fillable = [
        'name',
        'type',
        'brief_history',
        'cover_image',
        'gallery',
        'order',
        'is_published',
    ];

    protected $casts = [
        'gallery' => 'array',
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->orderBy('order')
            ->orderBy('name');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->cover_image);
    }

    public function getGalleryImageUrlsAttribute(): array
    {
        return $this->resolvePublicUrls($this->gallery);
    }
}
