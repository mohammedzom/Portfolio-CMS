<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingsResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name.' '.$this->last_name,
            'tagline' => $this->tagline,
            'bio' => $this->bio,
            'about_me' => $this->about_me,
            'avatar' => $this->avatar ? asset('storage/'.$this->avatar) : null,
            'cv_file' => $this->cv_file ? asset('storage/'.$this->cv_file) : null,
            'url_prefix' => $this->url_prefix,
            'url_suffix' => $this->url_suffix,
            'languages' => $this->languages,
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,
            'social_links' => $this->social_links,
            'years_experience' => $this->years_experience,
            'projects_count' => $this->projects_count,
            'clients_count' => $this->clients_count,
            'available_for_freelance' => $this->available_for_freelance,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
