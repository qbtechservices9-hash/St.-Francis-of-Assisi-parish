<?php

namespace App\Filament\Resources;

use UnitEnum;

use App\Filament\Resources\SermonResource\Pages;
use App\Models\Sermon;
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

class SermonResource extends Resource
{
    protected static ?string $model = Sermon::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-speaker-wave';

    protected static string | UnitEnum | null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('preacher')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Textarea::make('summary')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('video_url')
                    ->label('Video URL')
                    ->url()
                    ->helperText('Optional: YouTube/Vimeo link or direct video URL.'),
                Forms\Components\FileUpload::make('video_upload')
                    ->label('Video Upload')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('sermons/videos')
                    ->acceptedFileTypes(['video/*'])
                    ->helperText('Upload a video file if you do not have a URL.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('audio_url')
                    ->label('Audio URL')
                    ->url()
                    ->helperText('Optional: direct audio URL (mp3).'),
                Forms\Components\FileUpload::make('audio_upload')
                    ->label('Audio Upload')
                    ->disk(config('filesystems.media_disk', 'public'))
                    ->directory('sermons/audio')
                    ->acceptedFileTypes(['audio/*'])
                    ->helperText('Upload an audio file if you do not have a URL.')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('notes_url')
                    ->label('Notes Download URL')
                    ->url(),
                Forms\Components\Repeater::make('verses')
                    ->label('Key Bible Verses')
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('text')
                            ->rows(2),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('preacher')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Visibility')
                    ->badge()
                    ->getStateUsing(fn (Sermon $record): string => $record->is_published ? 'Published' : 'Hidden')
                    ->color(fn (Sermon $record): string => $record->is_published ? 'success' : 'gray'),
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
            ->defaultSort('date', 'desc')
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
            'index' => Pages\ListSermons::route('/'),
            'create' => Pages\CreateSermon::route('/create'),
            'edit' => Pages\EditSermon::route('/{record}/edit'),
        ];
    }

    public static function prepareFormData(array $data): array
    {
        $videoUpload = $data['video_upload'] ?? null;
        if (is_array($videoUpload)) {
            $videoUpload = $videoUpload[0] ?? null;
        }
        if (is_string($videoUpload) && $videoUpload !== '') {
            $data['video_url'] = $videoUpload;
        }

        $audioUpload = $data['audio_upload'] ?? null;
        if (is_array($audioUpload)) {
            $audioUpload = $audioUpload[0] ?? null;
        }
        if (is_string($audioUpload) && $audioUpload !== '') {
            $data['audio_url'] = $audioUpload;
        }

        unset($data['video_upload'], $data['audio_upload']);

        return $data;
    }
}
