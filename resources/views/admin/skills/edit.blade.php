@extends('layouts.admin')

@section('page-title', 'Edit Skill')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6">
            <h3 class="text-sm font-black uppercase tracking-widest text-indigo-600">Skill Details</h3>
        </div>
        
        <form action="{{ route('admin.skills.update', $skill) }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <x-admin.input label="Skill Name" name="name" :value="$skill->name" placeholder="PHP, React, UI Design..." required="true" :error="$errors->first('name')" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Category <span class="text-red-500">*</span></label>
                    <select name="type" required class="block w-full rounded-xl border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-indigo-500/20">
                        @foreach(['Frontend', 'Backend', 'Full Stack', 'Design', 'DevOps', 'Other'] as $cat)
                            <option value="{{ $cat }}" {{ old('type', $skill->type) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5" x-data="{ proficiency: {{ old('proficiency', $skill->proficiency) }} }">
                    <label class="block text-xs font-black uppercase tracking-widest text-slate-500 px-1">Proficiency (<span x-text="proficiency"></span>%) <span class="text-red-500">*</span></label>
                    <input type="range" name="proficiency" min="0" max="100" x-model="proficiency" class="w-full h-2 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                </div>
            </div>

            <x-admin.input label="Icon URL or Class" name="icon" :value="$skill->icon" placeholder="ri-code-line or https://..." :error="$errors->first('icon')" />

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.skills.index') }}" class="px-6 py-3 text-sm font-bold text-slate-500 hover:text-slate-700 transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 transition-all">
                    <i class="ri-save-line"></i>
                    Update Skill
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
