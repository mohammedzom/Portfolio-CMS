@extends('layouts.admin')

@section('title', isset($skill) ? 'Edit Skill' : 'Create Skill')
@section('page-title', isset($skill) ? 'Edit Skill' : 'New Skill')
@section('page-subtitle', isset($skill) ? 'Update ' . $skill->name : 'Add a new skill to your technical arsenal')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-card padding="p-8">
            <form action="{{ isset($skill) ? route('admin.skills.update', $skill) : route('admin.skills.store') }}" method="POST" class="space-y-8">
                @csrf
                @if(isset($skill))
                    @method('PUT')
                @endif

                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                        <i class="ri-information-line"></i> Skill Details
                    </h3>
                    
                    <x-input label="Skill Name" name="name" value="{{ $skill->name ?? '' }}" placeholder="Laravel, React, Docker..." required="true" :error="$errors->first('name')" />
                    
                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Skill Category" name="type" value="{{ $skill->type ?? '' }}" placeholder="Backend, Frontend, DevOps..." :error="$errors->first('type')" />
                        
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Proficiency Level (%) <span class="text-neon-500">*</span></label>
                            <div class="flex items-center gap-4">
                                <input type="range" name="proficiency" min="0" max="100" step="1" value="{{ old('proficiency', $skill->proficiency ?? 80) }}" class="w-full h-2 bg-dark-800 rounded-lg appearance-none cursor-pointer accent-neon-500 focus:outline-none" oninput="this.nextElementSibling.value = this.value + '%'">
                                <output class="text-sm font-bold text-neon-400 min-w-[3rem] text-right">{{ old('proficiency', $skill->proficiency ?? 80) }}%</output>
                            </div>
                            @error('proficiency') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Icon Class (RemixIcon)" name="icon" value="{{ $skill->icon ?? 'ri-terminal-box-line' }}" placeholder="ri-terminal-box-line" :error="$errors->first('icon')" />
                        
                        <x-input label="Sort Order" name="sort_order" type="number" value="{{ $skill->sort_order ?? 0 }}" required="true" :error="$errors->first('sort_order')" />
                    </div>
                </div>

                {{-- Icon Preview --}}
                <div class="bg-dark-950/50 border border-dark-800 rounded-2xl p-6 flex flex-col items-center gap-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Icon Preview</p>
                    <div class="w-20 h-20 rounded-2xl bg-dark-800 border border-dark-700 flex items-center justify-center text-neon-400 text-4xl shadow-neon-sm">
                        <i id="icon-preview" class="{{ $skill->icon ?? 'ri-terminal-box-line' }}"></i>
                    </div>
                    <p class="text-xs text-dark-600">Enter a <a href="https://remixicon.com" target="_blank" class="text-neon-500 hover:underline">RemixIcon</a> class above to change</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-dark-800">
                    <x-button variant="secondary" size="lg" href="{{ route('admin.skills.index') }}">
                        Cancel
                    </x-button>
                    <x-button variant="neon" size="lg" type="submit">
                        <i class="ri-save-line"></i> {{ isset($skill) ? 'Update Skill' : 'Create Skill' }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        const iconInput = document.querySelector('input[name="icon"]');
        const iconPreview = document.getElementById('icon-preview');
        
        iconInput?.addEventListener('input', (e) => {
            iconPreview.className = e.target.value || 'ri-terminal-box-line';
        });
    </script>
    @endpush
@endsection
