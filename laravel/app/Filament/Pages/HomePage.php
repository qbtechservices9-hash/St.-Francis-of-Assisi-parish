<?php

namespace App\Filament\Pages;


use Illuminate\Support\Facades\Cache;
use App\Models\Page;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;

class HomePage extends FilamentPage implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Home Page';
    protected static \UnitEnum|string|null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.home-page';

    public ?array $data = [];

    public function mount(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'home'],
            ['title' => 'Home', 'sections' => []]
        );

        $sections = is_array($page->sections) ? $page->sections : [];
        $this->form->fill(!empty($sections) ? $sections : $this->defaultSections());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Hero')
                    ->schema([
                        TextInput::make('hero.title')->required(),
                        TextInput::make('hero.subtitle'),
                        Textarea::make('hero.description')->rows(3),
                        FileUpload::make('hero.background_image')
                            ->label('Background Image')
                            ->disk(config('filesystems.media_disk', 'public'))
                            ->directory('pages/home')
                            ->image()
                            ->dehydrateStateUsing(fn ($state) => $this->normalizeFileState($state))
                            ->formatStateUsing(fn ($state) => is_array($state) ? $state : ($state ? [$state] : [])),
                        TextInput::make('hero.primary_cta_label')->label('Primary Button Label'),
                        TextInput::make('hero.primary_cta_url')->label('Primary Button URL'),
                        TextInput::make('hero.secondary_cta_label')->label('Secondary Button Label'),
                        TextInput::make('hero.secondary_cta_url')->label('Secondary Button URL'),
                        Repeater::make('hero.slides')
                            ->label('Hero Slides')
                            ->schema([
                                TextInput::make('title')->required(),
                                TextInput::make('subtitle'),
                                Textarea::make('description')->rows(3)->columnSpanFull(),
                                FileUpload::make('background_image')
                                    ->label('Slide Background Image')
                                    ->disk(config('filesystems.media_disk', 'public'))
                                    ->directory('pages/home/slides')
                                    ->image()
                                    ->dehydrateStateUsing(fn ($state) => $this->normalizeFileState($state))
                                    ->formatStateUsing(fn ($state) => is_array($state) ? $state : ($state ? [$state] : []))
                                    ->columnSpanFull(),
                                TextInput::make('primary_cta_label')->label('Primary Button Label'),
                                TextInput::make('primary_cta_url')->label('Primary Button URL'),
                                TextInput::make('secondary_cta_label')->label('Secondary Button Label'),
                                TextInput::make('secondary_cta_url')->label('Secondary Button URL'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => $state['title'] ?? 'Slide')
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('Welcome Section')
                    ->schema([
                        TextInput::make('welcome.title'),
                        Repeater::make('welcome.paragraphs')
                            ->schema([
                                Textarea::make('text')->rows(2)->required(),
                            ])
                            ->defaultItems(2)
                            ->itemLabel(fn (array $state): string => 'Paragraph')
                            ->dehydrateStateUsing(fn ($state) => collect($state)->pluck('text')->all())
                            ->formatStateUsing(function ($state) {
                                if (is_array($state) && isset($state[0]['text'])) {
                                    return $state;
                                }
                                return collect($state ?? [])->map(fn ($text) => ['text' => $text])->all();
                            })
                            ->columnSpanFull(),
                        FileUpload::make('welcome.image')
                            ->label('Welcome Image')
                            ->disk(config('filesystems.media_disk', 'public'))
                            ->directory('pages/home')
                            ->image()
                            ->dehydrateStateUsing(fn ($state) => $this->normalizeFileState($state))
                            ->formatStateUsing(fn ($state) => is_array($state) ? $state : ($state ? [$state] : [])),
                        TextInput::make('welcome.cta_label')->label('Button Label'),
                        TextInput::make('welcome.cta_url')->label('Button URL'),
                    ])->columns(2),
                Section::make('Features')
                    ->schema([
                        TextInput::make('features_title')->label('Section Title'),
                        TextInput::make('features_subtitle')->label('Section Subtitle'),
                        Repeater::make('features')
                            ->schema([
                                TextInput::make('title')->required(),
                                Textarea::make('text')->rows(2)->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(4),
                    ]),
                Section::make('Announcements')
                    ->schema([
                        Repeater::make('announcements')
                            ->schema([
                                TextInput::make('badge')->label('Badge')->placeholder('Announcement'),
                                TextInput::make('title')->required(),
                                Textarea::make('text')->rows(3)->required()->columnSpanFull(),
                                TextInput::make('link_label')->label('Button Label'),
                                TextInput::make('link_url')->label('Button URL'),
                            ])
                            ->columns(2)
                            ->defaultItems(3)
                            ->collapsible()
                            ->reorderable()
                            ->itemLabel(fn (array $state): string => $state['title'] ?? 'Announcement'),
                    ]),
                Section::make('Events Section')
                    ->schema([
                        TextInput::make('events.title')->label('Section Title'),
                        TextInput::make('events.subtitle')->label('Section Subtitle'),
                        TextInput::make('events.cta_label')->label('Button Label'),
                        TextInput::make('events.cta_url')->label('Button URL'),
                    ])->columns(2),
                Section::make('Call To Action')
                    ->schema([
                        TextInput::make('cta.title')->label('Title'),
                        Textarea::make('cta.text')->label('Text')->rows(3),
                        TextInput::make('cta.location')->label('Location'),
                        TextInput::make('cta.service_time')->label('Service Time'),
                        TextInput::make('cta.button_label')->label('Button Label'),
                        TextInput::make('cta.button_url')->label('Button URL'),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $sections = $this->form->getState();

        $sections['hero']['background_image'] = $this->normalizeFileState($sections['hero']['background_image'] ?? null);
        $sections['hero']['slides'] = $this->normalizeHeroSlides($sections['hero']['slides'] ?? []);
        $sections['welcome']['image'] = $this->normalizeFileState($sections['welcome']['image'] ?? null);

        Page::query()->updateOrCreate(
            ['slug' => 'home'],
            ['title' => 'Home', 'sections' => $sections]
        );

                Cache::forget('home_sections');
        Cache::forget('home_upcoming_events');
Notification::make()
            ->title('Home page updated')
            ->success()
            ->send();
    }
    private function normalizeFileState($value): ?string
    {
        if (is_array($value)) {
            $direct = $value['path'] ?? $value['file'] ?? $value['url'] ?? null;
            if (is_string($direct) && $direct !== '') {
                return $direct;
            }

            foreach ($value as $item) {
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

    private function normalizeHeroSlides(?array $slides): array
    {
        return collect($slides ?? [])
            ->map(function ($slide) {
                if (! is_array($slide)) {
                    return null;
                }

                return [
                    'title' => $slide['title'] ?? '',
                    'subtitle' => $slide['subtitle'] ?? '',
                    'description' => $slide['description'] ?? '',
                    'background_image' => $this->normalizeFileState($slide['background_image'] ?? null),
                    'primary_cta_label' => $slide['primary_cta_label'] ?? '',
                    'primary_cta_url' => $slide['primary_cta_url'] ?? '',
                    'secondary_cta_label' => $slide['secondary_cta_label'] ?? '',
                    'secondary_cta_url' => $slide['secondary_cta_url'] ?? '',
                ];
            })
            ->filter(fn ($slide) => is_array($slide) && filled($slide['title']) && filled($slide['background_image']))
            ->values()
            ->all();
    }

    protected function defaultSections(): array
    {
        return [
            'hero' => [
                'title' => 'Welcome to St. Francis Of Assisi',
                'subtitle' => 'Catholic Church, Awoshie Anyaa',
                'description' => 'A community where faith, hope, and love come together. Join us as we worship and serve together.',
                'primary_cta_label' => 'Learn More',
                'primary_cta_url' => '/about',
                'secondary_cta_label' => 'Visit Us',
                'secondary_cta_url' => '/contact',
                'background_image' => 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjaHVyY2glMjBpbnRlcmlvciUyMHN1bmxpZ2h0fGVufDF8fHx8MTc3MjM3NTU1N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'slides' => [],
            ],
            'welcome' => [
                'title' => 'Welcome Home',
                'paragraphs' => [
                    'St. Francis Of Assisi Catholic Church is a vibrant community of believers dedicated to spreading the love of Christ in Awoshie Anyaa and beyond.',
                    'Whether you\'re a longtime Catholic or exploring faith for the first time, you\'ll find a warm welcome here. Our doors are open, and our hearts are ready to embrace you.',
                ],
                'cta_label' => 'Discover Our Story',
                'cta_url' => '/about',
                'image' => 'https://images.unsplash.com/photo-1644292364271-59f2b61a2323?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwZW9wbGUlMjBwcmF5aW5nJTIwd29yc2hpcHxlbnwxfHx8fDE3NzIzODQyMjN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
            ],
            'features_title' => 'Why Join Our Parish?',
            'features_subtitle' => 'We are more than just a church - we\'re a family united in faith and purpose.',
            'features' => [
                ['title' => 'Welcoming Community', 'text' => 'Experience the warmth of genuine fellowship and belonging.'],
                ['title' => 'Biblical Teaching', 'text' => 'Grow in your faith through sound doctrine and spiritual guidance.'],
                ['title' => 'Active Ministries', 'text' => 'Find your place to serve and make a difference in our community.'],
                ['title' => 'Engaging Events', 'text' => 'Participate in activities that strengthen faith and build community.'],
            ],
            'announcements' => [
                [
                    'badge' => 'Announcement',
                    'title' => 'Welcome to the parish',
                    'text' => 'Use this section for important church notices, upcoming drives, special liturgical seasons, and messages for first-time visitors.',
                    'link_label' => 'Contact us',
                    'link_url' => '/contact',
                ],
                [
                    'badge' => 'Formation',
                    'title' => 'Weekly reflection',
                    'text' => 'Highlight a sermon, parish article, or spiritual emphasis here so the homepage stays current and pastoral.',
                    'link_label' => 'View sermons',
                    'link_url' => '/sermons',
                ],
                [
                    'badge' => 'Event',
                    'title' => 'Prepare for the next parish gathering',
                    'text' => 'Point visitors to upcoming liturgies, feast celebrations, ministry meetings, and outreach events from one place.',
                    'link_label' => 'View events',
                    'link_url' => '/events',
                ],
            ],
            'events' => [
                'title' => 'Upcoming Events',
                'subtitle' => 'Join us for worship and fellowship',
                'cta_label' => 'View All Events',
                'cta_url' => '/events',
            ],
            'cta' => [
                'title' => 'Plan Your Visit',
                'text' => 'We would love to meet you. Join us for worship this Sunday or reach out if you have any questions.',
                'location' => 'Awoshie Anyaa, Accra',
                'service_time' => 'Sundays: 7:00 AM & 9:30 AM',
                'button_label' => 'Get Directions',
                'button_url' => '/contact',
            ],
        ];
    }
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }
}
