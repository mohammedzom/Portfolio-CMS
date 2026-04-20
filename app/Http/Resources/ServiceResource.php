<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'tags' => $this->when($this->tags, $this->tags),
            'sort_order' => $this->sort_order,
            'deleted_at' => $this->deleted_at,
            'deleted_at_human' => $this->deleted_at?->diffForHumans(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
