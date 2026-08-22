@props([
    'title' => null,
    'description' => null,
])

<!DOCTYPE html>
<html lang="en" class="no-js scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Removes the .no-js class immediately, before first paint, so reveal
         animations work when JS is on and content stays visible when it isn't. --}}
    <script>document.documentElement.classList.remove('no-js');</script>

    <title>{{ $title ? $title . ' — ' . config('portfolio.name') : config('portfolio.seo.title') }}</title>
    <meta name="description" content="{{ $description ?? config('portfolio.seo.description') }}">
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? config('portfolio.seo.title') }}">
    <meta property="og:description" content="{{ $description ?? config('portfolio.seo.description') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    {{-- x-cloak hides Alpine-controlled elements until Alpine initialises,
         preventing a flash of the open mobile menu on load. --}}
    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire ships its own Alpine build with the collapse, focus and
         intersect plugins. Never npm-install Alpine alongside it — two copies
         on one page fight over the same directives. --}}
    @livewireStyles
</head>
<body class="overflow-x-hidden">

    {{-- Skip link: the first thing a keyboard or screen-reader user hits.
         Visually hidden until focused. --}}
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100]
              focus:rounded-lg focus:bg-violet focus:px-4 focus:py-2 focus:text-white">
        Skip to content
    </a>

    <x-aurora />

    <x-site-nav />

    <main id="main" class="relative z-10">
        {{ $slot }}
    </main>

    <x-site-footer />

    @livewireScripts
</body>
</html>
