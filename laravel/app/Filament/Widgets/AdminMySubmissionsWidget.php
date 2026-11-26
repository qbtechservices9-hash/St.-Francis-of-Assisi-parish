<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\GalleryItemResource;
use App\Models\Event;
use App\Models\GalleryItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminMySubmissionsWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'My Ministry Submissions';

    protected ?string $description = 'Track review status of your ministry content.';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) $user
            && $user->hasRole('ministry_admin')
            && ! $user->hasAnyRole(['super_admin', 'content_admin']);
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $ministryIds = $user?->ministries()->pluck('ministries.id') ?? collect();

        $pending = Event::query()
            ->whereIn('ministry_id', $ministryIds)
            ->where('approval_status', Event::STATUS_PENDING)
            ->count();
        $pending += GalleryItem::query()
            ->whereIn('ministry_id', $ministryIds)
            ->where('approval_status', GalleryItem::STATUS_PENDING)
            ->count();

        $rejected = Event::query()
            ->whereIn('ministry_id', $ministryIds)
            ->where('approval_status', Event::STATUS_REJECTED)
            ->count();
        $rejected += GalleryItem::query()
            ->whereIn('ministry_id', $ministryIds)
            ->where('approval_status', GalleryItem::STATUS_REJECTED)
            ->count();

        $approved = Event::query()
            ->whereIn('ministry_id', $ministryIds)
            ->where('approval_status', Event::STATUS_APPROVED)
            ->count();
        $approved += GalleryItem::query()
            ->whereIn('ministry_id', $ministryIds)
            ->where('approval_status', GalleryItem::STATUS_APPROVED)
            ->count();

        return [
            Stat::make('Pending Review', $pending)
                ->description('Waiting for approval')
                ->url(EventResource::getUrl('index'))
                ->color($pending > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-clock'),
            Stat::make('Returned (Rejected)', $rejected)
                ->description('Needs update and resubmission')
                ->url(GalleryItemResource::getUrl('index'))
                ->color($rejected > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-arrow-uturn-left'),
            Stat::make('Approved', $approved)
                ->description('Ready on website')
                ->color('success')
                ->icon('heroicon-o-check-badge'),
        ];
    }
}
