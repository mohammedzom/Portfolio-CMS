<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('full_name', 'tagline', 'bio', 'avatar', 'cv_file', 'email', 'phone', 'location', 'github_url', 'linkedin_url', 'twitter_url', 'dribbble_url', 'years_experience', 'projects_count', 'clients_count', 'available_for_freelance')]
class SiteSettings extends Model
{
    protected function casts(): array
    {
        return [
            'years_experience' => 'integer',
            'projects_count' => 'integer',
            'clients_count' => 'integer',
            'available_for_freelance' => 'boolean',
        ];
    }
}
