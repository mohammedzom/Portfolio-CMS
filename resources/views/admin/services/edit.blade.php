@extends('layouts.admin')

@section('title', isset($service) ? 'Edit Service' : 'Create Service')
@section('page-title', isset($service) ? 'Edit Service' : 'New Service')
@section('page-subtitle', isset($service) ? 'Update ' . $service->title : 'Define a new service for your clients')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-card padding="p-8">
            <form action="{{ isset($service) ? route('admin.services.update', $service) : route('admin.services.store') }}" method="POST" class="space-y-8">
                @csrf
                @if(isset($service))
                    @method('PUT')
                @endif

                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                        <i class="ri-information-line"></i> Service Details
                    </h3>
                    
                    <x-input label="Service Title" name="title" value="{{ $service->title ?? '' }}" placeholder="Web Development, Cloud Solutions..." required="true" :error="$errors->first('title')" />
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Description <span class="text-neon-500">*</span></label>
                        <textarea name="description" rows="4" required class="w-full bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none {{ $errors->has('description') ? 'border-red-500/50' : '' }}" placeholder="Describe what you offer in this service...">{{ old('description', $service->description ?? '') }}</textarea>
                        @error('description') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Icon Class (RemixIcon)" name="icon" value="{{ $service->icon ?? 'ri-stack-line' }}" placeholder="ri-stack-line" :error="$errors->first('icon')" />
                        
                        <x-input label="Sort Order" name="sort_order" type="number" value="{{ $service->sort_order ?? 0 }}" required="true" :error="$errors->first('sort_order')" />
                    </div>

                    <x-input label="Tags (comma separated)" name="tags" value="{{ isset($service) && is_array($service->tags) ? implode(', ', $service->tags) : ($service->tags ?? '') }}" placeholder="Laravel, React, AWS..." :error="$errors->first('tags')" />
                </div>

                {{-- Icon Preview --}}
                <div class="bg-dark-950/50 border border-dark-800 rounded-2xl p-6 flex flex-col items-center gap-4">
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Icon Preview</p>
                    <div class="w-20 h-20 rounded-2xl bg-dark-800 border border-dark-700 flex items-center justify-center text-neon-400 text-4xl shadow-neon-sm">
                        <i id="icon-preview" class="{{ $service->icon ?? 'ri-stack-line' }}"></i>
                    </div>
                    <p class="text-xs text-dark-600">Enter a <a href="https://remixicon.com" target="_blank" class="text-neon-500 hover:underline">RemixIcon</a> class above to change</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-dark-800">
                    <x-button variant="secondary" size="lg" href="{{ route('admin.services.index') }}">
                        Cancel
                    </x-button>
                    <x-button variant="neon" size="lg" type="submit">
                        <i class="ri-save-line"></i> {{ isset($service) ? 'Update Service' : 'Create Service' }}
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
