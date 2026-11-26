<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\GalleryItemResource\Pages;
use App\Models\GalleryItem;
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
use Illuminate\Validation\ValidationException;

class GalleryItemResource extends Resource
{
    protected static ?string $model = GalleryItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static string | UnitEnum | null $navigationGroup = 'Management';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
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
                    ->helperText('Assign this gallery content to a ministry for ministry-specific management.'),
                Forms\Components\Select::make('type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TextInput::make('category')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('url')
                    ->label(fn ($get) => $get('type') === 'image' ? 'Image URL (optional)' : 'Video URL (optional)')
                    ->placeholder(fn ($get) => $get('type') === 'image'
                        ? 'https://example.com/image.jpg'
                        : 'https://www.youtube.com/embed/...'
                    )
                    ->helperText('Upload files below, or paste a single URL if hosted elsewhere.')
                    ->visible(fn ($get) => in_array($get('type'), ['image', 'video'], true)),
                Forms\Components\FileUpload::make('video_uploads')
                    ->label('Video Uploads')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('gallery/videos')
                    ->acceptedFileTypes(['video/*'])
                    ->multiple()
                    ->visible(fn ($get) => $get('type') === 'video')
                    ->helperText('You can select multiple videos at once.'),
                Forms\Components\FileUpload::make('thumbnail_upload')
                    ->label('Video Thumbnail')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('gallery/thumbnails')
                    ->image()
                    ->visible(fn ($get) => $get('type') === 'video')
                    ->helperText('Optional thumbnail used for all uploaded videos.'),
                Forms\Components\FileUpload::make('image_uploads')
                    ->label('Image Uploads')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('gallery/images')
                    ->image()
                    ->multiple()
                    ->visible(fn ($get) => $get('type') === 'image')
                    ->helperText('You can select multiple images at once.'),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(true)
                    ->visible(fn (): bool => (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin'])),
                Forms\Components\Placeholder::make('approval_status_display')
                    ->label('Approval Status')
                    ->content(fn (?GalleryItem $record): string => GalleryItem::approvalStatusOptions()[$record?->approval_status ?? GalleryItem::STATUS_DRAFT] ?? 'Draft')
                    ->visible(fn (?GalleryItem $record): bool => $record !== null),
                Forms\Components\Placeholder::make('submitted_by_display')
                    ->label('Submitted By')
                    ->content(fn (?GalleryItem $record): string => $record?->submittedBy?->name ?? '-')
                    ->visible(fn (?GalleryItem $record): bool => $record !== null),
                Forms\Components\Placeholder::make('approved_by_display')
                    ->label('Reviewed By')
                    ->content(fn (?GalleryItem $record): string => $record?->approvedBy?->name ?? '-')
                    ->visible(fn (?GalleryItem $record): bool => $record !== null),
                Forms\Components\Placeholder::make('approved_at_display')
                    ->label('Reviewed At')
                    ->content(fn (?GalleryItem $record): string => $record?->approved_at?->format('M j, Y g:i A') ?? '-')
                    ->visible(fn (?GalleryItem $record): bool => $record !== null),
                Forms\Components\Placeholder::make('approval_notes_display')
                    ->label('Reviewer Note')
                    ->content(fn (?GalleryItem $record): string => $record?->approval_notes ?: '-')
                    ->visible(fn (?GalleryItem $record): bool => $record !== null)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('ministry.name')
                    ->label('Ministry')
                    ->badge()
                    ->color('info')
                    ->placeholder('General')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'video' ? 'info' : 'success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('approval_status')
                    ->label('Approval')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => GalleryItem::approvalStatusOptions()[$state ?? GalleryItem::STATUS_DRAFT] ?? 'Draft')
                    ->color(fn (?string $state): string => match ($state) {
                        GalleryItem::STATUS_APPROVED => 'success',
                        GalleryItem::STATUS_PENDING => 'warning',
                        GalleryItem::STATUS_REJECTED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Visibility')
                    ->badge()
                    ->getStateUsing(fn (GalleryItem $record): string => $record->is_published ? 'Published' : 'Hidden')
                    ->color(fn (GalleryItem $record): string => $record->is_published ? 'success' : 'gray'),
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('ministry_id')
                    ->label('Ministry')
                    ->options(fn () => Ministry::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('type')
                    ->options(['image' => 'Image', 'video' => 'Video']),
                Tables\Filters\SelectFilter::make('approval_status')
                    ->label('Approval')
                    ->default(fn (): ?string => auth()->user()?->hasAnyRole(['super_admin', 'content_admin']) ? GalleryItem::STATUS_PENDING : null)
                    ->options(GalleryItem::approvalStatusOptions()),
                Tables\Filters\Filter::make('needs_action')
                    ->label('Needs Action')
                    ->default(fn (): bool => (bool) auth()->user()?->hasRole('ministry_admin'))
                    ->query(fn (Builder $query): Builder => $query->whereIn('approval_status', [
                        GalleryItem::STATUS_DRAFT,
                        GalleryItem::STATUS_REJECTED,
                    ])),
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('submit_for_review')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (GalleryItem $record): bool => static::canSubmitForReview($record))
                    ->action(function (GalleryItem $record): void {
                        $record->update([
                            'approval_status' => GalleryItem::STATUS_PENDING,
                            'submitted_by' => auth()->id(),
                            'approved_by' => null,
                            'approved_at' => null,
                            'approval_notes' => null,
                            'is_published' => false,
                        ]);

                        ApprovalNotifications::notifyReviewers(
                            contentType: 'Gallery Item',
                            title: $record->title,
                            editUrl: static::getUrl('edit', ['record' => $record]),
                            ministryId: $record->ministry_id ? (int) $record->ministry_id : null,
                            excludeUserId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Gallery item submitted for review')
                            ->success()
                            ->send();
                    }),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (GalleryItem $record): bool => static::canReview($record))
                    ->form([
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Approval note')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->action(function (GalleryItem $record, array $data): void {
                        $record->update([
                            'approval_status' => GalleryItem::STATUS_APPROVED,
                            'is_published' => true,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'approval_notes' => $data['approval_notes'] ?? null,
                        ]);

                        ApprovalNotifications::notifySubmitterDecision(
                            submitter: $record->submittedBy,
                            contentType: 'Gallery Item',
                            title: $record->title,
                            decision: 'Approved',
                            editUrl: static::getUrl('edit', ['record' => $record]),
                            notes: $data['approval_notes'] ?? null,
                            excludeUserId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Gallery item approved and published')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (GalleryItem $record): bool => static::canReview($record))
                    ->form([
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Reason')
                            ->rows(3)
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (GalleryItem $record, array $data): void {
                        $record->update([
                            'approval_status' => GalleryItem::STATUS_REJECTED,
                            'is_published' => false,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'approval_notes' => $data['approval_notes'],
                        ]);

                        ApprovalNotifications::notifySubmitterDecision(
                            submitter: $record->submittedBy,
                            contentType: 'Gallery Item',
                            title: $record->title,
                            decision: 'Rejected',
                            editUrl: static::getUrl('edit', ['record' => $record]),
                            notes: $data['approval_notes'] ?? null,
                            excludeUserId: auth()->id(),
                        );

                        Notification::make()
                            ->title('Gallery item rejected')
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
                                if ($record->approval_status !== GalleryItem::STATUS_PENDING) {
                                    continue;
                                }

                                $record->update([
                                    'approval_status' => GalleryItem::STATUS_APPROVED,
                                    'is_published' => true,
                                    'approved_by' => auth()->id(),
                                    'approved_at' => now(),
                                ]);

                                ApprovalNotifications::notifySubmitterDecision(
                                    submitter: $record->submittedBy,
                                    contentType: 'Gallery Item',
                                    title: $record->title,
                                    decision: 'Approved',
                                    editUrl: static::getUrl('edit', ['record' => $record]),
                                    notes: null,
                                    excludeUserId: auth()->id(),
                                );

                                $approved++;
                            }

                            $toast = Notification::make()
                                ->title($approved > 0 ? "Approved {$approved} gallery item(s)" : 'No pending gallery items selected');

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
                                if ($record->approval_status !== GalleryItem::STATUS_PENDING) {
                                    continue;
                                }

                                $record->update([
                                    'approval_status' => GalleryItem::STATUS_REJECTED,
                                    'is_published' => false,
                                    'approved_by' => auth()->id(),
                                    'approved_at' => now(),
                                    'approval_notes' => $data['approval_notes'],
                                ]);

                                ApprovalNotifications::notifySubmitterDecision(
                                    submitter: $record->submittedBy,
                                    contentType: 'Gallery Item',
                                    title: $record->title,
                                    decision: 'Rejected',
                                    editUrl: static::getUrl('edit', ['record' => $record]),
                                    notes: $data['approval_notes'],
                                    excludeUserId: auth()->id(),
                                );

                                $rejected++;
                            }

                            $toast = Notification::make()
                                ->title($rejected > 0 ? "Rejected {$rejected} gallery item(s)" : 'No pending gallery items selected');

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
            ->defaultSort('date', 'desc')
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

    private static function canSubmitForReview(GalleryItem $record): bool
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

        return in_array($record->approval_status, [GalleryItem::STATUS_DRAFT, GalleryItem::STATUS_REJECTED], true);
    }

    private static function canReview(GalleryItem $record): bool
    {
        $user = auth()->user();

        if (! $user || ! $user->hasAnyRole(['super_admin', 'content_admin'])) {
            return false;
        }

        return $record->approval_status === GalleryItem::STATUS_PENDING;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleryItems::route('/'),
            'create' => Pages\CreateGalleryItem::route('/create'),
            'edit' => Pages\EditGalleryItem::route('/{record}/edit'),
        ];
    }

    public static function prepareFormData(array $data, ?GalleryItem $record = null, bool $keepUploads = false): array
    {
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        $imageUploads = $data['image_uploads'] ?? [];
        $videoUploads = $data['video_uploads'] ?? [];

        if (is_array($imageUploads) && !empty($imageUploads) && empty($data['url'])) {
            $data['url'] = $imageUploads[0];
        }

        if (is_array($videoUploads) && !empty($videoUploads) && empty($data['url'])) {
            $data['url'] = $videoUploads[0];
        }

        if (!empty($data['thumbnail_upload'])) {
            $data['thumbnail'] = $data['thumbnail_upload'];
        }

        if (!$keepUploads) {
            unset($data['image_uploads'], $data['video_uploads'], $data['thumbnail_upload']);
        }

        if (($data['type'] ?? null) === 'video') {
            $hasUploads = is_array($videoUploads) && !empty($videoUploads);
            $hasUrl = !empty($data['url']) || !empty($record?->url) || $hasUploads;
            if (!$hasUrl) {
                throw ValidationException::withMessages([
                    'url' => 'Please provide a video URL or upload at least one video file.',
                ]);
            }
        }

        if (($data['type'] ?? null) === 'image') {
            $hasUploads = is_array($imageUploads) && !empty($imageUploads);
            $hasImage = !empty($data['url']) || !empty($record?->url) || $hasUploads;
            if (!$hasImage) {
                throw ValidationException::withMessages([
                    'image_uploads' => 'Please upload at least one image for this gallery item.',
                ]);
            }
        }

        return $data;
    }
}
