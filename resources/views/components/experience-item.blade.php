@props(['experience'])

<li class="reveal group relative pb-10 last:pb-0">
    {{-- Timeline dot. Sits on the vertical rule drawn by the parent list. --}}
    <span aria-hidden="true"
          class="absolute top-1.5 -left-[29px] h-2.75 w-2.75 rounded-full border-2 transition
                 {{ $experience->isCurrent()
                     ? 'border-lavender bg-lavender shadow-[0_0_14px_rgba(167,139,250,0.65)]'
                     : 'border-line bg-canvas group-hover:border-lavender' }}"></span>

    <p class="mb-1.5 font-mono text-[11.5px] uppercase tracking-wide text-faint">
        {{ $experience->period() }} · {{ $experience->duration() }}@if ($experience->location) · {{ $experience->location }}@endif
    </p>

    <h3 class="text-[1.08rem] font-bold tracking-tight">
        {{ $experience->role }}

        @if ($experience->isCurrent())
            <span class="ml-2.5 align-middle rounded-full border border-ok/30 bg-ok/15 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-ok">
                Current
            </span>
        @endif
    </h3>

    <p class="text-gradient mt-1 font-mono text-[0.88rem] font-medium">
        {{ $experience->organisation }}
    </p>

    @if ($experience->summary)
        <p class="mt-2.5 max-w-[66ch] text-[0.93rem] text-dim">{{ $experience->summary }}</p>
    @endif
</li>
