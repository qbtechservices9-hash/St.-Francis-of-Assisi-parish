@extends('layouts.site')

@section('content')
@php
  $coverUrl = $ministry->image_url ?: 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjaHVyY2glMjBpbnRlcmlvciUyMHN1bmxpZ2h0fGVufDF8fHx8MTc3MjM3NTU1N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral';

  $mediaItems = collect($media ?? [])->filter(function ($item) {
    return is_array($item) && !empty($item['url']);
  })->values()->all();

  $resolveUrl = function (?string $path): string {
    if (!$path) {
      return '';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
      return $path;
    }
    if (str_starts_with($path, 'storage/')) {
      return '/' . ltrim($path, '/');
    }
    if (str_starts_with($path, '/')) {
      return $path;
    }

    return asset('storage/' . ltrim($path, '/'));
  };
@endphp

  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0">
      <img src="{{ $coverUrl }}" alt="{{ $ministry->name }}" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-b from-[#6B2C2C]/92 via-[#8C6A5D]/78 to-[#6B2C2C]/45"></div>
    </div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Ministry</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">{{ $ministry->name }}</h1>
      @if ($ministry->summary)
        <p class="text-lg text-white/80 mt-4 max-w-3xl mx-auto">{{ $ministry->summary }}</p>
      @endif
      <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
        <a href="mailto:{{ $ministry->contact_email }}" class="btn btn-secondary !px-5 !py-2.5">Email Coordinator</a>
        @if ($ministry->contact_phone)
          <a href="tel:{{ $ministry->contact_phone }}" class="btn btn-secondary !px-5 !py-2.5">Call Coordinator</a>
        @endif
      </div>
    </div>
  </section>

  <section class="py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 xl:grid-cols-[2fr,1fr] gap-8">
        <article class="card p-7 md:p-8">
          <p class="section-kicker">About This Ministry</p>
          <h2 class="text-3xl font-semibold mt-3">{{ $ministry->name }}</h2>
          <p class="text-slate-600 text-lg mt-5">{{ $ministry->description }}</p>
          @if ($ministry->details)
            <div class="mt-6 prose max-w-none text-slate-600 prose-headings:text-[#2b1a12]">
              {!! nl2br(e($ministry->details)) !!}
            </div>
          @endif
        </article>

        <aside class="space-y-6">
          <div class="card p-6">
            <h3 class="text-xl font-semibold mb-4">Contact & Schedule</h3>
            <div class="space-y-4 text-sm text-slate-700">
              <div>
                <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Email</p>
                <a class="text-amber-700 font-semibold" href="mailto:{{ $ministry->contact_email }}">{{ $ministry->contact_email }}</a>
              </div>
              @if ($ministry->contact_phone)
                <div>
                  <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Phone</p>
                  <a class="text-amber-700 font-semibold" href="tel:{{ $ministry->contact_phone }}">{{ $ministry->contact_phone }}</a>
                </div>
              @endif
              @if ($ministry->meeting_time)
                <div>
                  <p class="text-xs uppercase tracking-[0.15em] text-[#8b6a58]">Meeting Time</p>
                  <p class="font-semibold text-[#2b1a12]">{{ $ministry->meeting_time }}</p>
                </div>
              @endif
            </div>
            <a href="{{ route('contact') }}" class="btn btn-soft w-full mt-6">Contact Parish Office</a>
          </div>

          <div class="surface-soft p-5">
            <p class="text-sm text-slate-600">
              Interested in joining? Reach out and we will guide you through the next steps.
            </p>
          </div>
        </aside>
      </div>
    </div>
  </section>

  @if (!empty($mediaItems))
    <section class="py-20 bg-[#F1EEE9]">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <p class="section-kicker">Gallery</p>
          <h2 class="section-title mt-3">Life in {{ $ministry->name }}</h2>
          <p class="section-lead mt-3">Photos and videos that show ministry moments.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach ($mediaItems as $item)
            @php
              $type = $item['type'] ?? 'image';
              $url = $resolveUrl($item['url'] ?? null);
              $caption = $item['caption'] ?? '';
              $embedUrl = '';

              if ($type === 'video' && $url) {
                if (preg_match('~youtu\.be/([^?&]+)~', $url, $matches) || preg_match('~v=([^?&]+)~', $url, $matches)) {
                    $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                } elseif (preg_match('~vimeo\.com/(\d+)~', $url, $matches)) {
                    $embedUrl = 'https://player.vimeo.com/video/' . $matches[1];
                }
              }
            @endphp

            <article class="card overflow-hidden">
              <div class="relative">
                @if ($type === 'video')
                  @if ($embedUrl)
                    <iframe
                      class="w-full aspect-video"
                      src="{{ $embedUrl }}"
                      title="{{ $caption ?: $ministry->name }} video"
                      frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                      allowfullscreen
                    ></iframe>
                  @else
                    <video class="w-full aspect-video object-cover" controls preload="metadata">
                      <source src="{{ $url }}" type="video/mp4">
                    </video>
                  @endif
                @else
                  <img src="{{ $url }}" alt="{{ $caption ?: $ministry->name }}" class="w-full h-56 object-cover">
                @endif
              </div>
              @if ($caption)
                <div class="p-4 text-sm text-slate-600">{{ $caption }}</div>
              @endif
            </article>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
