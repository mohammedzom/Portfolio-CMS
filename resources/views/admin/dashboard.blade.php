@extends('layouts.admin')

@section('page-title', 'Overview')

@section('content')
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
    {{-- Stats Cards --}}
    @php
        $stats = [
            ['label' => 'Total Projects', 'value' => $projectsCount, 'icon' => 'ri-folder-open-line', 'color' => 'indigo'],
            ['label' => 'Total Skills', 'value' => $skillsCount, 'icon' => 'ri-medal-line', 'color' => 'emerald'],
            ['label' => 'New Messages', 'value' => $messagesCountnew, 'icon' => 'ri-mail-unread-line', 'color' => 'amber'],
            ['label' => 'Total Messages', 'value' => $messagesCount, 'icon' => 'ri-mail-line', 'color' => 'rose'],
        ];
    @endphp

    @foreach($stats as $stat)
        <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-200 group hover:shadow-md transition-all duration-300">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-slate-50 p-3 text-slate-600 group-hover:scale-110 transition-transform">
                    <i class="{{ $stat['icon'] }} text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-black text-slate-900">{{ $stat['value'] }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-2">
    {{-- Recent Messages --}}
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-mail-line text-indigo-600"></i>
                Recent Messages
            </h3>
            <a href="{{ route('admin.messages.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700">View All</a>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse($messages as $message)
                <div class="px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-bold text-slate-900">{{ $message->name }}</span>
                        <span class="text-[10px] font-medium text-slate-400">{{ $message->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-slate-500 line-clamp-1">{{ $message->message }}</p>
                </div>
            @empty
                <div class="px-6 py-10 text-center">
                    <p class="text-sm text-slate-400">No messages yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="ri-flashlight-line text-amber-500"></i>
                Quick Actions
            </h3>
        </div>
        <div class="p-6 grid grid-cols-2 gap-4">
            <a href="{{ route('admin.projects.create') }}" class="flex flex-col items-center justify-center gap-3 p-6 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-100 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-indigo-600 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="ri-add-line text-2xl"></i>
                </div>
                <span class="text-xs font-bold text-slate-700 group-hover:text-indigo-600">New Project</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="flex flex-col items-center justify-center gap-3 p-6 rounded-2xl border border-slate-100 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-100 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-slate-600 shadow-sm group-hover:scale-110 transition-transform">
                    <i class="ri-settings-line text-2xl"></i>
                </div>
                <span class="text-xs font-bold text-slate-700 group-hover:text-indigo-600">Site Settings</span>
            </a>
        </div>
    </div>
</div>
@endsection
