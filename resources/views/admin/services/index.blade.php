@extends('admin.layout')
@section('title', 'Services')
@section('page-title', 'Services Management')
@section('page-subtitle', 'Manage your professional services and offerings')

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
        <form action="{{ route('admin.services.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:flex-initial">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-dark-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search services..."
                    class="w-full sm:w-64 glass rounded-xl border border-dark-700 pl-10 pr-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
            </div>
            <button type="submit" class="p-2.5 rounded-xl glass border border-dark-700 text-neon-500 hover:bg-neon-500/10 transition-all">
                <i class="ri-filter-3-line"></i>
            </button>
        </form>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.services.index', ['archived' => request('archived') ? '0' : '1']) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl glass border border-dark-700 text-sm font-medium hover:border-neon-500/40 transition-all">
                <i class="ri-archive-line"></i>
                {{ request('archived') ? 'View Active' : 'View Archived' }}
            </a>
            <a href="{{ route('admin.services.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl gradient-neon text-dark-950 text-sm font-bold hover:neon-glow transition-all">
                <i class="ri-add-line"></i> Add Service
            </a>
        </div>
    </div>

    {{-- Services Grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse ($services as $service)
            <div class="group glass rounded-2xl border border-dark-700 p-5 hover:border-neon-500/30 transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        @if ($service->icon)
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-neon-500/10">
                                <img src="{{ $service->icon }}" alt="{{ $service->title }}" class="w-6 h-6 object-contain">
                            </div>
                        @else
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center gradient-neon">
                                <i class="ri-service-line text-dark-950 text-lg"></i>
                            </div>
                        @endif
                        <div>
                            <h3 class="font-display font-semibold text-dark-100">{{ $service->title }}</h3>
                            <span class="text-xs text-dark-500">Order: {{ $service->sort_order }}</span>
                        </div>
                    </div>
                    @if ($service->trashed())
                        <span class="text-xs px-2 py-1 rounded-full bg-red-500/10 text-red-400 font-medium">Archived</span>
                    @endif
                </div>

                {{-- Description Preview --}}
                <p class="text-dark-400 text-sm mb-4 line-clamp-2">{{ $service->description }}</p>

                {{-- Tags --}}
                @if ($service->tags && count($service->tags) > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach (array_slice($service->tags, 0, 3) as $tag)
                            <span class="text-xs px-2 py-1 rounded-full bg-dark-700 text-dark-300">{{ $tag }}</span>
                        @endforeach
                        @if (count($service->tags) > 3)
                            <span class="text-xs px-2 py-1 rounded-full bg-dark-700 text-dark-400">+{{ count($service->tags) - 3 }}</span>
                        @endif
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-4 border-t border-dark-700">
                    @if ($service->trashed())
                        <form action="{{ route('admin.services.restore', $service) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-green-500/10 text-green-400 hover:bg-green-500/20 transition-all">
                                <i class="ri-refresh-line"></i> Restore
                            </button>
                        </form>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                            onsubmit="return confirm('Permanently delete this service?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition-all">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.services.edit', $service) }}"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-neon-500/10 text-neon-400 hover:bg-neon-500/20 transition-all">
                            <i class="ri-pencil-line"></i> Edit
                        </a>
                        <a href="{{ route('admin.services.show', $service) }}"
                            class="p-2 rounded-lg text-dark-400 hover:text-neon-400 hover:bg-neon-500/10 transition-all">
                            <i class="ri-eye-line"></i>
                        </a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                            onsubmit="return confirm('Archive this service?');">
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
                    <i class="ri-service-line text-5xl text-dark-600 mb-4"></i>
                    <p class="text-dark-400 font-medium">No services found</p>
                    <p class="text-dark-600 text-sm mt-1">Start by adding your first service</p>
                    <a href="{{ route('admin.services.create') }}"
                        class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl gradient-neon text-dark-950 text-sm font-bold hover:neon-glow transition-all">
                        <i class="ri-add-line"></i> Add Your First Service
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($services->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $services->links() }}
        </div>
    @endif
@endsection
