@props([
    'padding' => 'p-6',
    'hover' => false,
])

<div {{ $attributes->merge(['class' => 'bg-dark-900/50 backdrop-blur-sm border border-dark-800 rounded-2xl overflow-hidden ' . ($hover ? 'hover:border-neon-500/30 hover:shadow-neon-sm transition-all duration-300' : '')]) }}>
    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
