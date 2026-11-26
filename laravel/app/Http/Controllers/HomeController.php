<?php

namespace App\Http\Controllers;

use App\Support\MediaStorage;
use App\Models\Event;
use App\Models\DailyVerse;
use App\Models\Friar;
use App\Models\GalleryItem;
use App\Models\Ministry;
use App\Models\PastoralCouncilChairperson;
use App\Models\Page;
use App\Models\Sermon;
use App\Models\ServiceSchedule;
use App\Models\Society;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $homeMinistries = Cache::remember('home_ministries_preview', now()->addMinutes(10), function () {
            return Ministry::active()->take(6)->get();
        });

        $upcomingEvents = Cache::remember('home_upcoming_events', now()->addMinutes(5), function () {
            return Event::published()->upcoming()->take(4)->get();
        });

        $homeGallery = Cache::remember('home_gallery_preview', now()->addMinutes(10), function () {
            return GalleryItem::published()->take(6)->get();
        });

        $homeSocieties = Cache::remember('home_societies_preview', now()->addMinutes(15), function () {
            return Society::published()->take(6)->get();
        });

        $homeFriars = Cache::remember('home_friars_preview', now()->addMinutes(15), function () {
            return Friar::published()->take(2)->get();
        });

        $homeChairpersons = Cache::remember('home_chairpersons_preview', now()->addMinutes(15), function () {
            return PastoralCouncilChairperson::published()->take(2)->get();
        });

        $homepageStats = Cache::remember('home_stats', now()->addMinutes(10), function () {
            return [
                'ministries' => Ministry::active()->count(),
                'gallery_items' => GalleryItem::published()->count(),
                'sermons' => Sermon::published()->count(),
            ];
        });

        $featuredEvent = Cache::remember('home_featured_event', now()->addMinutes(5), function () {
            return Event::published()
                ->upcoming()
                ->where('is_featured', true)
                ->orderBy('date')
                ->first();
        });

        $serviceSchedules = Cache::remember('home_service_schedules', now()->addMinutes(10), function () {
            return ServiceSchedule::active()->get();
        });

        $dailyVerse = Cache::remember('home_daily_verse', now()->addMinutes(10), function () {
            $today = now();

            return DailyVerse::published()
                ->forDate($today)
                ->orderByDesc('verse_date')
                ->first()
                ?? DailyVerse::published()->orderByDesc('verse_date')->first();
        });

        $latestSermon = Cache::remember('home_latest_sermon', now()->addMinutes(10), function () {
            return Sermon::published()->first();
        });

        $latestSermons = Cache::remember('home_latest_sermons_preview', now()->addMinutes(10), function () {
            return Sermon::published()->take(3)->get();
        });

        $sections = Cache::remember('home_sections', now()->addMinutes(10), function () {
            $homePage = Page::query()->where('slug', 'home')->first();
            return $homePage?->sections ?? [];
        });

        $defaults = [
            'hero' => [
                'title' => 'Welcome to St. Francis Of Assisi',
                'subtitle' => 'Catholic Church, Awoshie Anyaa',
                'description' => 'A community where faith, hope, and love come together. Join us as we worship and serve together.',
                'primary_cta_label' => 'Learn More',
                'primary_cta_url' => route('about'),
                'secondary_cta_label' => 'Visit Us',
                'secondary_cta_url' => route('contact'),
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
                'cta_url' => route('about'),
                'image' => 'https://images.unsplash.com/photo-1644292364271-59f2b61a2323?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwZW9wbGUlMjBwcmF5aW5nJTIwd29yc2hpcHxlbnwxfHx8fDE3NzIzODQyMjN8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
            ],
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
                    'link_url' => route('contact'),
                ],
                [
                    'badge' => 'Formation',
                    'title' => 'Weekly reflection',
                    'text' => 'Highlight a sermon, parish article, or spiritual emphasis here so the homepage stays current and pastoral.',
                    'link_label' => 'View sermons',
                    'link_url' => route('sermons'),
                ],
                [
                    'badge' => 'Event',
                    'title' => 'Prepare for the next parish gathering',
                    'text' => 'Point visitors to upcoming liturgies, feast celebrations, ministry meetings, and outreach events from one place.',
                    'link_label' => 'View events',
                    'link_url' => route('events'),
                ],
            ],
            'features_title' => 'Why Join Our Parish?',
            'features_subtitle' => 'We are more than just a church - we\'re a family united in faith and purpose.',
            'events' => [
                'title' => 'Upcoming Events',
                'subtitle' => 'Join us for worship and fellowship',
                'cta_label' => 'View All Events',
                'cta_url' => route('events'),
            ],
            'cta' => [
                'title' => 'Plan Your Visit',
                'text' => 'We would love to meet you. Join us for worship this Sunday or reach out if you have any questions.',
                'location' => 'Awoshie Anyaa, Accra',
                'service_time' => 'Sundays: 7:00 AM & 9:30 AM',
                'button_label' => 'Get Directions',
                'button_url' => route('contact'),
            ],
        ];
        $sections = is_array($sections) ? $sections : [];
        $homeContent = array_replace_recursive($defaults, $sections);

        if (!empty($sections['features']) && is_array($sections['features'])) {
            $homeContent['features'] = $this->normalizeList($sections['features']);
        }

        if (! empty($sections['announcements']) && is_array($sections['announcements'])) {
            $homeContent['announcements'] = collect($sections['announcements'])
                ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null) && filled($item['text'] ?? null))
                ->map(fn ($item) => [
                    'badge' => (string) ($item['badge'] ?? 'Announcement'),
                    'title' => (string) ($item['title'] ?? ''),
                    'text' => (string) ($item['text'] ?? ''),
                    'link_label' => (string) ($item['link_label'] ?? ''),
                    'link_url' => (string) ($item['link_url'] ?? ''),
                ])
                ->values()
                ->all();
        }

        if (!empty($sections['welcome']['paragraphs']) && is_array($sections['welcome']['paragraphs'])) {
            $homeContent['welcome']['paragraphs'] = $this->normalizeTextList($sections['welcome']['paragraphs']);
        }

        $normalizedHeroImage = $this->normalizeImage($sections['hero']['background_image'] ?? null);
        if (!empty($normalizedHeroImage)) {
            $homeContent['hero']['background_image'] = $normalizedHeroImage;
        } else {
            $homeContent['hero']['background_image'] = $defaults['hero']['background_image'];
        }

        $heroSlides = $this->normalizeHeroSlides($sections['hero']['slides'] ?? []);
        if (! empty($heroSlides)) {
            $homeContent['hero']['slides'] = $heroSlides;
        } else {
            $homeContent['hero']['slides'] = [[
                'title' => $homeContent['hero']['title'] ?? $defaults['hero']['title'],
                'subtitle' => $homeContent['hero']['subtitle'] ?? $defaults['hero']['subtitle'],
                'description' => $homeContent['hero']['description'] ?? $defaults['hero']['description'],
                'background_image' => $homeContent['hero']['background_image'] ?? $defaults['hero']['background_image'],
                'primary_cta_label' => $homeContent['hero']['primary_cta_label'] ?? $defaults['hero']['primary_cta_label'],
                'primary_cta_url' => $homeContent['hero']['primary_cta_url'] ?? $defaults['hero']['primary_cta_url'],
                'secondary_cta_label' => $homeContent['hero']['secondary_cta_label'] ?? $defaults['hero']['secondary_cta_label'],
                'secondary_cta_url' => $homeContent['hero']['secondary_cta_url'] ?? $defaults['hero']['secondary_cta_url'],
            ]];
        }

        $normalizedWelcomeImage = $this->normalizeImage($sections['welcome']['image'] ?? null);
        if (!empty($normalizedWelcomeImage)) {
            $homeContent['welcome']['image'] = $normalizedWelcomeImage;
        } else {
            $homeContent['welcome']['image'] = $defaults['welcome']['image'];
        }

        return view('home', compact(
            'homeMinistries',
            'homeGallery',
            'homeSocieties',
            'homeFriars',
            'homeChairpersons',
            'upcomingEvents',
            'featuredEvent',
            'serviceSchedules',
            'dailyVerse',
            'latestSermon',
            'latestSermons',
            'homeContent',
            'homepageStats'
        ));
    }

    public function about()
    {
        $sections = Cache::remember('about_sections', now()->addMinutes(10), function () {
            $aboutPage = Page::query()->where('slug', 'about')->first();
            return $aboutPage?->sections ?? [];
        });

        $defaults = [
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
        $sections = is_array($sections) ? $sections : [];
        $aboutContent = array_replace_recursive($defaults, $sections);

        if (!empty($sections['values']) && is_array($sections['values'])) {
            $aboutContent['values'] = $this->normalizeList($sections['values']);
        }

        if (!empty($sections['story']['paragraphs']) && is_array($sections['story']['paragraphs'])) {
            $aboutContent['story']['paragraphs'] = $this->normalizeTextList($sections['story']['paragraphs']);
        }

        if (!empty($sections['sacraments']['items']) && is_array($sections['sacraments']['items'])) {
            $aboutContent['sacraments']['items'] = $this->normalizeSacraments($sections['sacraments']['items']);
        }

        if (!empty($sections['saint']['paragraphs']) && is_array($sections['saint']['paragraphs'])) {
            $aboutContent['saint']['paragraphs'] = $this->normalizeTextList($sections['saint']['paragraphs']);
        }

        $normalizedAboutHero = $this->normalizeImage($sections['hero']['background_image'] ?? null);
        if (!empty($normalizedAboutHero)) {
            $aboutContent['hero']['background_image'] = $normalizedAboutHero;
        } else {
            $aboutContent['hero']['background_image'] = $defaults['hero']['background_image'];
        }

        $normalizedStoryImage = $this->normalizeImage($sections['story']['image'] ?? null);
        if (!empty($normalizedStoryImage)) {
            $aboutContent['story']['image'] = $normalizedStoryImage;
        } else {
            $aboutContent['story']['image'] = $defaults['story']['image'];
        }

        $historyArchives = [
            [
                'title' => 'Groups & Societies',
                'description' => 'Browse the societies and groups that have shaped parish life, with a brief history and photo highlights for each one.',
                'count' => Society::published()->count(),
                'href' => route('history.societies'),
                'cta' => 'View societies',
            ],
            [
                'title' => 'Friars Who Served',
                'description' => 'See the friars who have served the church from the beginning to date, together with their service years and collaborators.',
                'count' => Friar::published()->count(),
                'href' => route('history.friars'),
                'cta' => 'View friars',
            ],
            [
                'title' => 'Parish Pastoral Council Chairpersons',
                'description' => 'Follow the timeline of chairpersons who have led the Parish Pastoral Council and the years they served.',
                'count' => PastoralCouncilChairperson::published()->count(),
                'href' => route('history.pastoral-council'),
                'cta' => 'View chairpersons',
            ],
        ];

        return view('about', compact('aboutContent', 'historyArchives'));
    }

    public function ministries()
    {
        $ministries = Cache::remember('ministries_active', now()->addMinutes(5), function () {
            return Ministry::active()->get();
        });

        return view('ministries', compact('ministries'));
    }

    public function ministry(Ministry $ministry)
    {
        if (!$ministry->is_active) {
            abort(404);
        }

        $media = is_array($ministry->media) ? $ministry->media : [];

        return view('ministry', [
            'ministry' => $ministry,
            'media' => $media,
        ]);
    }

    public function events(Request $request)
    {
        $category = $request->get('category', 'all');

        $events = Cache::remember("events_list_{$category}", now()->addMinutes(5), function () use ($category) {
            $eventsQuery = Event::published()->upcoming();

            if ($category !== 'all') {
                $eventsQuery->where('category', $category);
            }

            return $eventsQuery->get();
        });

        $categories = Cache::remember('events_categories', now()->addMinutes(10), function () {
            return Event::published()
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->all();
        });

        if (empty($categories)) {
            $categories = ['Mass', 'Prayer', 'Fellowship', 'Formation', 'Music', 'Service', 'Sacraments', 'Special'];
        }

        return view('events', compact('events', 'category', 'categories'));
    }

    public function sermons()
    {
        $sermons = Cache::remember('sermons_list', now()->addMinutes(5), function () {
            return Sermon::published()->get();
        });

        return view('sermons', compact('sermons'));
    }

    public function sermon(Sermon $sermon)
    {
        if (! $sermon->is_published) {
            abort(404);
        }

        return view('sermon', compact('sermon'));
    }

    public function gallery(Request $request)
    {
        $category = $request->get('category', 'all');

        $galleryItems = Cache::remember("gallery_items_{$category}", now()->addMinutes(5), function () use ($category) {
            return GalleryItem::published()
                ->byCategory($category)
                ->get();
        });

        $categories = Cache::remember('gallery_categories', now()->addMinutes(10), function () {
            return GalleryItem::published()
                ->reorder()
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->all();
        });

        if (empty($categories)) {
            $categories = ['worship', 'events', 'sacraments', 'youth', 'outreach', 'church'];
        }

        return view('gallery', compact('galleryItems', 'category', 'categories'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function societies()
    {
        $societies = Society::published()->get();

        return view('societies', compact('societies'));
    }

    public function friars()
    {
        $friars = Friar::published()->get();

        return view('friars', compact('friars'));
    }

    public function pastoralCouncil()
    {
        $chairpersons = PastoralCouncilChairperson::published()->get();

        return view('pastoral-council', compact('chairpersons'));
    }

    private function normalizeList(?array $items): array
    {
        return array_values($items ?? []);
    }

    private function normalizeTextList(?array $items, string $key = 'text'): array
    {
        return array_values(array_map(function ($item) use ($key) {
            if (is_array($item)) {
                return $item[$key]
                    ?? ($item['text'] ?? ($item['item'] ?? ''));
            }

            return $item;
        }, $items ?? []));
    }

    private function normalizeSacraments(?array $items): array
    {
        return collect($items ?? [])
            ->map(function ($item) {
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
            })
            ->filter(fn ($item) => trim((string) ($item['name'] ?? '')) !== '')
            ->values()
            ->all();
    }

    private function normalizeHeroSlides(?array $slides): array
    {
        return collect($slides ?? [])
            ->map(function ($slide) {
                if (! is_array($slide)) {
                    return null;
                }

                $image = $this->normalizeImage($slide['background_image'] ?? null);

                return [
                    'title' => (string) ($slide['title'] ?? ''),
                    'subtitle' => (string) ($slide['subtitle'] ?? ''),
                    'description' => (string) ($slide['description'] ?? ''),
                    'background_image' => $image,
                    'primary_cta_label' => (string) ($slide['primary_cta_label'] ?? ''),
                    'primary_cta_url' => (string) ($slide['primary_cta_url'] ?? ''),
                    'secondary_cta_label' => (string) ($slide['secondary_cta_label'] ?? ''),
                    'secondary_cta_url' => (string) ($slide['secondary_cta_url'] ?? ''),
                ];
            })
            ->filter(fn ($slide) => is_array($slide) && filled($slide['title']) && filled($slide['background_image']))
            ->values()
            ->all();
    }

    private function normalizeImage($value): ?string
    {
        if (is_array($value)) {
            $valueArray = $value;
            $direct = $valueArray['url'] ?? $valueArray['path'] ?? $valueArray['file'] ?? null;
            if (is_string($direct) && $direct !== '') {
                $value = $direct;
            } else {
                $value = '';
                foreach ($valueArray as $item) {
                    if (is_string($item) && $item !== '') {
                        $value = $item;
                        break;
                    }

                    if (is_array($item)) {
                        $nested = $item['url'] ?? $item['path'] ?? $item['file'] ?? null;
                        if (is_string($nested) && $nested !== '') {
                            $value = $nested;
                            break;
                        }
                    }
                }
            }
        }

        if (!is_string($value) || $value === '') {
            return $value;
        }

        return MediaStorage::url($value);
    }
}
















