<?php

namespace App\Http\Requests\Projects;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique(Project::class)->ignore($this->route('id')),
            ],
            'description' => 'sometimes|string',
            'category' => 'sometimes|in:Web,App,Mobile,Script,Other',
            'tech_stack' => 'sometimes|array',
            'images' => 'sometimes|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:15360',
            'deleted_images' => 'nullable|array',
            'deleted_images.*' => 'required|string',
            'live_url' => 'sometimes|url',
            'repo_url' => 'sometimes|url',
            'is_featured' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer',
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
            'description.string' => 'Description must be a string.',

            // * Category validation
            'category.required' => 'Category is required.',
            'category.in' => 'The selected category is invalid. Allowed categories are: Web, App, Mobile, Script, Other.',

            // * Live URL validation
            'live_url.url' => 'Live URL must be a valid URL.',

            // * Repository URL validation
            'repo_url.url' => 'Repository URL must be a valid URL.',

            // * Images validation
            'images.*.mimes' => 'Image must be a valid image file format like: (jpeg, png, jpg, gif, webp).',
            'images.*.max' => 'Image size must not exceed 15MB.',

            // * Sort Order validation
            'sort_order.integer' => 'Sort Order must be a valid number.',

            // * Tech Stack validation
            'tech_stack.array' => 'Tech stack must be a valid list of items.',
        ];
    }
}
