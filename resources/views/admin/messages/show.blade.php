@extends('layouts.admin')

@section('page-title', 'View Message')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
            <i class="ri-arrow-left-line"></i>
            Back to Messages
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-8 py-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-lg">
                    {{ strtoupper(substr($message->name, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">{{ $message->name }}</h3>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ $message->email }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Received On</p>
                <p class="text-sm font-bold text-slate-900">{{ $message->created_at->format('M d, Y - H:i') }}</p>
            </div>
        </div>
        
        <div class="p-8 space-y-8">
            <div>
                <p class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-2">Subject</p>
                <h4 class="text-xl font-bold text-slate-900">{{ $message->subject }}</h4>
            </div>

            <div class="bg-slate-50 rounded-2xl p-8 border border-slate-100">
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Message Content</p>
                <div class="text-slate-700 leading-relaxed whitespace-pre-wrap font-medium">
                    {{ $message->message }}
                </div>
            </div>

            <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                <div class="flex items-center gap-4">
                    @if($message->trashed())
                        <form action="{{ route('admin.messages.restore', $message->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-2 text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                                <i class="ri-refresh-line text-lg"></i>
                                Restore Message
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this message?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 text-sm font-bold text-red-500 hover:text-red-700 transition-colors">
                            <i class="ri-delete-bin-line"></i>
                            {{ $message->trashed() ? 'Delete Permanently' : 'Archive Message' }}
                        </button>
                    </form>
                </div>

                <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 transition-all">
                    <i class="ri-reply-line"></i>
                    Reply via Email
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
