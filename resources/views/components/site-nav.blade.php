@php
    $links = [
        '#work' => 'Work',
        '#experience' => 'Experience',
        '#about' => 'About',
        '#contact' => 'Contact',
    ];
@endphp

<nav x-data="{ open: false }"
     class="sticky top-0 z-50 border-b border-line bg-canvas/60 backdrop-blur-lg">
    <div class="mx-auto flex max-w-[1140px] items-center justify-between gap-4 px-5 py-3.5 sm:px-7">

        <a href="{{ route('home') }}"
           class="text-gradient text-base font-extrabold tracking-tight">
            vylwyn.com
        </a>

        <div class="hidden gap-1 lg:flex">
            @foreach ($links as $href => $label)
                <a href="{{ $href }}"
                   class="rounded-lg px-3.5 py-1.5 text-sm font-medium text-dim transition hover:bg-surface-2 hover:text-ink">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-3">
            @if (config('portfolio.available'))
                <span class="hidden items-center gap-2 whitespace-nowrap rounded-full border border-line bg-surface px-3 py-1.5 text-xs text-dim sm:inline-flex">
                    <span class="h-1.5 w-1.5 rounded-full bg-ok motion-safe:animate-pulse"></span>
                    Open to work
                </span>
            @endif

            {{-- Mobile toggle --}}
            <button type="button"
                    @click="open = !open"
                    :aria-expanded="open.toString()"
                    aria-controls="mobile-nav"
                    class="rounded-lg border border-line bg-surface p-2 text-dim transition hover:text-ink lg:hidden">
                <span class="sr-only">Toggle navigation</span>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path x-show="!open" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                    <path x-show="open" x-cloak stroke-linecap="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-nav"
         x-show="open"
         x-cloak
         x-collapse
         class="border-t border-line bg-canvas/95 lg:hidden">
        <div class="mx-auto flex max-w-[1140px] flex-col px-5 py-2 sm:px-7">
            @foreach ($links as $href => $label)
                <a href="{{ $href }}"
                   @click="open = false"
                   class="rounded-lg px-3 py-2.5 text-sm font-medium text-dim transition hover:bg-surface-2 hover:text-ink">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</nav>
