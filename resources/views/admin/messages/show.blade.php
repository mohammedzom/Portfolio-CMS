@extends('admin.layout')
@section('title', 'Message Details')
@section('page-title', $message->subject)
@section('page-subtitle', 'Message details and conversation')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Back Button --}}
        <a href="{{ route('admin.messages.index') }}"
            class="inline-flex items-center gap-2 text-dark-400 hover:text-neon-500 transition-colors mb-6">
            <i class="ri-arrow-left-line"></i> Back to Messages
        </a>

        {{-- Message Detail Card --}}
        <div class="glass rounded-2xl border border-dark-700 overflow-hidden">
            {{-- Header --}}
            <div class="p-6 border-b border-dark-700">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full gradient-neon flex items-center justify-center text-dark-950 text-lg font-bold">
                            {{ strtoupper(substr($message->name, 0, 1)) }}
                        </div>
                        <div>
                            <h1 class="font-display font-bold text-xl text-dark-100">{{ $message->subject }}</h1>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-sm text-dark-400">From: <span class="text-dark-300">{{ $message->name }}</span></span>
                                <span class="text-dark-600">•</span>
                                <span class="text-sm text-dark-400">{{ $message->email }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($message->is_read)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full bg-dark-700 text-dark-400">
                                <i class="ri-checkbox-circle-line"></i> Read
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full bg-neon-500/15 text-neon-400">
                                <i class="ri-notification-badge-line"></i> New
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="p-6 space-y-6">
                {{-- Message Body --}}
                <div class="glass rounded-xl border border-dark-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm text-dark-400">Message Content</span>
                        <span class="text-xs text-dark-500">{{ $message->created_at->format('M d, Y - h:i A') }}</span>
                    </div>
                    <div class="text-dark-200 leading-relaxed whitespace-pre-wrap">{{ $message->message }}</div>
                </div>

                {{-- Meta Information --}}
                <div class="grid sm:grid-cols-3 gap-4">
                    <div class="glass rounded-xl border border-dark-700 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ri-calendar-line text-neon-500"></i>
                            <span class="text-sm text-dark-400">Received</span>
                        </div>
                        <p class="text-dark-200 font-medium">{{ $message->created_at->diffForHumans() }}</p>
                        <p class="text-dark-500 text-xs mt-0.5">{{ $message->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="glass rounded-xl border border-dark-700 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ri-time-line text-neon-500"></i>
                            <span class="text-sm text-dark-400">Read At</span>
                        </div>
                        @if ($message->read_at)
                            <p class="text-dark-200 font-medium">{{ $message->read_at->diffForHumans() }}</p>
                            <p class="text-dark-500 text-xs mt-0.5">{{ $message->read_at->format('M d, Y - h:i A') }}</p>
                        @else
                            <p class="text-dark-500 text-sm italic">Not read yet</p>
                        @endif
                    </div>
                    <div class="glass rounded-xl border border-dark-700 p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <i class="ri-shield-keyhole-line text-neon-500"></i>
                            <span class="text-sm text-dark-400">Status</span>
                        </div>
                        @if ($message->trashed())
                            <p class="text-red-400 font-medium">Archived</p>
                        @else
                            <p class="text-green-400 font-medium">Active</p>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4 pt-6 border-t border-dark-700">
                    @if (!$message->is_read)
                        <form action="{{ route('admin.messages.mark-as-read', $message) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl gradient-neon text-dark-950 font-bold hover:neon-glow transition-all">
                                <i class="ri-checkbox-circle-line"></i> Mark as Read
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.messages.mark-as-unread', $message) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl glass border border-dark-700 text-dark-300 font-medium hover:bg-dark-700/50 transition-all">
                                <i class="ri-mail-unread-line"></i> Mark as Unread
                            </button>
                        </form>
                    @endif
                    
                    @if ($message->trashed())
                        <form action="{{ route('admin.messages.restore', $message) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="px-6 py-3.5 rounded-xl glass border border-dark-700 text-green-400 font-medium hover:bg-green-500/10 transition-all">
                                <i class="ri-refresh-line"></i> Restore
                            </button>
                        </form>
                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST"
                            onsubmit="return confirm('Permanently delete this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-6 py-3.5 rounded-xl glass border border-dark-700 text-red-400 font-medium hover:bg-red-500/10 transition-all">
                                <i class="ri-delete-bin-line"></i> Delete Permanently
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.messages.destroy', $message) }}" method="POST"
                            onsubmit="return confirm('Archive this message?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-6 py-3.5 rounded-xl glass border border-dark-700 text-dark-300 font-medium hover:bg-red-500/10 hover:text-red-400 transition-all">
                                <i class="ri-archive-line"></i> Archive
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.messages.index') }}"
                        class="px-6 py-3.5 rounded-xl glass border border-dark-700 text-dark-300 font-medium hover:bg-dark-700/50 transition-all">
                        Close
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
