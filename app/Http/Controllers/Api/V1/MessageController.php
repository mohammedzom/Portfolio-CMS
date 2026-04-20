<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Messages\StoreMessageRequest;
use App\Http\Resources\MessageResource;
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

        $messages = $query->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Messages retrieved successfully',
            'data' => MessageResource::collection($messages),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'total' => $messages->total(),
                'per_page' => $messages->perPage(),
            ],
            'paginationLinks' => [
                'self' => $messages->url($messages->currentPage()),
                'first' => $messages->url(1),
                'last' => $messages->url($messages->lastPage()),
                'prev' => $messages->previousPageUrl(),
                'next' => $messages->nextPageUrl(),
            ],
        ]);
    }

    public function show(string $id)
    {
        $message = Message::withoutTrashed()->findOrFail($id);
        $message->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Message retrieved successfully',
            'data' => new MessageResource($message),
        ]);
    }

    public function destroy(string $id)
    {
        $message = Message::withoutTrashed()->findOrFail($id);
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
            'data' => [],
        ]);
    }

    public function restore(string $id)
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->restore();

        return response()->json([
            'success' => true,
            'message' => 'Message restored successfully',
            'data' => new MessageResource($message),
        ]);
    }

    public function markAsRead(string $id)
    {
        $message = Message::findOrFail($id);
        if ($message->read_at) {
            return response()->json([
                'success' => false,
                'message' => 'Message is already read',
                'data' => [
                    'read_at' => $message->read_at->diffForHumans(),
                ],
            ]);
        }
        $message->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as read',
            'data' => [
                'read_at' => $message->read_at->diffForHumans(),
            ],
        ]);
    }

    public function markAsUnread(string $id)
    {
        $message = Message::findOrFail($id);

        $message->update(['read_at' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Message marked as unread',
            'data' => [],
        ]);
    }

    public function forceDelete(string $id)
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Message permanently deleted successfully',
            'data' => [],
        ]);
    }

    public function store(StoreMessageRequest $request)
    {
        $message = Message::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your message. I will get back to you as soon as possible.',
            'data' => [],
        ], 201);
    }
}
