<footer class="relative z-10 border-t border-line">
    <div class="mx-auto flex max-w-[1140px] flex-wrap justify-between gap-4 px-5 py-7 text-xs text-faint sm:px-7">
        <span>&copy; {{ now()->year }} {{ config('portfolio.full_name') }}</span>

        <span>
            Laravel {{ Illuminate\Foundation\Application::VERSION }} · Livewire 4 · Tailwind 4 —
            <a href="{{ config('portfolio.contact.github') }}/vylwyn.com"
               target="_blank"
               rel="noopener"
               class="text-dim underline-offset-2 transition hover:text-lavender hover:underline">
                source on GitHub
            </a>
        </span>
    </div>
</footer>
