@extends('layouts.admin')

@section('title', 'Create Service')
@section('page-title', 'New Service')
@section('page-subtitle', 'Define a new service for your clients')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-card padding="p-8">
            <form action="{{ route('admin.services.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-500 border-b border-dark-600/50 pb-3 flex items-center gap-2">
                        <i class="ri-information-line"></i> Service Details
                    </h3>
                    
                    <x-input label="Service Title" name="title" value="{{ old('title') }}" placeholder="Web Development, Cloud Solutions..." required="true" :error="$errors->first('title')" />
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-dark-400 uppercase tracking-widest px-1">Description <span class="text-neon-500">*</span></label>
                        <textarea name="description" rows="4" required class="w-full bg-dark-900 border-dark-600 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none {{ $errors->has('description') ? 'border-red-500/50' : '' }}" placeholder="Describe what you offer in this service...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-xs text-red-400 px-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Icon Class (RemixIcon)" name="icon" value="{{ old('icon', 'ri-stack-line') }}" placeholder="ri-stack-line" :error="$errors->first('icon')" />
                        
                        <x-input label="Sort Order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" required="true" :error="$errors->first('sort_order')" />
                    </div>

                    <x-input label="Tags (comma separated)" name="tags" value="{{ old('tags') }}" placeholder="Laravel, React, AWS..." :error="$errors->first('tags')" />
                </div>

                {{-- Icon Preview --}}
                <div class="bg-dark-950/50 border border-dark-600/30 rounded-2xl p-6 flex flex-col items-center gap-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Icon Preview</p>
                    <div class="w-20 h-20 rounded-2xl bg-dark-800 border border-dark-600 flex items-center justify-center text-neon-500 text-4xl shadow-[0_0_20px_oklch(0.66_0.17_195_/_0.2)]">
                        <i id="icon-preview" class="{{ old('icon', 'ri-stack-line') }}"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-600">Enter a <a href="https://remixicon.com" target="_blank" class="text-neon-500 hover:underline">RemixIcon</a> class above</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-dark-600/50">
                    <x-button variant="secondary" size="lg" href="{{ route('admin.services.index') }}">
                        Cancel
                    </x-button>
                    <x-button variant="neon" size="lg" type="submit">
                        <i class="ri-save-line"></i> Create Service
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
            iconPreview.className = e.target.value || 'ri-stack-line';
        });
    </script>
    @endpush
@endsection
