@extends('layouts.admin')

@section('title', 'View Message')
@section('page-title', 'Message Details')
@section('page-subtitle', 'Inquiry from ' . $message->name)

@section('content')
    <div class="max-w-4xl mx-auto space-y-8">
        <div class="flex items-center justify-between">
            <x-button variant="secondary" size="sm" href="{{ route('admin.messages.index') }}">
                <i class="ri-arrow-left-line"></i> Back to Inbox
            </x-button>
            <div class="flex items-center gap-3">
                @if(!$message->is_read)
                    <form action="{{ route('admin.messages.mark-as-read', $message) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-button variant="neon" size="sm" type="submit">
                            <i class="ri-checkbox-circle-line"></i> Mark as Read
                        </x-button>
                    </form>
                @else
                    <form action="{{ route('admin.messages.mark-as-unread', $message) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <x-button variant="secondary" size="sm" type="submit">
                            <i class="ri-mail-line"></i> Mark as Unread
                        </x-button>
                    </form>
                @endif
                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-button variant="danger" size="sm" type="submit">
                        <i class="ri-delete-bin-line"></i> Trash
                    </x-button>
                </form>
            </div>
        </div>

        <x-card padding="p-0" class="overflow-hidden">
            <div class="bg-dark-950/50 border-b border-dark-800 p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-neon-500 to-emerald-600 flex items-center justify-center text-dark-950 font-black text-2xl shadow-neon-sm">
                            {{ strtoupper(substr($message->name, 0, 1)) }}
                        </div>
                        <div class="space-y-1">
                            <h2 class="text-2xl font-black text-dark-100">{{ $message->name }}</h2>
                            <p class="text-neon-400 font-medium text-sm flex items-center gap-2">
                                <i class="ri-mail-line"></i> {{ $message->email }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Received On</p>
                        <p class="text-dark-200 font-bold">{{ $message->created_at->format('M d, Y') }}</p>
                        <p class="text-dark-500 text-xs">{{ $message->created_at->format('H:i (T)') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-8">
                <div class="space-y-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Subject</p>
                    <h3 class="text-xl font-bold text-dark-100">{{ $message->subject }}</h3>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] font-black uppercase tracking-widest text-dark-500">Message Body</p>
                    <div class="bg-dark-950/30 border border-dark-800 rounded-2xl p-6 text-dark-300 leading-relaxed whitespace-pre-wrap">
                        {{ $message->message }}
                    </div>
                </div>

                <div class="pt-8 border-t border-dark-800 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-dark-500">Status:</span>
                        @if(!$message->is_read)
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-neon-500/10 text-neon-400 border border-neon-500/20">
                                <span class="w-1 h-1 rounded-full bg-neon-500 shadow-neon-sm"></span> Unread
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-dark-800 text-dark-500 border border-dark-700">
                                Read on {{ $message->read_at->format('M d, Y H:i') }}
                            </span>
                        @endif
                    </div>
                    
                    <x-button variant="neon" size="md" href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}">
                        <i class="ri-reply-line"></i> Reply via Email
                    </x-button>
                </div>
            </div>
        </x-card>
    </div>
@endsection
