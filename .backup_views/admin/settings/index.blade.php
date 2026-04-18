@extends('admin.layout')
@section('title', 'Site Settings')
@section('page-title', 'Site Settings')
@section('page-subtitle', 'Configure your portfolio website settings')

@section('content')
    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 rounded-xl border border-green-500/30 bg-green-500/10 flex items-center gap-3">
            <i class="ri-checkbox-circle-line text-green-400 text-xl"></i>
            <span class="text-green-300 text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-400 hover:text-green-300">
                <i class="ri-close-line"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 p-4 rounded-xl border border-red-500/30 bg-red-500/10">
            <div class="flex items-center gap-3 mb-2">
                <i class="ri-error-warning-line text-red-400 text-xl"></i>
                <span class="text-red-300 font-medium">Validation Errors</span>
            </div>
            <ul class="list-disc list-inside text-red-400 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Settings Form --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" 
          x-data="settingsForm()" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Personal Information Section --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-dark-700 bg-dark-800/50">
                <h3 class="text-lg font-display font-semibold text-dark-100 flex items-center gap-2">
                    <i class="ri-user-3-line text-neon-500"></i>
                    Personal Information
                </h3>
            </div>
            <div class="p-6 grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $settings?->first_name ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('first_name') border-red-500/50 @enderror"
                        placeholder="John">
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $settings?->last_name ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('last_name') border-red-500/50 @enderror"
                        placeholder="Doe">
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-dark-400 mb-2">Tagline *</label>
                    <input type="text" name="tagline" value="{{ old('tagline', $settings?->tagline ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('tagline') border-red-500/50 @enderror"
                        placeholder="Software Engineer & UI/UX Designer">
                    @error('tagline')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-dark-400 mb-2">Bio *</label>
                    <textarea name="bio" rows="3"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('bio') border-red-500/50 @enderror"
                        placeholder="Brief description about yourself...">{{ old('bio', $settings?->bio ?? '') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Contact Information Section --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-dark-700 bg-dark-800/50">
                <h3 class="text-lg font-display font-semibold text-dark-100 flex items-center gap-2">
                    <i class="ri-mail-send-line text-neon-500"></i>
                    Contact Information
                </h3>
            </div>
            <div class="p-6 grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $settings?->email ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('email') border-red-500/50 @enderror"
                        placeholder="john@example.com">
                    @error('email')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Phone *</label>
                    <input type="tel" name="phone" value="{{ old('phone', $settings?->phone ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('phone') border-red-500/50 @enderror"
                        placeholder="+1 234 567 8900">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-dark-400 mb-2">Location *</label>
                    <input type="text" name="location" value="{{ old('location', $settings?->location ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('location') border-red-500/50 @enderror"
                        placeholder="New York, USA">
                    @error('location')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Avatar Image *</label>
                    <div class="flex items-center gap-4">
                        @if($settings?->avatar)
                            <img src="{{ asset('storage/' . $settings->avatar) }}" alt="Avatar" 
                                class="w-20 h-20 rounded-xl object-cover border border-dark-700">
                        @endif
                        <input type="file" name="avatar" accept="image/*"
                            class="flex-1 glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors @error('avatar') border-red-500/50 @enderror">
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">CV File *</label>
                    <div class="flex items-center gap-4">
                        @if($settings?->cv_file)
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-dark-800 border border-dark-700">
                                <i class="ri-file-pdf-line text-red-400"></i>
                                <span class="text-xs text-dark-400">CV Uploaded</span>
                            </div>
                        @endif
                        <input type="file" name="cv_file" accept=".pdf,.doc,.docx"
                            class="flex-1 glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors @error('cv_file') border-red-500/50 @enderror">
                    </div>
                    @error('cv_file')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- About Section Stats --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-dark-700 bg-dark-800/50">
                <h3 class="text-lg font-display font-semibold text-dark-100 flex items-center gap-2">
                    <i class="ri-bar-chart-box-line text-neon-500"></i>
                    About Section Stats
                </h3>
            </div>
            <div class="p-6 grid md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Years Experience *</label>
                    <input type="number" name="years_experience" value="{{ old('years_experience', $settings?->years_experience ?? 0) }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('years_experience') border-red-500/50 @enderror"
                        min="0">
                    @error('years_experience')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Projects Count *</label>
                    <input type="number" name="projects_count" value="{{ old('projects_count', $settings?->projects_count ?? 0) }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('projects_count') border-red-500/50 @enderror"
                        min="0">
                    @error('projects_count')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Clients Count *</label>
                    <input type="number" name="clients_count" value="{{ old('clients_count', $settings?->clients_count ?? 0) }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('clients_count') border-red-500/50 @enderror"
                        min="0">
                    @error('clients_count')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">Available for Freelance</label>
                    <div class="mt-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="available_for_freelance" value="0">
                            <input type="checkbox" name="available_for_freelance" value="1"
                                {{ old('available_for_freelance', $settings?->available_for_freelance ?? false) ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="relative w-11 h-6 bg-dark-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-neon-500/40 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neon-500"></div>
                            <span class="ml-3 text-sm text-dark-400">Active</span>
                        </label>
                    </div>
                    @error('available_for_freelance')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-dark-400 mb-2">About Me *</label>
                    <textarea name="about_me" rows="4"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('about_me') border-red-500/50 @enderror"
                        placeholder="Detailed description about yourself and your work...">{{ old('about_me', $settings?->about_me ?? '') }}</textarea>
                    @error('about_me')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Social Links Section --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-dark-700 bg-dark-800/50 flex items-center justify-between">
                <h3 class="text-lg font-display font-semibold text-dark-100 flex items-center gap-2">
                    <i class="ri-share-box-line text-neon-500"></i>
                    Social Links
                </h3>
                <button type="button" @click="addSocialLink()" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neon-500/10 text-neon-500 text-sm font-medium hover:bg-neon-500/20 transition-colors">
                    <i class="ri-add-line"></i> Add Link
                </button>
            </div>
            <div class="p-6">
                <div id="social-links-container" class="space-y-4">
                    <template x-for="(link, index) in socialLinks" :key="index">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <input type="text" :name="'social_links[' + index + '][name]'" :value="link.name"
                                    placeholder="Platform (e.g., GitHub)"
                                    class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                                <input type="url" :name="'social_links[' + index + '][url]'" :value="link.url"
                                    placeholder="https://github.com/username"
                                    class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                            </div>
                            <button type="button" @click="removeSocialLink(index)"
                                class="p-2.5 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </template>
                    @if(old('social_links') || ($settings?->social_links && count($settings->social_links) > 0))
                        @foreach(old('social_links', $settings->social_links ?? []) as $index => $link)
                            <div class="flex items-center gap-3 social-link-item">
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <input type="text" name="social_links[{{ $index }}][name]" value="{{ $link['name'] ?? '' }}"
                                        placeholder="Platform (e.g., GitHub)"
                                        class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                                    <input type="url" name="social_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}"
                                        placeholder="https://github.com/username"
                                        class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                                </div>
                                <button type="button" onclick="this.parentElement.remove()"
                                    class="p-2.5 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
                @error('social_links')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Languages Section --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-dark-700 bg-dark-800/50 flex items-center justify-between">
                <h3 class="text-lg font-display font-semibold text-dark-100 flex items-center gap-2">
                    <i class="ri-translate-2 text-neon-500"></i>
                    Languages
                </h3>
                <button type="button" @click="addLanguage()" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-neon-500/10 text-neon-500 text-sm font-medium hover:bg-neon-500/20 transition-colors">
                    <i class="ri-add-line"></i> Add Language
                </button>
            </div>
            <div class="p-6">
                <div id="languages-container" class="space-y-4">
                    <template x-for="(lang, index) in languages" :key="index">
                        <div class="flex items-center gap-3">
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <input type="text" :name="'languages[' + index + '][name]'" :value="lang.name"
                                    placeholder="Language (e.g., English)"
                                    class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                                <select :name="'languages[' + index + '][level]'"
                                    class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                                    <option value="Beginner" :selected="lang.level === 'Beginner'">Beginner</option>
                                    <option value="Intermediate" :selected="lang.level === 'Intermediate'">Intermediate</option>
                                    <option value="Advanced" :selected="lang.level === 'Advanced'">Advanced</option>
                                    <option value="Fluent" :selected="lang.level === 'Fluent'">Fluent</option>
                                    <option value="Native" :selected="lang.level === 'Native'">Native</option>
                                </select>
                            </div>
                            <button type="button" @click="removeLanguage(index)"
                                class="p-2.5 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </template>
                    @if(old('languages') || ($settings?->languages && count($settings->languages) > 0))
                        @foreach(old('languages', $settings->languages ?? []) as $index => $lang)
                            <div class="flex items-center gap-3 language-item">
                                <div class="flex-1 grid grid-cols-2 gap-3">
                                    <input type="text" name="languages[{{ $index }}][name]" value="{{ $lang['name'] ?? '' }}"
                                        placeholder="Language (e.g., English)"
                                        class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                                    <select name="languages[{{ $index }}][level]"
                                        class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                                        <option value="Beginner" {{ ($lang['level'] ?? '') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="Intermediate" {{ ($lang['level'] ?? '') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="Advanced" {{ ($lang['level'] ?? '') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                        <option value="Fluent" {{ ($lang['level'] ?? '') == 'Fluent' ? 'selected' : '' }}>Fluent</option>
                                        <option value="Native" {{ ($lang['level'] ?? '') == 'Native' ? 'selected' : '' }}>Native</option>
                                    </select>
                                </div>
                                <button type="button" onclick="this.parentElement.remove()"
                                    class="p-2.5 rounded-xl bg-red-500/10 text-red-400 hover:bg-red-500/20 transition-colors">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
                @error('languages')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- URL Configuration Section --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-dark-700 bg-dark-800/50">
                <h3 class="text-lg font-display font-semibold text-dark-100 flex items-center gap-2">
                    <i class="ri-link text-neon-500"></i>
                    URL Configuration
                </h3>
            </div>
            <div class="p-6 grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">URL Prefix *</label>
                    <input type="text" name="url_prefix" value="{{ old('url_prefix', $settings?->url_prefix ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('url_prefix') border-red-500/50 @enderror"
                        placeholder="portfolio">
                    @error('url_prefix')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-400 mb-2">URL Suffix *</label>
                    <input type="text" name="url_suffix" value="{{ old('url_suffix', $settings?->url_suffix ?? '') }}"
                        class="w-full glass rounded-xl border border-dark-700 px-4 py-2.5 focus:border-neon-500/40 transition-colors @error('url_suffix') border-red-500/50 @enderror"
                        placeholder="com">
                    @error('url_suffix')
                        <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('admin.dashboard') }}" 
                class="px-6 py-3 rounded-xl glass border border-dark-700 text-dark-400 font-medium hover:border-dark-500 transition-colors">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl gradient-neon text-dark-950 font-bold hover:neon-glow transition-all">
                <i class="ri-save-3-line"></i>
                Save Settings
            </button>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function settingsForm() {
    return {
        socialLinks: [],
        languages: [],
        
        addSocialLink() {
            this.socialLinks.push({ name: '', url: '' });
        },
        
        removeSocialLink(index) {
            this.socialLinks.splice(index, 1);
        },
        
        addLanguage() {
            this.languages.push({ name: '', level: 'Beginner' });
        },
        
        removeLanguage(index) {
            this.languages.splice(index, 1);
        }
    }
}
</script>
@endpush
