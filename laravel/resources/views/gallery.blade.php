@extends('layouts.site')

@section('content')
@php
  $currentCategory = $category ?? 'all';
  $categoryList = array_merge(['all'], $categories ?? []);
  $totalItems = $galleryItems->count();
@endphp

  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Gallery</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">Photo &amp; Video Gallery</h1>
      <p class="text-lg text-white/75 mt-4">Moments of faith, love, and community life.</p>
      <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold tracking-[0.2em] uppercase">
        {{ $totalItems }} Item{{ $totalItems === 1 ? '' : 's' }}
      </div>
    </div>
  </section>

  <section class="sticky-filter-bar py-6 bg-white/90 border-b sticky z-40 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="surface-soft p-3">
        <div class="flex flex-wrap gap-2 justify-center">
          @foreach ($categoryList as $cat)
            @php
              $isActive = $currentCategory === $cat;
              $label = $cat === 'all' ? 'All' : ucfirst($cat);
            @endphp
            <a
              href="{{ route('gallery', ['category' => $cat]) }}"
              class="pill {{ $isActive ? 'bg-[#6B2C2C] text-white border-[#6B2C2C]' : '' }}"
            >
              {{ $label }}
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 gallery-masonry">
        @forelse ($galleryItems as $item)
          @php
            $videoUrl = $item->media_url ?? $item->url;
            $videoHost = parse_url($videoUrl, PHP_URL_HOST) ?? '';
            $videoPath = parse_url($videoUrl, PHP_URL_PATH) ?? '';
            $extension = strtolower(pathinfo($videoPath, PATHINFO_EXTENSION));
            $isYoutube = \Illuminate\Support\Str::contains($videoHost, ['youtube.com', 'youtu.be']);
            $isVimeo = \Illuminate\Support\Str::contains($videoHost, ['vimeo.com']);
            $isEmbed = $isYoutube || $isVimeo || \Illuminate\Support\Str::contains($videoUrl, 'embed');
            $isPlayable = in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'm4v'], true);
            $embedUrl = $videoUrl;
            if ($isYoutube && !\Illuminate\Support\Str::contains($videoUrl, 'embed')) {
                if (\Illuminate\Support\Str::contains($videoUrl, 'youtu.be/')) {
                    $id = trim(\Illuminate\Support\Str::after($videoUrl, 'youtu.be/'));
                    $embedUrl = 'https://www.youtube.com/embed/' . $id;
                } elseif (\Illuminate\Support\Str::contains($videoUrl, 'watch?v=')) {
                    $id = trim(\Illuminate\Support\Str::after($videoUrl, 'watch?v='));
                    $id = \Illuminate\Support\Str::before($id, '&');
                    $embedUrl = 'https://www.youtube.com/embed/' . $id;
                }
            }
            if ($isVimeo && !\Illuminate\Support\Str::contains($videoUrl, 'player.vimeo.com')) {
                $id = trim(\Illuminate\Support\Str::afterLast($videoUrl, '/'));
                $embedUrl = 'https://player.vimeo.com/video/' . $id;
            }
          @endphp

          <article class="card overflow-hidden group gallery-item">
            <div class="relative aspect-[4/3] bg-slate-100 media-frame">
              @if ($item->type === 'video')
                <button
                  type="button"
                  class="preview-trigger w-full h-full"
                  data-preview
                  data-type="video"
                  data-url="{{ $videoUrl }}"
                  data-embed="{{ $isEmbed ? $embedUrl : '' }}"
                  data-playable="{{ $isPlayable ? '1' : '0' }}"
                  data-title="{{ $item->title }}"
                  aria-label="Preview {{ $item->title }}"
                >
                  <img
                    src="{{ $item->thumbnail_url ?: 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHxjaHVyY2glMjB2aWRlb3xlbnwxfHx8fDE3NzIzODU3NjV8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral' }}"
                    alt="{{ $item->title }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                  >
                  <div class="absolute inset-0 bg-gradient-to-t from-[#3b2314]/75 via-[#3b2314]/25 to-transparent"></div>
                  <div class="absolute inset-0 flex items-center justify-center">
                    <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-white/90 text-slate-900 shadow-lg">
                      <svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor">
                        <path d="M8 5v14l11-7z"></path>
                      </svg>
                    </span>
                  </div>
                </button>
                <div class="absolute top-3 right-3 rounded-full bg-amber-600 text-white px-3 py-1 text-[0.68rem] font-semibold tracking-wider uppercase">
                  Video
                </div>
              @else
                <button
                  type="button"
                  class="preview-trigger w-full h-full"
                  data-preview
                  data-type="image"
                  data-url="{{ $item->media_url ?? $item->url }}"
                  data-title="{{ $item->title }}"
                  aria-label="Preview {{ $item->title }}"
                >
                  <img
                    src="{{ $item->media_url ?? $item->url }}"
                    alt="{{ $item->title }}"
                    class="w-full h-full object-cover"
                    loading="lazy"
                  >
                </button>
              @endif
            </div>

            <div class="p-4">
              <h3 class="text-lg mb-1 group-hover:text-amber-700 transition-colors">{{ $item->title }}</h3>
              @if ($item->description)
                <p class="text-sm text-slate-600">{{ $item->description }}</p>
              @endif
              <div class="mt-3 flex items-center justify-between gap-3">
                <p class="text-xs text-slate-500">
                  {{ \Illuminate\Support\Carbon::parse($item->date)->format('M d, Y') }}
                </p>
                <span class="text-xs rounded-full bg-slate-100 px-2.5 py-1 text-slate-600">
                  {{ ucfirst($item->category) }}
                </span>
              </div>
              @if ($item->type === 'video' && !$isEmbed && !$isPlayable)
                <a href="{{ $item->media_url ?? $item->url }}" class="inline-block mt-3 text-sm text-amber-700 hover:text-amber-800" target="_blank" rel="noopener">
                  Open video
                </a>
              @endif
            </div>
          </article>
        @empty
          <div class="col-span-full">
            <div class="surface-soft p-10 text-center">
              <h3 class="text-2xl font-semibold text-[#2b1a12]">No Media Found</h3>
              <p class="text-slate-600 mt-3">No gallery items are available in this category yet.</p>
              <a href="{{ route('gallery') }}" class="btn btn-soft mt-6">View All Media</a>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <div class="lightbox" data-lightbox role="dialog" aria-modal="true" aria-label="Gallery media preview">
    <button type="button" class="lightbox-backdrop" data-lightbox-close aria-label="Close preview"></button>
    <div class="lightbox-panel">
      <div class="lightbox-frame">
        <button
          type="button"
          class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-slate-900 shadow"
          data-lightbox-close
          aria-label="Close preview"
        >
          &times;
        </button>
        <div class="lightbox-media" data-lightbox-content></div>
      </div>
    </div>
  </div>
@endsection
