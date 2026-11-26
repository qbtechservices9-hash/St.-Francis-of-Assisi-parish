@extends('layouts.site')

@section('content')
@php
  $currentCategory = $category ?? 'all';
  $categoryList = array_merge(['all'], $categories ?? []);
  $eventsCount = $events->count();
@endphp

  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Calendar</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">Events &amp; Worship</h1>
      <p class="text-lg text-white/75 mt-4">Join us for worship, fellowship, and service.</p>
      <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold tracking-[0.2em] uppercase">
        {{ $eventsCount }} Upcoming Item{{ $eventsCount === 1 ? '' : 's' }}
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
              $label = $cat === 'all' ? 'All' : $cat;
            @endphp
            <a
              href="{{ route('events', ['category' => $cat]) }}"
              class="pill {{ $isActive ? 'bg-[#6B2C2C] text-white border-[#6B2C2C]' : '' }}"
            >
              {{ ucfirst($label) }}
            </a>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 event-scroll">
        @forelse ($events as $event)
          @php
            $date = \Illuminate\Support\Carbon::parse($event->date);
            $time = \Illuminate\Support\Carbon::parse($event->time);
          @endphp
          <article class="card overflow-hidden">
            @if ($event->image_url)
              <div class="media-frame">
                <img
                  src="{{ $event->image_url }}"
                  alt="{{ $event->title }}"
                  class="w-full h-52 object-cover"
                  loading="lazy"
                >
              </div>
            @endif
            <div class="p-6">
              <div class="flex items-start gap-4">
                <div class="flex-shrink-0 rounded-2xl bg-amber-100 px-4 py-3 text-center text-amber-700 min-w-[76px]">
                  <div class="text-xs uppercase tracking-widest">{{ $date->format('M') }}</div>
                  <div class="text-2xl font-semibold leading-none mt-1">{{ $date->format('d') }}</div>
                </div>

                <div class="flex-1">
                  <div class="flex flex-wrap items-start justify-between gap-3">
                    <h3 class="text-xl">{{ $event->title }}</h3>
                    <div class="flex flex-wrap items-center gap-2">
                      <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-600">
                        {{ $event->category }}
                      </span>
                      @if ($event->is_featured)
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs text-amber-700">
                          Featured
                        </span>
                      @endif
                    </div>
                  </div>

                  <p class="text-slate-600 mt-3">{{ $event->description }}</p>

                  <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                    <div class="rounded-xl bg-white px-3 py-2 text-slate-600 border border-[#eadcc9]">
                      <span class="font-semibold text-[#2b1a12]">Time:</span> {{ $time->format('g:i A') }}
                    </div>
                    <div class="rounded-xl bg-white px-3 py-2 text-slate-600 border border-[#eadcc9]">
                      <span class="font-semibold text-[#2b1a12]">Location:</span> {{ $event->location }}
                    </div>
                  </div>
                  @if ($event->recurring)
                    <p class="mt-3 text-sm font-semibold text-amber-700">{{ $event->recurring }}</p>
                  @endif
                </div>
              </div>
            </div>
          </article>
        @empty
          <div class="col-span-full">
            <div class="surface-soft p-10 text-center">
              <h3 class="text-2xl font-semibold text-[#2b1a12]">No Events Found</h3>
              <p class="text-slate-600 mt-3">No events are currently available in this category. Try another category.</p>
              <a href="{{ route('events') }}" class="btn btn-soft mt-6">View All Events</a>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="card p-8 md:p-12 text-center">
        <p class="section-kicker">Stay Connected</p>
        <h2 class="section-title mt-3">Never Miss an Event</h2>
        <p class="section-lead max-w-2xl mx-auto mt-4">
          Stay connected with our parish community and be the first to know about upcoming events,
          special celebrations, and service opportunities.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-8">
          <a href="mailto:info@stfrancisawoshie.org?subject=Subscribe%20to%20Calendar" class="btn btn-primary">
            Subscribe to Calendar
          </a>
          <a href="mailto:info@stfrancisawoshie.org?subject=Event%20Information" class="btn btn-soft">
            Contact for Details
          </a>
        </div>
      </div>
    </div>
  </section>
@endsection
