<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'body' => $this->body,
            'is_read' => $this->read_at !== null,
            'read_at_human' => $this->read_at?->diffForHumans(),
            'read_at' => $this->read_at?->format('Y-m-d H:i'),
            'deleted_at_human' => $this->deleted_at?->diffForHumans(),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
