{{--
    Animated background. Purely decorative, so aria-hidden and pointer-events-none —
    it must never be announced to a screen reader or intercept a click.

    The blobs animate transform only. Animating transform and opacity keeps the
    work on the compositor; animating top/left/width would force layout on every
    frame and drop the framerate on a phone.
--}}
<div aria-hidden="true" class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
    <div class="absolute -top-[18vw] -left-[10vw] h-[52vw] w-[52vw] rounded-full bg-violet opacity-30 blur-[90px] motion-safe:animate-[aurora-1_22s_ease-in-out_infinite_alternate]"></div>
    <div class="absolute -top-[8vw] -right-[12vw] h-[44vw] w-[44vw] rounded-full bg-azure opacity-30 blur-[90px] motion-safe:animate-[aurora-2_26s_ease-in-out_infinite_alternate]"></div>
    <div class="absolute top-[40vh] left-[36vw] h-[34vw] w-[34vw] rounded-full bg-rose opacity-15 blur-[90px] motion-safe:animate-[aurora-3_30s_ease-in-out_infinite_alternate]"></div>
</div>

{{-- Film grain. Inline SVG so it costs no extra request. --}}
<div aria-hidden="true"
     class="pointer-events-none fixed inset-0 z-[1] opacity-[0.028]"
     style="background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='200' height='200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='200' height='200' filter='url(%23n)'/%3E%3C/svg%3E&quot;)">
</div>

@once
    @push('styles')
    @endpush
    <style>
        @keyframes aurora-1 { to { transform: translate(8vw, 10vh) scale(1.15); } }
        @keyframes aurora-2 { to { transform: translate(-9vw, 14vh) scale(1.10); } }
        @keyframes aurora-3 { to { transform: translate(-12vw, -8vh) scale(1.20); } }
    </style>
@endonce
