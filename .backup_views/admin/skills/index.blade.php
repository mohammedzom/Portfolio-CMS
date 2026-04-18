@extends('admin.layout')
@section('title', 'Skills')
@section('page-title', 'Skills Management')
@section('page-subtitle', 'Manage your technical skills and proficiencies')

@section('content')
    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 rounded-xl border border-green-500/30 bg-green-500/10 flex items-center gap-3">
            <i class="ri-checkbox-circle-line text-green-400 text-xl"></i>
            <span class="text-green-300 text-sm font-medium">{{ session('success') }}</span>
            <button @click="show = false" class="ml-auto text-green-400 hover:text-green-300">
                <i class="ri-close-line"></i>
            </button>
        </div>
    @endif

    {{-- Action Bar --}}
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <form action="{{ route('admin.skills.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:flex-initial">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-dark-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search skills..."
                    class="w-full sm:w-64 glass rounded-xl border border-dark-700 pl-10 pr-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
            </div>
            <select name="category" onchange="this.form.submit()"
                class="glass rounded-xl border border-dark-700 px-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
                <option value="">All Categories</option>
                <option value="Frontend" {{ request('category') == 'Frontend' ? 'selected' : '' }}>Frontend</option>
                <option value="Backend" {{ request('category') == 'Backend' ? 'selected' : '' }}>Backend</option>
                <option value="Full Stack" {{ request('category') == 'Full Stack' ? 'selected' : '' }}>Full Stack</option>
                <option value="Design" {{ request('category') == 'Design' ? 'selected' : '' }}>Design</option>
                <option value="DevOps" {{ request('category') == 'DevOps' ? 'selected' : '' }}>DevOps</option>
                <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <button type="submit" class="p-2.5 rounded-xl glass border border-dark-700 text-neon-500 hover:bg-neon-500/10 transition-all">
                <i class="ri-filter-3-line"></i>
            </button>
        </form>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.skills.index', ['archived' => request('archived') ? '0' : '1']) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl glass border border-dark-700 text-sm font-medium hover:border-neon-500/40 transition-all">
                <i class="ri-archive-line"></i>
                {{ request('archived') ? 'View Active' : 'View Archived' }}
            </a>
            <a href="{{ route('admin.skills.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl gradient-neon text-dark-950 text-sm font-bold hover:neon-glow transition-all">
                <i class="ri-add-line"></i> Add Skill
            </a>
        </div>
    </div>

    {{-- Skills Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($skills as $skill)
            <div class="group glass rounded-2xl border border-dark-700 p-5 hover:border-neon-500/30 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        @if ($skill->icon)
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                style="background: {{ $skill->color ?? 'oklch(0.66 0.17 195 / 0.1)' }};">
                                <img src="{{ $skill->icon }}" alt="{{ $skill->name }}" class="w-6 h-6 object-contain">
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center gradient-neon">
                                <i class="ri-code-s-slash-line text-dark-950 text-lg"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-display font-semibold text-dark-100">{{ $skill->name }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-dark-700 text-dark-400">{{ $skill->type }}</span>
                        </div>
                    </div>
                    @if ($skill->trashed())
                        <span class="text-xs px-2 py-1 rounded-full bg-red-500/10 text-red-400 font-medium">Archived</span>
                    @endif
                </div>

                {{-- Proficiency Bar --}}
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-dark-400">Proficiency</span>
                        <span class="text-xs font-semibold" 
                            style="color: {{ $skill->proficiency >= 80 ? 'oklch(0.72 0.18 160)' : ($skill->proficiency >= 60 ? 'oklch(0.66 0.17 195)' : 'oklch(0.58 0.22 290)') }};">
                            {{ $skill->proficiency }}% - {{ $skill->level }}
                        </span>
                    </div>
                    <div class="h-2 rounded-full bg-dark-700 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500"
                            style="width: {{ $skill->proficiency }}%; background: linear-gradient(90deg, {{ $skill->color ?? 'oklch(0.66 0.17 195)' }}, {{ $skill->color ?? 'oklch(0.60 0.15 220)' }});">
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-4 border-t border-dark-700">
                    @if ($skill->trashed())
                        <form action="{{ route('admin.skills.restore', $skill) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-green-500/10 text-green-400 hover:bg-green-500/20 transition-all">
                                <i class="ri-refresh-line"></i> Restore
                            </button>
                        </form>
                        <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST"
                            onsubmit="return confirm('Permanently delete this skill?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition-all">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.skills.edit', $skill) }}"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-neon-500/10 text-neon-400 hover:bg-neon-500/20 transition-all">
                            <i class="ri-pencil-line"></i> Edit
                        </a>
                        <a href="{{ route('admin.skills.show', $skill) }}"
                            class="p-2 rounded-lg text-dark-400 hover:text-neon-400 hover:bg-neon-500/10 transition-all">
                            <i class="ri-eye-line"></i>
                        </a>
                        <form action="{{ route('admin.skills.destroy', $skill) }}" method="POST"
                            onsubmit="return confirm('Archive this skill?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-500/10 transition-all">
                                <i class="ri-archive-line"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="glass rounded-2xl border border-dark-700 p-12 text-center">
                    <i class="ri-bar-chart-2-line text-5xl text-dark-600 mb-4"></i>
                    <p class="text-dark-400 font-medium">No skills found</p>
                    <p class="text-dark-600 text-sm mt-1">Start by adding your first skill</p>
                    <a href="{{ route('admin.skills.create') }}"
                        class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl gradient-neon text-dark-950 text-sm font-bold hover:neon-glow transition-all">
                        <i class="ri-add-line"></i> Add Your First Skill
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($skills->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $skills->links() }}
        </div>
    @endif
@endsection
