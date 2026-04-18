@props([
    'padding' => 'p-6',
    'hover' => false,
])

<div {{ $attributes->merge(['class' => 'bg-dark-800/80 backdrop-blur-md border border-dark-600 rounded-2xl overflow-hidden ' . ($hover ? 'hover:border-neon-500/30 hover:shadow-[0_0_30px_oklch(0.66_0.17_195_/_0.05)] transition-all duration-300' : '')]) }}>
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
