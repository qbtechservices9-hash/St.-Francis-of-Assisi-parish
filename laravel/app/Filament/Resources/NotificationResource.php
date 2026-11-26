<?php

namespace App\Filament\Resources;

use UnitEnum;
use App\Filament\Resources\NotificationResource\Pages;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification as Toast;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;

class NotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-bell-alert';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'My Notifications';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->getStateUsing(fn (DatabaseNotification $record): string => (string) data_get($record->data, 'title', 'Notification')),
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->getStateUsing(fn (DatabaseNotification $record): string => (string) data_get($record->data, 'message', ''))
                    ->limit(90)
                    ->wrap(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->badge()
                    ->getStateUsing(fn (DatabaseNotification $record): string => (string) data_get($record->data, 'level', 'info'))
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'warning' => 'warning',
                        'danger' => 'danger',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (DatabaseNotification $record): string => $record->read_at ? 'Read' : 'Unread')
                    ->color(fn (DatabaseNotification $record): string => $record->read_at ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('read_at')
                    ->label('Read')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('read_at'),
                        false: fn (Builder $query) => $query->whereNull('read_at'),
                        blank: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (DatabaseNotification $record): string => (string) data_get($record->data, 'url', url('/admin')))
                    ->openUrlInNewTab(),
                Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (DatabaseNotification $record): bool => $record->read_at === null)
                    ->action(fn (DatabaseNotification $record) => $record->markAsRead()),
                Action::make('mark_unread')
                    ->label('Mark Unread')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->visible(fn (DatabaseNotification $record): bool => $record->read_at !== null)
                    ->action(fn (DatabaseNotification $record) => $record->update(['read_at' => null])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_selected_read')
                        ->label('Mark Selected Read')
                        ->icon('heroicon-o-check')
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                $record->markAsRead();
                            }

                            Toast::make()
                                ->title('Selected notifications marked as read')
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('mark_selected_unread')
                        ->label('Mark Selected Unread')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                $record->update(['read_at' => null]);
                            }

                            Toast::make()
                                ->title('Selected notifications marked as unread')
                                ->success()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id);
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin', 'ministry_admin']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $record->notifiable_type === User::class
            && (int) $record->notifiable_id === (int) $user->id;
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
        ];
    }
}
