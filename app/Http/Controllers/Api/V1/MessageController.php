<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Messages\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request): JsonResponse
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

        return $this->successResponse([
            'messages' => MessageResource::collection($messages),
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
        ], 'Messages retrieved successfully.');
    }

    public function show(string $id): JsonResponse
    {
        $message = Message::withoutTrashed()->findOrFail($id);
        $message->update(['read_at' => now()]);

        return $this->successResponse(
            new MessageResource($message),
            'Message retrieved successfully.'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $message = Message::withoutTrashed()->findOrFail($id);
        $message->delete();

        return $this->successResponse(
            [],
            'Message Archived successfully.'
        );
    }

    public function restore(string $id): JsonResponse
    {
        $message = Message::onlyTrashed()->findOrFail($id);
        $message->restore();

        return $this->successResponse(
            new MessageResource($message),
            'Message restored successfully.'
        );
    }

    public function markAsRead(string $id): JsonResponse
    {
        $message = Message::findOrFail($id);
        if ($message->read_at) {
            return $this->errorResponse(
                'Message is already read.',
            );
        }
        $message->read_at = now();
        $message->save();

        return $this->successResponse(
            [
                'read_at' => $message->read_at->diffForHumans(),
            ],
            'Message marked as read.'
        );
    }

    public function markAsUnread(string $id): JsonResponse
    {
        $message = Message::findOrFail($id);
        if (! $message->read_at) {
            return $this->errorResponse(
                'Message is already unread.'
            );
        }

        $message->read_at = null;
        $message->save();

        return $this->successResponse(
            [],
            'Message marked as unread.'
        );
    }

    public function forceDelete(string $id): JsonResponse
    {
        $message = Message::findOrFail($id);
        $message->forceDelete();

        return $this->successResponse(
            [],
            'Message deleted successfully.'
        );
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        Message::create($request->validated());

        return $this->successResponse(
            [],
            'Thank you for your message. I will get back to you as soon as possible.',
            201
        );
    }
}
