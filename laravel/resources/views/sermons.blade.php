@extends('layouts.site')

@section('content')
@php
  $sermonsCount = $sermons->count();
@endphp

  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Sermons</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">Sermon Experience</h1>
      <p class="text-lg text-white/75 mt-4">Watch, listen, and reflect on messages that shape our faith.</p>
      <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold tracking-[0.2em] uppercase">
        {{ $sermonsCount }} Message{{ $sermonsCount === 1 ? '' : 's' }}
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($sermons as $sermon)
          <article class="card p-6 sermon-card">
            <div class="sermon-thumb">
              <span class="sermon-play" aria-hidden="true"></span>
            </div>
            <p class="section-kicker !tracking-[0.22em]">{{ $sermon->date?->format('M d, Y') }}</p>
            <h3 class="text-xl mt-3">{{ $sermon->title }}</h3>
            @if ($sermon->preacher)
              <p class="text-sm text-slate-600 mt-2">Priest/Friar: {{ $sermon->preacher }}</p>
            @endif
            @if ($sermon->summary)
              <p class="text-sm text-slate-600 mt-3">{{ $sermon->summary }}</p>
            @endif
            <div class="mt-4 flex flex-wrap gap-3 items-center">
              <a href="{{ route('sermons.show', $sermon) }}" class="btn btn-soft">
                View Sermon
              </a>
              @if ($sermon->video_url)
                <span class="pill">Video</span>
              @endif
              @if ($sermon->audio_url)
                <span class="pill">Audio</span>
              @endif
            </div>
          </article>
        @empty
          <div class="col-span-full">
            <div class="surface-soft p-10 text-center">
              <h3 class="text-2xl font-semibold text-[#2b1a12]">No Sermons Yet</h3>
              <p class="text-slate-600 mt-3">Sermons will appear here once they are published.</p>
              <a href="{{ route('home') }}" class="btn btn-soft mt-6">Back to Home</a>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
