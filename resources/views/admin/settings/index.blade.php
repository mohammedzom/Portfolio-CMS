@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Site Settings')
@section('page-subtitle', 'Manage your profile and site configuration')

@section('content')
    <div class="max-w-6xl mx-auto">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid lg:grid-cols-3 gap-8">
                {{-- LEFT COLUMN: PERSONAL INFO --}}
                <div class="lg:col-span-2 space-y-8">
                    <x-card padding="p-8">
                        <div class="space-y-8">
                            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                                <i class="ri-user-settings-line"></i> Personal Information
                            </h3>

                            <div class="grid md:grid-cols-2 gap-6">
                                <x-input label="First Name" name="first_name" value="{{ $settings->first_name ?? '' }}" placeholder="John" required="true" :error="$errors->first('first_name')" />
                                <x-input label="Last Name" name="last_name" value="{{ $settings->last_name ?? '' }}" placeholder="Doe" required="true" :error="$errors->first('last_name')" />
                            </div>

                            <x-input label="Professional Tagline" name="tagline" value="{{ $settings->tagline ?? '' }}" placeholder="Senior Full-Stack Engineer & Architect" required="true" :error="$errors->first('tagline')" />

                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Short Bio <span class="text-neon-500">*</span></label>
                                <textarea name="bio" rows="3" required class="w-full bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none {{ $errors->has('bio') ? 'border-red-500/50' : '' }}" placeholder="A brief introduction for the hero section...">{{ old('bio', $settings->bio ?? '') }}</textarea>
                                @error('bio') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">About Me (Full Story) <span class="text-neon-500">*</span></label>
                                <textarea name="about_me" rows="8" required class="w-full bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl px-4 py-3 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all duration-300 resize-none {{ $errors->has('about_me') ? 'border-red-500/50' : '' }}" placeholder="Your professional background and journey...">{{ old('about_me', $settings->about_me ?? '') }}</textarea>
                                @error('about_me') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </x-card>

                    <x-card padding="p-8">
                        <div class="space-y-8">
                            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                                <i class="ri-contacts-book-line"></i> Contact & Location
                            </h3>

                            <div class="grid md:grid-cols-2 gap-6">
                                <x-input label="Public Email" name="email" type="email" value="{{ $settings->email ?? '' }}" placeholder="hello@example.com" required="true" :error="$errors->first('email')" />
                                <x-input label="Phone Number" name="phone" value="{{ $settings->phone ?? '' }}" placeholder="+1 234 567 890" :error="$errors->first('phone')" />
                            </div>

                            <x-input label="Location" name="location" value="{{ $settings->location ?? '' }}" placeholder="San Francisco, CA (Remote)" required="true" :error="$errors->first('location')" />
                        </div>
                    </x-card>
                </div>

                {{-- RIGHT COLUMN: ASSETS & STATS --}}
                <div class="space-y-8">
                    <x-card padding="p-8">
                        <div class="space-y-8">
                            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                                <i class="ri-image-line"></i> Profile Assets
                            </h3>

                            <div class="space-y-6">
                                {{-- Avatar --}}
                                <div class="space-y-4">
                                    <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Avatar / Profile Picture</label>
                                    <div class="flex items-center gap-6">
                                        <div class="w-20 h-20 rounded-2xl bg-dark-900 border border-dark-800 overflow-hidden shrink-0 shadow-neon-sm">
                                            <img src="{{ $settings->avatar ? Storage::url($settings->avatar) : asset('assets/img/perfil.png') }}" alt="" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1">
                                            <input type="file" name="avatar" class="w-full text-xs text-dark-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-dark-800 file:text-dark-300 hover:file:bg-neon-500 hover:file:text-dark-950 transition-all">
                                        </div>
                                    </div>
                                    @error('avatar') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- CV File --}}
                                <div class="space-y-4">
                                    <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Curriculum Vitae (PDF)</label>
                                    <div class="flex items-center gap-4 bg-dark-950/50 border border-dark-800 rounded-xl p-4">
                                        <i class="ri-file-pdf-2-line text-2xl text-red-400"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-dark-200 truncate">{{ $settings->cv_file ? basename($settings->cv_file) : 'No CV uploaded' }}</p>
                                            <input type="file" name="cv_file" class="mt-2 w-full text-[10px] text-dark-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-dark-800 file:text-dark-300 hover:file:bg-neon-500 hover:file:text-dark-950 transition-all">
                                        </div>
                                    </div>
                                    @error('cv_file') <p class="text-xs text-red-400 px-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </x-card>

                    <x-card padding="p-8">
                        <div class="space-y-8">
                            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                                <i class="ri-bar-chart-line"></i> Dashboard Stats
                            </h3>

                            <div class="grid grid-cols-2 gap-6">
                                <x-input label="Years Exp." name="years_experience" type="number" value="{{ $settings->years_experience ?? 0 }}" required="true" :error="$errors->first('years_experience')" />
                                <x-input label="Projects Done" name="projects_count" type="number" value="{{ $settings->projects_count ?? 0 }}" required="true" :error="$errors->first('projects_count')" />
                                <x-input label="Happy Clients" name="clients_count" type="number" value="{{ $settings->clients_count ?? 0 }}" required="true" :error="$errors->first('clients_count')" />
                                <div class="space-y-2">
                                    <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest px-1">Freelance</label>
                                    <div class="flex items-center gap-3 bg-dark-900 border border-dark-800 rounded-xl px-4 py-2.5">
                                        <input type="checkbox" name="available_for_freelance" value="1" {{ old('available_for_freelance', $settings->available_for_freelance ?? false) ? 'checked' : '' }} class="w-5 h-5 rounded bg-dark-800 border-dark-700 text-neon-500 focus:ring-neon-500/20 focus:ring-offset-dark-950">
                                        <span class="text-xs text-dark-300">Available</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </x-card>

                    <x-card padding="p-8">
                        <div class="space-y-8">
                            <h3 class="text-sm font-black uppercase tracking-[0.2em] text-neon-400 border-b border-dark-800 pb-3 flex items-center gap-2">
                                <i class="ri-global-line"></i> Branding
                            </h3>

                            <div class="grid grid-cols-2 gap-4">
                                <x-input label="URL Prefix" name="url_prefix" value="{{ $settings->url_prefix ?? 'Patrick' }}" placeholder="Patrick" :error="$errors->first('url_prefix')" />
                                <x-input label="URL Suffix" name="url_suffix" value="{{ $settings->url_suffix ?? 'cms' }}" placeholder="cms" :error="$errors->first('url_suffix')" />
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>

            {{-- BOTTOM BAR ACTIONS --}}
            <div class="sticky bottom-8 z-20">
                <x-card padding="p-4" class="shadow-neon-lg border-neon-500/20 bg-dark-900/90 backdrop-blur-xl">
                    <div class="flex items-center justify-between gap-6">
                        <p class="text-xs text-dark-500 font-medium hidden md:block">
                            <i class="ri-information-line text-neon-400"></i> Review your changes before saving.
                        </p>
                        <div class="flex items-center gap-4 w-full md:w-auto">
                            <x-button variant="secondary" size="lg" href="{{ route('admin.dashboard') }}" class="flex-1 md:flex-none">
                                Cancel
                            </x-button>
                            <x-button variant="neon" size="lg" type="submit" class="flex-1 md:flex-none">
                                <i class="ri-save-line"></i> Save Settings
                            </x-button>
                        </div>
                    </div>
                </x-card>
            </div>
        </form>
    </div>
@endsection
