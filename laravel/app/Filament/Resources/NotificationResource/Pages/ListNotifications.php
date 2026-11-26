<?php

namespace App\Filament\Resources\NotificationResource\Pages;

use App\Filament\Resources\NotificationResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Notifications\DatabaseNotification;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_all_read')
                ->label('Mark All Read')
                ->icon('heroicon-o-check-circle')
                ->action(function (): void {
                    $user = auth()->user();

                    if (! $user) {
                        return;
                    }

                    DatabaseNotification::query()
                        ->where('notifiable_type', $user::class)
                        ->where('notifiable_id', $user->id)
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);

                    Notification::make()
                        ->title('All notifications marked as read')
                        ->success()
                        ->send();
                }),
        ];
    }
}
