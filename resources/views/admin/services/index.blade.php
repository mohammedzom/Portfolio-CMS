@extends('layouts.admin')

@section('title', 'Services')
@section('page-title', 'Offered Services')
@section('page-subtitle', 'Manage the services you offer to clients')

@section('content')
    <div class="space-y-8">
        {{-- HEADER ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="{{ !request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.services.index') }}">
                    Active <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Service::withoutTrashed()->count() }}</span>
                </x-button>
                <x-button variant="{{ request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.services.index', ['archived' => 'true']) }}">
                    Archived <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Service::onlyTrashed()->count() }}</span>
                </x-button>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('admin.services.index') }}" method="GET" class="relative group">
                    <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-dark-500 group-focus-within:text-neon-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search services..." class="bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl pl-10 pr-4 py-2.5 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all w-64">
                </form>
                <x-button variant="neon" size="md" href="{{ route('admin.services.create') }}">
                    <i class="ri-add-line"></i> New Service
                </x-button>
            </div>
        </div>

        {{-- SERVICES GRID --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($services as $service)
                <x-card padding="p-6" hover="true" class="group relative overflow-hidden">
                    <div class="space-y-6">
                        <div class="flex items-start justify-between">
                            <div class="w-14 h-14 rounded-2xl bg-dark-800 border border-dark-700 flex items-center justify-center text-neon-400 text-3xl group-hover:bg-neon-500 group-hover:text-dark-950 group-hover:scale-110 transition-all duration-500">
                                <i class="{{ $service->icon ?? 'ri-stack-line' }}"></i>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($service->trashed())
                                    <form action="{{ route('admin.services.restore', $service->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 rounded-lg bg-neon-500/10 text-neon-400 border border-neon-500/20 hover:bg-neon-500 hover:text-dark-950 transition-all">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                    </form>
                                @else
                                    <x-button variant="ghost" size="sm" href="{{ route('admin.services.edit', $service) }}" class="p-2">
                                        <i class="ri-edit-line"></i>
                                    </x-button>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-dark-100 group-hover:text-neon-400 transition-colors">{{ $service->title }}</h3>
                                <span class="text-[10px] font-mono text-dark-500">#{{ str_pad($service->sort_order, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <p class="text-dark-400 text-sm leading-relaxed line-clamp-3">{{ $service->description }}</p>
                        </div>

                        @if($service->tags)
                            <div class="flex flex-wrap gap-2 pt-2 border-t border-dark-800">
                                @foreach(is_array($service->tags) ? $service->tags : explode(',', $service->tags) as $tag)
                                    <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-1 rounded-lg bg-dark-950 text-dark-500 border border-dark-800">{{ trim($tag) }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </x-card>
            @empty
                <div class="col-span-full text-center py-24 bg-dark-900/50 border border-dark-800 rounded-2xl">
                    <div class="w-16 h-16 rounded-full bg-dark-800 border border-dark-700 flex items-center justify-center text-dark-600 mx-auto mb-4">
                        <i class="ri-service-line text-3xl"></i>
                    </div>
                    <p class="text-dark-300 font-bold">No services listed yet</p>
                    <p class="text-dark-500 text-sm mt-1">Start by adding your first service to your professional profile.</p>
                    <x-button variant="neon" size="md" href="{{ route('admin.services.create') }}" class="mt-6">
                        <i class="ri-add-line"></i> Add Service
                    </x-button>
                </div>
            @endforelse
        </div>

        @if($services->hasPages())
            <div class="px-6 py-4 border-t border-dark-800 bg-dark-950/30 rounded-2xl">
                {{ $services->links() }}
            </div>
        @endif
    </div>
@endsection
