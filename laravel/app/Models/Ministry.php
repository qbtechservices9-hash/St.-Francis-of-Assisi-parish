<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Ministry extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'summary',
        'details',
        'contact_email',
        'contact_phone',
        'meeting_time',
        'image',
        'media',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'media' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('nav_ministries');
        });

        static::deleted(function (): void {
            Cache::forget('nav_ministries');
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function galleryItems(): HasMany
    {
        return $this->hasMany(GalleryItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->image);
    }

    private function resolvePublicUrl(?string $value): ?string
    {
        return MediaStorage::url($value);
    }
}
