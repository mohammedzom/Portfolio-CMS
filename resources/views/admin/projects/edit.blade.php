@extends('layouts.admin')

@section('page-title', 'Edit Project')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600 border-b border-slate-100 pb-4">Basic Information</h3>
                    
                    <x-admin.input label="Project Title" name="title" :value="$project->title" placeholder="E-commerce Platform..." required="true" :error="$errors->first('title')" />
                    
                    <x-admin.input label="Custom Slug" name="slug" :value="$project->slug" placeholder="my-awesome-project" :error="$errors->first('slug')" />

                    <div class="space-y-1.5">
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" required class="block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20 placeholder:text-slate-400 resize-none {{ $errors->has('description') ? 'border-red-300' : '' }}">{{ old('description', $project->description) }}</textarea>
                        @error('description') <p class="mt-1 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Technical Details --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600 border-b border-slate-100 pb-4">Technical Details</h3>
                    
                    <div class="space-y-1.5" x-data="{ tags: {{ json_encode(old('tech_stack', $project->tech_stack ?? [])) }}, newTag: '' }">
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Tech Stack</label>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <template x-for="(tag, index) in tags" :key="index">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-2 py-1 text-xs font-bold text-indigo-600 border border-indigo-100">
                                    <span x-text="tag"></span>
                                    <button type="button" @click="tags.splice(index, 1)" class="hover:text-indigo-800">
                                        <i class="ri-close-line"></i>
                                    </button>
                                    <input type="hidden" name="tech_stack[]" :value="tag">
                                </span>
                            </template>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" x-model="newTag" @keydown.enter.prevent="if(newTag.trim()) { tags.push(newTag.trim()); newTag = ''; }" class="block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20 placeholder:text-slate-400" placeholder="Add a technology...">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input label="Live URL" name="live_url" type="url" :value="$project->live_url" placeholder="https://..." :error="$errors->first('live_url')" />
                        <x-admin.input label="Repository URL" name="repo_url" type="url" :value="$project->repo_url" placeholder="https://github.com/..." :error="$errors->first('repo_url')" />
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Configuration --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600 border-b border-slate-100 pb-4">Settings</h3>
                    
                    <div class="space-y-1.5">
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Category <span class="text-red-500">*</span></label>
                        <select name="category" required class="block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20">
                            @foreach(['Web', 'App', 'Mobile', 'Script', 'Other'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $project->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <x-admin.input label="Sort Order" name="sort_order" type="number" :value="$project->sort_order" :error="$errors->first('sort_order')" />

                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-100 bg-slate-50 cursor-pointer hover:bg-indigo-50 transition-colors">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $project->is_featured) ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-bold text-slate-700">Feature this project</span>
                    </label>
                </div>

                {{-- Images --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-200 space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600 border-b border-slate-100 pb-4">Project Media</h3>
                    
                    @if(!empty($project->images))
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            @foreach($project->images as $image)
                                <div class="relative group aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50" x-data="{ deleted: false }">
                                    <img src="{{ Storage::url($image) }}" class="h-full w-full object-cover transition-all" :class="deleted ? 'opacity-20 grayscale' : ''">
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" @click="deleted = !deleted" class="p-2 rounded-lg bg-white text-red-600 shadow-lg hover:scale-110 transition-transform">
                                            <i :class="deleted ? 'ri-refresh-line' : 'ri-delete-bin-line'"></i>
                                        </button>
                                    </div>
                                    <input type="checkbox" name="delete_images[]" :value="'{{ $image }}'" :checked="deleted" class="hidden">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-4" x-data="{ files: [] }">
                        <div class="relative group cursor-pointer">
                            <input type="file" name="images[]" multiple @change="files = Array.from($event.target.files)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="p-6 rounded-2xl border-2 border-dashed border-slate-200 group-hover:border-indigo-400 transition-all flex flex-col items-center gap-2">
                                <i class="ri-upload-cloud-2-line text-3xl text-slate-400 group-hover:text-indigo-500"></i>
                                <span class="text-xs font-bold text-slate-500 group-hover:text-indigo-600">Upload new images</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-200">
            <a href="{{ route('admin.projects.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 transition-all">
                <i class="ri-save-line"></i>
                Update Project
            </button>
        </div>
    </form>
</div>
@endsection
