@extends('layouts.admin')

@section('page-title', 'Skills')

@section('content')
<div class="sm:flex sm:items-center justify-between mb-8">
    <div class="sm:flex-auto">
        <h1 class="text-2xl font-black text-slate-900">Technical Skills</h1>
        <p class="mt-2 text-sm text-slate-500">Manage your technical expertise, proficiency levels, and skill categories.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex gap-3">
        <a href="?archived={{ request('archived') ? '0' : '1' }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
            <i class="{{ request('archived') ? 'ri-eye-line' : 'ri-archive-line' }}"></i>
            {{ request('archived') ? 'View Active' : 'View Archived' }}
        </a>
        <a href="{{ route('admin.skills.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm hover:bg-indigo-500 transition-all">
            <i class="ri-add-line"></i>
            Add Skill
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($skills as $skill)
        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                        @if($skill->icon)
                            <img src="{{ $skill->icon }}" class="w-6 h-6 object-contain" alt="">
                        @else
                            <i class="ri-code-s-slash-line text-2xl"></i>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">{{ $skill->name }}</h3>
                        <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-0.5 text-[10px] font-black text-slate-500 border border-slate-200 uppercase tracking-widest mt-1">
                            {{ $skill->category }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    @if($skill->trashed())
                        <form action="{{ route('admin.skills.restore', $skill->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Restore">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.skills.edit', $skill) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit">
                            <i class="ri-pencil-line"></i>
                        </a>
                        <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                    <span>Proficiency</span>
                    <span class="text-slate-900">{{ $skill->proficiency }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000" style="width: {{ $skill->proficiency }}%"></div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full py-20 text-center bg-white rounded-2xl border border-slate-200">
            <div class="flex flex-col items-center gap-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300">
                    <i class="ri-medal-line text-3xl"></i>
                </div>
                <p class="text-sm text-slate-400 font-medium">No skills found.</p>
                <a href="{{ route('admin.skills.create') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700">Add your first skill</a>
            </div>
        </div>
    @endforelse
</div>

@if($skills->hasPages())
    <div class="mt-8">
        {{ $skills->links() }}
    </div>
@endif
@endsection
