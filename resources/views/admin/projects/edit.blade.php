@extends('admin.layout')
@section('title', 'Edit ' . $project->title)
@section('page-title', 'Edit ' . $project->title)
@section('page-subtitle', 'Edit Project')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.projects.index') }}"
            class="text-dark-400 hover:text-neon-500 transition-colors text-sm flex items-center gap-2 w-fit font-medium">
            <i class="ri-arrow-left-line"></i> Back to Projects
        </a>
    </div>

    <div class="glass rounded-2xl border border-dark-700 overflow-hidden shadow-xl">
        <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- ── SECTION 1: Identity ── --}}
            <div class="p-6 md:p-8 border-b border-dark-700">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 rounded-xl bg-neon-500/10 border border-neon-500/20 flex items-center justify-center shrink-0">
                        <i class="ri-file-text-line text-neon-500 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-dark-100 text-lg tracking-wide">Identity</h2>
                        <p class="text-dark-500 text-sm">Title, slug and category</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Title --}}
                    <div class="space-y-2">
                        <label for="title" class="text-sm font-medium text-dark-300">Project Title <span
                                class="text-neon-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}"
                            class="w-full bg-dark-900/50 rounded-xl border border-dark-700 px-4 py-3 text-dark-100 focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/50 outline-none transition-all placeholder:text-dark-600"
                            placeholder="e.g. E-Commerce Platform" required>
                        @error('title')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Slug --}}
                    <div class="space-y-2">
                        <label for="slug" class="text-sm font-medium text-dark-300">Slug</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug', $project->slug) }}"
                            class="w-full bg-dark-900/50 rounded-xl border border-dark-700 px-4 py-3 text-dark-100 focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/50 outline-none transition-all placeholder:text-dark-600"
                            placeholder="e-commerce-platform">
                        <p class="text-dark-500 text-xs mt-1">Leave empty to auto-generate from title</p>
                        @error('slug')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div class="space-y-3 lg:col-span-2 mt-2">
                        <label class="text-sm font-medium text-dark-300">Category <span
                                class="text-neon-500">*</span></label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                            @foreach (['Web' => ['ri-global-line', '🌐'], 'App' => ['ri-window-line', '🖥️'], 'Mobile' => ['ri-smartphone-line', '📱'], 'Script' => ['ri-terminal-line', '⚡'], 'Other' => ['ri-box-3-line', '📦']] as $cat => [$icon, $emoji])
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="category" value="{{ $cat }}" class="peer sr-only"
                                        {{ old('category', $project->category) == $cat ? 'checked' : '' }} required>
                                    <div
                                        class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl bg-dark-900/50 border border-dark-700 transition-all peer-checked:border-neon-500 peer-checked:bg-neon-500/10 hover:border-dark-500 select-none">
                                        <span class="text-2xl">{{ $emoji }}</span>
                                        <span
                                            class="text-xs font-display font-bold text-dark-400 peer-checked:text-neon-400 group-hover:text-dark-200 transition-colors uppercase tracking-wider">{{ $cat }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('category')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tech Stack (Alpine Tag Input) --}}
                    @php
                        $techStack = old('tech_stack', is_array($project->tech_stack) ? $project->tech_stack : (is_string($project->tech_stack) ? array_map('trim', explode(',', $project->tech_stack)) : []));
                    @endphp
                    <div class="space-y-2 lg:col-span-2 mt-2" x-data="{
                        tags: {{ json_encode($techStack) }},
                        newTag: '',
                        addTag() {
                            const val = this.newTag.trim().replace(/,$/, '');
                            if (val && !this.tags.includes(val)) {
                                this.tags.push(val);
                            }
                            this.newTag = '';
                        },
                        removeTag(index) {
                            this.tags.splice(index, 1);
                        }
                    }">
                        <label class="text-sm font-medium text-dark-300">Tech Stack</label>
                        <div class="min-h-[50px] w-full bg-dark-900/50 rounded-xl border border-dark-700 p-2.5 flex flex-wrap gap-2 focus-within:border-neon-500/50 focus-within:ring-1 focus-within:ring-neon-500/50 transition-all cursor-text"
                            @click="$refs.tagInput.focus()">
                            <template x-for="(tag, index) in tags" :key="index">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neon-500/10 border border-neon-500/20 text-neon-400 text-xs font-semibold font-display tracking-wide">
                                    <span x-text="tag"></span>
                                    <input type="hidden" name="tech_stack[]" :value="tag">
                                    <button type="button" @click.stop="removeTag(index)"
                                        class="hover:text-neon-300 transition-colors ml-1">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </span>
                            </template>
                            <input x-ref="tagInput" type="text" x-model="newTag" @keydown.enter.prevent="addTag"
                                @keydown.comma.prevent="addTag"
                                @keydown.backspace="if(newTag === '' && tags.length > 0) removeTag(tags.length - 1)"
                                class="flex-1 bg-transparent border-none outline-none text-dark-100 text-sm min-w-[140px] p-1 placeholder:text-dark-600"
                                placeholder="Type a technology and press Enter...">
                        </div>
                        <p class="text-dark-500 text-xs mt-1">Press Enter or comma to add tags. Backspace to remove last.
                        </p>
                        @error('tech_stack')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ── SECTION 2: Links & Media ── --}}
            <div class="p-6 md:p-8 border-b border-dark-700 bg-dark-900/10">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 rounded-xl bg-neon-500/10 border border-neon-500/20 flex items-center justify-center shrink-0">
                        <i class="ri-links-line text-neon-500 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-dark-100 text-lg tracking-wide">Links & Media</h2>
                        <p class="text-dark-500 text-sm">Live URL, repository and project images</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Live URL --}}
                    <div class="space-y-2">
                        <label for="live_url" class="text-sm font-medium text-dark-300">Live URL</label>
                        <input type="url" name="live_url" id="live_url" value="{{ old('live_url', $project->live_url) }}"
                            class="w-full bg-dark-900/50 rounded-xl border border-dark-700 px-4 py-3 text-dark-100 focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/50 outline-none transition-all placeholder:text-dark-600"
                            placeholder="https://example.com">
                        @error('live_url')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Repo URL --}}
                    <div class="space-y-2">
                        <label for="repo_url" class="text-sm font-medium text-dark-300">Repository URL</label>
                        <input type="url" name="repo_url" id="repo_url" value="{{ old('repo_url', $project->repo_url) }}"
                            class="w-full bg-dark-900/50 rounded-xl border border-dark-700 px-4 py-3 text-dark-100 focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/50 outline-none transition-all placeholder:text-dark-600"
                            placeholder="https://github.com/username/repo">
                        @error('repo_url')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Images --}}
                    <div class="space-y-3 lg:col-span-2 mt-2">
                        <label for="images" class="text-sm font-medium text-dark-300">Project Images</label>
                        
                        @if ($project->images && is_array($project->images) && count($project->images) > 0)
                            <div x-data="{
                                deletedImages: [],
                                mainImage: '{{ $project->main_image ?? '' }}',
                                deleteImage(image) {
                                    if (!this.deletedImages.includes(image)) {
                                        this.deletedImages.push(image);
                                    }
                                    if (this.mainImage === image) {
                                        this.mainImage = '';
                                    }
                                },
                                setMainImage(image) {
                                    this.mainImage = image;
                                }
                            }" class="mb-4">
                                
                                {{-- Hidden inputs for deleted images --}}
                                <template x-for="image in deletedImages" :key="image">
                                    <input type="hidden" name="deleted_images[]" :value="image">
                                </template>
                                
                                {{-- Hidden input for main image --}}
                                <input type="hidden" name="main_image" :value="mainImage">

                                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 p-5 bg-dark-900/30 rounded-xl border border-dark-700">
                                    @foreach ($project->images as $index => $image)
                                        <div x-show="!deletedImages.includes('{{ $image }}')" x-transition
                                            class="relative group aspect-video rounded-xl overflow-hidden border border-dark-700 bg-dark-950">
                                            <img src="{{ Storage::url($image) }}" alt="Image {{ $index + 1 }}"
                                                class="w-full h-full object-cover transition-transform group-hover:scale-105 duration-500">
                                            
                                            {{-- Top Actions --}}
                                            <div class="absolute top-2 right-2 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                                {{-- Main Image Button --}}
                                                <button type="button" @click.stop="setMainImage('{{ $image }}')" title="Set as main image"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full backdrop-blur-md transition-all border"
                                                    :class="mainImage === '{{ $image }}' 
                                                        ? 'bg-neon-500/20 border-neon-500 text-neon-400 shadow-[0_0_10px_oklch(0.66_0.17_195/0.3)]' 
                                                        : 'bg-black/50 border-white/10 text-white/70 hover:bg-black/70 hover:text-white'">
                                                    <i class="text-sm transition-transform" :class="mainImage === '{{ $image }}' ? 'ri-star-fill scale-110' : 'ri-star-line'"></i>
                                                </button>
                                                
                                                {{-- Delete Button --}}
                                                <button type="button" @click.stop="deleteImage('{{ $image }}')" title="Delete image"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-black/50 border border-white/10 text-white/70 hover:bg-red-500/90 hover:text-white hover:border-red-500 backdrop-blur-md transition-all">
                                                    <i class="ri-delete-bin-line text-sm"></i>
                                                </button>
                                            </div>

                                            {{-- Bottom Label --}}
                                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 to-transparent p-3 translate-y-full group-hover:translate-y-0 transition-transform">
                                                <p class="text-white text-xs font-medium">Existing Image {{ $index + 1 }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div x-data="{
                            showPreview: false,
                            files: [],
                            handleFiles(e) {
                                // Add new files to the existing ones
                                const newFiles = Array.from(e.target.files);
                                if (!newFiles.length) return;
                        
                                this.files = [...this.files, ...newFiles];
                                this.updateInput();
                            },
                            removeFile(index) {
                                this.files.splice(index, 1);
                                this.updateInput();
                            },
                            updateInput() {
                                const dt = new DataTransfer();
                                this.files.forEach(f => dt.items.add(f));
                                this.$refs.imagesInput.files = dt.files;
                                this.showPreview = this.files.length > 0;
                            }
                        }">
                            {{-- Dropzone styling for input --}}
                            <div
                                class="relative border-2 border-dashed border-dark-700 hover:border-neon-500/50 bg-dark-900/30 hover:bg-neon-500/5 rounded-2xl p-8 text-center transition-all cursor-pointer group">
                                <input type="file" name="images[]" id="images" accept="image/*" multiple
                                    @change="handleFiles" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    x-ref="imagesInput">

                                <div class="w-14 h-14 rounded-full bg-dark-800 border border-dark-600 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="ri-upload-cloud-2-line text-2xl text-dark-400 group-hover:text-neon-500 transition-colors"></i>
                                </div>
                                <h3 class="text-dark-100 font-display font-bold mb-1">Upload additional images</h3>
                                <p class="text-dark-500 text-xs">Supports JPG, PNG, WebP (Max 15MB each). New images will be appended to existing ones.</p>
                            </div>

                            {{-- Previews Grid --}}
                            <div x-show="showPreview" x-transition
                                class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 p-5 bg-dark-900/30 rounded-xl border border-dark-700 mt-4"
                                style="display: none;">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="relative group aspect-video rounded-xl overflow-hidden border border-dark-700 bg-dark-950">
                                        <img :src="URL.createObjectURL(file)" :alt="'Preview ' + (index + 1)"
                                            class="w-full h-full object-cover">
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/90 to-transparent p-3 translate-y-full group-hover:translate-y-0 transition-transform">
                                            <p class="text-white text-xs truncate font-medium" x-text="file.name"></p>
                                            <p class="text-dark-400 text-[10px]" x-text="(file.size/1024/1024).toFixed(2) + ' MB'"></p>
                                        </div>
                                        <button type="button" @click.stop="removeFile(index)" title="Remove image"
                                            class="absolute top-2 right-2 bg-black/50 hover:bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm backdrop-blur-md transition-all opacity-0 group-hover:opacity-100 z-20">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('images.*')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ── SECTION 3: Details ── --}}
            <div class="p-6 md:p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 rounded-xl bg-neon-500/10 border border-neon-500/20 flex items-center justify-center shrink-0">
                        <i class="ri-align-left text-neon-500 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="font-display font-bold text-dark-100 text-lg tracking-wide">Details</h2>
                        <p class="text-dark-500 text-sm">Description, ordering and visibility</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Description --}}
                    <div class="space-y-2 lg:col-span-2" x-data="{ count: 0 }" x-init="count = $refs.desc.value.length">
                        <div class="flex items-center justify-between">
                            <label for="description" class="text-sm font-medium text-dark-300">Description <span
                                    class="text-neon-500">*</span></label>
                            <span class="text-xs text-dark-500 font-medium" x-text="count + ' / 800'"></span>
                        </div>
                        <textarea x-ref="desc" name="description" id="description" rows="5"
                            @input="count = $event.target.value.length"
                            class="w-full bg-dark-900/50 rounded-xl border border-dark-700 px-4 py-3 text-dark-100 focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/50 outline-none transition-all placeholder:text-dark-600 resize-y leading-relaxed"
                            placeholder="Describe the project — what it does, who it's for, and what makes it special…" required>{{ old('description', $project->description) }}</textarea>
                        @error('description')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Sort Order --}}
                    <div class="space-y-3 lg:col-span-2 mt-2">
                        <label class="text-sm font-medium text-dark-300">Sort Order</label>
                        <div class="flex flex-wrap gap-2">
                            @for ($i = 1; $i <= $projectsCount + 1; $i++)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="sort_order" value="{{ $i }}" class="peer sr-only"
                                        {{ old('sort_order', $project->sort_order) == $i ? 'checked' : '' }} required>
                                    <div
                                        class="w-11 h-11 rounded-xl bg-dark-900/50 border border-dark-700 flex items-center justify-center text-sm font-display font-bold text-dark-400 peer-checked:border-neon-500 peer-checked:bg-neon-500/10 peer-checked:text-neon-400 hover:border-dark-500 transition-all select-none">
                                        {{ $i == $projectsCount + 1 ? $i . '★' : $i }}
                                    </div>
                                </label>
                            @endfor
                        </div>
                        <p class="text-dark-500 text-xs mt-1">Lower numbers appear first. Default is last (newest).</p>
                        @error('sort_order')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Is Featured Checkbox --}}
                    <label
                        class="flex items-center gap-4 bg-dark-900/40 hover:bg-dark-900/60 p-5 rounded-xl border border-dark-700 hover:border-dark-600 transition-all cursor-pointer group lg:col-span-2 mt-2">
                        <div class="relative flex items-center shrink-0">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1"
                                {{ old('is_featured', $project->is_featured) ? 'checked' : '' }} class="peer sr-only">
                            <div
                                class="w-12 h-6 bg-dark-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neon-500">
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold font-display text-dark-100 group-hover:text-white transition-colors">
                                Feature on homepage</p>
                            <p class="text-xs text-dark-500 mt-0.5">Pins this project in the portfolio hero section</p>
                        </div>
                        <div class="hidden sm:block">
                            <span
                                class="px-3 py-1 rounded-md bg-neon-500/10 border border-neon-500/20 text-neon-500 text-[10px] font-bold tracking-widest uppercase opacity-50 peer-checked:opacity-100 transition-opacity">Featured</span>
                        </div>
                    </label>

                </div>
            </div>

            {{-- ── ACTIONS ── --}}
            <div
                class="p-6 md:p-8 bg-dark-900/30 border-t border-dark-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-dark-500 text-xs hidden sm:flex items-center gap-2">
                    <i class="ri-information-line text-lg"></i>
                    All fields marked with <span class="text-neon-500 text-base leading-none">*</span> are required
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <a href="{{ route('admin.projects.index') }}"
                        class="flex-1 sm:flex-none text-center px-6 py-3 rounded-xl border border-dark-700 text-dark-300 hover:text-dark-100 hover:bg-dark-800 transition-all font-medium text-sm">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-8 py-3 rounded-xl bg-neon-500 text-dark-950 hover:bg-neon-400 transition-all font-bold font-display tracking-wide text-sm"
                        style="box-shadow: 0 0 20px oklch(0.66 0.17 195 / 0.4);">
                        Update Project <i class="ri-arrow-right-line"></i>
                    </button>
                </div>
            </div>

        </form>
    </div>
@endsection
