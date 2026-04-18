@extends('admin.layout')
@section('title', 'Projects')
@section('page-title', 'Projects')
@section('page-subtitle', 'Manage your portfolio projects')
@section('content')
    <div class="mb-6 w-full flex items-center justify-between">
        <form action="" class="flex items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}"
                class="glass rounded-2xl border border-dark-700 px-4 py-2" placeholder="Search...">
            <button type="submit" class="glass rounded-2xl border border-dark-700 px-4 py-2 text-neon-500"><i
                    class="ri-search-line"></i></button>
        </form>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.create') }}" class="glass rounded-2xl border border-dark-700 px-4 py-2"><i
                    class="ri-add-line mr-2"></i>Add Project</a>
            <a href="?archived={{ request('archived') ? '0' : '1' }}"
                class="glass rounded-2xl border border-dark-700 px-4 py-2">{{ request('archived') ? 'View Active' : 'View Archived' }}</a>
        </div>
    </div>
    <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-700">
                        <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-6 py-3">Project
                        </th>
                        <th
                            class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-3 hidden sm:table-cell">
                            Category</th>
                        <th
                            class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-3 hidden md:table-cell">
                            Tech</th>
                        <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-3">Status
                        </th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700">
                    @forelse ($projects as $project)
                        <tr class="hover:bg-dark-800/40 transition-colors group">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if ($project->thumbnail)
                                        <img src="{{ $project->thumbnail }}" alt="{{ $project->title }}"
                                            class="w-10 h-10 rounded-lg object-cover border border-dark-700">
                                    @else
                                        <div
                                            class="w-10 h-10 rounded-lg bg-dark-700 flex items-center justify-center text-dark-400 border border-dark-600">
                                            <i class="ri-folder-4-line"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-dark-100 font-medium text-sm">{{ $project->title }}</p>
                                        <p class="text-dark-500 text-xs mt-0.5">{{ $project->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                @if ($project->category)
                                    <span
                                        class="text-xs px-2.5 py-1 rounded-full bg-neon-500/10 text-neon-400 font-medium">{{ $project->category }}</span>
                                @else
                                    <span class="text-dark-500 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                @if ($project->tech_stack)
                                    @for ($i = 0; $i < 3; $i++)
                                        @if (isset($project->tech_stack[$i]))
                                            <span class="text-dark-400 text-xs">{{ $project->tech_stack[$i] }}</span>
                                        @endif
                                    @endfor
                                @else
                                    <span class="text-dark-500 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                @if ($project->trashed())
                                    <span class="text-dark-500 text-xs flex items-center gap-1.5">
                                        <i class="ri-archive-line"></i> Archived
                                    </span>
                                @elseif ($project->live_url)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium"
                                        style="color: oklch(0.72 0.18 160);">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                            style="background: oklch(0.72 0.18 160); box-shadow: 0 0 6px oklch(0.72 0.18 160);"></span>
                                        Live
                                    </span>
                                @else
                                    <span class="text-dark-500 text-xs flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-dark-500"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <div
                                    class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if ($project->trashed())
                                        <form action="{{ route('admin.projects.restore', $project->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-dark-500 hover:text-green-400 hover:bg-green-500/5 transition-all"
                                                title="Restore">
                                                <i class="ri-refresh-line text-sm"></i>
                                            </button>
                                        </form>
                                    @else
                                        @if ($project->live_url)
                                            <a href="{{ $project->live_url }}" target="_blank"
                                                class="p-1.5 rounded-lg text-dark-500 hover:text-blue-400 hover:bg-blue-500/5 transition-all"
                                                title="View Live">
                                                <i class="ri-external-link-line text-sm"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.projects.edit', $project) }}"
                                            class="p-1.5 rounded-lg text-dark-500 hover:text-neon-500 hover:bg-neon-500/5 transition-all"
                                            title="Edit">
                                            <i class="ri-pencil-line text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Are you sure you want to archive this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 rounded-lg text-dark-500 hover:text-red-400 hover:bg-red-500/5 transition-all"
                                                title="Archive">
                                                <i class="ri-archive-line text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-dark-500 text-sm">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="ri-folder-4-line text-3xl mb-2 text-dark-600"></i>
                                    <p>No projects found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($projects->hasPages())
            <div class="px-6 py-4 border-t border-dark-700 flex justify-between items-center">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection
