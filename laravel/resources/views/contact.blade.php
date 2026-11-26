@extends('layouts.site')

@section('content')
  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.15),_transparent_55%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Contact</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">We Would Love to Hear from You</h1>
      <p class="text-lg text-white/70 mt-4">Questions, prayer requests, or visiting? Reach out any time.</p>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <div class="card p-8">
          <p class="section-kicker">Get in Touch</p>
          <h2 class="text-3xl font-semibold mt-3">Parish Contact Details</h2>
          <p class="section-lead mt-4">
            Whether you're new to the area, looking for a church home, or have questions about
            our parish, we're here to help.
          </p>

          <div class="mt-8 space-y-6 text-slate-700">
            <div>
              <h3 class="text-lg font-semibold mb-1">Address</h3>
              <p>
                {{ $siteSettings['church_name'] ?? 'St. Francis Of Assisi Catholic Church' }}<br>
                {{ $siteSettings['church_location'] ?? 'Awoshie Anyaa' }}<br>
                {{ $siteSettings['church_city'] ?? 'Accra, Ghana' }}
              </p>
            </div>

            <div>
              <h3 class="text-lg font-semibold mb-1">Phone</h3>
              <p>{{ $siteSettings['contact_phone'] ?? '+233 XX XXX XXXX' }}</p>
              <p class="text-sm text-slate-500">{{ $siteSettings['office_hours_weekday'] ?? 'Monday - Friday, 9:00 AM - 5:00 PM' }}</p>
            </div>

            <div>
              <h3 class="text-lg font-semibold mb-1">Email</h3>
              <p>{{ $siteSettings['contact_email'] ?? 'info@stfrancisawoshie.org' }}</p>
              <p class="text-sm text-slate-500">{{ $siteSettings['contact_email_note'] ?? 'We typically respond within 24-48 hours' }}</p>
            </div>

            <div>
              <h3 class="text-lg font-semibold mb-1">Office Hours</h3>
              <p>{{ $siteSettings['office_hours_weekday'] ?? 'Monday - Friday: 9:00 AM - 5:00 PM' }}</p>
              <p>{{ $siteSettings['office_hours_sat'] ?? 'Saturday: 10:00 AM - 2:00 PM' }}</p>
              <p>{{ $siteSettings['office_hours_sun'] ?? 'Sunday: Closed (Worship Services Only)' }}</p>
            </div>
          </div>
        </div>

        <div class="card p-8">
          <p class="section-kicker">Message</p>
          <h2 class="text-3xl font-semibold mt-3">Send Us a Note</h2>

          @if(session('success'))
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700" role="status" aria-live="polite">
              {{ session('success') }}
            </div>
          @endif

          @if($errors->any())
            <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700" role="alert" aria-live="assertive">
              <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('contact.store') }}" method="POST" class="space-y-5 mt-6">
            @csrf

            <div>
              <label for="name" class="block text-slate-700 mb-2">Your Name *</label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="input">
            </div>

            <div>
              <label for="email" class="block text-slate-700 mb-2">Email Address *</label>
              <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="input">
            </div>

            <div>
              <label for="phone" class="block text-slate-700 mb-2">Phone Number</label>
              <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="input">
            </div>

            <div>
              <label for="subject" class="block text-slate-700 mb-2">Subject *</label>
              <select id="subject" name="subject" required class="input">
                <option value="">Select a subject</option>
                <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                <option value="new-member" {{ old('subject') == 'new-member' ? 'selected' : '' }}>New Member Information</option>
                <option value="sacraments" {{ old('subject') == 'sacraments' ? 'selected' : '' }}>Sacraments</option>
                <option value="ministries" {{ old('subject') == 'ministries' ? 'selected' : '' }}>Ministry Information</option>
                <option value="events" {{ old('subject') == 'events' ? 'selected' : '' }}>Events</option>
                <option value="prayer" {{ old('subject') == 'prayer' ? 'selected' : '' }}>Prayer Request</option>
                <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>

            <div>
              <label for="message" class="block text-slate-700 mb-2">Message *</label>
              <textarea id="message" name="message" required rows="6" class="input resize-none">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary w-full">
              Send Message
            </button>

            <p class="text-sm text-slate-500 text-center">
              * Required fields. We respect your privacy and will never share your information.
            </p>
          </form>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">
        <div class="card p-8">
          <p class="section-kicker">Parking</p>
          <h2 class="text-2xl font-semibold mt-3">Parking Information</h2>
          <p class="section-lead mt-4">
            {!! nl2br(e($siteSettings['parking_info'] ?? 'Parking details will be shared soon.')) !!}
          </p>
        </div>

        <div class="card p-8">
          <p class="section-kicker">New Here?</p>
          <h2 class="text-2xl font-semibold mt-3">What to Expect</h2>
          <p class="section-lead mt-4">
            {!! nl2br(e($siteSettings['what_to_expect'] ?? 'We are preparing a helpful guide for first-time visitors.')) !!}
          </p>
        </div>
      </div>
    </div>
  </section>

  @php
    $mapQuery = $siteSettings['map_query']
      ?? trim(($siteSettings['church_name'] ?? '') . ' ' . ($siteSettings['church_location'] ?? '') . ' ' . ($siteSettings['church_city'] ?? ''));
    $mapEmbedQuery = $siteSettings['map_embed_query'] ?? 'Ghana';
    $mapEmbedUrl = 'https://www.google.com/maps?q=' . urlencode($mapEmbedQuery) . '&output=embed';
    $mapLink = $siteSettings['map_link'] ?? ('https://www.google.com/maps?q=' . urlencode($mapQuery));
  @endphp

  <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <p class="section-kicker">Visit Us</p>
        <h2 class="section-title mt-3">Find Us</h2>
        <p class="section-lead mt-3">Located in Ghana. Tap directions to navigate from your current location.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-[2fr,1fr] gap-8">
        <div class="card overflow-hidden">
          <iframe
            src="{{ $mapEmbedUrl }}"
            class="w-full h-[420px]"
            style="border:0;"
            allowfullscreen
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="Church location map"
          ></iframe>
        </div>

        <div class="card p-6 flex flex-col gap-4">
          <h3 class="text-xl font-semibold">Get Directions</h3>
          <p class="text-sm text-slate-600">Use your current location to get turn-by-turn directions to the church.</p>
          <button
            type="button"
            class="btn btn-primary"
            data-directions
            data-destination="{{ $mapQuery }}"
          >
            Use My Location
          </button>
          <a
            href="{{ $mapLink }}"
            target="_blank"
            rel="noopener noreferrer"
            class="btn btn-soft text-center"
          >
            Open in Google Maps
          </a>
          <p class="text-xs text-slate-500">
            {{ $siteSettings['church_name'] ?? 'St. Francis Of Assisi Catholic Church' }}<br>
            {{ $siteSettings['church_location'] ?? 'Awoshie Anyaa' }}, {{ $siteSettings['church_city'] ?? 'Accra, Ghana' }}
          </p>
        </div>
      </div>
    </div>
  </section>
@endsection
