<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // Personal Info
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            // Introduction
            'tagline' => 'required|string|max:255', // e.g. "Software Engineer"
            'bio' => 'required|string|max:255',

            // Contact Info
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max=15360',
            'cv_file' => 'required|file|mimes:pdf,doc,docx|max=15360',
            'email' => 'required|email|max:255',
            'phone' => 'required|phone:AUTO,AR,US,GB', // International phone format
            'location' => 'required|string',

            // Social Links
            'social_links' => 'nullable|array', // array of objects {name: string, url: string}
            'social_links.*.name' => 'required|string|max:255',
            'social_links.*.url' => 'required|url|max:255',

            // Languages
            'languages' => 'nullable|array', // array of objects {name: string, level: string}
            'languages.*.name' => 'required|string|max:255',
            'languages.*.level' => 'required|string|max:255',

            // About Section
            'years_experience' => 'required|integer',
            'projects_count' => 'required|integer',
            'clients_count' => 'required|integer',
            'available_for_freelance' => 'required|boolean',
            'about_me' => 'required|string',

            // URLs
            'url_prefix' => 'required|string|max:255',
            'url_suffix' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            // Personal Info
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',

            // Introduction
            'tagline.required' => 'Tagline is required.',
            'bio.required' => 'Bio is required.',
            'bio.max' => 'Bio cannot be longer than 255 characters.',

            // Contact Info
            'avatar.required' => 'Avatar is required.',
            'avatar.image' => 'Avatar must be an image.',
            'avatar.mimes' => 'Avatar must be a JPEG, PNG, GIF or JPG image.',
            'avatar.max' => 'Avatar cannot be larger than 15MB.',

            'cv_file.required' => 'CV file is required.',
            'cv_file.file' => 'CV file must be a file.',
            'cv_file.mimes' => 'CV file must be a PDF, DOC, or DOCX file.',
            'cv_file.max' => 'CV file cannot be larger than 15MB.',

            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot be longer than 255 characters.',

            'phone.required' => 'Phone is required.',

            'location.required' => 'Location is required.',

            // Social Links
            'social_links.required' => 'Social links is required.',
            'social_links.array' => 'Social links must be an array.',
            'social_links.*.name.required' => 'Social link name is required.',
            'social_links.*.name.max' => 'Social link name cannot be longer than 255 characters.',
            'social_links.*.url.required' => 'Social link URL is required.',
            'social_links.*.url.url' => 'Social link URL must be a valid URL.',
            'social_links.*.url.max' => 'Social link URL cannot be longer than 255 characters.',

            // Languages
            'languages.required' => 'Languages is required.',
            'languages.array' => 'Languages must be an array.',
            'languages.*.name.required' => 'Language name is required.',
            'languages.*.name.max' => 'Language name cannot be longer than 255 characters.',
            'languages.*.level.required' => 'Language level is required.',
            'languages.*.level.max' => 'Language level cannot be longer than 255 characters.',

            // About Section
            'years_experience.required' => 'Years experience is required.',
            'years_experience.integer' => 'Years experience must be an integer.',

            'projects_count.required' => 'Projects count is required.',
            'projects_count.integer' => 'Projects count must be an integer.',

            'clients_count.required' => 'Clients count is required.',
            'clients_count.integer' => 'Clients count must be an integer.',

            'available_for_freelance.required' => 'Available for freelance is required.',
            'available_for_freelance.boolean' => 'Available for freelance must be a boolean.',

            'about_me.required' => 'About me is required.',

            // URLs
            'url_prefix.required' => 'URL prefix is required.',
            'url_prefix.max' => 'URL prefix cannot be longer than 255 characters.',

            'url_suffix.required' => 'URL suffix is required.',
            'url_suffix.max' => 'URL suffix cannot be longer than 255 characters.',
        ];
    }
}
