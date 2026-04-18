@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'System Overview')
@section('page-subtitle', 'Welcome back, ' . (auth()->user()->name ?? 'Patrick'))

@section('content')
    <div class="space-y-8">
        {{-- STATS GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $stats = [
                    [
                        'label' => 'Total Projects',
                        'value' => $projectsCount,
                        'icon' => 'ri-folder-4-line',
                        'color' => 'text-neon-400',
                        'bg' => 'bg-neon-500/10',
                        'border' => 'border-neon-500/20',
                    ],
                    [
                        'label' => 'Unread Messages',
                        'value' => $messagesCountnew,
                        'icon' => 'ri-mail-unread-line',
                        'color' => 'text-emerald-400',
                        'bg' => 'bg-emerald-500/10',
                        'border' => 'border-emerald-500/20',
                    ],
                    [
                        'label' => 'Technical Skills',
                        'value' => $skillsCount,
                        'icon' => 'ri-bar-chart-2-line',
                        'color' => 'text-blue-400',
                        'bg' => 'bg-blue-500/10',
                        'border' => 'border-blue-500/20',
                    ],
                    [
                        'label' => 'Total Messages',
                        'value' => $messagesCount,
                        'icon' => 'ri-mail-line',
                        'color' => 'text-purple-400',
                        'bg' => 'bg-purple-500/10',
                        'border' => 'border-purple-500/20',
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <x-card padding="p-6" class="relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <i class="{{ $stat['icon'] }} text-6xl"></i>
                    </div>
                    <div class="flex items-center gap-4 relative z-10">
                        <div class="w-12 h-12 rounded-xl {{ $stat['bg'] }} {{ $stat['border'] }} border flex items-center justify-center {{ $stat['color'] }} text-xl">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-dark-500">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-black text-dark-100 tracking-tight">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="grid lg:grid-cols-3 gap-8">
            {{-- RECENT PROJECTS --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-dark-100 flex items-center gap-3">
                        <span class="w-8 h-px bg-neon-500"></span> Recent Projects
                    </h3>
                    <x-button variant="ghost" size="sm" href="{{ route('admin.projects.index') }}">
                        View All <i class="ri-arrow-right-line"></i>
                    </x-button>
                </div>

                <x-card padding="p-0" class="overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-dark-950/50 border-b border-dark-800">
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Project</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Category</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Status</th>
                                    <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-800">
                                @forelse ($projects as $project)
                                    <tr class="hover:bg-dark-800/30 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg bg-dark-800 border border-dark-700 overflow-hidden shrink-0">
                                                    @if(!empty($project->images))
                                                        <img src="{{ Storage::url($project->images[0]) }}" alt="" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-dark-600">
                                                            <i class="ri-image-line"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="text-sm font-bold text-dark-200 group-hover:text-neon-400 transition-colors">{{ $project->title }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs font-medium text-dark-400">{{ $project->category }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-neon-500/10 text-neon-400 border border-neon-500/20">
                                                <span class="w-1 h-1 rounded-full bg-neon-500"></span> Live
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <x-button variant="ghost" size="sm" href="{{ route('admin.projects.edit', $project) }}">
                                                <i class="ri-edit-line"></i>
                                            </x-button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-dark-500 italic text-sm">No projects found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            {{-- RECENT MESSAGES --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-dark-100 flex items-center gap-3">
                        <span class="w-8 h-px bg-neon-500"></span> Recent Messages
                    </h3>
                    <x-button variant="ghost" size="sm" href="{{ route('admin.messages.index') }}">
                        All <i class="ri-arrow-right-line"></i>
                    </x-button>
                </div>

                <div class="space-y-4">
                    @forelse ($messages as $message)
                        <x-card padding="p-4" hover="true" class="{{ !$message->is_read ? 'border-neon-500/20 bg-neon-500/5' : '' }}">
                            <a href="{{ route('admin.messages.show', $message) }}" class="block space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-sm font-bold text-dark-100 truncate">{{ $message->name }}</h4>
                                    <span class="text-[10px] text-dark-500 shrink-0">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-dark-400 line-clamp-2 leading-relaxed">
                                    {{ $message->message }}
                                </p>
                                <div class="flex items-center justify-between pt-2 border-t border-dark-800">
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-dark-500">{{ $message->email }}</span>
                                    @if(!$message->is_read)
                                        <span class="w-2 h-2 rounded-full bg-neon-500 shadow-neon-sm"></span>
                                    @endif
                                </div>
                            </a>
                        </x-card>
                    @empty
                        <div class="text-center py-12 bg-dark-900/50 border border-dark-800 rounded-2xl text-dark-500 italic text-sm">
                            No messages yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- QUICK ACTIONS --}}
        <div class="space-y-6">
            <h3 class="text-lg font-bold text-dark-100 flex items-center gap-3">
                <span class="w-8 h-px bg-neon-500"></span> Quick Actions
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                <x-button variant="secondary" size="md" href="{{ route('admin.projects.create') }}" class="flex-col gap-2 py-6">
                    <i class="ri-add-circle-line text-2xl text-neon-400"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Add Project</span>
                </x-button>
                <x-button variant="secondary" size="md" href="{{ route('admin.skills.create') }}" class="flex-col gap-2 py-6">
                    <i class="ri-bar-chart-box-line text-2xl text-blue-400"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Add Skill</span>
                </x-button>
                <x-button variant="secondary" size="md" href="{{ route('admin.services.create') }}" class="flex-col gap-2 py-6">
                    <i class="ri-service-line text-2xl text-emerald-400"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Add Service</span>
                </x-button>
                <x-button variant="secondary" size="md" href="{{ route('admin.experience.create') }}" class="flex-col gap-2 py-6">
                    <i class="ri-briefcase-line text-2xl text-purple-400"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Add Experience</span>
                </x-button>
                <x-button variant="secondary" size="md" href="{{ route('admin.settings.index') }}" class="flex-col gap-2 py-6">
                    <i class="ri-settings-3-line text-2xl text-dark-400"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Settings</span>
                </x-button>
                <x-button variant="secondary" size="md" href="{{ route('home') }}" target="_blank" class="flex-col gap-2 py-6">
                    <i class="ri-external-link-line text-2xl text-dark-400"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">View Site</span>
                </x-button>
            </div>
        </div>
    </div>
@endsection
