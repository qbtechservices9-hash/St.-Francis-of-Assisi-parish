<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactMessageResource;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AdminRecentActivityWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Messages';

    protected int|string|array $columnSpan = 1;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContactMessage::query()->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (ContactMessage $record): string => $record->is_read ? 'Read' : 'New')
                    ->color(fn (ContactMessage $record): string => $record->is_read ? 'success' : 'warning'),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->actions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ContactMessage $record): string => ContactMessageResource::getUrl('view', ['record' => $record])),
            ])
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25]);
    }
}
