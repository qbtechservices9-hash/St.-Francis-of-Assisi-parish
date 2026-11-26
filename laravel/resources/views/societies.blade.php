@extends('layouts.site')

@section('title', 'Groups & Societies')

@section('content')
  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(212,175,55,0.18),_transparent_38%),linear-gradient(135deg,#6B2C2C,#8C6A5D_55%,#6B2C2C)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Church History</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">Groups &amp; Societies</h1>
      <p class="text-lg text-white/75 mt-4 max-w-3xl mx-auto">
        Discover the church societies and parish groups that have helped shape worship, fellowship, and service through the years.
      </p>
    </div>
  </section>

  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      @php
        $groups = $societies->filter(fn ($entry) => ($entry->type ?? 'society') === 'group');
        $societyEntries = $societies->filter(fn ($entry) => ($entry->type ?? 'society') !== 'group');
      @endphp

      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
        <div class="max-w-3xl">
          <p class="section-kicker">Parish Communities</p>
          <h2 class="section-title mt-3">Day Born groups and societies that shape parish life</h2>
          <p class="section-lead mt-4">
            These entries are maintained by the parish administration and split into Day Born groups and parish societies, each with a brief history and photo memories.
          </p>
        </div>
        <div class="card px-6 py-5 min-w-[220px]">
          <p class="text-sm uppercase tracking-[0.25em] text-[#9b6d4b]">Published entries</p>
          <p class="text-4xl font-semibold mt-3">{{ $societies->count() }}</p>
        </div>
      </div>

      <div class="space-y-14">
        <div>
          <div class="flex items-center justify-between gap-4 mb-6">
            <div>
              <p class="section-kicker">Groups</p>
              <h3 class="text-2xl md:text-3xl font-semibold mt-2">Day Born Groups</h3>
            </div>
            <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.22em] text-[#6B2C2C]">
              {{ $groups->count() }} groups
            </span>
          </div>

          <div class="space-y-10">
            @forelse ($groups as $society)
          @php
            $galleryImages = $society->gallery_image_urls;
            $coverImage = $society->cover_image_url ?? ($galleryImages[0] ?? null);
            $thumbnailImages = collect($galleryImages)
              ->reject(fn ($image) => $image === $coverImage)
              ->take(4)
              ->values()
              ->all();
          @endphp

          <article class="card overflow-hidden">
            <div class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-0">
              <div class="p-8 md:p-10">
                <div class="flex flex-wrap items-center gap-3">
                  <span class="inline-flex rounded-full bg-amber-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-[#6B2C2C]">
                    Day Born Group
                  </span>
                </div>
                <h3 class="text-3xl font-semibold mt-5">{{ $society->name }}</h3>
                <div class="mt-6 prose prose-slate max-w-none prose-p:text-slate-600 prose-p:leading-8">
                  {!! nl2br(e($society->brief_history)) !!}
                </div>
              </div>

              <div class="bg-[#F1EEE9] p-6 md:p-8 border-t xl:border-t-0 xl:border-l border-amber-100/70">
                @if ($coverImage)
                  <div class="rounded-[1.75rem] overflow-hidden shadow-[0_22px_45px_rgba(45,25,14,0.16)]">
                    <img
                      src="{{ $coverImage }}"
                      alt="{{ $society->name }}"
                      class="h-72 w-full object-cover"
                      loading="lazy"
                    >
                  </div>
                @else
                  <div class="h-72 rounded-[1.75rem] bg-gradient-to-br from-amber-100 to-[#ead7bf] flex items-center justify-center text-[#6B2C2C] text-5xl font-semibold">
                    {{ strtoupper(substr($society->name, 0, 1)) }}
                  </div>
                @endif

                @if (!empty($thumbnailImages))
                  <div class="grid grid-cols-2 gap-3 mt-4">
                    @foreach ($thumbnailImages as $image)
                      <div class="rounded-2xl overflow-hidden bg-white shadow-sm">
                        <img
                          src="{{ $image }}"
                          alt="{{ $society->name }} gallery image"
                          class="h-28 w-full object-cover"
                          loading="lazy"
                        >
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          </article>
            @empty
              <div class="card p-10 text-center">
                <p class="section-kicker">Nothing Published Yet</p>
                <h3 class="text-2xl font-semibold mt-3">Day Born groups will appear here once the admin adds them.</h3>
              </div>
            @endforelse
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between gap-4 mb-6">
            <div>
              <p class="section-kicker">Societies</p>
              <h3 class="text-2xl md:text-3xl font-semibold mt-2">Parish Societies</h3>
            </div>
            <span class="inline-flex items-center rounded-full bg-amber-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.22em] text-[#6B2C2C]">
              {{ $societyEntries->count() }} societies
            </span>
          </div>

          <div class="space-y-10">
            @forelse ($societyEntries as $society)
              @php
                $galleryImages = $society->gallery_image_urls;
                $coverImage = $society->cover_image_url ?? ($galleryImages[0] ?? null);
                $thumbnailImages = collect($galleryImages)
                  ->reject(fn ($image) => $image === $coverImage)
                  ->take(4)
                  ->values()
                  ->all();
              @endphp

              <article class="card overflow-hidden">
                <div class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-0">
                  <div class="p-8 md:p-10">
                    <div class="flex flex-wrap items-center gap-3">
                      <span class="inline-flex rounded-full bg-amber-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-[#6B2C2C]">
                        Society
                      </span>
                    </div>
                    <h3 class="text-3xl font-semibold mt-5">{{ $society->name }}</h3>
                    <div class="mt-6 prose prose-slate max-w-none prose-p:text-slate-600 prose-p:leading-8">
                      {!! nl2br(e($society->brief_history)) !!}
                    </div>
                  </div>

                  <div class="bg-[#F1EEE9] p-6 md:p-8 border-t xl:border-t-0 xl:border-l border-amber-100/70">
                    @if ($coverImage)
                      <div class="rounded-[1.75rem] overflow-hidden shadow-[0_22px_45px_rgba(45,25,14,0.16)]">
                        <img
                          src="{{ $coverImage }}"
                          alt="{{ $society->name }}"
                          class="h-72 w-full object-cover"
                          loading="lazy"
                        >
                      </div>
                    @else
                      <div class="h-72 rounded-[1.75rem] bg-gradient-to-br from-amber-100 to-[#ead7bf] flex items-center justify-center text-[#6B2C2C] text-5xl font-semibold">
                        {{ strtoupper(substr($society->name, 0, 1)) }}
                      </div>
                    @endif

                    @if (!empty($thumbnailImages))
                      <div class="grid grid-cols-2 gap-3 mt-4">
                        @foreach ($thumbnailImages as $image)
                          <div class="rounded-2xl overflow-hidden bg-white shadow-sm">
                            <img
                              src="{{ $image }}"
                              alt="{{ $society->name }} gallery image"
                              class="h-28 w-full object-cover"
                              loading="lazy"
                            >
                          </div>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
              </article>
            @empty
              <div class="card p-10 text-center">
                <p class="section-kicker">Nothing Published Yet</p>
                <h3 class="text-2xl font-semibold mt-3">Societies will appear here once the admin adds them.</h3>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
