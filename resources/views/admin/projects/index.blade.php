@extends('layouts.admin')

@section('title', 'Projects')
@section('page-title', 'Portfolio Projects')
@section('page-subtitle', 'Manage your showcased work')

@section('content')
    <div class="space-y-8">
        {{-- HEADER ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="{{ !request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.projects.index') }}">
                    Active <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Project::withoutTrashed()->count() }}</span>
                </x-button>
                <x-button variant="{{ request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.projects.index', ['archived' => 'true']) }}">
                    Archived <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Project::onlyTrashed()->count() }}</span>
                </x-button>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('admin.projects.index') }}" method="GET" class="relative group">
                    <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-dark-500 group-focus-within:text-neon-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl pl-10 pr-4 py-2.5 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all w-64">
                </form>
                <x-button variant="neon" size="md" href="{{ route('admin.projects.create') }}">
                    <i class="ri-add-line"></i> New Project
                </x-button>
            </div>
        </div>

        {{-- PROJECTS TABLE --}}
        <x-card padding="p-0" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-dark-950/50 border-b border-dark-800">
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Order</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Project</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Category</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Tech Stack</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Featured</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-800">
                        @forelse ($projects as $project)
                            <tr class="hover:bg-dark-800/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono text-dark-500">#{{ str_pad($project->sort_order, 2, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-xl bg-dark-800 border border-dark-700 overflow-hidden shrink-0">
                                            @if(!empty($project->images))
                                                <img src="{{ Storage::url($project->images[0]) }}" alt="" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-dark-600">
                                                    <i class="ri-image-line text-xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-dark-200 group-hover:text-neon-400 transition-colors">{{ $project->title }}</span>
                                            <span class="text-[10px] text-dark-500 font-medium truncate max-w-[200px]">{{ $project->slug }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-lg bg-dark-800 text-dark-400 text-[10px] font-black uppercase tracking-widest border border-dark-700">
                                        {{ $project->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1 max-w-[200px]">
                                        @foreach (is_array($project->tech_stack) ? $project->tech_stack : explode(',', $project->tech_stack) as $tech)
                                            <span class="text-[9px] font-bold text-dark-500 px-1.5 py-0.5 rounded bg-dark-950 border border-dark-800">{{ trim($tech) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($project->is_featured)
                                        <span class="text-neon-500 text-lg"><i class="ri-star-fill shadow-neon-sm"></i></span>
                                    @else
                                        <span class="text-dark-700 text-lg"><i class="ri-star-line"></i></span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($project->trashed())
                                            <form action="{{ route('admin.projects.restore', $project->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <x-button variant="neon" size="sm" type="submit" title="Restore Project">
                                                    <i class="ri-refresh-line"></i>
                                                </x-button>
                                            </form>
                                            <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this project?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="danger" size="sm" type="submit" title="Delete Permanently">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </x-button>
                                            </form>
                                        @else
                                            <x-button variant="ghost" size="sm" href="{{ route('admin.projects.edit', $project) }}" title="Edit Project">
                                                <i class="ri-edit-line"></i>
                                            </x-button>
                                            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="secondary" size="sm" type="submit" title="Archive Project">
                                                    <i class="ri-archive-line"></i>
                                                </x-button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-full bg-dark-900 border border-dark-800 flex items-center justify-center text-dark-700">
                                            <i class="ri-folder-open-line text-3xl"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-dark-300 font-bold">No projects found</p>
                                            <p class="text-dark-500 text-sm">Start by adding your first project to your portfolio.</p>
                                        </div>
                                        <x-button variant="neon" size="md" href="{{ route('admin.projects.create') }}" class="mt-2">
                                            <i class="ri-add-line"></i> Add Project
                                        </x-button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($projects->hasPages())
                <div class="px-6 py-4 border-t border-dark-800 bg-dark-950/30">
                    {{ $projects->links() }}
                </div>
            @endif
        </x-card>
    </div>
@endsection
