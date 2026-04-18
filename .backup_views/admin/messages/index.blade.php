@extends('admin.layout')
@section('title', 'Messages')
@section('page-title', 'Messages')
@section('page-subtitle', 'Contact messages from visitors')

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
        <form action="{{ route('admin.messages.index') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative flex-1 sm:flex-initial">
                <i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-dark-500"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search messages..."
                    class="w-full sm:w-64 glass rounded-xl border border-dark-700 pl-10 pr-4 py-2.5 text-sm focus:border-neon-500/40 transition-colors">
            </div>
            <button type="submit" class="p-2.5 rounded-xl glass border border-dark-700 text-neon-500 hover:bg-neon-500/10 transition-all">
                <i class="ri-filter-3-line"></i>
            </button>
        </form>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.messages.index', ['archived' => request('archived') ? '0' : '1']) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl glass border border-dark-700 text-sm font-medium hover:border-neon-500/40 transition-all">
                <i class="ri-archive-line"></i>
                {{ request('archived') ? 'View Active' : 'View Archived' }}
            </a>
        </div>
    </div>

    {{-- Messages List --}}
    <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-dark-700 bg-dark-800/30">
                        <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-6 py-4">Sender</th>
                        <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-4 hidden md:table-cell">Subject</th>
                        <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-4 hidden lg:table-cell">Message</th>
                        <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-4">Status</th>
                        <th class="text-left text-xs font-semibold text-dark-500 uppercase tracking-wider px-4 py-4">Date</th>
                        <th class="px-4 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-dark-700">
                    @forelse ($messages as $message)
                        <tr class="hover:bg-dark-800/40 transition-colors group {{ !$message->is_read ? 'bg-neon-500/5' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full gradient-neon flex items-center justify-center text-dark-950 text-sm font-bold shrink-0">
                                        {{ strtoupper(substr($message->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-dark-100 font-medium text-sm">{{ $message->name }}</p>
                                        <p class="text-dark-500 text-xs">{{ $message->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell">
                                <p class="text-dark-300 text-sm font-medium">{{ $message->subject }}</p>
                            </td>
                            <td class="px-4 py-4 hidden lg:table-cell">
                                <p class="text-dark-400 text-sm line-clamp-2 max-w-md">{{ Str::limit($message->message, 80) }}</p>
                            </td>
                            <td class="px-4 py-4">
                                @if ($message->is_read)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full bg-dark-700 text-dark-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-dark-500"></span>
                                        Read
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full bg-neon-500/15 text-neon-400">
                                        <span class="w-1.5 h-1.5 rounded-full bg-neon-500" style="box-shadow: 0 0 6px oklch(0.66 0.17 195);"></span>
                                        New
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="text-dark-400 text-sm">{{ $message->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.messages.show', $message) }}"
                                        class="p-2 rounded-lg text-dark-400 hover:text-neon-400 hover:bg-neon-500/10 transition-all" title="View">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                    @if ($message->trashed())
                                        <form action="{{ route('admin.messages.restore', $message) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="p-2 rounded-lg text-green-400 hover:bg-green-500/10 transition-all" title="Restore">
                                                <i class="ri-refresh-line"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Permanently delete this message?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-lg text-red-400 hover:bg-red-500/10 transition-all" title="Delete Permanently">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Archive this message?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 rounded-lg text-dark-400 hover:text-red-400 hover:bg-red-500/10 transition-all" title="Archive">
                                                <i class="ri-archive-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <i class="ri-inbox-line text-5xl text-dark-600 mb-4"></i>
                                <p class="text-dark-400 font-medium">No messages found</p>
                                <p class="text-dark-600 text-sm mt-1">Messages from visitors will appear here</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($messages->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $messages->links() }}
        </div>
    @endif
@endsection
