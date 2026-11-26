@extends('layouts.site')

@section('content')
@php
  $hero = $aboutContent['hero'] ?? [];
  $story = $aboutContent['story'] ?? [];
  $mission = $aboutContent['mission'] ?? [];
  $vision = $aboutContent['vision'] ?? [];
  $values = $aboutContent['values'] ?? [];
  $sacraments = $aboutContent['sacraments'] ?? [];
  $saint = $aboutContent['saint'] ?? [];
  $historyArchives = $historyArchives ?? [];
@endphp

  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0">
      <img
        src="{{ $hero['background_image'] ?? '' }}"
        alt="Church Interior"
        class="w-full h-full object-cover"
      >
    <div class="absolute inset-0 bg-gradient-to-b from-[#6B2C2C]/92 via-[#8C6A5D]/78 to-[#6B2C2C]/46"></div>
    </div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Our Story</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">{{ $hero['title'] ?? 'About Us' }}</h1>
      <p class="text-lg text-white/70 mt-4">{{ $hero['subtitle'] ?? '' }}</p>
    </div>
  </section>

  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
          <p class="section-kicker">Our Parish</p>
          <h2 class="section-title mt-3">{{ $story['title'] ?? 'Our Story' }}</h2>
          @if (!empty($story['paragraphs']))
            @foreach ($story['paragraphs'] as $paragraph)
              @php
                $paragraphText = is_array($paragraph) ? (data_get($paragraph, 'text', '') ?? '') : $paragraph;
                if (is_array($paragraphText)) {
                  $paragraphText = '';
                }
              @endphp
              <p class="section-lead mt-4">{{ $paragraphText }}</p>
            @endforeach
          @endif
        </div>
        <div class="relative">
          <div class="absolute -top-6 -left-6 h-24 w-24 rounded-full bg-amber-100"></div>
          <div class="relative h-[460px] rounded-3xl overflow-hidden shadow-[0_24px_52px_rgba(45,25,14,0.2)] media-frame">
            <img
              src="{{ $story['image'] ?? '' }}"
              alt="Our Story"
              class="w-full h-full object-cover"
              loading="lazy"
            >
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="rounded-[2rem] bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C] px-6 py-10 md:px-10 md:py-12 text-white shadow-[0_24px_52px_rgba(45,25,14,0.22)]">
        <div class="max-w-3xl">
          <p class="section-kicker text-amber-300">Church History</p>
          <h2 class="text-3xl md:text-4xl font-semibold mt-3">Explore the people and societies behind our parish story</h2>
          <p class="text-white/75 text-lg mt-4">
            Continue from our parish history into dedicated archive pages covering church societies, friars who served, and Parish Pastoral Council chairpersons.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mt-10">
          @foreach ($historyArchives as $archive)
            <a
              href="{{ $archive['href'] }}"
              class="glass block rounded-[1.75rem] border border-white/15 p-6 transition duration-300 hover:-translate-y-1 hover:border-amber-300/60"
            >
              <div class="flex items-start justify-between gap-4">
                <div>
                  <p class="text-xs uppercase tracking-[0.3em] text-amber-200">Archive</p>
                  <h3 class="text-2xl font-semibold mt-3">{{ $archive['title'] }}</h3>
                </div>
                <span class="inline-flex min-w-16 items-center justify-center rounded-full bg-white/10 px-3 py-2 text-sm font-semibold text-amber-100">
                  {{ $archive['count'] }} entries
                </span>
              </div>
              <p class="text-white/75 mt-4">{{ $archive['description'] }}</p>
              <span class="inline-flex items-center gap-2 text-amber-200 font-semibold mt-6">
                {{ $archive['cta'] }}
                <span aria-hidden="true">&rarr;</span>
              </span>
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <p class="section-kicker">Purpose</p>
        <h2 class="section-title mt-3">Our Mission &amp; Vision</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="card p-8">
          <h3 class="text-2xl mb-4">{{ $mission['title'] ?? 'Our Mission' }}</h3>
          <p class="text-slate-600 text-lg">
            {{ $mission['text'] ?? '' }}
          </p>
        </div>

        <div class="card p-8">
          <h3 class="text-2xl mb-4">{{ $vision['title'] ?? 'Our Vision' }}</h3>
          <p class="text-slate-600 text-lg">
            {{ $vision['text'] ?? '' }}
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <p class="section-kicker">Our Values</p>
        <h2 class="section-title mt-3">Core Values</h2>
        <p class="section-lead max-w-2xl mx-auto mt-4">
          These values guide everything we do as a parish community.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($values as $value)
          <div class="card p-6 text-center">
            <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold">
              {{ strtoupper(substr($value['title'] ?? 'V', 0, 1)) }}
            </div>
            <h3 class="text-xl mb-3">{{ $value['title'] ?? '' }}</h3>
            <p class="text-slate-600">{{ $value['text'] ?? '' }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="py-20 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C] text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <p class="section-kicker text-amber-300">Sacraments</p>
        <h2 class="text-3xl md:text-4xl font-semibold mt-3">{{ $sacraments['title'] ?? 'Sacraments' }}</h2>
        <p class="text-white/70 text-lg max-w-2xl mx-auto mt-4">
          {{ $sacraments['subtitle'] ?? '' }}
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach (($sacraments['items'] ?? []) as $sacrament)
          @php
            $sacramentName = is_array($sacrament)
              ? (data_get($sacrament, 'name') ?? data_get($sacrament, 'item') ?? data_get($sacrament, 'title') ?? '')
              : $sacrament;
            $sacramentDescription = is_array($sacrament) ? (data_get($sacrament, 'description') ?? '') : '';
            $sacramentProcess = is_array($sacrament) ? (data_get($sacrament, 'process') ?? '') : '';
          @endphp
          <div class="glass p-6">
            <h3 class="text-lg mb-2">{{ $sacramentName }}</h3>
            @if (!empty($sacramentDescription))
              <p class="text-white/80 text-sm leading-6">{{ $sacramentDescription }}</p>
            @endif
            <div class="mt-4 text-xs uppercase tracking-[0.2em] text-amber-200">How To Receive</div>
            <p class="text-white/70 text-sm leading-6 mt-2">
              {{ $sacramentProcess ?: 'Contact the parish office for the full process and requirements.' }}
            </p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <p class="section-kicker">Our Patron</p>
        <h2 class="section-title mt-3">{{ $saint['title'] ?? 'About St. Francis Of Assisi' }}</h2>
      </div>
      <div class="card p-8">
        @if (!empty($saint['paragraphs']))
          @foreach ($saint['paragraphs'] as $paragraph)
            @php
              $paragraphText = is_array($paragraph) ? (data_get($paragraph, 'text', '') ?? '') : $paragraph;
              if (is_array($paragraphText)) {
                $paragraphText = '';
              }
            @endphp
            <p class="text-slate-600 text-lg mb-4">{{ $paragraphText }}</p>
          @endforeach
        @endif
      </div>
    </div>
  </section>
@endsection



