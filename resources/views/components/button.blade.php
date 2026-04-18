@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, neon, ghost, danger
    'size' => 'md', // sm, md, lg
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-950';
    
    $variants = [
        'primary' => 'bg-dark-800 text-dark-100 hover:bg-dark-700 border border-dark-700 focus:ring-dark-600',
        'secondary' => 'bg-dark-900 text-dark-400 hover:text-dark-100 border border-dark-800 hover:border-dark-700 focus:ring-dark-800',
        'neon' => 'bg-neon-500 text-dark-950 hover:bg-neon-400 hover:shadow-neon-md focus:ring-neon-500',
        'ghost' => 'bg-transparent text-dark-400 hover:text-neon-400 hover:bg-neon-500/5 focus:ring-neon-500/20',
        'danger' => 'bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white border border-red-500/20 focus:ring-red-500',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-5 py-2.5 text-sm gap-2',
        'lg' => 'px-8 py-3.5 text-base gap-3',
    ];

    $classes = "{$baseClasses} {$variants[$variant]} {$sizes[$size]}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
