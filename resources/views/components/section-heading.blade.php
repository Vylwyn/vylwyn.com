@props([
    'tag' => null,
    'heading',
    'intro' => null,
])

<div class="mb-13 text-center">
    @if ($tag)
        <div class="reveal mb-4.5 inline-block rounded-full border border-violet/25 bg-violet/10 px-3.5 py-1.5 font-mono text-[11.5px] text-lavender">
            {{ $tag }}
        </div>
    @endif

    <h2 class="reveal mb-3.5 text-3xl font-extrabold tracking-tight sm:text-4xl md:text-[2.7rem]">
        {{ $heading }}
    </h2>

    @if ($intro)
        <p class="reveal mx-auto max-w-[58ch] text-dim">{{ $intro }}</p>
    @endif
</div>
