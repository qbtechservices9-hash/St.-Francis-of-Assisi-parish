<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\ServiceScheduleResource\Pages;
use App\Models\ServiceSchedule;
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

class ServiceScheduleResource extends Resource
{
    protected static ?string $model = ServiceSchedule::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string | UnitEnum | null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Service Schedule';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('day_of_week')
                    ->options([
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                        'Sunday' => 'Sunday',
                    ])
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('time')
                    ->label('Time')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('location')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('calendar_url')
                    ->label('Add To Calendar Link')
                    ->url()
                    ->helperText('Optional link for Google/Apple calendar.'),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
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
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('day_of_week')->sortable(),
                Tables\Columns\TextColumn::make('time')->sortable(),
                Tables\Columns\TextColumn::make('location')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (ServiceSchedule $record): string => $record->is_active ? 'Active' : 'Inactive')
                    ->color(fn (ServiceSchedule $record): string => $record->is_active ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('order')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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
            'index' => Pages\ListServiceSchedules::route('/'),
            'create' => Pages\CreateServiceSchedule::route('/create'),
            'edit' => Pages\EditServiceSchedule::route('/{record}/edit'),
        ];
    }
}
