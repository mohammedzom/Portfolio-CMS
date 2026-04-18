<?php

namespace App\Http\Controllers;

use App\Http\Requests\Messages\StoreMessageRequest;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $query = Message::query();

        if ($request->has('archived') && $request->archived) {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
                ->orWhere('subject', 'like', '%'.$request->search.'%')
                ->orWhere('message', 'like', '%'.$request->search.'%');
        }

        $messages = $query->orderBy('is_read', 'asc')->latest()->paginate(10);

        return view('admin.messages.index', compact('messages'));
    }

    public function show(Message $message)
    {
        $message->update(['is_read' => true, 'read_at' => now()]);

        return view('admin.messages.show', compact('message'));
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted.');
    }

    public function restore(Message $message)
    {
        $message->restore();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message restored.');
    }

    public function markAsRead(Message $message)
    {
        $message->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message marked as read.');
    }

    public function markAsUnread(Message $message)
    {
        $message->update(['is_read' => false, 'read_at' => null]);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Message marked as unread.');
    }

    public function store(StoreMessageRequest $request)
    {
        Message::create($request->validated());

        return redirect()->route('home', ['#contact'])
            ->with('success', 'Thank you for your message. I will get back to you as soon as possible.');
    }
}
