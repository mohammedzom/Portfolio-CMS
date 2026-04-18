@extends('layouts.admin')

@section('title', isset($project) ? 'Edit Project' : 'Create Project')
@section('page-title', isset($project) ? 'Edit Project' : 'New Project')
@section('page-subtitle', isset($project) ? 'Update ' . $project->title : 'Add a new piece to your portfolio')

@section('content')
    <div class="max-w-4xl mx-auto">
        <x-card padding="p-8">
            <form action="{{ isset($project) ? route('admin.projects.update', $project) : route('admin.projects.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @if(isset($project))
                    @method('PUT')
                @endif

                <div class="grid md:grid-cols-2 gap-8">
                    {{-- Basic Info --}}
                    <div class="space-y-6">
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                            <i class="ri-information-line"></i> Basic Information
                        </h3>
                        
                        <x-input label="Project Title" name="title" value="{{ $project->title ?? '' }}" placeholder="My Awesome Project" required="true" :error="$errors->first('title')" />
                        
                        <x-input label="Project Slug" name="slug" value="{{ $project->slug ?? '' }}" placeholder="my-awesome-project" :error="$errors->first('slug')" />
                        
                        <x-input label="Category" name="category" value="{{ $project->category ?? '' }}" placeholder="Web Development, UI/UX Design..." required="true" :error="$errors->first('category')" />
                        
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Description <span class="text-neon-500">*</span></label>
                            <textarea name="description" rows="4" required class="w-full bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none {{ $errors->has('description') ? 'border-red-500/50' : '' }}" placeholder="Tell the story of this project...">{{ old('description', $project->description ?? '') }}</textarea>
                            @error('description') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Links & Settings --}}
                    <div class="space-y-6">
                        <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                            <i class="ri-link"></i> Links & Meta
                        </h3>

                        <x-input label="Live URL" name="live_url" type="url" value="{{ $project->live_url ?? '' }}" placeholder="https://demo.example.com" :error="$errors->first('live_url')" />
                        
                        <x-input label="Repository URL" name="repo_url" type="url" value="{{ $project->repo_url ?? '' }}" placeholder="https://github.com/user/repo" :error="$errors->first('repo_url')" />
                        
                        <x-input label="Tech Stack (comma separated)" name="tech_stack" value="{{ isset($project) && is_array($project->tech_stack) ? implode(', ', $project->tech_stack) : ($project->tech_stack ?? '') }}" placeholder="Laravel, Tailwind, Vue.js..." :error="$errors->first('tech_stack')" />

                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="Sort Order" name="sort_order" type="number" value="{{ $project->sort_order ?? ($projectsCount + 1) }}" required="true" :error="$errors->first('sort_order')" />
                            
                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Is Featured?</label>
                                <div class="flex items-center gap-3 bg-dark-900 border border-dark-800 rounded-xl px-4 py-2.5">
                                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $project->is_featured ?? false) ? 'checked' : '' }} class="w-5 h-5 rounded bg-dark-800 border-dark-700 text-neon-500 focus:ring-neon-500/20 focus:ring-offset-dark-950">
                                    <span class="text-sm text-dark-300">Feature this project</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Media --}}
                <div class="space-y-6 pt-4">
                    <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                        <i class="ri-image-line"></i> Project Media
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-8 items-start">
                        <div class="space-y-4">
                            <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Upload Project Images</label>
                            <div class="relative group">
                                <input type="file" name="images[]" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="border-2 border-dashed border-dark-800 group-hover:border-neon-500/40 group-hover:bg-neon-500/5 rounded-2xl p-12 flex flex-col items-center justify-center gap-4 transition-all duration-300">
                                    <div class="w-16 h-16 rounded-full bg-dark-900 flex items-center justify-center text-dark-600 group-hover:text-neon-400 group-hover:scale-110 transition-all duration-500">
                                        <i class="ri-upload-cloud-2-line text-3xl"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-bold text-dark-300">Click or drag to upload</p>
                                        <p class="text-xs text-dark-600 mt-1">PNG, JPG, WEBP up to 2MB each</p>
                                    </div>
                                </div>
                            </div>
                            @error('images') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                        </div>

                        @if(isset($project) && !empty($project->images))
                            <div class="space-y-4">
                                <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Current Images</label>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach($project->images as $image)
                                        <div class="relative aspect-video rounded-xl overflow-hidden border border-dark-800 group">
                                            <img src="{{ Storage::url($image) }}" alt="" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                            <div class="absolute inset-0 bg-dark-950/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <button type="button" class="w-8 h-8 rounded-lg bg-red-500/20 text-red-400 border border-red-500/30 hover:bg-red-500 hover:text-white transition-all">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-4 pt-8 border-t border-dark-800">
                    <x-button variant="secondary" size="lg" href="{{ route('admin.projects.index') }}">
                        Cancel
                    </x-button>
                    <x-button variant="neon" size="lg" type="submit">
                        <i class="ri-save-line"></i> {{ isset($project) ? 'Update Project' : 'Create Project' }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
@endsection
