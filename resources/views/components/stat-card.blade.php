@props([
    'value' => null,
    'label',
    'pending' => false,
])

<div class="rounded-2xl border border-line bg-gradient-to-br from-surface to-surface/40 px-4.5 py-6 text-center backdrop-blur-sm transition duration-300 hover:-translate-y-0.5 hover:border-violet/35">
    @if ($pending)
        {{-- Deliberately visible placeholder. A missing number is honest;
             an invented one is not. --}}
        <span class="inline-block rounded-md border border-dashed border-warn px-2.5 py-1 text-[0.88rem] font-semibold text-warn">
            TBD
        </span>
    @else
        <div class="bg-gradient-to-r from-white to-[#c4b5fd] bg-clip-text text-2xl font-extrabold leading-none tracking-tight text-transparent sm:text-[2.1rem]">
            {{ $value }}
        </div>
    @endif

    <div class="mt-2.5 text-[11.5px] font-medium text-faint">{{ $label }}</div>
</div>
