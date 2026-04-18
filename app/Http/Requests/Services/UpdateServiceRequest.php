<?php

namespace App\Http\Requests\Services;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'required|url|max:255',
            'sort_order' => 'required|integer',
            'tags' => 'nullable|array',
            'tags.*.string' => 'Tag must be a string.',
        ];
    }

    public function messages(): array
    {
        return [
            // title
            'title.required' => 'Title is required.',
            'title.max' => 'Title cannot be longer than 255 characters.',

            // description
            'description.required' => 'Description is required.',

            // icon
            'icon.required' => 'Icon is required.',
            'icon.url' => 'Icon must be a valid URL.',
            'icon.max' => 'Icon cannot be longer than 255 characters.',

            // sort order
            'sort_order.required' => 'Display Order is required.',
            'sort_order.integer' => 'Display Order must be an integer.',

            // tags
            'tags.array' => 'Tags must be an array.',
            'tags.*.string' => 'Tag must be a string.',
        ];
    }
}
