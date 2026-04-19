@extends('layouts.admin')

@section('page-title', 'Messages')

@section('content')
<div class="sm:flex sm:items-center justify-between mb-8">
    <div class="sm:flex-auto">
        <h1 class="text-2xl font-black text-slate-900">Contact Messages</h1>
        <p class="mt-2 text-sm text-slate-500">View and manage messages sent by visitors through your portfolio contact form.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <a href="?archived={{ request('archived') ? '0' : '1' }}" class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
            <i class="{{ request('archived') ? 'ri-eye-line' : 'ri-archive-line' }}"></i>
            {{ request('archived') ? 'View Active' : 'View Archived' }}
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th scope="col" class="py-4 pl-6 pr-3 text-left text-xs font-black uppercase tracking-widest text-slate-500">Sender</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-500">Subject</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-500">Date</th>
                    <th scope="col" class="px-3 py-4 text-left text-xs font-black uppercase tracking-widest text-slate-500">Status</th>
                    <th scope="col" class="relative py-4 pl-3 pr-6 text-right">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($messages as $message)
                    <tr class="hover:bg-slate-50 transition-colors group {{ !$message->is_read ? 'bg-indigo-50/30' : '' }}">
                        <td class="whitespace-nowrap py-5 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-sm">
                                    {{ strtoupper(substr($message->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $message->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $message->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-5">
                            <div class="text-sm font-medium text-slate-700 line-clamp-1">{{ $message->subject }}</div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-5 text-xs font-bold text-slate-500 uppercase tracking-widest">
                            {{ $message->created_at->diffForHumans() }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-5">
                            @if(!$message->is_read)
                                <span class="inline-flex items-center rounded-lg bg-indigo-100 px-2 py-1 text-[10px] font-black text-indigo-600 border border-indigo-200 uppercase tracking-widest">
                                    New
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-black text-slate-500 border border-slate-200 uppercase tracking-widest">
                                    Read
                                </span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.messages.show', $message) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="View">
                                    <i class="ri-eye-line text-lg"></i>
                                </a>
                                @if($message->trashed())
                                    <form action="{{ route('admin.messages.restore', $message->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Restore">
                                            <i class="ri-refresh-line text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300">
                                    <i class="ri-mail-line text-3xl"></i>
                                </div>
                                <p class="text-sm text-slate-400 font-medium">No messages yet.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
