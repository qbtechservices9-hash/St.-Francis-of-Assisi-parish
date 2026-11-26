<?php

namespace App\Models;

use App\Support\MediaStorage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'title',
        'description',
        'image',
        'date',
        'time',
        'location',
        'category',
        'recurring',
        'is_featured',
        'countdown_label',
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
        'time' => 'datetime',
        'is_featured' => 'boolean',
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
            ->where('approval_status', self::STATUS_APPROVED);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('date', '>=', now()->startOfDay())->orderBy('date');
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
