@extends('layouts.site')

@section('title', 'Friars Who Served')

@section('content')
  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(212,175,55,0.14),_transparent_35%),linear-gradient(135deg,#6B2C2C,#8C6A5D_50%,#6B2C2C)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Church History</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">Friars Who Served the Church</h1>
      <p class="text-lg text-white/75 mt-4 max-w-3xl mx-auto">
        A historical roll of the friars who served this parish from the beginning to the present, together with their years of service and collaborators.
      </p>
    </div>
  </section>

  <section class="py-20 bg-[#fbf6ed]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-14">
        <p class="section-kicker">Service Timeline</p>
        <h2 class="section-title mt-3">A record of pastoral service and partnership</h2>
        <p class="section-lead mt-4">
          Each entry highlights when a friar served the parish and the collaborators who worked alongside him during that period.
        </p>
      </div>

      <div class="relative space-y-8 before:absolute before:left-7 before:top-0 before:h-full before:w-px before:bg-amber-200/80 md:before:left-[6.55rem]">
        @forelse ($friars as $friar)
          <article class="relative pl-16 md:pl-32">
            <div class="absolute left-4 top-10 h-7 w-7 rounded-full border-4 border-[#fbf6ed] bg-[#6B2C2C] shadow md:left-[5.7rem]"></div>
            <div class="card overflow-hidden">
              <div class="grid grid-cols-1 md:grid-cols-[220px_1fr]">
                <div class="bg-[#F1EEE9] p-6 flex items-center justify-center border-b md:border-b-0 md:border-r border-amber-100/70">
                  @if ($friar->photo_url)
                    <img
                      src="{{ $friar->photo_url }}"
                      alt="{{ $friar->name }}"
                      class="h-48 w-48 rounded-[1.75rem] object-cover shadow-[0_18px_35px_rgba(45,25,14,0.14)]"
                      loading="lazy"
                    >
                  @else
                    <div class="h-48 w-48 rounded-[1.75rem] bg-gradient-to-br from-amber-100 to-[#ead7bf] flex items-center justify-center text-[#6B2C2C] text-5xl font-semibold">
                      {{ strtoupper(substr($friar->name, 0, 1)) }}
                    </div>
                  @endif
                </div>

                <div class="p-8">
                  <div class="flex flex-wrap items-center gap-3">
                    <span class="inline-flex rounded-full bg-amber-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-[#6B2C2C]">
                      {{ $friar->service_period_label }}
                    </span>
                  </div>
                  <h3 class="text-3xl font-semibold mt-5">{{ $friar->name }}</h3>

                  @if (!empty($friar->collaborators))
                    <div class="mt-6">
                      <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9b6d4b]">Collaborators</p>
                      <div class="flex flex-wrap gap-2 mt-3">
                        @foreach ($friar->collaborators as $collaborator)
                          <span class="inline-flex rounded-full border border-amber-200 bg-white px-4 py-2 text-sm text-slate-600">
                            {{ $collaborator }}
                          </span>
                        @endforeach
                      </div>
                    </div>
                  @endif

                  @if ($friar->notes)
                    <p class="text-slate-600 text-lg leading-8 mt-6">{{ $friar->notes }}</p>
                  @endif
                </div>
              </div>
            </div>
          </article>
        @empty
          <div class="card p-10 text-center">
            <p class="section-kicker">Nothing Published Yet</p>
            <h3 class="text-2xl font-semibold mt-3">Friars will appear here once the admin publishes the archive entries.</h3>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
