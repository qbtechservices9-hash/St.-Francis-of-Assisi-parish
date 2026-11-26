<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\DailyVerseResource\Pages;
use App\Models\DailyVerse;
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

class DailyVerseResource extends Resource
{
    protected static ?string $model = DailyVerse::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-book-open';

    protected static string | UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Daily Verse';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('text')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('verse_date')
                    ->label('Verse Date')
                    ->required(),
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
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('verse_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Visibility')
                    ->badge()
                    ->getStateUsing(fn (DailyVerse $record): string => $record->is_published ? 'Published' : 'Hidden')
                    ->color(fn (DailyVerse $record): string => $record->is_published ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y g:i A')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
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
            ->defaultSort('verse_date', 'desc')
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
            'index' => Pages\ListDailyVerses::route('/'),
            'create' => Pages\CreateDailyVerse::route('/create'),
            'edit' => Pages\EditDailyVerse::route('/{record}/edit'),
        ];
    }
}
