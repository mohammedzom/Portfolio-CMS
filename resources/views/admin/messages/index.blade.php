@extends('layouts.admin')

@section('title', 'Messages')
@section('page-title', 'Contact Messages')
@section('page-subtitle', 'Manage inquiries from your portfolio visitors')

@section('content')
    <div class="space-y-8">
        {{-- HEADER ACTIONS --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex flex-wrap items-center gap-3">
                <x-button variant="{{ !request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.messages.index') }}">
                    Inbox <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Message::withoutTrashed()->count() }}</span>
                </x-button>
                <x-button variant="{{ request('archived') ? 'neon' : 'secondary' }}" size="sm" href="{{ route('admin.messages.index', ['archived' => 'true']) }}">
                    Trash <span class="ml-2 text-[10px] font-black opacity-50">{{ \App\Models\Message::onlyTrashed()->count() }}</span>
                </x-button>
            </div>

            <div class="flex items-center gap-4">
                <form action="{{ route('admin.messages.index') }}" method="GET" class="relative group">
                    <i class="ri-search-line absolute left-4 top-1/2 -translate-y-1/2 text-dark-500 group-focus-within:text-neon-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search messages..." class="bg-dark-900 border-dark-800 text-dark-100 text-sm rounded-xl pl-10 pr-4 py-2.5 placeholder:text-dark-600 focus:outline-none focus:border-neon-500/50 focus:ring-1 focus:ring-neon-500/20 transition-all w-64">
                </form>
            </div>
        </div>

        {{-- MESSAGES LIST --}}
        <x-card padding="p-0" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-dark-950/50 border-b border-dark-800">
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Sender</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Subject</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500">Date</th>
                            <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-dark-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-dark-800">
                        @forelse ($messages as $message)
                            <tr class="hover:bg-dark-800/30 transition-colors group {{ !$message->is_read ? 'bg-neon-500/[0.02]' : '' }}">
                                <td class="px-6 py-4">
                                    @if(!$message->is_read)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-neon-500/10 text-neon-400 border border-neon-500/20">
                                            <span class="w-1 h-1 rounded-full bg-neon-500 shadow-neon-sm"></span> New
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-dark-800 text-dark-500 border border-dark-700">
                                            Read
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-dark-100 group-hover:text-neon-400 transition-colors">{{ $message->name }}</span>
                                        <span class="text-[10px] text-dark-500 font-medium">{{ $message->email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col max-w-[300px]">
                                        <span class="text-sm font-medium text-dark-200 truncate">{{ $message->subject }}</span>
                                        <span class="text-[10px] text-dark-500 truncate">{{ $message->message }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs text-dark-400">{{ $message->created_at->format('M d, Y') }}</span>
                                    <p class="text-[10px] text-dark-600 font-medium">{{ $message->created_at->format('H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($message->trashed())
                                            <form action="{{ route('admin.messages.restore', $message->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <x-button variant="neon" size="sm" type="submit" title="Restore Message">
                                                    <i class="ri-refresh-line"></i>
                                                </x-button>
                                            </form>
                                            <form action="{{ route('admin.messages.destroy', $message->id) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete this message?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="danger" size="sm" type="submit" title="Delete Permanently">
                                                    <i class="ri-delete-bin-2-line"></i>
                                                </x-button>
                                            </form>
                                        @else
                                            <x-button variant="ghost" size="sm" href="{{ route('admin.messages.show', $message) }}" title="View Message">
                                                <i class="ri-eye-line"></i>
                                            </x-button>
                                            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="secondary" size="sm" type="submit" title="Move to Trash">
                                                    <i class="ri-delete-bin-line"></i>
                                                </x-button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-24 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="w-16 h-16 rounded-full bg-dark-900 border border-dark-800 flex items-center justify-center text-dark-700">
                                            <i class="ri-mail-line text-3xl"></i>
                                        </div>
                                        <div class="space-y-1">
                                            <p class="text-dark-300 font-bold">Inbox is empty</p>
                                            <p class="text-dark-500 text-sm">When visitors contact you from your portfolio, their messages will appear here.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($messages->hasPages())
                <div class="px-6 py-4 border-t border-dark-800 bg-dark-950/30">
                    {{ $messages->links() }}
                </div>
            @endif
        </x-card>
    </div>
@endsection
