<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\MinistryResource\Pages;
use App\Models\Ministry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MinistryResource extends Resource
{
    protected static ?string $model = Ministry::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static string | UnitEnum | null $navigationGroup = 'Management';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('Generated automatically from the name.'),
                Forms\Components\Textarea::make('description')
                    ->label('Short Description')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('summary')
                    ->label('Summary')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('details')
                    ->label('Detailed Description')
                    ->rows(6)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('contact_email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('contact_phone')
                    ->label('Contact Phone')
                    ->tel()
                    ->maxLength(50),
                Forms\Components\TextInput::make('meeting_time')
                    ->label('Meeting Time')
                    ->maxLength(255),
                Forms\Components\TextInput::make('icon')
                    ->maxLength(50)
                    ->default('users'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\FileUpload::make('image')
                    ->label('Cover Image / Logo')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('ministries')
                    ->image(),
                Forms\Components\Repeater::make('media')
                    ->label('Ministry Gallery')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'image' => 'Image',
                                'video' => 'Video',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('caption')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('url')
                            ->label(fn ($get) => $get('type') === 'video' ? 'Video URL (optional)' : 'Image URL (optional)')
                            ->placeholder(fn ($get) => $get('type') === 'video'
                                ? 'https://www.youtube.com/watch?v=... or direct mp4 link'
                                : 'https://example.com/image.jpg'
                            )
                            ->helperText('Upload a file below, or paste a URL if hosted elsewhere.'),
                        Forms\Components\FileUpload::make('image_upload')
                            ->label('Upload Image')
                            ->disk(config('filesystems.media_disk', 'public'))
                            ->directory('ministries/media')
                            ->image()
                            ->visible(fn ($get) => $get('type') === 'image'),
                        Forms\Components\FileUpload::make('video_upload')
                            ->label('Upload Video')
                            ->disk(config('filesystems.media_disk', 'public'))
                            ->directory('ministries/media')
                            ->acceptedFileTypes(['video/*'])
                            ->visible(fn ($get) => $get('type') === 'video'),
                    ])
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->helperText('Add multiple images or videos to showcase this ministry.'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('contact_phone')->label('Phone'),
                Tables\Columns\TextColumn::make('meeting_time')->label('Meeting')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (Ministry $record): string => $record->is_active ? 'Active' : 'Inactive')
                    ->color(fn (Ministry $record): string => $record->is_active ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('order')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->visible(fn () => auth()->user()?->hasRole('super_admin') ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => auth()->user()?->hasRole('super_admin') ?? false),
            ])
            ->defaultSort('order', 'asc')
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

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('ministry_admin')) {
            $ids = $user->ministries()->pluck('ministries.id');

            return $query->whereIn('id', $ids);
        }

        return $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) $user?->hasAnyRole(['super_admin', 'ministry_admin']);
    }

    public static function canCreate(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        return $user->hasRole('ministry_admin')
            && $user->ministries()->whereKey($record->getKey())->exists();
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMinistries::route('/'),
            'create' => Pages\CreateMinistry::route('/create'),
            'edit' => Pages\EditMinistry::route('/{record}/edit'),
        ];
    }
}