@extends('layouts.site')

@section('title', 'Parish Pastoral Council Chairpersons')

@section('content')
  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(212,175,55,0.12),_transparent_34%),linear-gradient(135deg,#6B2C2C,#8C6A5D_52%,#6B2C2C)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Church Leadership</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">Parish Pastoral Council Chairpersons</h1>
      <p class="text-lg text-white/75 mt-4 max-w-3xl mx-auto">
        A record of all Parish Pastoral Council chairpersons, with their years of service and key notes preserved by the parish administration.
      </p>
    </div>
  </section>

  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-14">
        <p class="section-kicker">Leadership Timeline</p>
        <h2 class="section-title mt-3">Parish Pastoral Council service across the years</h2>
        <p class="section-lead mt-4">
          These entries present the succession of chairpersons who have guided the Parish Pastoral Council and supported the growth of the church.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-7">
        @forelse ($chairpersons as $chairperson)
          <article class="card overflow-hidden">
            <div class="bg-[#F1EEE9] p-6 flex items-center justify-center">
              @if ($chairperson->photo_url)
                <img
                  src="{{ $chairperson->photo_url }}"
                  alt="{{ $chairperson->name }}"
                  class="h-56 w-full max-w-[240px] rounded-[1.75rem] object-cover shadow-[0_18px_35px_rgba(45,25,14,0.14)]"
                  loading="lazy"
                >
              @else
                <div class="h-56 w-full max-w-[240px] rounded-[1.75rem] bg-gradient-to-br from-amber-100 to-[#ead7bf] flex items-center justify-center text-[#6B2C2C] text-5xl font-semibold">
                  {{ strtoupper(substr($chairperson->name, 0, 1)) }}
                </div>
              @endif
            </div>

            <div class="p-7">
              <span class="inline-flex rounded-full bg-amber-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.22em] text-[#6B2C2C]">
                {{ $chairperson->service_period_label }}
              </span>
              <h3 class="text-2xl font-semibold mt-5">{{ $chairperson->name }}</h3>

              @if ($chairperson->notes)
                <p class="text-slate-600 leading-8 mt-5">{{ $chairperson->notes }}</p>
              @else
                <p class="text-slate-500 leading-8 mt-5">No additional notes have been added for this chairperson yet.</p>
              @endif
            </div>
          </article>
        @empty
          <div class="card p-10 text-center md:col-span-2 xl:col-span-3">
            <p class="section-kicker">Nothing Published Yet</p>
            <h3 class="text-2xl font-semibold mt-3">Chairpersons will appear here once the admin adds the leadership records.</h3>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
