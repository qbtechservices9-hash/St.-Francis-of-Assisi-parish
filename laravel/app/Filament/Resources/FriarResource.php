<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\FriarResource\Pages;
use App\Models\Friar;
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

class FriarResource extends Resource
{
    protected static ?string $model = Friar::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-identification';

    protected static string | UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Friars';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('photo')
                    ->label('Photo')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('friars')
                    ->image(),
                Forms\Components\TextInput::make('served_from_year')
                    ->label('Year Started')
                    ->required()
                    ->numeric()
                    ->minValue(1800)
                    ->maxValue(2100),
                Forms\Components\TextInput::make('served_to_year')
                    ->label('Year Ended')
                    ->numeric()
                    ->minValue(1800)
                    ->maxValue(2100)
                    ->helperText('Leave blank if the service period is ongoing or open-ended.'),
                Forms\Components\Repeater::make('collaborators')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->defaultItems(0)
                    ->itemLabel(fn (): string => 'Collaborator')
                    ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->pluck('name')->filter()->values()->all())
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && isset($state[0]['name'])) {
                            return $state;
                        }

                        return collect($state ?? [])
                            ->map(fn ($name) => ['name' => $name])
                            ->values()
                            ->all();
                    })
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->rows(5)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
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
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Photo')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_period_label')
                    ->label('Years Served')
                    ->sortable(['served_from_year', 'served_to_year']),
                Tables\Columns\TextColumn::make('collaborators_count')
                    ->label('Collaborators')
                    ->getStateUsing(fn (Friar $record): string => count($record->collaborators ?? []) . ' listed'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Visibility')
                    ->badge()
                    ->getStateUsing(fn (Friar $record): string => $record->is_published ? 'Published' : 'Hidden')
                    ->color(fn (Friar $record): string => $record->is_published ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
            ])
            ->filters([
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
            ->defaultSort('served_from_year')
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
            'index' => Pages\ListFriars::route('/'),
            'create' => Pages\CreateFriar::route('/create'),
            'edit' => Pages\EditFriar::route('/{record}/edit'),
        ];
    }
}
