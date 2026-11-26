<footer class="bg-[#1f140e] text-[#f3e7d8]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="rounded-3xl border border-white/10 bg-white/5 p-8 md:p-10">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="rounded-full bg-white/10 p-1">
              <img
                src="{{ $siteLogoUrl ?? asset('images/logo.jpg') }}"
                alt="{{ $siteSettings['church_name'] ?? 'St. Francis Of Assisi' }} logo"
                class="w-10 h-10 rounded-full object-cover border border-white/20"
                loading="lazy"
              />
            </div>
            <div>
              <div class="font-semibold text-white">{{ $siteSettings['church_name'] ?? 'St. Francis Of Assisi' }}</div>
              <div class="text-xs text-[#cdb8a7]">{{ $siteSettings['church_location'] ?? 'Awoshie Anyaa' }}</div>
            </div>
          </div>
          <p class="text-sm text-[#cdb8a7]">
            A Catholic family walking in faith, serving in love, and building hope in our community.
          </p>
        </div>

        <div>
          <h3 class="font-semibold text-white mb-4">Explore</h3>
          <ul class="space-y-2 text-sm text-[#d9c8b9]">
            <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Home</a></li>
            <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About</a></li>
            <li><a href="{{ route('ministries') }}" class="hover:text-white transition-colors">Ministries</a></li>
            <li><a href="{{ route('events') }}" class="hover:text-white transition-colors">Events</a></li>
            <li><a href="{{ route('sermons') }}" class="hover:text-white transition-colors">Sermons</a></li>
            <li><a href="{{ route('gallery') }}" class="hover:text-white transition-colors">Gallery</a></li>
            <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold text-white mb-4">Worship Schedule</h3>
          <ul class="space-y-2 text-sm text-[#d9c8b9]">
            <li>{{ $siteSettings['service_sunday'] ?? 'Sunday Mass: 7:00 AM & 9:30 AM' }}</li>
            <li>{{ $siteSettings['service_wednesday'] ?? 'Monday, Wednesday & Friday Mass: 6:00 AM' }}</li>
            <li>{{ $siteSettings['service_friday'] ?? 'Sunday Adoration: 5:00 PM' }}</li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold text-white mb-4">Reach Us</h3>
          <ul class="space-y-2 text-sm text-[#d9c8b9]">
            <li>{{ $siteSettings['church_city'] ?? 'Accra, Ghana' }}</li>
            <li>{{ $siteSettings['contact_phone'] ?? '+233 XX XXX XXXX' }}</li>
            <li>{{ $siteSettings['contact_email'] ?? 'info@stfrancisawoshie.org' }}</li>
          </ul>

          <div class="flex items-center gap-3 mt-4">
            <a
              href="{{ $siteSettings['social_facebook'] ?? '#' }}"
              aria-label="Facebook"
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 text-[#e6d6c6] transition hover:border-amber-300 hover:text-amber-100"
              target="_blank"
              rel="noopener"
            >
              <span class="sr-only">Facebook</span>
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                <path d="M13.5 9.5h2.5V7h-2.5c-2.5 0-4 1.7-4 4v2H7v2.5h2.5V22h2.7v-6.5H15l.5-2.5h-3.3V11c0-1 .5-1.5 1.3-1.5z"/>
              </svg>
            </a>
            <a
              href="{{ $siteSettings['social_twitter'] ?? '#' }}"
              aria-label="Twitter/X"
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 text-[#e6d6c6] transition hover:border-amber-300 hover:text-amber-100"
              target="_blank"
              rel="noopener"
            >
              <span class="sr-only">Twitter/X</span>
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                <path d="M18.9 3h2.7l-6 6.9 6.6 11.1h-5.2l-4-6.8-6 6.8H4.4l6.5-7.4L4.6 3h5.3l3.7 6.2L18.9 3z"/>
              </svg>
            </a>
            <a
              href="{{ $siteSettings['social_instagram'] ?? '#' }}"
              aria-label="Instagram"
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 text-[#e6d6c6] transition hover:border-amber-300 hover:text-amber-100"
              target="_blank"
              rel="noopener"
            >
              <span class="sr-only">Instagram</span>
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                <path d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm0 2a2 2 0 0 0-2 2v10c0 1.1.9 2 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H7zm5 3.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5zm0 2A1.5 1.5 0 1 0 13.5 12 1.5 1.5 0 0 0 12 10.5zm5-3.6a1 1 0 1 1-1 1 1 1 0 0 1 1-1z"/>
              </svg>
            </a>
            <a
              href="{{ $siteSettings['social_youtube'] ?? '#' }}"
              aria-label="YouTube"
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 text-[#e6d6c6] transition hover:border-amber-300 hover:text-amber-100"
              target="_blank"
              rel="noopener"
            >
              <span class="sr-only">YouTube</span>
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                <path d="M23.5 7.2a3 3 0 0 0-2.1-2.1C19.6 4.6 12 4.6 12 4.6s-7.6 0-9.4.5A3 3 0 0 0 .5 7.2 31.7 31.7 0 0 0 0 12a31.7 31.7 0 0 0 .5 4.8 3 3 0 0 0 2.1 2.1c1.8.5 9.4.5 9.4.5s7.6 0 9.4-.5a3 3 0 0 0 2.1-2.1A31.7 31.7 0 0 0 24 12a31.7 31.7 0 0 0-.5-4.8zM9.7 15.1V8.9l5.8 3.1-5.8 3.1z"/>
              </svg>
            </a>
            <a
              href="{{ $siteSettings['social_tiktok'] ?? '#' }}"
              aria-label="TikTok"
              class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/20 text-[#e6d6c6] transition hover:border-amber-300 hover:text-amber-100"
              target="_blank"
              rel="noopener"
            >
              <span class="sr-only">TikTok</span>
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                <path d="M16.6 4.2c.9 1.2 2.3 2 3.8 2.1v3.5c-1.9.1-3.7-.5-5.2-1.6v6.7c0 3.4-2.8 6.1-6.2 6.1-3.4 0-6.2-2.7-6.2-6.1 0-3.4 2.8-6.1 6.2-6.1.4 0 .8 0 1.2.1v3.6c-.3-.1-.7-.2-1.2-.2-1.4 0-2.6 1.1-2.6 2.5 0 1.4 1.2 2.5 2.6 2.5 1.5 0 2.6-1.2 2.6-2.9V3h2.8c.2.4.4.8.6 1.2z"/>
              </svg>
            </a>
          </div>
        </div>
      </div>

      <div class="border-t border-white/10 mt-10 pt-6 text-center text-xs text-[#cdb8a7]">
        <p>&copy; {{ date('Y') }} St. Francis Of Assisi Catholic Church, Awoshie Anyaa. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>
