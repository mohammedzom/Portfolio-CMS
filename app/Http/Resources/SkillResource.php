<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'proficiency' => $this->proficiency,
            'icon' => $this->icon,
            'color' => $this->color,
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'deleted_at_human' => $this->deleted_at?->diffForHumans(),
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
