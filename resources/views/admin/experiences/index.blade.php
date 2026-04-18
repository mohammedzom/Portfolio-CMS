@extends('layouts.admin')

@section('title', 'Experience')
@section('page-title', 'Professional Experience')
@section('page-subtitle', 'Manage your career timeline and work history')

@section('content')
    <div class="space-y-8">
        {{-- HEADER ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="{{ !request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.experience.index') }}">
                    Active <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Experience::withoutTrashed()->count() }}</span>
                </x-button>
                <x-button variant="{{ request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.experience.index', ['archived' => 'true']) }}">
                    Archived <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Experience::onlyTrashed()->count() }}</span>
                </x-button>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('admin.experience.index') }}" method="GET" class="relative group">
                    <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-dark-500 group-focus-within:text-neon-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search experiences..." class="bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl pl-10 pr-4 py-2.5 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all w-64">
                </form>
                <x-button variant="neon" size="md" href="{{ route('admin.experience.create') }}">
                    <i class="ri-add-line"></i> New Experience
                </x-button>
            </div>
        </div>

        {{-- TIMELINE LIST --}}
        <div class="space-y-6 relative before:absolute before:inset-y-0 before:left-8 before:w-px before:bg-dark-800">
            @forelse ($experiences as $experience)
                <div class="relative pl-16 group">
                    <div class="absolute left-6 top-6 w-4 h-4 rounded-full bg-dark-950 border-2 border-neon-500 flex items-center justify-center shadow-neon-sm z-10 group-hover:scale-125 transition-transform">
                        <div class="w-1 h-1 rounded-full bg-neon-500"></div>
                    </div>
                    
                    <x-card padding="p-6" hover="true" class="relative">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                            <div class="space-y-4 flex-1">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h3 class="text-xl font-bold text-dark-100 group-hover:text-neon-400 transition-colors">{{ $experience->job_title }}</h3>
                                    <span class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded bg-dark-800 text-dark-400 border border-dark-700">
                                        {{ $experience->start_date }} — {{ $experience->is_current ? 'Present' : $experience->end_date }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-neon-500 font-medium">
                                    <i class="ri-building-line"></i>
                                    <span>{{ $experience->company }}</span>
                                </div>
                                <p class="text-dark-400 text-sm leading-relaxed max-w-3xl">{{ $experience->description }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($experience->trashed())
                                    <form action="{{ route('admin.experience.restore', $experience->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <x-button variant="neon" size="sm" type="submit" title="Restore Experience">
                                            <i class="ri-refresh-line"></i>
                                        </x-button>
                                    </form>
                                @else
                                    {{-- Note: ExperienceController had no edit method in mapping, using update route pattern --}}
                                    <x-button variant="ghost" size="sm" href="{{ route('admin.experience.edit', $experience) }}" title="Edit Experience">
                                        <i class="ri-edit-line"></i>
                                    </x-button>
                                    <form action="{{ route('admin.experience.destroy', $experience) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="secondary" size="sm" type="submit" title="Archive Experience">
                                            <i class="ri-archive-line"></i>
                                        </x-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </x-card>
                </div>
            @empty
                <div class="pl-16">
                    <div class="text-center py-24 bg-dark-900/50 border border-dark-800 rounded-2xl">
                        <div class="w-16 h-16 rounded-full bg-dark-800 border border-dark-700 flex items-center justify-center text-dark-600 mx-auto mb-4">
                            <i class="ri-briefcase-line text-3xl"></i>
                        </div>
                        <p class="text-dark-300 font-bold">No experience records found</p>
                        <p class="text-dark-500 text-sm mt-1">Start by adding your professional history to your portfolio.</p>
                        <x-button variant="neon" size="md" href="{{ route('admin.experience.create') }}" class="mt-6">
                            <i class="ri-add-line"></i> Add Experience
                        </x-button>
                    </div>
                </div>
            @endforelse
        </div>

        @if($experiences->hasPages())
            <div class="px-6 py-4 border-t border-dark-800 bg-dark-950/30 rounded-2xl">
                {{ $experiences->links() }}
            </div>
        @endif
    </div>
@endsection
