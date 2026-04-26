<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingstResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            // Personal
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name.' '.$this->last_name,
            'tagline' => $this->tagline,
            'bio' => $this->bio,
            'about_me' => $this->about_me,
            'avatar' => $this->avatar ?? null,
            'cv_file' => $this->cv_file ?? null,
            'cv_file_name' => $this->cv_file_name,
            'url_prefix' => $this->url_prefix,
            'url_suffix' => $this->url_suffix,
            'languages' => $this->languages,

            // Contact
            'email' => $this->email,
            'phone' => $this->phone,
            'location' => $this->location,

            // Social links
            'social_links' => $this->social_links,

            // Stats
            'years_experience' => $this->years_experience,
            'projects_count' => $this->projects_count,
            'clients_count' => $this->clients_count,

            // Availability
            'available_for_freelance' => $this->available_for_freelance,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
