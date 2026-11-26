<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Ministry;
use App\Support\ApprovalNotifications;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | UnitEnum | null $navigationGroup = 'Management';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('ministry_id')
                    ->label('Ministry')
                    ->options(function (): array {
                        $user = auth()->user();

                        if (!$user) {
                            return [];
                        }

                        if ($user->hasRole('ministry_admin') && !$user->hasAnyRole(['super_admin', 'content_admin'])) {
                            return $user->ministries()->orderBy('name')->pluck('name', 'ministries.id')->all();
                        }

                        return Ministry::query()->orderBy('name')->pluck('name', 'id')->all();
                    })
                    ->searchable()
                    ->preload()
                    ->helperText('Assign this event to a ministry for ministry-specific management.'),
                Forms\Components\FileUpload::make('image')
                    ->label('Event Image / Flyer')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('events')
                    ->image(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TimePicker::make('time')
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('recurring')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured Event (Countdown)')
                    ->helperText('Highlight this event on the homepage with a countdown timer.')
                    ->visible(fn (): bool => (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin'])),
                Forms\Components\TextInput::make('countdown_label')
                    ->label('Countdown Label')
                    ->maxLength(255)
                    ->placeholder('Easter Convention')
                    ->visible(fn (): bool => (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin'])),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(true)
                    ->visible(fn (): bool => (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin'])),
                Forms\Components\Placeholder::make('approval_status_display')
                    ->label('Approval Status')
                    ->content(fn (?Event $record): string => Event::approvalStatusOptions()[$record?->approval_status ?? Event::STATUS_DRAFT] ?? 'Draft')
                    ->visible(fn (?Event $record): bool => $record !== null),
                Forms\Components\Placeholder::make('submitted_by_display')
                    ->label('Submitted By')
                    ->content(fn (?Event $record): string => $record?->submittedBy?->name ?? '-')
                    ->visible(fn (?Event $record): bool => $record !== null),
                Forms\Components\Placeholder::make('approved_by_display')
                    ->label('Reviewed By')
                    ->content(fn (?Event $record): string => $record?->approvedBy?->name ?? '-')
                    ->visible(fn (?Event $record): bool => $record !== null),
                Forms\Components\Placeholder::make('approved_at_display')
                    ->label('Reviewed At')
                    ->content(fn (?Event $record): string => $record?->approved_at?->format('M j, Y g:i A') ?? '-')
                    ->visible(fn (?Event $record): bool => $record !== null),
                Forms\Components\Placeholder::make('approval_notes_display')
                    ->label('Reviewer Note')
                    ->content(fn (?Event $record): string => $record?->approval_notes ?: '-')
                    ->visible(fn (?Event $record): bool => $record !== null)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Flyer')
                    ->square()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('ministry.name')
                    ->label('Ministry')
                    ->badge()
                    ->color('info')
                    ->placeholder('General')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('warning')
                    ->searchable(),
                Tables\Columns\TextColumn::make('featured')
                    ->label('Featured')
                    ->badge()
                    ->getStateUsing(fn (Event $record): string => $record->is_featured ? 'Featured' : 'Standard')
                    ->color(fn (Event $record): string => $record->is_featured ? 'success' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Approval')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Event::approvalStatusOptions()[$state ?? Event::STATUS_DRAFT] ?? 'Draft')
                    ->color(fn (?string $state): string => match ($state) {
                        Event::STATUS_APPROVED => 'success',
                        Event::STATUS_PENDING => 'warning',
                        Event::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Visibility')
                    ->badge()
                    ->getStateUsing(fn (Event $record): string => $record->is_published ? 'Published' : 'Hidden')
                    ->color(fn (Event $record): string => $record->is_published ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('submittedBy.name')
                    ->label('Submitted By')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approvedBy.name')
                    ->label('Reviewed By')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Reviewed At')
                    ->dateTime('M j, Y g:i A')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Approval')
                    ->default(fn (): ?string => auth()->user()?->hasAnyRole(['super_admin', 'content_admin']) ? Event::STATUS_PENDING : null)
                    ->options(Event::approvalStatusOptions()),
                Tables\Filters\Filter::make('needs_action')
                    ->label('Needs Action')
                    ->default(fn (): bool => (bool) auth()->user()?->hasRole('ministry_admin'))
                    ->query(fn (Builder $query): Builder => $query->whereIn('approval_status', [
                        Event::STATUS_DRAFT,
                        Event::STATUS_REJECTED,
                    ])),
                Tables\Filters\SelectFilter::make('ministry_id')
                    ->label('Ministry')
                    ->options(fn () => Ministry::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn () => Event::query()->select('category')->distinct()->pluck('category', 'category')->all())
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('submit_for_review')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Event $record): bool => static::canSubmitForReview($record))
                    ->action(function (Event $record): void {
                        $record->update([
                            'approval_status' => Event::STATUS_PENDING,
                            'submitted_by' => auth()->id(),
                            'approved_by' => null,
                            'approved_at' => null,
                            'approval_notes' => null,
                            'is_published' => false,
                        ]);

                        Cache::forget('home_upcoming_events');
                        Cache::forget('home_featured_event');
                        ApprovalNotifications::notifyReviewers(
                            contentType: 'Event',
                            title: $record->title,
                            editUrl: static::getUrl('edit', ['record' => $record]),
                            ministryId: $record->ministry_id ? (int) $record->ministry_id : null,
                            excludeUserId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Event submitted for review')
                            ->success()
                            ->send();
                    }),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Event $record): bool => static::canReview($record))
                    ->form([
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Approval note')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->action(function (Event $record, array $data): void {
                        $record->update([
                            'approval_status' => Event::STATUS_APPROVED,
                            'is_published' => true,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'approval_notes' => $data['approval_notes'] ?? null,
                        ]);

                        Cache::forget('home_upcoming_events');
                        Cache::forget('home_featured_event');
                        ApprovalNotifications::notifySubmitterDecision(
                            submitter: $record->submittedBy,
                            contentType: 'Event',
                            title: $record->title,
                            decision: 'Approved',
                            editUrl: static::getUrl('edit', ['record' => $record]),
                            notes: $data['approval_notes'] ?? null,
                            excludeUserId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Event approved and published')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Event $record): bool => static::canReview($record))
                    ->form([
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Reason')
                            ->rows(3)
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Event $record, array $data): void {
                        $record->update([
                            'approval_status' => Event::STATUS_REJECTED,
                            'is_published' => false,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'approval_notes' => $data['approval_notes'],
                        ]);

                        Cache::forget('home_upcoming_events');
                        Cache::forget('home_featured_event');
                        ApprovalNotifications::notifySubmitterDecision(
                            submitter: $record->submittedBy,
                            contentType: 'Event',
                            title: $record->title,
                            decision: 'Rejected',
                            editUrl: static::getUrl('edit', ['record' => $record]),
                            notes: $data['approval_notes'] ?? null,
                            excludeUserId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Event rejected')
                            ->danger()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn (): bool => (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']))
                        ->action(function ($records): void {
                            $approved = 0;

                            foreach ($records as $record) {
                                if ($record->approval_status !== Event::STATUS_PENDING) {
                                    continue;
                                }

                                $record->update([
                                    'approval_status' => Event::STATUS_APPROVED,
                                    'is_published' => true,
                                    'approved_by' => auth()->id(),
                                    'approved_at' => now(),
                                ]);

                                ApprovalNotifications::notifySubmitterDecision(
                                    submitter: $record->submittedBy,
                                    contentType: 'Event',
                                    title: $record->title,
                                    decision: 'Approved',
                                    editUrl: static::getUrl('edit', ['record' => $record]),
                                    notes: null,
                                    excludeUserId: auth()->id(),
                                );

                                $approved++;
                            }

                            Cache::forget('home_upcoming_events');
                            Cache::forget('home_featured_event');

                            $toast = Notification::make()
                                ->title($approved > 0 ? "Approved {$approved} event(s)" : 'No pending events selected');

                            if ($approved > 0) {
                                $toast->success();
                            } else {
                                $toast->warning();
                            }

                            $toast->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('reject_selected')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn (): bool => (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']))
                        ->form([
                            Forms\Components\Textarea::make('approval_notes')
                                ->label('Reason')
                                ->required()
                                ->rows(3)
                                ->maxLength(500),
                        ])
                        ->action(function ($records, array $data): void {
                            $rejected = 0;

                            foreach ($records as $record) {
                                if ($record->approval_status !== Event::STATUS_PENDING) {
                                    continue;
                                }

                                $record->update([
                                    'approval_status' => Event::STATUS_REJECTED,
                                    'is_published' => false,
                                    'approved_by' => auth()->id(),
                                    'approved_at' => now(),
                                    'approval_notes' => $data['approval_notes'],
                                ]);

                                ApprovalNotifications::notifySubmitterDecision(
                                    submitter: $record->submittedBy,
                                    contentType: 'Event',
                                    title: $record->title,
                                    decision: 'Rejected',
                                    editUrl: static::getUrl('edit', ['record' => $record]),
                                    notes: $data['approval_notes'],
                                    excludeUserId: auth()->id(),
                                );

                                $rejected++;
                            }

                            Cache::forget('home_upcoming_events');
                            Cache::forget('home_featured_event');

                            $toast = Notification::make()
                                ->title($rejected > 0 ? "Rejected {$rejected} event(s)" : 'No pending events selected');

                            if ($rejected > 0) {
                                $toast->warning();
                            } else {
                                $toast->info();
                            }

                            $toast->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'asc')
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasAnyRole(['super_admin', 'content_admin'])) {
            return $query;
        }

        if ($user->hasRole('ministry_admin')) {
            $ministryIds = $user->ministries()->pluck('ministries.id');

            return $query->whereIn('ministry_id', $ministryIds);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin', 'ministry_admin']);
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'content_admin'])) {
            return true;
        }

        return $user->hasRole('ministry_admin') && $user->ministries()->exists();
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasAnyRole(['super_admin', 'content_admin'])) {
            return true;
        }

        return $user->hasRole('ministry_admin')
            && $user->ministries()->whereKey($record->ministry_id)->exists();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canEdit($record);
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $user = auth()->user();

        if ($user?->hasAnyRole(['super_admin', 'content_admin'])) {
            return 'warning';
        }

        if ($user?->hasRole('ministry_admin')) {
            return 'danger';
        }

        return 'gray';
    }

    private static function canSubmitForReview(Event $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (! $user->hasRole('ministry_admin') || $user->hasAnyRole(['super_admin', 'content_admin'])) {
            return false;
        }

        if (! $user->ministries()->whereKey($record->ministry_id)->exists()) {
            return false;
        }

        return in_array($record->approval_status, [Event::STATUS_DRAFT, Event::STATUS_REJECTED], true);
    }

    private static function canReview(Event $record): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->hasAnyRole(['super_admin', 'content_admin'])) {
            return false;
        }

        return $record->approval_status === Event::STATUS_PENDING;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
