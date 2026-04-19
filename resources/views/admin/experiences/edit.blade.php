@extends('layouts.admin')

@section('page-title', 'Edit Experience')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600">Experience Details</h3>
        </div>
        
        <form action="{{ route('admin.experience.update', $experience) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input label="Job Title" name="title" :value="$experience->title" placeholder="Senior Web Developer..." required="true" :error="$errors->first('title')" />
                <x-admin.input label="Company Name" name="company" :value="$experience->company" placeholder="Google, Freelance, etc..." required="true" :error="$errors->first('company')" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-admin.input label="Start Date" name="start_date" type="date" :value="$experience->start_date ? $experience->start_date->format('Y-m-d') : ''" required="true" :error="$errors->first('start_date')" />
                <x-admin.input label="End Date" name="end_date" type="date" :value="$experience->end_date ? $experience->end_date->format('Y-m-d') : ''" :error="$errors->first('end_date')" />
            </div>

            <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-100 bg-slate-50 cursor-pointer hover:bg-indigo-50 transition-colors">
                <input type="checkbox" name="is_current" value="1" {{ old('is_current', $experience->is_current) ? 'checked' : '' }} class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm font-bold text-slate-700">I am currently working in this role</span>
            </label>

            <div class="space-y-1.5">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required class="block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20 placeholder:text-slate-400 resize-none {{ $errors->has('description') ? 'border-red-300' : '' }}">{{ old('description', $experience->description) }}</textarea>
                @error('description') <p class="mt-1 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.experience.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 transition-all">
                    <i class="ri-save-line"></i>
                    Update Experience
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
