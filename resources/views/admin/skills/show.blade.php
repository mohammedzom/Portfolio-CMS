@extends('admin.layout')
@section('title', 'Skill Details')
@section('page-title', $skill->name)
@section('page-subtitle', 'Skill information and details')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('admin.skills.index') }}"
            class="inline-flex items-center gap-2 text-dark-400 hover:text-neon-500 transition-colors mb-6">
            <i class="ri-arrow-left-line"></i> Back to Skills
        </a>

        {{-- Skill Detail Card --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            {{-- Header --}}
            <div class="relative h-32 gradient-neon">
                <div class="absolute inset-0 bg-gradient-to-b from-transparent to-dark-950/80"></div>
                <div class="absolute bottom-4 left-6 flex items-end gap-4">
                    @if ($skill->icon)
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center shadow-lg"
                            style="background: {{ $skill->color ?? 'oklch(0.66 0.17 195 / 0.2)' }};">
                            <img src="{{ $skill->icon }}" alt="{{ $skill->name }}" class="w-8 h-8 object-contain">
                        </div>
                    @else
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center shadow-lg gradient-neon">
                            <i class="ri-code-s-slash-line text-dark-950 text-2xl"></i>
                        </div>
                    @endif
                    <div class="mb-1">
                        <h1 class="font-display font-bold text-2xl text-dark-100">{{ $skill->name }}</h1>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-dark-700/80 text-dark-300">{{ $skill->type }}</span>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-6 space-y-6">
                {{-- Proficiency Section --}}
                <div>
                    <h3 class="font-display font-semibold text-dark-100 mb-4 flex items-center gap-2">
                        <i class="ri-bar-chart-line text-neon-500"></i> Proficiency Level
                    </h3>
                    <div class="glass rounded-xl border border-dark-700 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-dark-300">Current Level</span>
                            <span class="text-3xl font-bold" 
                                style="color: {{ $skill->proficiency >= 80 ? 'oklch(0.72 0.18 160)' : ($skill->proficiency >= 60 ? 'oklch(0.66 0.17 195)' : 'oklch(0.58 0.22 290)') }};">
                                {{ $skill->proficiency }}%
                            </span>
                        </div>
                        <div class="h-3 rounded-full bg-dark-700 overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                style="width: {{ $skill->proficiency }}%; background: linear-gradient(90deg, {{ $skill->color ?? 'oklch(0.66 0.17 195)' }}, {{ $skill->color ?? 'oklch(0.60 0.15 220)' }});">
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-dark-700">
                            <span class="text-sm text-dark-400">Level Classification:</span>
                            <span class="text-sm font-semibold px-3 py-1 rounded-full"
                                style="background: {{ $skill->proficiency >= 80 ? 'oklch(0.72 0.18 160 / 0.1)' : ($skill->proficiency >= 60 ? 'oklch(0.66 0.17 195 / 0.1)' : 'oklch(0.58 0.22 290 / 0.1)') }}; color: {{ $skill->proficiency >= 80 ? 'oklch(0.72 0.18 160)' : ($skill->proficiency >= 60 ? 'oklch(0.66 0.17 195)' : 'oklch(0.58 0.22 290)') }};">
                                {{ $skill->level }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Additional Info --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="glass rounded-xl border border-dark-700 p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="ri-palette-line text-neon-500"></i>
                            <span class="text-sm text-dark-400">Accent Color</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg border border-dark-600"
                                style="background: {{ $skill->color ?? 'oklch(0.66 0.17 195)' }};"></div>
                            <span class="font-mono text-sm text-dark-300">{{ $skill->color ?? 'Default (Cyan)' }}</span>
                        </div>
                    </div>
                    <div class="glass rounded-xl border border-dark-700 p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <i class="ri-image-line text-neon-500"></i>
                            <span class="text-sm text-dark-400">Icon</span>
                        </div>
                        @if ($skill->icon)
                            <div class="flex items-center gap-3">
                                <img src="{{ $skill->icon }}" alt="Icon" class="w-8 h-8 object-contain rounded-lg bg-dark-700/50 p-1">
                                <span class="text-sm text-dark-300 truncate max-w-[200px]">{{ $skill->icon }}</span>
                            </div>
                        @else
                            <span class="text-sm text-dark-500 italic">No custom icon set</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 pt-6 border-t border-dark-700">
                    <a href="{{ route('admin.skills.edit', $skill) }}"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl gradient-neon text-dark-950 font-bold hover:neon-glow transition-all">
                        <i class="ri-pencil-line"></i> Edit Skill
                    </a>
                    <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to archive this skill?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-6 py-3.5 rounded-xl glass border border-dark-700 text-red-400 font-medium hover:bg-red-500/10 hover:border-red-500/30 transition-all">
                            <i class="ri-archive-line"></i>
                        </button>
                    </form>
                    <a href="{{ route('admin.skills.index') }}"
                        class="px-6 py-3.5 rounded-xl glass border border-dark-700 text-dark-300 font-medium hover:bg-dark-700/50 transition-all">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
