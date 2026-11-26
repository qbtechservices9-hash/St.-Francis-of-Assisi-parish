<?php

namespace App\Filament\Pages;


use App\Support\MediaStorage;
use Illuminate\Support\Facades\Cache;
use App\Models\SiteSetting;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;

class SiteSettingsPage extends FilamentPage implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static \UnitEnum|string|null $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $defaults = $this->defaultSettings();
        $stored = SiteSetting::getMany(array_keys($defaults));

        $this->form->fill(array_merge($defaults, $stored));
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Church Info')
                    ->schema([
                        TextInput::make('church_name')->label('Church Name'),
                        TextInput::make('church_location')->label('Location'),
                        TextInput::make('church_city')->label('City'),
                        FileUpload::make('logo')
                            ->label('Church Logo')
                            ->disk(config('filesystems.media_disk', 'public'))
                            ->directory('settings')
                            ->image()
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Contact')
                    ->schema([
                        TextInput::make('contact_phone')->label('Phone'),
                        TextInput::make('contact_email')->label('Email'),
                        TextInput::make('contact_email_note')->label('Email Note'),
                    ])->columns(2),
                Section::make('Office Hours')
                    ->schema([
                        TextInput::make('office_hours_weekday')->label('Weekdays'),
                        TextInput::make('office_hours_sat')->label('Saturday'),
                        TextInput::make('office_hours_sun')->label('Sunday'),
                    ])->columns(2),
                Section::make('Service Times')
                    ->schema([
                        TextInput::make('service_sunday')->label('Sunday'),
                        TextInput::make('service_wednesday')->label('Wednesday'),
                        TextInput::make('service_friday')->label('Friday'),
                    ])->columns(2),
                Section::make('Social & Map')
                    ->schema([
                        TextInput::make('social_facebook')->label('Facebook URL'),
                        TextInput::make('social_twitter')->label('Twitter/X URL'),
                        TextInput::make('social_instagram')->label('Instagram URL'),
                        TextInput::make('social_youtube')->label('YouTube URL'),
                        TextInput::make('social_tiktok')->label('TikTok URL'),
                        TextInput::make('live_stream_url')->label('Livestream URL'),
                        TextInput::make('map_embed_query')->label('Map Embed Focus'),
                        TextInput::make('map_query')->label('Map Search Query'),
                        TextInput::make('map_link')->label('Map Link'),
                    ])->columns(2),
                Section::make('Visitor Guide')
                    ->schema([
                        Textarea::make('parking_info')
                            ->label('Parking Information')
                            ->rows(4),
                        Textarea::make('what_to_expect')
                            ->label('What to Expect')
                            ->rows(6),
                    ])
                    ->columns(2),
                Section::make('Moderation')
                    ->schema([
                        Toggle::make('approval_global_content_admin_reviewer')
                            ->label('Allow global content admins to review all ministries')
                            ->helperText('When enabled, content admins with no assigned ministry can review submissions from every ministry.')
                            ->default(true),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->data['logo'] = $this->normalizeFileState($this->data['logo'] ?? null);

        foreach ($this->data as $key => $value) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'site']
            );
        }

                Cache::forget('site_settings');
Notification::make()
            ->title('Site settings updated')
            ->success()
            ->send();
    }

    private function normalizeFileState($value): ?string
    {
        if ($value instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            return MediaStorage::storeUploadedFile($value, 'settings');
        }

        if ($value instanceof \Illuminate\Http\UploadedFile) {
            return MediaStorage::storeUploadedFile($value, 'settings');
        }

        if (is_array($value)) {
            $direct = $value['path'] ?? $value['file'] ?? $value['url'] ?? null;
            if (is_string($direct) && $direct !== '') {
                return $direct;
            }

            foreach ($value as $item) {
                if ($item instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    return MediaStorage::storeUploadedFile($item, 'settings');
                }

                if ($item instanceof \Illuminate\Http\UploadedFile) {
                    return MediaStorage::storeUploadedFile($item, 'settings');
                }

                if (is_string($item) && $item !== '') {
                    return $item;
                }

                if (is_array($item)) {
                    $nested = $item['path'] ?? $item['file'] ?? $item['url'] ?? null;
                    if (is_string($nested) && $nested !== '') {
                        return $nested;
                    }
                }
            }

            return null;
        }

        return is_string($value) ? $value : null;
    }

    protected function defaultSettings(): array
    {
        return [
            'church_name' => 'St. Francis Of Assisi Catholic Church',
            'church_location' => 'Awoshie Anyaa',
            'church_city' => 'Accra, Ghana',
            'logo' => null,
            'contact_phone' => '+233 XX XXX XXXX',
            'contact_email' => 'info@stfrancisawoshie.org',
            'contact_email_note' => 'We typically respond within 24-48 hours',
            'office_hours_weekday' => 'Monday - Friday: 9:00 AM - 5:00 PM',
            'office_hours_sat' => 'Saturday: 10:00 AM - 2:00 PM',
            'office_hours_sun' => 'Sunday: Closed (Worship Services Only)',
            'service_sunday' => 'Sunday Mass: 7:00 AM & 9:30 AM',
            'service_wednesday' => 'Wednesday Mass: 6:00 PM',
            'service_friday' => 'Friday Adoration: 5:00 PM',
            'map_embed_query' => 'Ghana',
            'map_query' => 'St. Francis Of Assisi Catholic Church, Awoshie Anyaa, Accra, Ghana',
            'map_link' => 'https://maps.app.goo.gl/wcWSve327pXT6wgh9',
            'social_facebook' => '#',
            'social_twitter' => '#',
            'social_instagram' => '#',
            'social_youtube' => '#',
            'social_tiktok' => '#',
            'live_stream_url' => '#',
            'parking_info' => 'Parking is available near the main entrance. Our ushers can assist first-time guests.',
            'what_to_expect' => 'Arrive a few minutes early for greetings. Worship includes music, prayer, Scripture readings, and a homily. Families and guests are warmly welcomed.',
            'approval_global_content_admin_reviewer' => true,
        ];
    }
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }
}
