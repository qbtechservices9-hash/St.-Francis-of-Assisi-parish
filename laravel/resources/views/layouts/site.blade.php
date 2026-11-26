<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'St. Francis Of Assisi - Awoshie Anyaa')</title>
    <link rel="icon" href="{{ $siteLogoUrl ?? asset('images/logo.jpg') }}" type="image/jpeg">
    <link rel="apple-touch-icon" href="{{ $siteLogoUrl ?? asset('images/logo.jpg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="min-h-screen site-3d flex flex-col bg-[#F8F6F3] text-[#333333]" data-animate="@yield('page_animate', 'true')">
    <div class="site-preloader" data-preloader aria-hidden="true">
      <div class="preloader-card">
        <img src="{{ $siteLogoUrl ?? asset('images/logo.jpg') }}" alt="Church logo" class="preloader-logo">
        <div class="preloader-spinner" aria-hidden="true"></div>
        <p class="preloader-text">Preparing a warm welcome...</p>
      </div>
    </div>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    @include('partials.header')

    <main id="main-content" class="flex-1" tabindex="-1">
      @yield('content')
    </main>

    @include('partials.footer')
  </body>
</html>

