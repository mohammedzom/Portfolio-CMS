<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Personal Info
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',

            // Introduction
            'tagline' => 'sometimes|string|max:255', // e.g. "Software Engineer"
            'bio' => 'sometimes|string|max:255',

            // Contact Info
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max=15360',
            'cv_file' => 'sometimes|file|mimes:pdf,doc,docx|max=15360',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|phone:AUTO,AR,US,GB', // International phone format
            'location' => 'sometimes|string',

            // Social Links
            'social_links' => 'nullable|array', // array of objects {name: string, url: string}
            'social_links.*.name' => 'sometimes|string|max:255',
            'social_links.*.url' => 'sometimes|url|max:255',

            // Languages
            'languages' => 'nullable|array', // array of objects {name: string, level: string}
            'languages.*.name' => 'sometimes|string|max:255',
            'languages.*.level' => 'sometimes|string|max:255',

            // About Section
            'years_experience' => 'sometimes|integer',
            'projects_count' => 'sometimes|integer',
            'clients_count' => 'sometimes|integer',
            'available_for_freelance' => 'sometimes|boolean',
            'about_me' => 'sometimes|string',

            // URLs
            'url_prefix' => 'sometimes|string|max:255',
            'url_suffix' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [

            // Introduction
            'bio.max' => 'Bio cannot be longer than 255 characters.',

            // Contact Info            'avatar.image' => 'Avatar must be an image.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, GIF or JPG image.',
            'avatar.max' => 'Avatar cannot be larger than 15MB.',

            'cv_file.file' => 'CV file must be a file.',
            'cv_file.mimes' => 'CV file must be a PDF, DOC, or DOCX file.',
            'cv_file.max' => 'CV file cannot be larger than 15MB.',

            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot be longer than 255 characters.',

            // Social Links
            'social_links.array' => 'Social links must be an array.',
            'social_links.*.name.sometimes' => 'Social link name is sometimes.',
            'social_links.*.name.max' => 'Social link name cannot be longer than 255 characters.',
            'social_links.*.url.sometimes' => 'Social link URL is sometimes.',
            'social_links.*.url.url' => 'Social link URL must be a valid URL.',
            'social_links.*.url.max' => 'Social link URL cannot be longer than 255 characters.',

            // Languages            'languages.array' => 'Languages must be an array.',
            'languages.*.name.max' => 'Language name cannot be longer than 255 characters.',
            'languages.*.level.max' => 'Language level cannot be longer than 255 characters.',

            // About Section
            'years_experience.integer' => 'Years experience must be an integer.',

            'projects_count.integer' => 'Projects count must be an integer.',
            'clients_count.integer' => 'Clients count must be an integer.',

            'available_for_freelance.sometimes' => 'Available for freelance is sometimes.',
            'available_for_freelance.boolean' => 'Available for freelance must be a boolean.',

            // URLs
            'url_prefix.max' => 'URL prefix cannot be longer than 255 characters.',

            'url_suffix.max' => 'URL suffix cannot be longer than 255 characters.',
        ];
    }
}
