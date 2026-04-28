<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Messages\DestroyMessageAction;
use App\Actions\Messages\ForceDeleteMessageAction;
use App\Actions\Messages\MarkMessageReadAction;
use App\Actions\Messages\MarkMessageUnreadAction;
use App\Actions\Messages\RestoreMessageAction;
use App\Actions\Messages\StoreMessageAction;
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

        if ($request->boolean('archived')) {
            $query->onlyTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%')
                    ->orWhere('body', 'like', '%'.$search.'%');
            });
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

    public function show(Message $message): JsonResponse
    {
        $message = MarkMessageReadAction::run($message, false);

        return $this->successResponse(
            new MessageResource($message),
            'Message retrieved successfully.'
        );
    }

    public function destroy(Message $message): JsonResponse
    {
        DestroyMessageAction::run($message);

        return $this->successResponse(
            [],
            'Message Archived successfully.'
        );
    }

    public function markAsRead(Message $message): JsonResponse
    {
        $message = MarkMessageReadAction::run($message);

        return $this->successResponse(
            [
                'read_at' => $message->read_at->diffForHumans(),
            ],
            'Message marked as read.'
        );
    }

    public function markAsUnread(Message $message): JsonResponse
    {
        MarkMessageUnreadAction::run($message);

        return $this->successResponse(
            [],
            'Message marked as unread.'
        );
    }

    public function store(StoreMessageRequest $request): JsonResponse
    {
        StoreMessageAction::run($request->validated());

        return $this->successResponse(
            [],
            'Thank you for your message. I will get back to you as soon as possible.',
            201
        );
    }

    public function restore(Message $message): JsonResponse
    {
        $message = RestoreMessageAction::run($message);

        return $this->successResponse(
            new MessageResource($message),
            'Message restored successfully.'
        );
    }

    public function forceDelete(Message $message): JsonResponse
    {
        ForceDeleteMessageAction::run($message);

        return $this->successResponse(
            [],
            'Message deleted permanently.'
        );
    }
}
