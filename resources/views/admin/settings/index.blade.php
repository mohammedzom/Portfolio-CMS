@extends('layouts.admin')

@section('page-title', 'Site Settings')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Profile Information --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600">Personal Information</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input label="First Name" name="first_name" :value="$settings->first_name" required="true" />
                        <x-admin.input label="Last Name" name="last_name" :value="$settings->last_name" required="true" />
                    </div>

                    <x-admin.input label="Tagline" name="tagline" :value="$settings->tagline" required="true"
                        placeholder="Software Engineer & Designer" />

                    <div class="space-y-1.5">
                        <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Bio <span
                                class="text-red-500">*</span></label>
                        <textarea name="bio" rows="4" required
                            class="block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20 resize-none">{{ old('bio', $settings->bio) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-admin.input label="Email Address" name="email" type="email" :value="$settings->email"
                            required="true" />
                        <x-admin.input label="Phone Number" name="phone" :value="$settings->phone" required="true" />
                    </div>

                    <x-admin.input label="Location" name="location" :value="$settings->location" required="true"
                        placeholder="City, Country" />
                </div>
            </div>

            {{-- Statistics --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600">About Section Stats</h3>
                </div>
                <div class="p-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <x-admin.input label="Years Experience" name="years_experience" type="number" :value="$settings->years_experience"
                        required="true" />
                    <x-admin.input label="Projects Count" name="projects_count" type="number" :value="$settings->projects_count"
                        required="true" />
                    <x-admin.input label="Clients Count" name="clients_count" type="number" :value="$settings->clients_count"
                        required="true" />
                    <x-admin.input label="Coffee Cups" name="coffee_cups" type="number" :value="$settings->coffee_cups"
                        required="true" />
                </div>
            </div>

            {{-- Assets --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600">Assets & Files</h3>
                </div>
                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Profile
                                Avatar</label>
                            <div class="flex items-center gap-4">
                                @if ($settings->avatar)
                                    <img src="{{ asset('storage/' . $settings->avatar) }}"
                                        class="w-16 h-16 rounded-2xl object-cover border border-slate-200">
                                @endif
                                <input type="file" name="avatar"
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all">
                            </div>
                        </div>
                        <div class="space-y-4">
                            <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">CV /
                                Resume (PDF)</label>
                            <div class="flex items-center gap-4">
                                @if ($settings->cv_file)
                                    <div
                                        class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center border border-slate-200 text-red-500">
                                        <i class="ri-file-pdf-line text-2xl"></i>
                                    </div>
                                @endif
                                <input type="file" name="cv_file"
                                    class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4">
                <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-10 py-4 text-sm font-black text-white shadow-xl shadow-indigo-200 hover:bg-indigo-500 hover:-translate-y-0.5 transition-all active:translate-y-0">
                    <i class="ri-save-line text-lg"></i>
                    Update Settings
                </button>
            </div>
        </form>
    </div>
@endsection
