@extends('layouts.site')

@section('content')
@php
    // Pull in data prepared by HomeController
    $hero = $homeContent['hero'] ?? [];
    $welcome = $homeContent['welcome'] ?? [];
    $features = collect($homeContent['features'] ?? []);
    $events = $eventCards ?? collect();
    $ministries = $homeMinistries ?? collect();
    $cta = $homeContent['cta'] ?? [];
    $churchName = $siteSettings['church_name'] ?? 'St. Francis Of Assisi Catholic Church';
    $visitLocation = $siteSettings['church_location'] ?? ($siteSettings['church_city'] ?? 'Awoshie Anyaa, Accra');
    $heroImage = $hero['background_image'] ?? asset('images/hero.jpg');
    if (is_array($heroImage)) {
        $heroImage = $heroImage['url'] ?? $heroImage['path'] ?? $heroImage[0] ?? asset('images/hero.jpg');
    }
@endphp

{{-- Hero Section – large background with headline and primary CTAs --}}
<section class="relative bg-cover bg-center min-h-[70vh] flex items-center" style="background-image: url('{{ $heroImage }}');">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative max-w-4xl mx-auto text-center text-white px-6">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $hero['title'] ?? 'Welcome to ' . $churchName }}</h1>
        @if(filled($hero['subtitle'] ?? null))
            <p class="text-xl md:text-2xl mb-6">{{ $hero['subtitle'] }}</p>
        @endif
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ $hero['primary_cta_url'] ?? route('about') }}" class="btn btn-primary px-6 py-3">{{ $hero['primary_cta_label'] ?? 'Learn More' }}</a>
            <a href="{{ $hero['secondary_cta_url'] ?? route('contact') }}" class="btn btn-secondary px-6 py-3">{{ $hero['secondary_cta_label'] ?? 'Contact Us' }}</a>
        </div>
    </div>
</section>

{{-- Quick Intro / Welcome Section – brief paragraph and visual --}}
<section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 px-6">
        <div>
            <h2 class="text-3xl font-semibold mb-4">{{ $welcome['title'] ?? 'Welcome Home' }}</h2>
            @foreach(collect($welcome['paragraphs'] ?? [])->take(2) as $para)
                @php $text = is_array($para) ? data_get($para, 'text', '') : $para; @endphp
                @if(filled($text))
                    <p class="mb-4 text-gray-700">{{ $text }}</p>
                @endif
            @endforeach
            <a href="{{ $welcome['cta_url'] ?? route('about') }}" class="btn btn-soft mt-4">{{ $welcome['cta_label'] ?? 'Discover Our Story' }}</a>
        </div>
        <div class="flex items-center justify-center">
            <img src="{{ filled($welcome['image']) ? $welcome['image'] : asset('images/logo.jpg') }}" alt="Parish view" class="rounded-lg shadow-lg max-h-80 object-cover" loading="lazy">
        </div>
    </div>
</section>

{{-- Ministries Grid – showcase up to 6 active ministries --}}
@if($ministries->isNotEmpty())
<section class="py-12 bg-gray-50">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-2xl font-semibold text-center mb-8">Our Ministries</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($ministries->take(6) as $ministry)
                <a href="{{ route('ministries.show', $ministry) }}" class="block p-6 bg-white rounded-lg shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-bold mb-2">{{ $ministry->title }}</h3>
                    @if(filled($ministry->excerpt))
                        <p class="text-gray-600 mb-4">{{ $ministry->excerpt }}</p>
                    @endif
                    <span class="text-primary-600 font-medium">Learn more →</span>
                </a>
            @endforeach
        </div>
        <div class="text-center mt-6">
            <a href="{{ route('ministries') }}" class="btn btn-soft">View All Ministries</a>
        </div>
    </div>
</section>
@endif

{{-- Upcoming Events – simple card carousel (no JS, just grid) --}}
@if($events->isNotEmpty())
<section class="py-12 bg-white">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-2xl font-semibold text-center mb-8">Upcoming Events</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($events->take(4) as $event)
                <div class="bg-gray-50 p-4 rounded-lg shadow">
                    @if($event->image_url)
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="w-full h-32 object-cover rounded mb-3" loading="lazy" />
                    @endif
                    <p class="text-sm text-primary-600 font-medium">{{ \Illuminate\Support\Carbon::parse($event->date)->format('M d, Y') }}</p>
                    <h3 class="text-lg font-semibold mb-1">{{ $event->title }}</h3>
                    <p class="text-gray-600 text-sm">{{ \Illuminate\Support\Carbon::parse($event->time)->format('g:i A') }}</p>
                    @if(filled($event->location))
                        <p class="text-gray-500 text-xs mt-1">{{ $event->location }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="text-center mt-6">
            <a href="{{ route('events') }}" class="btn btn-soft">View All Events</a>
        </div>
    </div>
</section>
@endif

{{-- Call to Action – plan your visit – prominent banner --}}
<section class="py-12 bg-primary-600 text-white text-center">
    <div class="max-w-4xl mx-auto px-6">
        <h2 class="text-3xl font-bold mb-4">{{ $cta['title'] ?? 'Plan Your Visit' }}</h2>
        <p class="mb-6">{{ $cta['text'] ?? 'We would love to meet you. Join us for worship this Sunday or reach out if you have any questions.' }}</p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="{{ $cta['button_url'] ?? route('contact') }}" class="btn btn-secondary px-6 py-3">{{ $cta['button_label'] ?? 'Get Directions' }}</a>
        </div>
        <div class="mt-4 text-sm opacity-90">
            <p>{{ $cta['location'] ?? $visitLocation }}</p>
            <p>{{ $cta['service_time'] ?? ($siteSettings['service_sunday'] ?? 'Sunday Mass: 7:00 AM & 9:30 AM') }}</p>
        </div>
    </div>
</section>

{{-- Footer – simple contact info – you may already have a footer in layout – this ensures key info is visible on landing page --}}
<footer class="bg-gray-800 text-gray-200 py-8">
    <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8">
        <div>
            <h3 class="font-semibold mb-2">Contact</h3>
            <p>{{ $churchName }}<br>{{ $visitLocation }}<br>Phone: +233 123 456 789<br>Email: <a href="mailto:info@stfrancis.org" class="underline">info@stfrancis.org</a></p>
        </div>
        <div>
            <h3 class="font-semibold mb-2">Quick Links</h3>
            <ul class="space-y-1">
                <li><a href="{{ url('/') }}" class="hover:underline">Home</a></li>
                <li><a href="{{ route('about') }}" class="hover:underline">About</a></li>
                <li><a href="{{ route('ministries') }}" class="hover:underline">Ministries</a></li>
                <li><a href="{{ route('events') }}" class="hover:underline">Events</a></li>
                <li><a href="{{ route('contact') }}" class="hover:underline">Contact</a></li>
            </ul>
        </div>
        <div class="flex items-center justify-center space-x-4">
            <a href="https://facebook.com" target="_blank" class="text-gray-400 hover:text-white"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://instagram.com" target="_blank" class="text-gray-400 hover:text-white"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://youtube.com" target="_blank" class="text-gray-400 hover:text-white"><i class="fa-brands fa-youtube"></i></a>
        </div>
    </div>
    <div class="text-center mt-6 text-xs opacity-70">© {{ date('Y') }} {{ $churchName }} – All rights reserved.</div>
</footer>
@endsection


@section('content')
@php
  $hero = $homeContent['hero'] ?? [];
  $welcome = $homeContent['welcome'] ?? [];
  $features = collect($homeContent['features'] ?? [])->filter(fn ($item) => is_array($item))->values();
  $featuresTitle = $homeContent['features_title'] ?? 'Why Join Our Parish?';
  $featuresSubtitle = $homeContent['features_subtitle'] ?? 'We are more than just a church - we\'re a family united in faith and purpose.';
  $eventsSection = $homeContent['events'] ?? [];
  $cta = $homeContent['cta'] ?? [];
  $serviceSchedules = $serviceSchedules ?? collect();
  $latestSermon = $latestSermon ?? null;
  $upcomingEvents = $upcomingEvents ?? collect();
  $heroSlides = collect($hero['slides'] ?? [])->filter(fn ($slide) => is_array($slide))->values();
  $heroSlideCount = $heroSlides->count();

  $churchName = $siteSettings['church_name'] ?? 'St. Francis Of Assisi Catholic Church';
  $visitLocation = $siteSettings['church_location'] ?? ($siteSettings['church_city'] ?? 'Awoshie Anyaa, Accra');

  $heroImage = $hero['background_image'] ?? '';
  if (is_array($heroImage)) {
    $heroImage = $heroImage['url'] ?? $heroImage['path'] ?? $heroImage[0] ?? '';
  }

  $welcomeImage = $welcome['image'] ?? '';
  if (is_array($welcomeImage)) {
    $welcomeImage = $welcomeImage['url'] ?? $welcomeImage['path'] ?? $welcomeImage[0] ?? '';
  }

  $primaryHeroSlide = $heroSlides->first() ?? [
    'title' => $hero['title'] ?? 'Welcome',
    'subtitle' => $hero['subtitle'] ?? '',
    'description' => $hero['description'] ?? '',
    'primary_cta_label' => $hero['primary_cta_label'] ?? 'Learn More',
    'primary_cta_url' => $hero['primary_cta_url'] ?? route('about'),
    'secondary_cta_label' => $hero['secondary_cta_label'] ?? 'Visit Us',
    'secondary_cta_url' => $hero['secondary_cta_url'] ?? route('contact'),
    'background_image' => $heroImage,
  ];

  $renderSlides = $heroSlides->isNotEmpty()
    ? $heroSlides
    : collect([$primaryHeroSlide]);

  $renderSlides = $renderSlides
    ->map(function ($slide) use ($heroImage) {
      $backgroundImage = $slide['background_image'] ?? $heroImage;

      if (is_array($backgroundImage)) {
        $backgroundImage = $backgroundImage['url'] ?? $backgroundImage['path'] ?? $backgroundImage['file'] ?? '';
      }

      $slide['background_image'] = filled($backgroundImage) ? (string) $backgroundImage : $heroImage;

      return $slide;
    })
    ->values();

  $renderSlideCount = $renderSlides->count();

  $serviceCards = $serviceSchedules instanceof \Illuminate\Support\Collection
    ? $serviceSchedules->take(3)
    : collect();

  $eventCards = $upcomingEvents instanceof \Illuminate\Support\Collection
    ? $upcomingEvents->take(4)
    : collect();

  $sermonVerses = collect($latestSermon?->verses ?? [])
    ->map(function ($verse) {
      if (is_array($verse)) {
        $text = data_get($verse, 'text') ?? data_get($verse, 'reference');

        if (filled($text)) {
          return (string) $text;
        }

        return collect($verse)
          ->filter(fn ($item) => is_scalar($item) && filled($item))
          ->map(fn ($item) => (string) $item)
          ->implode(' - ');
      }

      return is_scalar($verse) ? (string) $verse : null;
    })
    ->filter(fn ($verse) => filled($verse))
    ->take(4);

  $primaryService = $serviceCards->first();
  $secondaryServices = $serviceCards->slice(1, 2)->values();
  $nextEvent = $eventCards->first();
  $dailyVerseText = filled($dailyVerse?->text ?? null)
    ? \Illuminate\Support\Str::limit((string) $dailyVerse->text, 150)
    : null;
  $dailyVerseReference = filled($dailyVerse?->reference ?? null)
    ? (string) $dailyVerse->reference
    : null;
  $heroStats = collect([
    [
      'value' => number_format((int) ($homepageStats['ministries'] ?? 0)),
      'label' => 'Active ministries',
    ],
    [
      'value' => number_format((int) ($homepageStats['gallery_items'] ?? 0)),
      'label' => 'Gallery moments',
    ],
    [
      'value' => number_format((int) ($homepageStats['sermons'] ?? 0)),
      'label' => 'Sermon archive',
    ],
  ]);
@endphp

<section class="relative overflow-hidden hero-3d hero-full" id="hero" data-hero-slider @if($renderSlideCount > 1) data-autoplay="true" @endif>
  <div class="absolute inset-0">
    @foreach ($renderSlides as $index => $slide)
      @php $slideBg = $slide['background_image'] ?? $heroImage; @endphp
      @if (filled($slideBg))
        <div class="hero-slide-bg {{ $index === 0 ? 'is-active' : '' }}" data-hero-slide-bg aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
          <img
            src="{{ $slideBg }}"
            alt="{{ $slide['title'] ?? $churchName }}"
            class="w-full h-full object-cover hero-media"
            @if ($index === 0) fetchpriority="high" loading="eager" @else loading="lazy" @endif
          >
        </div>
      @endif
    @endforeach
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C]/95 via-[#8C6A5D]/82 to-[#6B2C2C]/50"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.25),_transparent_58%)]"></div>
    <div class="hero-glow hero-glow-left" aria-hidden="true"></div>
    <div class="hero-glow hero-glow-right" aria-hidden="true"></div>
  </div>

  <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24 lg:py-28">
    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.15fr),24rem] gap-8 xl:gap-10 items-stretch">
      <div class="hero-intro hero-delay-1 text-white" data-depth="0.04">
        <div class="hero-kicker-row">
          <span class="hero-kicker-badge">
            <span class="hero-kicker-dot" aria-hidden="true"></span>
            Parish life in motion
          </span>
          <span class="hero-kicker-meta">{{ $visitLocation }}</span>
        </div>
        <div class="flex flex-wrap gap-2 mt-5">
          <span class="metric-chip">Faith</span>
          <span class="metric-chip">Community</span>
          <span class="metric-chip">Service</span>
        </div>
        <div class="hero-slider-shell mt-6">
          <div class="hero-slides">
            @foreach ($renderSlides as $index => $slide)
              @php
                $primaryCtaUrl = filled($slide['primary_cta_url'] ?? null) ? $slide['primary_cta_url'] : route('about');
                $secondaryCtaUrl = filled($slide['secondary_cta_url'] ?? null) ? $slide['secondary_cta_url'] : route('contact');
              @endphp
              <div class="hero-slide-panel text-white {{ $index === 0 ? 'is-active' : '' }}" data-hero-slide-panel aria-hidden="{{ $index === 0 ? 'false' : 'true' }}">
                <div class="hero-slide-copy">
                  <div class="hero-copy-card hero-copy-card-spotlight">
                    <p class="hero-overline">Gather with us in worship, fellowship, and service</p>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-semibold leading-tight">
                      {{ $slide['title'] ?? 'Welcome to our parish' }}
                    </h1>
                    @if (filled($slide['subtitle'] ?? null))
                      <p class="text-xl text-white/85 mt-4">{{ $slide['subtitle'] }}</p>
                    @endif
                    @if (filled($slide['description'] ?? null))
                      <p class="text-lg text-white/75 mt-6 max-w-2xl">{{ \Illuminate\Support\Str::limit($slide['description'], 180) }}</p>
                    @endif

                    <div class="hero-quick-grid mt-8">
                      <article class="hero-quick-card">
                        <p class="hero-quick-label">Sunday worship</p>
                        <p class="hero-quick-value">
                          {{ $primaryService?->time ?? ($siteSettings['service_sunday'] ?? '7:00 AM & 9:30 AM') }}
                        </p>
                        <p class="hero-quick-meta">
                          {{ $primaryService?->title ?? 'Mass at the parish sanctuary' }}
                        </p>
                      </article>

                      <article class="hero-quick-card">
                        <p class="hero-quick-label">Next gathering</p>
                        <p class="hero-quick-value">
                          {{ $nextEvent ? \Illuminate\Support\Carbon::parse($nextEvent->date)->format('M d') : 'Parish calendar' }}
                        </p>
                        <p class="hero-quick-meta">
                          {{ $nextEvent?->title ?? 'Stay connected with upcoming events' }}
                        </p>
                      </article>

                      <article class="hero-quick-card">
                        <p class="hero-quick-label">First-time guests</p>
                        <p class="hero-quick-value">Warm welcome</p>
                        <p class="hero-quick-meta">Find worship times, location, and ministries in one place.</p>
                      </article>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 mt-8">
                      <a href="{{ $primaryCtaUrl }}" class="btn btn-primary">
                        {{ $slide['primary_cta_label'] ?? 'Learn More' }}
                      </a>
                      <a href="{{ $secondaryCtaUrl }}" class="btn btn-secondary">
                        {{ $slide['secondary_cta_label'] ?? 'Visit Us' }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          @if ($renderSlideCount > 1)
            <div class="mt-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
              <div class="flex items-center gap-3" role="tablist" aria-label="Hero slides">
                @foreach ($renderSlides as $index => $slide)
                  <button
                    type="button"
                    class="hero-dot {{ $index === 0 ? 'is-active' : '' }}"
                    data-hero-dot
                    data-index="{{ $index }}"
                    role="tab"
                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                    aria-label="Show slide {{ $index + 1 }}"
                  ></button>
                @endforeach
              </div>
              <div class="flex items-center gap-3">
                <button type="button" class="hero-arrow" data-hero-prev aria-label="Previous hero slide">
                  <span aria-hidden="true">&larr;</span> Previous
                </button>
                <button type="button" class="hero-arrow" data-hero-next aria-label="Next hero slide">
                  Next <span aria-hidden="true">&rarr;</span>
                </button>
              </div>
            </div>
          @endif
        </div>
      </div>

      <div class="hero-intro hero-delay-2 hero-service-card hero-aside-card" data-depth="0.08">
        <div class="flex items-start justify-between gap-4">
          <div>
            <p class="hero-aside-label">This Week at the Parish</p>
            <h2 class="hero-aside-heading mt-3">A peaceful way to plan your visit</h2>
          </div>
          <span class="hero-aside-badge">Awoshie</span>
        </div>

        <div class="hero-aside-highlight">
          <p class="hero-aside-label">Main service</p>
          <p class="hero-aside-value">
            {{ $primaryService?->day_of_week ?? 'Sunday' }}{{ $primaryService?->time ? ' • ' . $primaryService->time : '' }}
          </p>
          <p class="hero-aside-meta">
            {{ $primaryService?->title ?? 'Join the parish community in worship and prayer.' }}
          </p>
        </div>

        <div class="hero-aside-stat-grid">
          @foreach ($heroStats as $stat)
            <div class="hero-aside-stat">
              <p class="hero-aside-stat-value">{{ $stat['value'] }}</p>
              <p class="hero-aside-stat-label">{{ $stat['label'] }}</p>
            </div>
          @endforeach
        </div>

        @if ($secondaryServices->isNotEmpty())
          <div class="hero-aside-list">
            @foreach ($secondaryServices as $schedule)
              <div class="hero-aside-list-item">
                <div>
                  <p class="hero-aside-list-title">{{ $schedule->title }}</p>
                  <p class="hero-aside-list-meta">{{ $schedule->day_of_week }} • {{ $schedule->time }}</p>
                </div>
                @if (filled($schedule->location))
                  <span class="hero-aside-list-tag">{{ $schedule->location }}</span>
                @endif
              </div>
            @endforeach
          </div>
        @endif

        @if (filled($dailyVerseText))
          <div class="hero-aside-scripture">
            <p class="hero-aside-label">Daily verse</p>
            <p class="hero-aside-scripture-text">{{ $dailyVerseText }}</p>
            @if (filled($dailyVerseReference))
              <p class="hero-aside-scripture-ref">{{ $dailyVerseReference }}</p>
            @endif
          </div>
        @endif

        @if ($nextEvent)
          <div class="hero-aside-event">
            <p class="hero-aside-label">Upcoming event</p>
            <p class="hero-aside-event-title">{{ $nextEvent->title }}</p>
            <p class="hero-aside-meta">
              {{ \Illuminate\Support\Carbon::parse($nextEvent->date)->format('M d, Y') }}
              @if (filled($nextEvent->location))
                • {{ $nextEvent->location }}
              @endif
            </p>
          </div>
        @endif

        <a href="{{ route('events') }}" class="link-arrow mt-6">
          View upcoming events <span>&rarr;</span>
        </a>
      </div>
    </div>
  </div>
</section>

<section class="relative z-20 -mt-10 md:-mt-14">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="home-landing-band">
      <div class="home-landing-band-item">
        <p class="home-landing-band-label">Parish home</p>
        <p class="home-landing-band-value">{{ $churchName }}</p>
        <p class="home-landing-band-meta">{{ $visitLocation }}</p>
      </div>
      <div class="home-landing-band-item">
        <p class="home-landing-band-label">Plan your Sunday</p>
        <p class="home-landing-band-value">{{ $siteSettings['service_sunday'] ?? 'Sunday Mass: 7:00 AM & 9:30 AM' }}</p>
        <p class="home-landing-band-meta">Come early, settle in, and worship with the parish family.</p>
      </div>
      <div class="home-landing-band-item">
        <p class="home-landing-band-label">Next step</p>
        <p class="home-landing-band-value">{{ $nextEvent?->title ?? 'Explore ministries and events' }}</p>
        <p class="home-landing-band-meta">
          {{ $nextEvent ? \Illuminate\Support\Carbon::parse($nextEvent->date)->format('M d, Y') : 'Find where faith and fellowship meet.' }}
        </p>
      </div>
    </div>
  </div>
</section>

<section class="pt-24 md:pt-28 pb-16 md:pb-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
      <div>
        <p class="section-kicker">Welcome Home</p>
        <h2 class="section-title mt-3">{{ $welcome['title'] ?? 'Welcome Home' }}</h2>
        @foreach (collect($welcome['paragraphs'] ?? [])->take(2) as $paragraph)
          @php
            $paragraphText = is_array($paragraph) ? (data_get($paragraph, 'text', '') ?? '') : $paragraph;
          @endphp
          @if (filled($paragraphText))
            <p class="section-lead mt-4">{{ $paragraphText }}</p>
          @endif
        @endforeach
        <div class="mt-7 flex flex-wrap gap-3">
          <a href="{{ $welcome['cta_url'] ?? route('about') }}" class="btn btn-soft">
            {{ $welcome['cta_label'] ?? 'Discover Our Story' }}
          </a>
          <a href="{{ route('ministries') }}" class="pill">Explore Ministries</a>
        </div>
      </div>
      <div class="relative">
        <div class="absolute -top-5 -left-5 h-20 w-20 rounded-full bg-amber-100"></div>
        <div class="relative h-[420px] rounded-3xl overflow-hidden shadow-[0_22px_50px_rgba(46,26,15,0.18)] media-frame">
          <img src="{{ filled($welcomeImage) ? $welcomeImage : asset('images/logo.jpg') }}" alt="Parish life" class="w-full h-full object-cover" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

@if ($features->isNotEmpty())
  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <p class="section-kicker">Why Join Us</p>
        <h2 class="section-title mt-3">{{ $featuresTitle }}</h2>
        <p class="section-lead max-w-2xl mx-auto mt-4">{{ $featuresSubtitle }}</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        @foreach ($features->take(4) as $feature)
          <article class="card p-6">
            <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold">
              {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($feature['title'] ?? 'P', 0, 1)) }}
            </div>
            <h3 class="text-xl mt-4">{{ $feature['title'] ?? '' }}</h3>
            <p class="text-sm text-slate-600 mt-2">{{ $feature['text'] ?? '' }}</p>
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif

@if ($serviceCards->isNotEmpty())
  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-5">
        <div>
          <p class="section-kicker">Service Schedule</p>
          <h2 class="section-title mt-3">Worship With Us</h2>
          <p class="section-lead mt-3">Plan your week around prayer, worship, and community gatherings.</p>
        </div>
        <a href="{{ route('contact') }}" class="btn btn-soft">
          Get Directions
        </a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach ($serviceCards as $schedule)
          <article class="card p-6">
            <p class="section-kicker !tracking-[0.22em]">{{ $schedule->day_of_week }}</p>
            <h3 class="text-xl mt-3">{{ $schedule->title }}</h3>
            <p class="text-sm text-slate-600 mt-2">{{ $schedule->time }}</p>
            @if ($schedule->location)
              <p class="text-sm text-slate-500 mt-2">{{ $schedule->location }}</p>
            @endif
          </article>
        @endforeach
      </div>
    </div>
  </section>
@endif

<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-5">
      <div>
        <p class="section-kicker">Events</p>
        <h2 class="section-title mt-3">{{ $eventsSection['title'] ?? 'Upcoming Events' }}</h2>
        <p class="section-lead mt-3">{{ $eventsSection['subtitle'] ?? 'Join us for worship and fellowship' }}</p>
      </div>
      <a href="{{ $eventsSection['cta_url'] ?? route('events') }}" class="btn btn-soft">
        {{ $eventsSection['cta_label'] ?? 'View All Events' }}
      </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
      @forelse ($eventCards as $event)
        <article class="card p-6">
          @if ($event->image_url)
            <div class="media-frame mb-4">
              <img
                src="{{ $event->image_url }}"
                alt="{{ $event->title }}"
                class="w-full h-40 object-cover"
                loading="lazy"
              >
            </div>
          @endif
          <p class="section-kicker !tracking-[0.22em]">{{ \Illuminate\Support\Carbon::parse($event->date)->format('M d') }}</p>
          <h3 class="text-lg mt-2">{{ $event->title }}</h3>
          <p class="text-sm text-slate-600 mt-2">
            {{ \Illuminate\Support\Carbon::parse($event->time)->format('g:i A') }}
          </p>
          @if ($event->location)
            <p class="text-sm text-slate-500 mt-1">{{ $event->location }}</p>
          @endif
        </article>
      @empty
        <div class="xl:col-span-4 card p-8 text-center text-slate-600">No upcoming events have been published yet.</div>
      @endforelse
    </div>
  </div>
</section>

@if ($latestSermon)
  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-5">
        <div>
          <p class="section-kicker">Latest Sermon</p>
          <h2 class="section-title mt-3">{{ $latestSermon->title }}</h2>
          @if (filled($latestSermon->preacher))
            <p class="section-lead mt-3">Preacher: {{ $latestSermon->preacher }}</p>
          @endif
        </div>
        <a href="{{ route('sermons') }}" class="btn btn-soft">
          View All Sermons
        </a>
      </div>

      <div class="card p-8 md:p-10">
        <div class="grid grid-cols-1 lg:grid-cols-[1.2fr,0.8fr] gap-8 items-start">
          <div>
            <p class="section-kicker">Sermon Summary</p>
            <p class="section-lead mt-4">
              {{ $latestSermon->summary ?: "Dive into this week's message and reflect on the Word together." }}
            </p>
            <div class="flex flex-wrap gap-3 mt-6">
              <a href="{{ route('sermons.show', $latestSermon) }}" class="btn btn-primary">
                Explore Sermon
              </a>
              @if (filled($latestSermon->video_url))
                <a href="{{ $latestSermon->video_url }}" class="pill" target="_blank" rel="noopener">Watch Video</a>
              @endif
              @if (filled($latestSermon->audio_url))
                <a href="{{ $latestSermon->audio_url }}" class="pill" target="_blank" rel="noopener">Listen Audio</a>
              @endif
            </div>
          </div>
          <div class="surface-soft p-6">
            <div class="text-sm text-slate-600">
              <p class="font-semibold text-[#2b1a12]">Date</p>
              <p>{{ optional($latestSermon->date)->format('M d, Y') }}</p>
            </div>
            @if ($sermonVerses->isNotEmpty())
              <div class="mt-5">
                <p class="font-semibold text-[#2b1a12] text-sm">Key Verses</p>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                  @foreach ($sermonVerses as $verse)
                    <li>{{ $verse }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </section>
@endif

<section class="py-20 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C] text-white">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <p class="section-kicker text-amber-300">Plan Your Visit</p>
    <h2 class="text-3xl md:text-4xl font-semibold mt-3">{{ $cta['title'] ?? 'Plan Your Visit' }}</h2>
    <p class="text-lg text-white/75 mt-6">{{ $cta['text'] ?? 'We would love to meet you. Join us for worship this Sunday or reach out if you have any questions.' }}</p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8 text-sm text-white/75">
      <div>{{ $cta['location'] ?? $visitLocation }}</div>
      <div>{{ $cta['service_time'] ?? ($siteSettings['service_sunday'] ?? 'Sunday Mass details available at the parish office') }}</div>
    </div>
    <a href="{{ $cta['button_url'] ?? route('contact') }}" class="btn btn-secondary mt-8">
      {{ $cta['button_label'] ?? 'Get Directions' }}
    </a>
  </div>
</section>
@endsection

