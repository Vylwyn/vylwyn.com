<x-layouts.app>

    {{-- ─────────────────────────── Hero ─────────────────────────── --}}
    <header class="mx-auto max-w-[1140px] px-5 pt-26 pb-21 text-center sm:px-7">

        <p
            class="reveal mb-6.5 flex items-center justify-center gap-3 font-mono text-xs uppercase tracking-[0.09em] text-lavender">
            <span aria-hidden="true" class="h-px w-6.5 bg-gradient-to-r from-transparent to-lavender"></span>
            {{-- Relocation is only advertised while actively looking. Flip
                 PORTFOLIO_AVAILABLE in .env to turn the signalling back on. --}}
            {{ config('portfolio.location') }}@if (config('portfolio.available')) · {{ config('portfolio.relocating_to') }}@endif
            <span aria-hidden="true" class="h-px w-6.5 bg-gradient-to-r from-lavender to-transparent"></span>
        </p>

        {{-- All hero copy is editable at /vrdstudio → Site content.
             valueOr() falls back to config so a fresh install still renders. --}}
        <h1 class="reveal mb-3.5 text-4xl font-extrabold leading-[1.02] tracking-tighter sm:text-6xl lg:text-[4.3rem]">
            {{ $content->valueOr('hero_name', config('portfolio.name')) }}
        </h1>

        <p class="text-gradient reveal mb-8.5 font-mono text-sm font-medium sm:text-base">
            {{ $content->valueOr('hero_role', config('portfolio.role')) }}@if ($specialisms = $content->valueOr('hero_specialisms', config('portfolio.specialisms'))) — {{ $specialisms }}@endif
        </p>

        <p
            class="reveal mx-auto mb-5.5 max-w-[24ch] text-2xl font-extrabold leading-[1.12] tracking-tight sm:text-4xl lg:text-[2.5rem]">
            {{ $content->valueOr('hero_tagline_lead', 'I run the IT operations.') }}
            <span class="text-gradient">{{ $content->valueOr('hero_tagline_highlight', 'I also build the tools.') }}</span>
        </p>

        <p class="reveal mx-auto mb-9.5 max-w-[62ch] text-dim sm:text-lg">
            {{ $content->valueOr('hero_lede', 'Nine years leading a 22-person team at Alghanim International in Kuwait, and seven years building software alongside it.') }}
        </p>

        <div class="reveal mb-14.5 flex flex-wrap justify-center gap-3.5">
            <a href="#work"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet to-azure px-6.5 py-3.5 text-sm font-semibold text-white shadow-[0_8px_26px_-8px_rgba(139,92,246,0.6)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_14px_34px_-8px_rgba(139,92,246,0.78)]">
                Explore my work <span aria-hidden="true">→</span>
            </a>

            <a href="#contact"
                class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-6.5 py-3.5 text-sm font-semibold text-ink transition duration-300 hover:-translate-y-0.5 hover:border-violet/40">
                Get in touch
            </a>
        </div>

        <div class="reveal grid grid-cols-2 gap-3.5 md:grid-cols-4">
            {{-- Every one of these is verifiable. No placeholders. --}}
            <x-stat-card value="22" label="People led" />
            <x-stat-card value="9+" label="Years at Alghanim" />
            <x-stat-card value="7" label="Years building software" />
            <x-stat-card value="MCA" label="Computer Science" />
        </div>
    </header>

    {{-- ─────────────────────────── Work ─────────────────────────── --}}
    <section id="work" class="mx-auto max-w-[1140px] scroll-mt-20 px-5 py-22 sm:px-7">
        <x-section-heading tag="// selected work"
            :heading="$content->valueOr('work_heading', 'Things I’ve shipped')"
            :intro="$content->valueOr('work_intro', 'Real applications with real users, built alongside a full-time role.')" />

        @if ($projects->isEmpty())
            <p class="rounded-2xl border border-dashed border-line py-12 text-center text-dim">
                No published projects yet. Add one in the admin panel.
            </p>
        @else
            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-project-card :$project />
                @endforeach
            </div>
        @endif
    </section>

    {{-- ──────────────────────── Experience ──────────────────────── --}}
    <section id="experience" class="mx-auto max-w-[1140px] scroll-mt-20 px-5 py-22 sm:px-7">
        <x-section-heading tag="// career"
            :heading="$content->valueOr('experience_heading', 'Eleven years, two countries')"
            :intro="$content->valueOr('experience_intro', 'Operations and support leadership, alongside seven years of building software.')" />

        {{-- The vertical rule is drawn on the list; each item positions its own dot. --}}
        <ol class="relative mx-auto max-w-[820px] pl-7.5
                   before:absolute before:top-2 before:bottom-2 before:left-[5px] before:w-px
                   before:bg-gradient-to-b before:from-lavender before:via-azure before:to-line">
            @foreach ($experiences as $experience)
                <x-experience-item :$experience />
            @endforeach
        </ol>
    </section>

    {{-- ─────────────────────────── About ────────────────────────── --}}
    <section id="about" class="mx-auto max-w-[1140px] scroll-mt-20 px-5 py-22 sm:px-7">
        <x-section-heading tag="// about" :heading="$content->valueOr('about_heading', 'Hey, I’m Vylwyn')" />

        <div class="mx-auto grid max-w-[980px] items-start gap-11 lg:grid-cols-[220px_1fr]">

            {{-- Photo placeholder. Fixed aspect ratio so swapping in the real
            image causes no layout shift. --}}
            <div
                class="reveal relative flex aspect-square flex-col items-center justify-center gap-2.5 overflow-hidden rounded-[18px] border border-dashed border-violet/35 bg-gradient-to-br from-surface-2 to-surface p-4.5 text-center font-mono text-[11px] text-faint">
                <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-br from-violet/15 to-transparent"></div>
                <span class="relative text-2xl">◍</span>
                <span class="relative">PHOTO<br>600 × 600</span>
                <span class="relative text-[#3a3a4e]">placeholder</span>
            </div>

            {{-- Authored as markdown in the admin panel. Trusted content —
                 only you can write it — so rendering as HTML is safe here. --}}
            <div class="reveal space-y-4 text-dim
                        [&_a]:text-lavender [&_a]:underline [&_a]:underline-offset-2
                        [&_li]:ml-5 [&_li]:list-disc
                        [&_strong]:font-semibold [&_strong]:text-ink">
                {!! Str::markdown($content->valueOr('about_body', '')) !!}
            </div>
        </div>

        {{-- Skills, grouped by category from the database. --}}
        <div class="reveal mx-auto mt-11.5 grid max-w-[980px] gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($skills as $category => $technologies)
                <div class="rounded-2xl border border-line bg-surface p-5 transition hover:border-violet/30">
                    <h3 class="mb-3.5 font-mono text-[11px] uppercase tracking-[0.08em] text-lavender">
                        {{ \App\Enums\TechnologyCategory::from($category)->getLabel() }}
                    </h3>
                    <ul class="flex flex-wrap gap-1.5">
                        @foreach ($technologies as $technology)
                            <li class="rounded-md border border-line bg-surface-2 px-2.5 py-1 font-mono text-xs text-dim">
                                {{ $technology->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ────────────────────────── Contact ───────────────────────── --}}
    <section id="contact" class="mx-auto max-w-[1140px] scroll-mt-20 px-5 py-22 sm:px-7">
        <div
            class="reveal rounded-[26px] border border-violet/25 bg-gradient-to-br from-violet/15 to-azure/5 px-6 py-14 text-center backdrop-blur-md">
            <h2 class="mb-3 text-3xl font-extrabold tracking-tight sm:text-4xl">
                {{ $content->valueOr('contact_heading', 'Let’s build something') }}
            </h2>
            <p class="mx-auto mb-8 max-w-[46ch] text-dim">
                {{ $content->valueOr('contact_intro', 'Always happy to talk about internal tools, Laravel, or an interesting problem. Freelance enquiries welcome.') }}
            </p>

            <livewire:contact-form />

            <div class="mt-9 border-t border-violet/20 pt-7">
                <p class="mb-4 text-sm text-faint">Or reach me directly</p>

                <div class="flex flex-wrap justify-center gap-3">
                    @if ($email = config('portfolio.contact.email'))
                        <a href="mailto:{{ $email }}"
                           class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-5 py-2.5 text-sm font-semibold text-ink transition duration-300 hover:-translate-y-0.5 hover:border-violet/40">
                            Email
                        </a>
                    @endif

                    @if ($whatsapp = config('portfolio.contact.whatsapp'))
                        {{-- wa.me expects digits only, no plus sign or spaces. --}}
                        <a href="https://wa.me/{{ $whatsapp }}"
                           target="_blank"
                           rel="noopener"
                           class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-5 py-2.5 text-sm font-semibold text-ink transition duration-300 hover:-translate-y-0.5 hover:border-ok/40">
                            WhatsApp
                            <span class="sr-only">(opens in a new tab)</span>
                        </a>
                    @endif

                    <a href="{{ config('portfolio.contact.linkedin') }}"
                       target="_blank"
                       rel="noopener"
                       class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-5 py-2.5 text-sm font-semibold text-ink transition duration-300 hover:-translate-y-0.5 hover:border-azure/40">
                        LinkedIn
                        <span class="sr-only">(opens in a new tab)</span>
                    </a>

                    <a href="{{ config('portfolio.contact.github') }}"
                       target="_blank"
                       rel="noopener"
                       class="inline-flex items-center gap-2 rounded-xl border border-line bg-surface px-5 py-2.5 text-sm font-semibold text-ink transition duration-300 hover:-translate-y-0.5 hover:border-violet/40">
                        GitHub
                        <span class="sr-only">(opens in a new tab)</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</x-layouts.app>