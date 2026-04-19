@extends('layouts.admin')

@section('page-title', 'Services')

@section('content')
<div class="sm:flex sm:items-center justify-between mb-8">
    <div class="sm:flex-auto">
        <h1 class="text-2xl font-black text-slate-900">Offered Services</h1>
        <p class="mt-2 text-sm text-slate-500">Manage the professional services you offer to your clients.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex gap-3">
        <a href="?archived={{ request('archived') ? '0' : '1' }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
            <i class="{{ request('archived') ? 'ri-eye-line' : 'ri-archive-line' }}"></i>
            {{ request('archived') ? 'View Active' : 'View Archived' }}
        </a>
        <a href="{{ route('admin.services.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-center text-sm font-bold text-white shadow-sm hover:bg-indigo-500 transition-all">
            <i class="ri-add-line"></i>
            Add Service
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($services as $service)
        <div class="group bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="h-12 w-12 flex-shrink-0 flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="{{ $service->icon ?? 'ri-service-line' }} text-2xl"></i>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    @if($service->trashed())
                        <form action="{{ route('admin.services.restore', $service->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Restore">
                                <i class="ri-refresh-line"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('admin.services.edit', $service) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit">
                            <i class="ri-pencil-line"></i>
                        </a>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">{{ $service->title }}</h3>
            <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed">{{ $service->description }}</p>
        </div>
    @empty
        <div class="col-span-full py-20 text-center bg-white rounded-2xl border border-slate-200">
            <p class="text-sm text-slate-400 font-medium">No services found.</p>
        </div>
    @endforelse
</div>

@if($services->hasPages())
    <div class="mt-8">
        {{ $services->links() }}
    </div>
@endif
@endsection
