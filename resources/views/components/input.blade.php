@props([
    'label' => null,
    'id' => null,
    'type' => 'text',
    'name' => null,
    'value' => null,
    'placeholder' => null,
    'error' => null,
    'required' => false,
])

@php
    $id = $id ?? $name;
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-xs font-bold text-dark-400 uppercase tracking-widest px-1">
            {{ $label }} @if($required)<span class="text-neon-500">*</span>@endif
        </label>
    @endif

    <div class="relative">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}" 
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="w-full bg-dark-900 border-dark-600 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 {{ $error ? 'border-red-500/50 focus:border-red-500/50 focus:ring-red-500/20' : '' }}"
        >
        
        @if($error)
            <div class="mt-1 text-xs text-red-400 px-1 font-medium">
                {{ $error }}
            </div>
        @endif
    </div>
</div>
