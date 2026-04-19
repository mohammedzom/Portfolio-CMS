@extends('layouts.admin')

@section('page-title', 'Add Service')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600">Service Details</h3>
        </div>
        
        <form action="{{ route('admin.services.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <x-admin.input label="Service Title" name="title" placeholder="Web Development, SEO, UI Design..." required="true" :error="$errors->first('title')" />

            <x-admin.input label="Icon Class" name="icon" placeholder="ri-code-line, ri-layout-line..." :error="$errors->first('icon')" />

            <div class="space-y-1.5">
                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required class="block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20 placeholder:text-slate-400 resize-none {{ $errors->has('description') ? 'border-red-300' : '' }}">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs font-bold text-red-500 px-1">{{ $message }}</p> @enderror
            </div>

            <x-admin.input label="Sort Order" name="sort_order" type="number" value="{{ old('sort_order', 0) }}" :error="$errors->first('sort_order')" />

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.services.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 transition-all">
                    <i class="ri-save-line"></i>
                    Save Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
