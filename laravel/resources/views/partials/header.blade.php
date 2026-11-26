@php
  $navMinistries = $navMinistries ?? collect();
  $navLinks = [
    ['label' => 'Events', 'route' => 'events', 'active' => ['events']],
    ['label' => 'Sermons', 'route' => 'sermons', 'active' => ['sermons', 'sermons.show']],
    ['label' => 'Gallery', 'route' => 'gallery', 'active' => ['gallery']],
    ['label' => 'Contact', 'route' => 'contact', 'active' => ['contact']],
  ];
@endphp

<header class="sticky top-0 z-50 border-b border-[#E5DDD3]/80 bg-[#F8F6F3]/92 backdrop-blur-xl shadow-[0_10px_26px_rgba(107,44,44,0.08)]">
  <div class="hidden md:block border-b border-[#E5DDD3]/70 bg-[#F3EEE7]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-between text-[0.78rem]">
      <div class="flex items-center gap-4 text-[#8C6A5D]">
        <span>{{ $siteSettings['service_sunday'] ?? 'Sunday Mass: 7:00 AM & 9:30 AM' }}</span>
      </div>
      <div class="flex items-center gap-4 text-[#6B2C2C]">
        <a href="tel:{{ $siteSettings['contact_phone'] ?? '+233000000000' }}" class="hover:text-[#5b2a1e] transition-colors">
          {{ $siteSettings['contact_phone'] ?? '+233 XX XXX XXXX' }}
        </a>
        <a href="{{ route('contact') }}" class="hover:text-[#5b2a1e] transition-colors">Visit & Directions</a>
      </div>
    </div>
  </div>

  <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Primary">
    <div class="flex justify-between items-center min-h-[78px] py-3">
      <a href="{{ route('home') }}" class="flex items-center gap-3.5">
        <div class="rounded-full bg-white/80 p-1 shadow-[0_10px_20px_rgba(107,44,44,0.1)]">
          <img
            src="{{ $siteLogoUrl ?? asset('images/logo.jpg') }}"
            alt="{{ $siteSettings['church_name'] ?? 'St. Francis Of Assisi' }} logo"
            class="w-11 h-11 rounded-full object-cover border border-[#E5DDD3]"
            loading="lazy"
          />
        </div>
        <div class="flex flex-col leading-tight">
          <span class="font-semibold text-[#333333] text-[0.98rem]">{{ $siteSettings['church_name'] ?? 'St. Francis Of Assisi' }}</span>
          <span class="text-xs text-[#8C6A5D] tracking-wide">{{ $siteSettings['church_location'] ?? 'Awoshie Anyaa' }}</span>
        </div>
      </a>

      <div class="hidden md:flex items-center gap-2 text-sm">
        <a
          href="{{ route('home') }}"
          class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}"
          @if (request()->routeIs('home')) aria-current="page" @endif
        >
          Home
        </a>
        <a
          href="{{ route('about') }}"
          class="nav-link {{ request()->routeIs('about', 'history.*') ? 'nav-link-active' : '' }}"
          @if (request()->routeIs('about', 'history.*')) aria-current="page" @endif
        >
          About
        </a>
        <div class="nav-dropdown">
          <button type="button" class="nav-link nav-dropdown-trigger" aria-haspopup="true">
            Ministries
            <span class="nav-caret">&or;</span>
          </button>
          <div class="nav-dropdown-menu">
            @forelse ($navMinistries as $ministry)
              <a href="{{ route('ministries.show', $ministry->slug) }}" class="nav-dropdown-link">
                {{ $ministry->name }}
              </a>
            @empty
              <span class="nav-dropdown-empty">No ministries yet</span>
            @endforelse
            <a href="{{ route('ministries') }}" class="nav-dropdown-link nav-dropdown-cta">
              View All Ministries
            </a>
          </div>
        </div>
        @foreach ($navLinks as $link)
          @php($isActive = $link['route'] ? request()->routeIs(...($link['active'] ?? [$link['route']])) : false)
          <a
            href="{{ $link['route'] ? route($link['route']) : '#' }}"
            class="nav-link {{ $isActive ? 'nav-link-active' : '' }}"
            @if ($isActive) aria-current="page" @endif
          >
            {{ $link['label'] }}
          </a>
        @endforeach
      </div>

      <div class="hidden md:flex items-center gap-3">
        <a href="{{ route('contact') }}" class="btn btn-primary !px-5 !py-2.5">
          Join Us Sunday
        </a>
      </div>

      <button
        type="button"
        data-menu-toggle
        aria-controls="mobile-menu"
        aria-expanded="false"
        aria-label="Toggle navigation menu"
        class="menu-toggle md:hidden inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E5DDD3] bg-white/70 text-[#6B2C2C] shadow-sm transition hover:bg-white"
      >
        <svg class="menu-icon-open h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
        </svg>
        <svg class="menu-icon-close hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
        </svg>
      </button>
    </div>

    <div id="mobile-menu" data-mobile-menu aria-hidden="true" class="mobile-menu-panel md:hidden hidden py-4 border-t border-[#E5DDD3]">
      <div class="space-y-1">
        <a
          href="{{ route('home') }}"
          class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('home') ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70' }}"
          @if (request()->routeIs('home')) aria-current="page" @endif
        >
          Home
        </a>
        <a
          href="{{ route('about') }}"
          class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('about', 'history.*') ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70' }}"
          @if (request()->routeIs('about', 'history.*')) aria-current="page" @endif
        >
          About
        </a>
        <a
          href="{{ route('ministries') }}"
          class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('ministries', 'ministries.show') ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70' }}"
          @if (request()->routeIs('ministries', 'ministries.show')) aria-current="page" @endif
        >
          Ministries
        </a>
        <div class="pl-6 pr-4 pb-2">
          <div class="text-xs uppercase tracking-[0.2em] text-[#8C6A5D] mb-2">Ministries</div>
          @forelse ($navMinistries as $ministry)
            <a
              href="{{ route('ministries.show', $ministry->slug) }}"
              class="block py-2 text-sm text-[#6B2C2C] hover:text-[#333333]"
            >
              {{ $ministry->name }}
            </a>
          @empty
            <div class="text-xs text-[#8C6A5D]">No ministries yet</div>
          @endforelse
          <a href="{{ route('ministries') }}" class="inline-block mt-2 text-sm font-semibold text-[#6B2C2C]">
            View All Ministries
          </a>
        </div>
        @foreach ($navLinks as $link)
          @php($isActive = $link['route'] ? request()->routeIs(...($link['active'] ?? [$link['route']])) : false)
          <a
            href="{{ $link['route'] ? route($link['route']) : '#' }}"
            class="block py-2.5 px-4 rounded-xl text-sm font-semibold transition-colors {{ $isActive ? 'bg-white text-[#333333]' : 'text-[#6B2C2C] hover:bg-white/70' }}"
            @if ($isActive) aria-current="page" @endif
          >
            {{ $link['label'] }}
          </a>
        @endforeach
      </div>
      <a href="{{ route('contact') }}" class="mt-3 inline-flex w-full items-center justify-center rounded-full bg-[#6B2C2C] px-4 py-2.5 text-sm font-semibold text-white shadow">
        Join Us Sunday
      </a>
    </div>
  </nav>
</header>
