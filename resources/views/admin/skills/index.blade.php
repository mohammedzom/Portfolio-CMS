@extends('layouts.admin')

@section('title', 'Skills')
@section('page-title', 'Technical Skills')
@section('page-subtitle', 'Manage your technical expertise and proficiency levels')

@section('content')
    <div class="space-y-8">
        {{-- HEADER ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="{{ !request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.skills.index') }}">
                    Active <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Skill::withoutTrashed()->count() }}</span>
                </x-button>
                <x-button variant="{{ request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.skills.index', ['archived' => 'true']) }}">
                    Archived <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Skill::onlyTrashed()->count() }}</span>
                </x-button>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('admin.skills.index') }}" method="GET" class="relative group">
                    <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-dark-500 group-focus-within:text-neon-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search skills..." class="bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl pl-10 pr-4 py-2.5 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all w-64">
                </form>
                <x-button variant="neon" size="md" href="{{ route('admin.skills.create') }}">
                    <i class="ri-add-line"></i> New Skill
                </x-button>
            </div>
        </div>

        {{-- SKILLS GRID --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($skills as $skill)
                <x-card padding="p-6" hover="true" class="group relative overflow-hidden">
                    <div class="flex flex-col gap-6">
                        <div class="flex items-start justify-between">
                            <div class="w-12 h-12 rounded-xl bg-dark-800 border border-dark-700 flex items-center justify-center text-neon-400 text-2xl group-hover:bg-neon-500 group-hover:text-dark-950 group-hover:scale-110 transition-all duration-500">
                                <i class="{{ $skill->icon ?? 'ri-terminal-box-line' }}"></i>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($skill->trashed())
                                    <form action="{{ route('admin.skills.restore', $skill->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 rounded-lg bg-neon-500/10 text-neon-400 border border-neon-500/20 hover:bg-neon-500 hover:text-dark-950 transition-all">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                    </form>
                                @else
                                    <x-button variant="ghost" size="sm" href="{{ route('admin.skills.edit', $skill) }}" class="p-2">
                                        <i class="ri-edit-line"></i>
                                    </x-button>
                                    <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-lg font-bold text-dark-100 group-hover:text-neon-400 transition-colors">{{ $skill->name }}</h3>
                                <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">{{ $skill->type ?: 'Other' }}</p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-dark-400 font-medium">Proficiency</span>
                                    <span class="text-neon-400 font-bold">{{ $skill->proficiency }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-dark-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-neon-500 shadow-neon-sm transition-all duration-1000" style="width: {{ $skill->proficiency }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @empty
                <div class="col-span-full text-center py-24 bg-dark-900/50 border border-dark-800 rounded-2xl">
                    <div class="w-16 h-16 rounded-full bg-dark-800 border border-dark-700 flex items-center justify-center text-dark-600 mx-auto mb-4">
                        <i class="ri-bar-chart-box-line text-3xl"></i>
                    </div>
                    <p class="text-dark-300 font-bold">No skills found</p>
                    <p class="text-dark-500 text-sm mt-1">Add your first technical skill to showcase your expertise.</p>
                    <x-button variant="neon" size="md" href="{{ route('admin.skills.create') }}" class="mt-6">
                        <i class="ri-add-line"></i> Add Skill
                    </x-button>
                </div>
            @endforelse
        </div>

        @if($skills->hasPages())
            <div class="px-6 py-4 border-t border-dark-800 bg-dark-950/30 rounded-2xl">
                {{ $skills->links() }}
            </div>
        @endif
    </div>
@endsection
