<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'St. Francis Of Assisi - Awoshie Anyaa') }}</title>
        <link rel="icon" href="{{ asset('images/logo.jpg') }}" type="image/jpeg">
        <link rel="apple-touch-icon" href="{{ asset('images/logo.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Source+Sans+3:wght@400;500;600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#F8F6F3]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-10 sm:py-12 bg-gradient-to-b from-[#F8F6F3] via-[#F2ECE4] to-[#E9DFD3] px-4">
            <div class="text-center mb-6">
                <a href="/" class="inline-flex flex-col items-center gap-3 group">
                    <div class="p-1.5 rounded-full bg-white shadow-xl border border-[#E5DDD3] group-hover:scale-105 transition-transform duration-200">
                        <x-application-logo class="w-16 h-16 rounded-full object-cover" />
                    </div>
                    <span class="font-serif text-2xl font-bold text-[#6B2C2C] tracking-wide">
                        {{ config('app.name', 'St. Francis Of Assisi') }}
                    </span>
                    <span class="text-xs uppercase tracking-widest text-[#8C6A5D] font-semibold">Parish Staff & Admin Portal</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white/95 backdrop-blur shadow-[0_20px_50px_rgba(107,44,44,0.12)] border border-[#E5DDD3] rounded-3xl">
                {{ $slot }}
            </div>

            <div class="mt-8 text-center">
                <a href="/" class="text-sm text-[#8C6A5D] hover:text-[#6B2C2C] font-medium transition-colors inline-flex items-center gap-1.5">
                    <span>&larr;</span> Return to Church Website
                </a>
            </div>
        </div>
    </body>
</html>
