<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:projects,slug',
            'description' => 'required|string',
            'category' => 'required|in:Web,App,Mobile,Script,Other',
            'tech_stack' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:15360',
            'live_url' => 'nullable|url',
            'repo_url' => 'nullable|url',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [

            // * Title validation
            'title.required' => 'Title is required.',
            'title.max' => 'Title cannot exceed 255 characters.',
            'title.string' => 'Title must be a string.',

            // * Slug validation
            'slug.unique' => 'Slug already exists.',
            'slug.max' => 'Slug cannot exceed 255 characters.',
            'slug.string' => 'Slug must be a string.',

            // * Description validation
            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot exceed 255 characters.',
            'description.string' => 'Description must be a string.',

            // * Category validation
            'category.required' => 'Category is required.',
            'category.in' => 'Category must be one of the following: (Web, App, Mobile, Script, Other).',
            'category.string' => 'Category must be a string.',

            // * Live URL validation
            'live_url.url' => 'Live URL must be a valid URL.',

            // * Repository URL validation
            'repo_url.url' => 'Repository URL must be a valid URL.',

            // * Image validation
            'images.*.mimes' => 'Image must be a valid image file format like: (jpeg, png, jpg, gif, webp).',
            'images.*.max' => 'Image size must not exceed 15MB.',

            // * Sort Order validation
            'sort_order.integer' => 'Sort Order must be a valid number.',
        ];
    }
}
