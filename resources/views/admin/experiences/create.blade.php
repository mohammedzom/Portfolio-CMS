@extends('layouts.admin')

@section('title', 'Create Experience')
@section('page-title', 'New Experience')
@section('page-subtitle', 'Add a new chapter to your career journey')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-card padding="p-8">
            <form action="{{ route('admin.experience.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-500 border-b border-dark-600/50 pb-3 flex items-center gap-2">
                        <i class="ri-information-line"></i> Experience Details
                    </h3>
                    
                    <x-input label="Position Title" name="title" value="{{ old('title') }}" placeholder="Senior Full-Stack Developer, Lead UI Designer..." required="true" :error="$errors->first('title')" />
                    
                    <x-input label="Company Name" name="company" value="{{ old('company') }}" placeholder="Tech Solutions Inc., Freelance..." required="true" :error="$errors->first('company')" />
                    
                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Start Date" name="start_date" type="text" value="{{ old('start_date') }}" placeholder="Jan 2022, 2020..." required="true" :error="$errors->first('start_date')" />
                        
                        <x-input label="End Date" name="end_date" type="text" value="{{ old('end_date') }}" placeholder="Present, Dec 2023..." required="true" :error="$errors->first('end_date')" />
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-dark-400 uppercase tracking-widest px-1">Description <span class="text-neon-500">*</span></label>
                        <textarea name="description" rows="4" required class="w-full bg-dark-900 border-dark-600 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none {{ $errors->has('description') ? 'border-red-500/50' : '' }}" placeholder="Summarize your key achievements and responsibilities...">{{ old('description') }}</textarea>
                        @error('description') <p class="text-xs text-red-400 px-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <x-input label="Sort Order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" required="true" :error="$errors->first('sort_order')" />
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-dark-600/50">
                    <x-button variant="secondary" size="lg" href="{{ route('admin.experience.index') }}">
                        Cancel
                    </x-button>
                    <x-button variant="neon" size="lg" type="submit">
                        <i class="ri-save-line"></i> Create Experience
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
