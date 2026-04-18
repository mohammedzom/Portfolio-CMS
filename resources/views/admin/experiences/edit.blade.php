@extends('layouts.admin')

@section('title', isset($experience) ? 'Edit Experience' : 'Create Experience')
@section('page-title', isset($experience) ? 'Edit Experience' : 'New Experience')
@section('page-subtitle', isset($experience) ? 'Update ' . $experience->job_title : 'Add a new milestone to your career timeline')

@section('content')
    <div class="max-w-3xl mx-auto">
        <x-card padding="p-8">
            <form action="{{ isset($experience) ? route('admin.experience.update', $experience) : route('admin.experience.store') }}" method="POST" class="space-y-8">
                @csrf
                @if(isset($experience))
                    @method('PUT')
                @endif

                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                        <i class="ri-information-line"></i> Experience Details
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-6">
                        <x-input label="Job Title" name="job_title" value="{{ $experience->job_title ?? '' }}" placeholder="Senior Full-Stack Engineer" required="true" :error="$errors->first('job_title')" />
                        
                        <x-input label="Company Name" name="company" value="{{ $experience->company ?? '' }}" placeholder="Tech Solutions Inc." required="true" :error="$errors->first('company')" />
                    </div>

                    <div class="grid md:grid-cols-2 gap-6">
                        <x-input label="Start Date" name="start_date" value="{{ $experience->start_date ?? '' }}" placeholder="Jan 2020" required="true" :error="$errors->first('start_date')" />
                        
                        <div class="space-y-2">
                            <x-input label="End Date" name="end_date" value="{{ $experience->end_date ?? '' }}" placeholder="Dec 2023" :error="$errors->first('end_date')" />
                            <div class="flex items-center gap-3 px-1">
                                <input type="checkbox" name="is_current" id="is_current" value="1" {{ old('is_current', $experience->is_current ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded bg-dark-800 border-dark-700 text-neon-500 focus:ring-neon-500/20 focus:ring-offset-dark-950">
                                <label for="is_current" class="text-xs text-dark-500 font-medium">I am currently working here</label>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Description <span class="text-neon-500">*</span></label>
                        <textarea name="description" rows="6" required class="w-full bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none {{ $errors->has('description') ? 'border-red-500/50' : '' }}" placeholder="Detail your responsibilities and key achievements...">{{ old('description', $experience->description ?? '') }}</textarea>
                        @error('description') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-dark-800">
                    <x-button variant="secondary" size="lg" href="{{ route('admin.experience.index') }}">
                        Cancel
                    </x-button>
                    <x-button variant="neon" size="lg" type="submit">
                        <i class="ri-save-line"></i> {{ isset($experience) ? 'Update Experience' : 'Create Experience' }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        const isCurrentCheckbox = document.getElementById('is_current');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        isCurrentCheckbox?.addEventListener('change', (e) => {
            if (e.target.checked) {
                endDateInput.value = '';
                endDateInput.disabled = true;
                endDateInput.classList.add('opacity-50');
            } else {
                endDateInput.disabled = false;
                endDateInput.classList.remove('opacity-50');
            }
        });

        // Initialize state
        if (isCurrentCheckbox?.checked) {
            endDateInput.disabled = true;
            endDateInput.classList.add('opacity-50');
        }
    </script>
    @endpush
@endsection
