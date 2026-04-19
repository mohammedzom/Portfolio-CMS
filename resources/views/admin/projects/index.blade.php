@extends('layouts.admin')

@section('page-title', 'Projects')

@section('content')
<div class="sm:flex sm:items-center justify-between mb-8">
    <div class="sm:flex-auto">
        <h1 class="text-2xl font-black text-slate-900">Portfolio Projects</h1>
        <p class="mt-2 text-sm text-slate-500">A list of all projects in your portfolio including their title, category, and status.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex gap-3">
        <a href="?archived={{ request('archived') ? '0' : '1' }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
            <i class="{{ request('archived') ? 'ri-eye-line' : 'ri-archive-line' }}"></i>
            {{ request('archived') ? 'View Active' : 'View Archived' }}
        </a>
        <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm hover:bg-indigo-500 transition-all">
            <i class="ri-add-line"></i>
            Add Project
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-black uppercase tracking-widest text-slate-500">Project</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-500">Category</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-500">Status</th>
                    <th scope="col" class="relative py-4 pl-3 pr-6 text-right">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($projects as $project)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="whitespace-nowrap py-5 pl-6 pr-3">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                    @if(!empty($project->images))
                                        <img src="{{ Storage::url($project->images[0]) }}" alt="" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-300">
                                            <i class="ri-image-line text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $project->title }}</div>
                                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ $project->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-5">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-black text-slate-600 border border-slate-200 uppercase tracking-widest">
                                {{ $project->category }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-5">
                            @if($project->trashed())
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                    <i class="ri-archive-line"></i> Archived
                                </span>
                            @elseif($project->live_url)
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 uppercase tracking-widest">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                    Live
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-500 uppercase tracking-widest">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if($project->trashed())
                                    <form action="{{ route('admin.projects.restore', $project->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Restore">
                                            <i class="ri-refresh-line text-lg"></i>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit">
                                        <i class="ri-pencil-line text-lg"></i>
                                    </a>
                                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                            <i class="ri-delete-bin-line text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300">
                                    <i class="ri-folder-open-line text-3xl"></i>
                                </div>
                                <p class="text-sm text-slate-400 font-medium">No projects found.</p>
                                <a href="{{ route('admin.projects.create') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-700">Create your first project</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
