<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->category,
            'tech_stack' => $this->tech_stack,
            'images' => $this->images,
            'live_url' => $this->live_url,
            'repo_url' => $this->repo_url,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'deleted_at_human' => $this->deleted_at?->diffForHumans(),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
