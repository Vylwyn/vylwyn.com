@props(['project'])

<article class="reveal ring-gradient group relative overflow-hidden rounded-[20px] border border-line bg-gradient-to-br from-surface to-surface/50 p-7 backdrop-blur-sm transition-transform duration-300 ease-[cubic-bezier(.16,1,.3,1)] hover:-translate-y-1.5">

    @php
        $status = $project->status;
        $tone = match ($status->value) {
            'live' => 'bg-ok/15 text-ok border-ok/30',
            'in_progress' => 'bg-warn/15 text-warn border-warn/30',
            default => 'bg-faint/15 text-faint border-faint/30',
        };
        $badge = $status->getLabel() . ($project->client ? ' · Client' : '');
    @endphp

    @if ($project->cover_image)
        {{-- Explicit width and height reserve the space before the image loads,
             so the card doesn't jump as it arrives (cumulative layout shift). --}}
        <div class="relative mb-5 overflow-hidden rounded-xl border border-line">
            <img src="{{ Storage::disk('public')->url($project->cover_image) }}"
                 alt="Screenshot of {{ $project->title }}"
                 width="1200"
                 height="750"
                 loading="lazy"
                 decoding="async"
                 class="aspect-16/10 w-full object-cover object-top transition duration-500 group-hover:scale-[1.03]">

            <span class="absolute top-2.5 right-2.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-[10.5px] font-bold uppercase tracking-wider backdrop-blur-md {{ $tone }}">
                {{ $badge }}
            </span>
        </div>
    @else
        {{-- No cover yet: fall back to the icon tile so the grid stays even. --}}
        <div class="mb-4 flex items-center justify-between gap-3">
            <div class="grid h-10.5 w-10.5 place-items-center rounded-xl border border-violet/30 bg-gradient-to-br from-violet/25 to-azure/15">
                <svg class="h-5 w-5 text-lavender" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h12A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6z" />
                    <path stroke-linecap="round" d="M3.75 8.25h16.5" />
                </svg>
            </div>

            <span class="whitespace-nowrap rounded-full border px-2.5 py-1 text-[10.5px] font-bold uppercase tracking-wider {{ $tone }}">
                {{ $badge }}
            </span>
        </div>
    @endif

    <h3 class="mb-2.5 text-xl font-bold tracking-tight">
        @if ($project->hasCaseStudy())
            {{-- Stretched link: the whole card becomes clickable, but the
                 accessible name still comes from the heading text. --}}
            <a href="{{ route('projects.show', $project) }}"
               class="after:absolute after:inset-0 after:content-[''] hover:text-lavender">
                {{ $project->title }}
            </a>
        @else
            {{ $project->title }}
        @endif
    </h3>

    <p class="mb-4.5 text-[0.93rem] text-dim">{{ $project->summary }}</p>

    @if ($project->technologies->isNotEmpty())
        <ul class="mb-5 flex flex-wrap gap-1.5">
            @foreach ($project->technologies as $technology)
                <li class="rounded-md border border-line bg-surface-2 px-2.5 py-1 font-mono text-[11px] text-dim">
                    {{ $technology->name }}
                </li>
            @endforeach
        </ul>
    @endif

    {{-- relative z-10 keeps these above the stretched link so they stay clickable. --}}
    <div class="relative z-10 flex flex-wrap gap-4 text-[13.5px] font-semibold">
        @foreach ($project->links() as $link)
            <a href="{{ $link['url'] }}"
               target="_blank"
               rel="noopener"
               class="text-lavender transition hover:text-violet">
                {{ $link['label'] }} <span aria-hidden="true">↗</span>
                <span class="sr-only">(opens in a new tab)</span>
            </a>
        @endforeach

        @if ($project->hasCaseStudy())
            <a href="{{ route('projects.show', $project) }}"
               class="text-lavender transition hover:text-violet">
                Case study <span aria-hidden="true">→</span>
            </a>
        @endif
    </div>
</article>
