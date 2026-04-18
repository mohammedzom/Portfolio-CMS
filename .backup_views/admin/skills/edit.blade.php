@extends('admin.layout')
@section('title', 'Edit Skill')
@section('page-title', 'Edit Skill')
@section('page-subtitle', 'Update skill information')

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('admin.skills.index') }}"
            class="inline-flex items-center gap-2 text-dark-400 hover:text-neon-500 transition-colors mb-6">
            <i class="ri-arrow-left-line"></i> Back to Skills
        </a>

        {{-- Form Card --}}
        <div class="glass rounded-2xl border border-dark-700 p-6">
            <form action="{{ route('admin.skills.update', $skill) }}" method="POST" x-data="{ 
                proficiency: {{ $skill->proficiency }},
                type: '{{ $skill->type }}',
                color: '{{ $skill->color ?? '#06b6d4' }}'
            }">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Basic Info Section --}}
                    <div>
                        <h3 class="font-display font-semibold text-dark-100 mb-4 flex items-center gap-2">
                            <i class="ri-information-line text-neon-500"></i> Basic Information
                        </h3>
                        
                        <div class="grid sm:grid-cols-2 gap-5">
                            {{-- Name --}}
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-medium text-dark-300 mb-2">
                                    Skill Name <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $skill->name) }}" required
                                    class="w-full glass rounded-xl border border-dark-700 px-4 py-3 focus:border-neon-500/40 focus:ring-1 focus:ring-neon-500/20 transition-all @error('name') border-red-500/50 @enderror"
                                    placeholder="e.g., Laravel, React, TypeScript...">
                                @error('name')
                                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                        <i class="ri-error-warning-line"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Type --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-dark-300 mb-2">
                                    Category <span class="text-red-400">*</span>
                                </label>
                                <select name="type" id="type" x-model="type" required
                                    class="w-full glass rounded-xl border border-dark-700 px-4 py-3 focus:border-neon-500/40 focus:ring-1 focus:ring-neon-500/20 transition-all @error('type') border-red-500/50 @enderror">
                                    <option value="Frontend" {{ old('type', $skill->type) == 'Frontend' ? 'selected' : '' }}>Frontend</option>
                                    <option value="Backend" {{ old('type', $skill->type) == 'Backend' ? 'selected' : '' }}>Backend</option>
                                    <option value="Full Stack" {{ old('type', $skill->type) == 'Full Stack' ? 'selected' : '' }}>Full Stack</option>
                                    <option value="Design" {{ old('type', $skill->type) == 'Design' ? 'selected' : '' }}>Design</option>
                                    <option value="DevOps" {{ old('type', $skill->type) == 'DevOps' ? 'selected' : '' }}>DevOps</option>
                                    <option value="Database" {{ old('type', $skill->type) == 'Database' ? 'selected' : '' }}>Database</option>
                                    <option value="Other" {{ old('type', $skill->type) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('type')
                                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                        <i class="ri-error-warning-line"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Color Picker --}}
                            <div>
                                <label for="color" class="block text-sm font-medium text-dark-300 mb-2">
                                    Accent Color
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="color" id="color" x-model="color"
                                        value="{{ old('color', $skill->color ?? '#06b6d4') }}"
                                        class="w-12 h-12 rounded-xl border border-dark-700 cursor-pointer">
                                    <input type="text" name="color_text" x-model="color"
                                        class="flex-1 glass rounded-xl border border-dark-700 px-4 py-3 font-mono text-sm focus:border-neon-500/40 transition-all"
                                        placeholder="#06b6d4">
                                </div>
                                @error('color')
                                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                        <i class="ri-error-warning-line"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Icon URL --}}
                            <div class="sm:col-span-2">
                                <label for="icon" class="block text-sm font-medium text-dark-300 mb-2">
                                    Icon URL <span class="text-dark-500 font-normal">(Optional)</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="url" name="icon" id="icon" value="{{ old('icon', $skill->icon) }}"
                                        class="flex-1 glass rounded-xl border border-dark-700 px-4 py-3 focus:border-neon-500/40 focus:ring-1 focus:ring-neon-500/20 transition-all @error('icon') border-red-500/50 @enderror"
                                        placeholder="https://example.com/icon.svg">
                                    <button type="button" 
                                        class="px-4 py-3 rounded-xl glass border border-dark-700 text-dark-400 hover:text-neon-500 transition-all"
                                        onclick="document.getElementById('icon').value = ''">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                                @error('icon')
                                    <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                        <i class="ri-error-warning-line"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Proficiency Section --}}
                    <div>
                        <h3 class="font-display font-semibold text-dark-100 mb-4 flex items-center gap-2">
                            <i class="ri-bar-chart-line text-neon-500"></i> Proficiency Level
                        </h3>
                        
                        <div class="glass rounded-xl border border-dark-700 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-medium text-dark-300">Skill Level</span>
                                <span class="text-2xl font-bold" 
                                    x-text="proficiency + '%'"
                                    :style="proficiency >= 80 ? 'color: oklch(0.72 0.18 160)' : (proficiency >= 60 ? 'color: oklch(0.66 0.17 195)' : 'color: oklch(0.58 0.22 290)')">
                                </span>
                            </div>
                            
                            <input type="range" name="proficiency" min="0" max="100" x-model="proficiency"
                                class="w-full h-2 bg-dark-700 rounded-lg appearance-none cursor-pointer accent-neon-500"
                                value="{{ $skill->proficiency }}">
                            
                            <div class="flex items-center justify-between mt-3 text-xs text-dark-500">
                                <span>Beginner</span>
                                <span>Expert</span>
                            </div>

                            {{-- Level Indicator --}}
                            <div class="mt-4 pt-4 border-t border-dark-700">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-dark-400">Level:</span>
                                    <span class="text-sm font-semibold px-3 py-1 rounded-full"
                                        x-text="proficiency >= 95 ? 'Expert' : (proficiency >= 85 ? 'Advanced' : (proficiency >= 75 ? 'Upper Intermediate' : (proficiency >= 60 ? 'Intermediate' : (proficiency >= 40 ? 'Beginner' : 'Novice'))))"
                                        :style="proficiency >= 80 ? 'background: oklch(0.72 0.18 160 / 0.1); color: oklch(0.72 0.18 160)' : (proficiency >= 60 ? 'background: oklch(0.66 0.17 195 / 0.1); color: oklch(0.66 0.17 195)' : 'background: oklch(0.58 0.22 290 / 0.1); color: oklch(0.58 0.22 290)')">
                                    </span>
                                </div>
                            </div>
                        </div>
                        @error('proficiency')
                            <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                                <i class="ri-error-warning-line"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Preview Section --}}
                    <div>
                        <h3 class="font-display font-semibold text-dark-100 mb-4 flex items-center gap-2">
                            <i class="ri-eye-line text-neon-500"></i> Live Preview
                        </h3>
                        
                        <div class="glass rounded-xl border border-dark-700 p-5">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center"
                                    :style="'background: ' + color + '20;'">
                                    <template x-if="document.getElementById('icon').value">
                                        <img :src="document.getElementById('icon').value" alt="Icon" class="w-7 h-7 object-contain">
                                    </template>
                                    <template x-if="!document.getElementById('icon').value">
                                        <i class="ri-code-s-slash-line text-xl" :style="'color: ' + color;"></i>
                                    </template>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-dark-100" x-text="document.getElementById('name').value || 'Skill Name'"></h4>
                                    <span class="text-xs px-2 py-0.5 rounded-full bg-dark-700 text-dark-400" x-text="type"></span>
                                </div>
                                <div class="text-right">
                                    <div class="text-lg font-bold" x-text="proficiency + '%'""
                                        :style="proficiency >= 80 ? 'color: oklch(0.72 0.18 160)' : (proficiency >= 60 ? 'color: oklch(0.66 0.17 195)' : 'color: oklch(0.58 0.22 290)')">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="h-2 rounded-full bg-dark-700 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300"
                                        :style="'width: ' + proficiency + '%; background: linear-gradient(90deg, ' + color + ', ' + color + '80);'">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex items-center gap-4 pt-6 border-t border-dark-700">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl gradient-neon text-dark-950 font-bold hover:neon-glow transition-all">
                            <i class="ri-save-3-line"></i> Update Skill
                        </button>
                        <a href="{{ route('admin.skills.index') }}"
                            class="px-6 py-3.5 rounded-xl glass border border-dark-700 text-dark-300 font-medium hover:bg-dark-700/50 transition-all">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
