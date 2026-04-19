@props(['label', 'name', 'type' => 'text', 'value' => '', 'placeholder' => '', 'required' => false, 'error' => null])

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $name }}" class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">
            {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        id="{{ $name }}" 
        value="{{ old($name, $value) }}" 
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20 placeholder:text-slate-400' . ($error ? ' border-red-300 ring-red-500/10' : '')]) }}
    >
    @if($error)
        <p class="mt-1 text-xs font-bold text-red-500 px-1">{{ $error }}</p>
    @endif
</div>
