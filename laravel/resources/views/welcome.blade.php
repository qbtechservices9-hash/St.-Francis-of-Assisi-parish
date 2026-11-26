<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'St. Francis Of Assisi - Awoshie Anyaa') }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-white text-gray-800">
        <main class="min-h-screen flex items-center justify-center px-6">
            <div class="max-w-xl text-center space-y-4">
                <p class="text-sm uppercase tracking-widest text-gray-500">Welcome</p>
                <h1 class="text-3xl sm:text-4xl font-semibold text-gray-900">
                    St. Francis Of Assisi Church â€” Awoshie Anyaa
                </h1>
                <p class="text-gray-600">
                    This site is powered by our custom church platform. Please continue to the main website.
                </p>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center justify-center rounded-lg bg-blue-700 px-6 py-3 text-white hover:bg-blue-800">
                    Visit the Website
                </a>
            </div>
        </main>
    </body>
</html>

