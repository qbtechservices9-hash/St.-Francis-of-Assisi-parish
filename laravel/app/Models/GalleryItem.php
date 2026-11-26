<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryItem extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'title',
        'description',
        'type',
        'url',
        'thumbnail',
        'category',
        'date',
        'order',
        'is_published',
        'ministry_id',
        'approval_status',
        'submitted_by',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_published' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public static function approvalStatusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where('approval_status', self::STATUS_APPROVED)
            ->orderBy('date', 'desc');
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        if ($category !== 'all') {
            return $query->where('category', $category);
        }

        return $query;
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->url);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->thumbnail);
    }

    private function resolvePublicUrl(?string $value): ?string
    {
        return MediaStorage::url($value);
    }
}
