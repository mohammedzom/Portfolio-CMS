@extends('layouts.admin')

@section('title', 'Create Skill')
@section('page-title', 'New Skill')
@section('page-subtitle', 'Add a new technical or creative skill')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-card padding="p-8">
            <form action="{{ route('admin.skills.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-500 border-b border-dark-600/50 pb-3 flex items-center gap-2">
                        <i class="ri-information-line"></i> Skill Details
                    </h3>
                    
                    <x-input label="Skill Name" name="name" value="{{ old('name') }}" placeholder="Laravel, React, UI/UX..." required="true" :error="$errors->first('name')" />
                    
                    <x-input label="Category" name="category" value="{{ old('category') }}" placeholder="Backend, Frontend, Tools..." required="true" :error="$errors->first('category')" />
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-1">
                            <label class="block text-xs font-bold text-dark-400 uppercase tracking-widest">Proficiency Level <span class="text-neon-500">*</span></label>
                            <span id="level-display" class="text-xs font-black text-neon-500 bg-neon-500/10 px-2 py-0.5 rounded-lg">{{ old('level', 80) }}%</span>
                        </div>
                        <input type="range" name="level" min="0" max="100" value="{{ old('level', 80) }}" step="1" required class="w-full h-2 bg-dark-900 border border-dark-600 rounded-lg appearance-none cursor-pointer accent-neon-500 focus:outline-none focus:ring-1 focus:ring-neon-500/20 transition-all">
                        @error('level') <p class="text-xs text-red-400 px-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Icon Class (RemixIcon)" name="icon" value="{{ old('icon', 'ri-code-line') }}" placeholder="ri-code-line" :error="$errors->first('icon')" />
                        
                        <x-input label="Sort Order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" required="true" :error="$errors->first('sort_order')" />
                    </div>
                </div>

                {{-- Icon Preview --}}
                <div class="bg-dark-950/50 border border-dark-600/30 rounded-2xl p-6 flex flex-col items-center gap-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Icon Preview</p>
                    <div class="w-20 h-20 rounded-2xl bg-dark-800 border border-dark-600 flex items-center justify-center text-neon-500 text-4xl shadow-[0_0_20px_oklch(0.66_0.17_195_/_0.2)]">
                        <i id="icon-preview" class="{{ old('icon', 'ri-code-line') }}"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-600">Enter a <a href="https://remixicon.com" target="_blank" class="text-neon-500 hover:underline">RemixIcon</a> class above</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-dark-600/50">
                    <x-button variant="secondary" size="lg" href="{{ route('admin.skills.index') }}">
                        Cancel
                    </x-button>
                    <x-button variant="neon" size="lg" type="submit">
                        <i class="ri-save-line"></i> Create Skill
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        const iconInput = document.querySelector('input[name="icon"]');
        const iconPreview = document.getElementById('icon-preview');
        const levelInput = document.querySelector('input[name="level"]');
        const levelDisplay = document.getElementById('level-display');
        
        iconInput?.addEventListener('input', (e) => {
            iconPreview.className = e.target.value || 'ri-code-line';
        });

        levelInput?.addEventListener('input', (e) => {
            levelDisplay.textContent = `${e.target.value}%`;
        });
    </script>
    @endpush
@endsection
