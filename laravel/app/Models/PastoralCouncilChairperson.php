<?php

namespace App\Models;

use App\Models\Concerns\ResolvesPublicUploads;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PastoralCouncilChairperson extends Model
{
    use ResolvesPublicUploads;

    protected $table = 'pastoral_council_chairpersons';

    protected $fillable = [
        'name',
        'photo',
        'served_from_year',
        'served_to_year',
        'notes',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'served_from_year' => 'integer',
        'served_to_year' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->orderBy('served_from_year')
            ->orderBy('order')
            ->orderBy('name');
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->resolvePublicUrl($this->photo);
    }

    public function getServicePeriodLabelAttribute(): string
    {
        $start = $this->served_from_year;
        $end = $this->served_to_year;

        if (!$start) {
            return 'Service years not set';
        }

        return $start . ' - ' . ($end ?: 'Present');
    }
}
