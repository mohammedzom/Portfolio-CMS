<?php

namespace App\Http\Resources\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchievementResource extends JsonResource
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
            'issuer' => $this->issuer,
            'date' => $this->date,
            'url' => $this->url,
            'description' => $this->description,
            'certificate_url' => $this->certificate_url ? asset('storage/'.$this->certificate_url) : null,
        ];
    }
}
