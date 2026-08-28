{{-- Passing :project adds CreativeWork structured data and switches og:type to article. --}}
<x-layouts.app :title="$project->title" :description="$project->tagline" :project="$project">

    <article class="mx-auto max-w-[820px] px-5 pt-20 pb-16 sm:px-7">

        <nav aria-label="Breadcrumb" class="mb-9">
            <a href="{{ route('home') }}#work"
               class="font-mono text-xs text-dim transition hover:text-lavender">
                <span aria-hidden="true">←</span> Back to work
            </a>
        </nav>

        <header class="mb-11">
            @php
                $tone = match ($project->status->value) {
                    'live' => 'bg-ok/15 text-ok border-ok/30',
                    'in_progress' => 'bg-warn/15 text-warn border-warn/30',
                    default => 'bg-faint/15 text-faint border-faint/30',
                };
            @endphp

            <div class="mb-5 flex flex-wrap items-center gap-3 font-mono text-[11px] uppercase tracking-wider">
                <span class="rounded-full border px-2.5 py-1 font-bold {{ $tone }}">
                    {{ $project->status->getLabel() }}
                </span>

                @if ($project->client)
                    <span class="text-faint">Client · {{ $project->client }}</span>
                @endif

                @if ($project->year)
                    <span class="text-faint">{{ $project->year }}</span>
                @endif
            </div>

            <h1 class="mb-4 text-3xl font-extrabold leading-tight tracking-tighter sm:text-5xl">
                {{ $project->title }}
            </h1>

            <p class="max-w-[60ch] text-lg text-dim">{{ $project->tagline }}</p>
        </header>

        @if ($project->cover_image)
            <img src="{{ Storage::disk('public')->url($project->cover_image) }}"
                 alt="Screenshot of {{ $project->title }}"
                 loading="lazy"
                 class="mb-11 w-full rounded-2xl border border-line">
        @endif

        <div class="mb-11 grid gap-8 border-y border-line py-8 sm:grid-cols-2">
            <div>
                <h2 class="mb-3 font-mono text-[11px] uppercase tracking-[0.08em] text-lavender">Stack</h2>
                <ul class="flex flex-wrap gap-1.5">
                    @foreach ($project->technologies as $technology)
                        <li class="rounded-md border border-line bg-surface-2 px-2.5 py-1 font-mono text-[11px] text-dim">
                            {{ $technology->name }}
                        </li>
                    @endforeach
                </ul>
            </div>

            @if ($project->links() !== [])
                <div>
                    <h2 class="mb-3 font-mono text-[11px] uppercase tracking-[0.08em] text-lavender">Links</h2>
                    <ul class="space-y-1.5">
                        @foreach ($project->links() as $link)
                            <li>
                                <a href="{{ $link['url'] }}"
                                   target="_blank"
                                   rel="noopener"
                                   class="text-sm font-semibold text-lavender transition hover:text-violet">
                                    {{ $link['label'] }} <span aria-hidden="true">↗</span>
                                    <span class="sr-only">(opens in a new tab)</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{--
            Str::markdown() uses league/commonmark, already bundled with Laravel.
            The body is authored by you in the admin panel, so this content is
            trusted — if it ever accepted third-party input this would need
            sanitising before rendering as HTML.

            Tailwind resets all element styling, so headings and lists inside
            rendered markdown need styling back. That's what these arbitrary
            child selectors do; swap for @tailwindcss/typography if it grows.
        --}}
        <div class="max-w-none space-y-5 text-dim
                    [&_a]:text-lavender [&_a]:underline [&_a]:underline-offset-2
                    [&_blockquote]:border-l-2 [&_blockquote]:border-violet/40 [&_blockquote]:pl-4 [&_blockquote]:italic
                    [&_code]:rounded [&_code]:bg-surface-2 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:font-mono [&_code]:text-[0.85em] [&_code]:text-lavender
                    [&_h2]:mt-10 [&_h2]:text-2xl [&_h2]:font-bold [&_h2]:tracking-tight [&_h2]:text-ink
                    [&_h3]:mt-8 [&_h3]:text-lg [&_h3]:font-bold [&_h3]:text-ink
                    [&_li]:ml-5 [&_li]:list-disc
                    [&_pre]:overflow-x-auto [&_pre]:rounded-xl [&_pre]:border [&_pre]:border-line [&_pre]:bg-surface [&_pre]:p-4
                    [&_strong]:font-semibold [&_strong]:text-ink">
            {!! Str::markdown($project->body ?? '') !!}
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="mx-auto max-w-[1140px] border-t border-line px-5 py-16 sm:px-7">
            <h2 class="mb-8 text-center font-mono text-[11px] uppercase tracking-[0.08em] text-lavender">
                More work
            </h2>

            <div class="grid gap-5 md:grid-cols-2">
                @foreach ($related as $project)
                    <x-project-card :$project />
                @endforeach
            </div>
        </section>
    @endif

</x-layouts.app>
