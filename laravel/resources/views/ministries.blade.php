@extends('layouts.site')

@section('content')
@php
  $ministryCount = $ministries->count();
@endphp

  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Serve With Us</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">Our Ministries</h1>
      <p class="text-lg text-white/75 mt-4">Find your place to serve, grow, and build community.</p>
      <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold tracking-[0.2em] uppercase">
        {{ $ministryCount }} Active Ministr{{ $ministryCount === 1 ? 'y' : 'ies' }}
      </div>
    </div>
  </section>

  <section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="surface-soft p-8 md:p-10 text-center">
        <p class="section-kicker">Belong & Serve</p>
        <h2 class="section-title mt-3">Ministry Life at St. Francis</h2>
        <p class="section-lead max-w-3xl mx-auto mt-4">
          At St. Francis Of Assisi, everyone has unique gifts to share. Our ministries create spaces
          for worship, discipleship, outreach, and practical service.
        </p>
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-7">
        @forelse ($ministries as $ministry)
          <article class="card overflow-hidden">
            <div class="relative h-52 media-frame">
              <img
                src="{{ $ministry->image_url ?: 'https://images.unsplash.com/photo-1635782165159-c7df190d4023?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjaHVyY2glMjBpbnRlcmlvciUyMHN1bmxpZ2h0fGVufDF8fHx8MTc3MjM3NTU1N3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral' }}"
                alt="{{ $ministry->name }}"
                class="w-full h-full object-cover"
                loading="lazy"
              >
              <div class="absolute inset-0 bg-gradient-to-t from-[#3b2314]/75 to-transparent"></div>
            </div>
            <div class="p-6">
              <h3 class="text-2xl">{{ $ministry->name }}</h3>
              <p class="text-slate-600 mt-3">{{ $ministry->description }}</p>

              <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                <a href="mailto:{{ $ministry->contact_email }}" class="rounded-xl border border-[#eadcc9] px-3 py-2 text-amber-700 font-semibold hover:bg-amber-50 transition-colors">
                  Email Coordinator
                </a>
                @if ($ministry->contact_phone)
                  <a href="tel:{{ $ministry->contact_phone }}" class="rounded-xl border border-[#eadcc9] px-3 py-2 text-amber-700 font-semibold hover:bg-amber-50 transition-colors">
                    Call Coordinator
                  </a>
                @else
                  <div class="rounded-xl border border-[#eadcc9] px-3 py-2 text-slate-500">
                    Contact via email
                  </div>
                @endif
              </div>

              <a href="{{ route('ministries.show', $ministry) }}" class="btn btn-soft mt-5 w-full">
                View Ministry Details
              </a>
            </div>
          </article>
        @empty
          <div class="col-span-full">
            <div class="surface-soft p-10 text-center">
              <h3 class="text-2xl font-semibold text-[#2b1a12]">No Ministries Available</h3>
              <p class="text-slate-600 mt-3">Ministries will appear here once they are added.</p>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-10">
        <p class="section-kicker">How To Join</p>
        <h2 class="section-title mt-3">Start Serving in Three Steps</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6 text-center">
          <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold">1</div>
          <h3 class="text-xl">Explore</h3>
          <p class="text-slate-600 mt-3">Browse ministries and find one that fits your gifts and passion.</p>
        </div>
        <div class="card p-6 text-center">
          <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold">2</div>
          <h3 class="text-xl">Connect</h3>
          <p class="text-slate-600 mt-3">Reach out to the coordinator and ask about next meeting dates.</p>
        </div>
        <div class="card p-6 text-center">
          <div class="mx-auto mb-4 h-12 w-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg font-semibold">3</div>
          <h3 class="text-xl">Participate</h3>
          <p class="text-slate-600 mt-3">Attend and start serving with the team in faith and unity.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C] text-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
      <h2 class="text-3xl md:text-4xl font-semibold">Ready to Get Involved?</h2>
      <p class="text-lg text-white/75 mt-5">
        Whether you are new to our parish or have been here for years, there is a place for you to serve.
      </p>
      <a href="mailto:info@stfrancisawoshie.org" class="btn btn-secondary mt-8">
        Contact Parish Office
      </a>
    </div>
  </section>
@endsection
