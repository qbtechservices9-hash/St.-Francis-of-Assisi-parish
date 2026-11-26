<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\GalleryItem;
use App\Models\Ministry;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends StatsOverviewWidget
{
    protected ?string $heading = 'Overview';
    protected ?string $description = 'Key metrics across the ministry platform.';

    protected int|array|null $columns = ['@xl' => 3, '@lg' => 2, '@sm' => 1];

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }

    protected function getStats(): array
    {
        $upcomingEvents = Event::query()
            ->where('is_published', true)
            ->where('approval_status', Event::STATUS_APPROVED)
            ->whereDate('date', '>=', now())
            ->count();

        $ministries = Ministry::query()
            ->where('is_active', true)
            ->count();

        $galleryItems = GalleryItem::query()
            ->where('is_published', true)
            ->where('approval_status', GalleryItem::STATUS_APPROVED)
            ->count();

        $unreadMessages = ContactMessage::query()
            ->where('is_read', false)
            ->count();

        $users = User::query()->count();

        return [
            Stat::make('Total Users', $users)
                ->description('Registered members')
                ->color('primary')
                ->icon('heroicon-o-user-group'),
            Stat::make('Upcoming Events', $upcomingEvents)
                ->description('Published')
                ->color('success')
                ->icon('heroicon-o-calendar-days'),
            Stat::make('Active Ministries', $ministries)
                ->description('Serving now')
                ->color('info')
                ->icon('heroicon-o-users'),
            Stat::make('Gallery Items', $galleryItems)
                ->description('Published')
                ->color('warning')
                ->icon('heroicon-o-photo'),
            Stat::make('Unread Messages', $unreadMessages)
                ->description('Needs reply')
                ->color('danger')
                ->icon('heroicon-o-inbox'),
        ];
    }
}
