<?php

namespace App\Filament\Pages;


use Illuminate\Support\Facades\Cache;
use App\Models\Page;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page as FilamentPage;

class AboutPage extends FilamentPage implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'About Page';
    protected static \UnitEnum|string|null $navigationGroup = 'Content';
    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.about-page';

    public ?array $data = [];

    public function mount(): void
    {
        $page = Page::query()->firstOrCreate(
            ['slug' => 'about'],
            ['title' => 'About', 'sections' => []]
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
                        \Filament\Forms\Components\FileUpload::make('hero.background_image')
                            ->label('Background Image')
                            ->disk(config('filesystems.media_disk', 'public'))
                            ->directory('pages/about')
                            ->image()
                            ->dehydrateStateUsing(fn ($state) => $this->normalizeFileState($state))
                            ->formatStateUsing(fn ($state) => is_array($state) ? $state : ($state ? [$state] : [])),
                    ])->columns(2),
                Section::make('Story')
                    ->schema([
                        TextInput::make('story.title'),
                        Repeater::make('story.paragraphs')
                            ->schema([
                                Textarea::make('text')->rows(2)->required(),
                            ])
                            ->defaultItems(3)
                            ->itemLabel(fn (): string => 'Paragraph')
                            ->dehydrateStateUsing(fn ($state) => collect($state)->pluck('text')->all())
                            ->formatStateUsing(function ($state) {
                                if (is_array($state) && isset($state[0]['text'])) {
                                    return $state;
                                }
                                return collect($state ?? [])->map(fn ($text) => ['text' => $text])->all();
                            })
                            ->columnSpanFull(),
                        \Filament\Forms\Components\FileUpload::make('story.image')
                            ->label('Story Image')
                            ->disk(config('filesystems.media_disk', 'public'))
                            ->directory('pages/about')
                            ->image()
                            ->dehydrateStateUsing(fn ($state) => $this->normalizeFileState($state))
                            ->formatStateUsing(fn ($state) => is_array($state) ? $state : ($state ? [$state] : [])),
                    ])->columns(2),
                Section::make('Mission & Vision')
                    ->schema([
                        TextInput::make('mission.title')->label('Mission Title'),
                        Textarea::make('mission.text')->label('Mission Text')->rows(3),
                        TextInput::make('vision.title')->label('Vision Title'),
                        Textarea::make('vision.text')->label('Vision Text')->rows(3),
                    ])->columns(2),
                Section::make('Values')
                    ->schema([
                        Repeater::make('values')
                            ->schema([
                                TextInput::make('title')->required(),
                                Textarea::make('text')->rows(2)->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(3),
                    ]),
                Section::make('Sacraments')
                    ->schema([
                        TextInput::make('sacraments.title'),
                        TextInput::make('sacraments.subtitle'),
                        Repeater::make('sacraments.items')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Sacrament Name')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2),
                                Textarea::make('process')
                                    ->label('How To Receive')
                                    ->rows(3)
                                    ->helperText('Outline the steps or process for receiving this sacrament.'),
                            ])
                            ->defaultItems(8)
                            ->itemLabel(fn (): string => 'Sacrament')
                            ->dehydrateStateUsing(fn ($state) => collect($state ?? [])->map(function ($item) {
                                if (!is_array($item)) {
                                    return [
                                        'name' => (string) $item,
                                        'description' => '',
                                        'process' => '',
                                    ];
                                }

                                return [
                                    'name' => $item['name'] ?? ($item['item'] ?? ($item['title'] ?? '')),
                                    'description' => $item['description'] ?? '',
                                    'process' => $item['process'] ?? '',
                                ];
                            })->values()->all())
                            ->formatStateUsing(function ($state) {
                                if (is_array($state) && isset($state[0]['name'])) {
                                    return $state;
                                }
                                return collect($state ?? [])->map(function ($item) {
                                    if (is_array($item)) {
                                        return [
                                            'name' => $item['name'] ?? ($item['item'] ?? ($item['title'] ?? '')),
                                            'description' => $item['description'] ?? '',
                                            'process' => $item['process'] ?? '',
                                        ];
                                    }

                                    return [
                                        'name' => $item,
                                        'description' => '',
                                        'process' => '',
                                    ];
                                })->values()->all();
                            }),
                    ])->columns(2),
                Section::make('St. Francis')
                    ->schema([
                        TextInput::make('saint.title'),
                        Repeater::make('saint.paragraphs')
                            ->schema([
                                Textarea::make('text')->rows(2)->required(),
                            ])
                            ->defaultItems(3)
                            ->itemLabel(fn (): string => 'Paragraph')
                            ->dehydrateStateUsing(fn ($state) => collect($state)->pluck('text')->all())
                            ->formatStateUsing(function ($state) {
                                if (is_array($state) && isset($state[0]['text'])) {
                                    return $state;
                                }
                                return collect($state ?? [])->map(fn ($text) => ['text' => $text])->all();
                            })
                            ->columnSpanFull(),
                    ])->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $sections = $this->form->getState();

        $sections['hero']['background_image'] = $this->normalizeFileState($sections['hero']['background_image'] ?? null);
        $sections['story']['image'] = $this->normalizeFileState($sections['story']['image'] ?? null);

        Page::query()->updateOrCreate(
            ['slug' => 'about'],
            ['title' => 'About', 'sections' => $sections]
        );

                Cache::forget('about_sections');
Notification::make()
            ->title('About page updated')
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
    protected function defaultSections(): array
    {
        return [
            'hero' => [
                'title' => 'About Us',
                'subtitle' => 'Our Story, Mission, and Values',
                'background_image' => 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjaHVyY2glMjBpbnRlcmlvciUyMHN1bmxpZ2h0fGVufDF8fHx8MTc3MjM3NTU1N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
            ],
            'story' => [
                'title' => 'Our Story',
                'paragraphs' => [
                    'St. Francis Of Assisi Catholic Church was established in Awoshie Anyaa with a vision to create a spiritual home for the faithful in our community. Named after the beloved saint known for his humility, love of nature, and dedication to the poor, our parish strives to embody these same virtues.',
                    'Over the years, we have grown from a small gathering of believers to a thriving parish community. Through God\'s grace and the dedication of our parishioners, we continue to be a beacon of faith, hope, and love in Awoshie Anyaa.',
                    'Today, we are blessed to serve hundreds of families, providing spiritual nourishment, sacramental life, and opportunities for fellowship and service.',
                ],
                'image' => 'https://images.unsplash.com/photo-1709390658353-08d14087f616?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxnb3NwZWwlMjBiaWJsZSUyMG9wZW58ZW58MXx8fHwxNzcyMzg0MjI0fDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
            ],
            'mission' => [
                'title' => 'Our Mission',
                'text' => 'To proclaim the Gospel of Jesus Christ, celebrate the sacraments, and serve as a spiritual family that nurtures faith, fosters community, and responds to the needs of all people with love and compassion.',
            ],
            'vision' => [
                'title' => 'Our Vision',
                'text' => 'To be a vibrant Catholic community where every person encounters Christ, grows in discipleship, and lives out their faith through prayer, worship, and service to others, making a transformative impact on Awoshie Anyaa and beyond.',
            ],
            'values' => [
                ['title' => 'Faith', 'text' => 'We believe in the teachings of Jesus Christ and the Catholic Church, rooted in Scripture and Tradition.'],
                ['title' => 'Community', 'text' => 'We foster genuine relationships and create a welcoming environment where everyone belongs and is valued.'],
                ['title' => 'Service', 'text' => 'Following St. Francis\'s example, we serve the poor, care for creation, and work for justice and peace.'],
            ],
            'sacraments' => [
                'title' => 'Sacraments',
                'subtitle' => 'We celebrate the seven sacraments of the Catholic Church.',
                'items' => [
                    ['name' => 'Baptism', 'description' => '', 'process' => ''],
                    ['name' => 'Confirmation', 'description' => '', 'process' => ''],
                    ['name' => 'Eucharist', 'description' => '', 'process' => ''],
                    ['name' => 'Reconciliation', 'description' => '', 'process' => ''],
                    ['name' => 'Anointing of the Sick', 'description' => '', 'process' => ''],
                    ['name' => 'Holy Orders', 'description' => '', 'process' => ''],
                    ['name' => 'Matrimony', 'description' => '', 'process' => ''],
                ],
            ],
            'saint' => [
                'title' => 'About St. Francis Of Assisi',
                'paragraphs' => [
                    'St. Francis of Assisi (1181-1226) was an Italian Catholic friar, deacon, mystic, and preacher. He founded the Franciscan order and is one of the most venerated religious figures in Christianity.',
                    'Born into a wealthy family, Francis renounced his inheritance to live a life of poverty, dedicating himself to serving the poor and sick. He is known for his love of nature and animals, his profound humility, and his commitment to peace.',
                    'His famous prayer, "Lord, make me an instrument of your peace," continues to inspire millions around the world. His feast day is celebrated on October 4th, and he is the patron saint of animals, ecology, and Italy.',
                ],
            ],
        ];
    }
    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->hasAnyRole(['super_admin', 'content_admin']);
    }
}
