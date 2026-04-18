<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('first_name', 'last_name', 'tagline', 'bio', 'avatar', 'cv_file', 'email', 'phone', 'location', 'social_links', 'languages', 'years_experience', 'projects_count', 'clients_count', 'available_for_freelance', 'url_prefix', 'url_suffix', 'about_me')]
class SiteSettings extends Model
{
    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'years_experience' => 'integer',
            'projects_count' => 'integer',
            'clients_count' => 'integer',
            'available_for_freelance' => 'boolean',
            'social_links' => 'array',
        ];
    }
}
