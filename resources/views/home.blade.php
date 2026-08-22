<x-layouts.app>

    {{-- ─────────────────────────── Hero ─────────────────────────── --}}
    <header class="mx-auto max-w-[1140px] px-5 pt-26 pb-21 text-center sm:px-7">

        <p class="reveal mb-6.5 flex items-center justify-center gap-3 font-mono text-xs uppercase tracking-[0.09em] text-lavender">
            <span aria-hidden="true" class="h-px w-6.5 bg-gradient-to-r from-transparent to-lavender"></span>
            {{ config('portfolio.location') }} · relocating to {{ config('portfolio.relocating_to') }}
            <span aria-hidden="true" class="h-px w-6.5 bg-gradient-to-r from-lavender to-transparent"></span>
        </p>

        <h1 class="reveal mb-3.5 text-4xl font-extrabold leading-[1.02] tracking-tighter sm:text-6xl lg:text-[4.3rem]">
            {{ config('portfolio.name') }}
        </h1>

        <p class="text-gradient reveal mb-8.5 font-mono text-sm font-medium sm:text-base">
            {{ config('portfolio.role') }} — {{ config('portfolio.specialisms') }}
        </p>

        <p class="reveal mx-auto mb-5.5 max-w-[20ch] text-2xl font-extrabold leading-[1.12] tracking-tight sm:text-4xl lg:text-[2.5rem]">
            I build the systems <span class="text-gradient">people depend on.</span>
        </p>

        <p class="reveal mx-auto mb-9.5 max-w-[62ch] text-dim sm:text-lg">
            Nine years at <strong class="font-semibold text-ink">Alghanim International</strong> in Kuwait —
            leading IT delivery, and still shipping production code most weeks.
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
            <x-stat-card value="9+" label="Years at Alghanim" />
            <x-stat-card value="11" label="Years experience" />
            <x-stat-card pending label="Users supported" />
            <x-stat-card pending label="Systems shipped" />
        </div>
    </header>

    {{-- ─────────────────────────── Work ─────────────────────────── --}}
    <section id="work" class="mx-auto max-w-[1140px] scroll-mt-20 px-5 py-22 sm:px-7">
        <x-section-heading
            tag="// selected work"
            heading="Things I've shipped"
            intro="Real systems with real users. Each has a full case study covering the problem, constraints, decisions and outcome."
        />

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
        <x-section-heading
            tag="// career"
            heading="Eleven years, two countries"
            intro="Long tenures. I stay long enough to maintain what I build — which is where the real lessons are."
        />

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
        <x-section-heading tag="// about" heading="Hey, I'm Vylwyn" />

        <div class="mx-auto grid max-w-[980px] items-start gap-11 lg:grid-cols-[220px_1fr]">

            {{-- Photo placeholder. Fixed aspect ratio so swapping in the real
                 image causes no layout shift. --}}
            <div class="reveal relative flex aspect-square flex-col items-center justify-center gap-2.5 overflow-hidden rounded-[18px] border border-dashed border-violet/35 bg-gradient-to-br from-surface-2 to-surface p-4.5 text-center font-mono text-[11px] text-faint">
                <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-br from-violet/15 to-transparent"></div>
                <span class="relative text-2xl">◍</span>
                <span class="relative">PHOTO<br>600 × 600</span>
                <span class="relative text-[#3a3a4e]">placeholder</span>
            </div>

            <div class="reveal space-y-4 text-dim">
                <p>
                    I'm a full-stack engineer who also leads a team — not a manager who stopped coding.
                    That distinction matters, and it's the thing this site exists to prove.
                </p>
                <p>
                    Nine years at <strong class="font-semibold text-ink">Alghanim International</strong> taught me
                    what side projects can't: what happens to software in year three. The shortcuts that compound,
                    the data model you regret, the integration nobody documented. Most of what I know about
                    building well came from maintaining things I built badly.
                </p>
                <p>
                    My work lives in <strong class="font-semibold text-ink">Laravel and Flutter</strong>, and I'm
                    drawn to the invisible parts — data modelling, failure handling, offline behaviour, the
                    reliability nobody notices until it's gone.
                </p>
                <p>
                    I'm preparing to <strong class="font-semibold text-ink">relocate to India</strong> and looking
                    for senior or lead full-stack roles. I also take on selected freelance work.
                </p>
                <p class="text-[0.85rem] text-faint">Google UX Design Certificate — Coursera, 2023</p>
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
        <div class="reveal rounded-[26px] border border-violet/25 bg-gradient-to-br from-violet/15 to-azure/5 px-6 py-14 text-center backdrop-blur-md">
            <h2 class="mb-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Let's build something</h2>
            <p class="mx-auto mb-8 max-w-[46ch] text-dim">
                Open to senior and lead full-stack roles in India, and to selected freelance work.
            </p>

            <p class="mx-auto max-w-[46ch] rounded-xl border border-dashed border-warn/50 px-4 py-3 text-sm text-warn">
                Contact form goes here next — Livewire, with validation and rate limiting.
            </p>
        </div>
    </section>

</x-layouts.app>
