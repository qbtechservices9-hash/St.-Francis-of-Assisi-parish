<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\GalleryItemResource;
use App\Models\Event;
use App\Models\GalleryItem;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminApprovalQueueWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Approval Queue';

    protected ?string $description = 'Items waiting for moderation.';

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }

    protected function getStats(): array
    {
        $pendingEvents = Event::query()
            ->where('approval_status', Event::STATUS_PENDING)
            ->count();

        $pendingGallery = GalleryItem::query()
            ->where('approval_status', GalleryItem::STATUS_PENDING)
            ->count();

        $pendingTotal = $pendingEvents + $pendingGallery;

        return [
            Stat::make('Pending Reviews', $pendingTotal)
                ->description('Events + gallery items')
                ->color($pendingTotal > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-inbox-stack'),
            Stat::make('Event Requests', $pendingEvents)
                ->description('Awaiting approval')
                ->url(EventResource::getUrl('index'))
                ->color($pendingEvents > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-calendar-days'),
            Stat::make('Gallery Requests', $pendingGallery)
                ->description('Awaiting approval')
                ->url(GalleryItemResource::getUrl('index'))
                ->color($pendingGallery > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-photo'),
        ];
    }
}
