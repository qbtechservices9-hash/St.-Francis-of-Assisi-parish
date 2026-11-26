<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\SocietyResource\Pages;
use App\Models\Society;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SocietyResource extends Resource
{
    protected static ?string $model = Society::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user-group';

    protected static string | UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Groups & Societies';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Entry Type')
                    ->options([
                        'group' => 'Day Born Group',
                        'society' => 'Society',
                    ])
                    ->required()
                    ->default('society')
                    ->helperText('Choose Group for Day Born entries; choose Society for other societies.'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Textarea::make('brief_history')
                    ->required()
                    ->rows(8)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('cover_image')
                    ->label('Cover Image')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('societies/covers')
                    ->image(),
                Forms\Components\FileUpload::make('gallery')
                    ->label('Gallery Images')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('societies/gallery')
                    ->image()
                    ->multiple()
                    ->helperText('Add extra pictures for this society or group.'),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'group' ? 'Day Born Group' : 'Society')
                    ->color(fn (?string $state): string => $state === 'group' ? 'info' : 'success'),
                Tables\Columns\TextColumn::make('brief_history')
                    ->label('History')
                    ->limit(70)
                    ->wrap(),
                Tables\Columns\TextColumn::make('gallery_count')
                    ->label('Gallery')
                    ->getStateUsing(fn (Society $record): string => count($record->gallery ?? []) . ' images'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Visibility')
                    ->badge()
                    ->getStateUsing(fn (Society $record): string => $record->is_published ? 'Published' : 'Hidden')
                    ->color(fn (Society $record): string => $record->is_published ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y g:i A')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'group' => 'Day Born Group',
                        'society' => 'Society',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order')
            ->striped()
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([10, 25, 50]);
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocieties::route('/'),
            'create' => Pages\CreateSociety::route('/create'),
            'edit' => Pages\EditSociety::route('/{record}/edit'),
        ];
    }
}
