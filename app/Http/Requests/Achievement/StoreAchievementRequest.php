<?php

namespace App\Http\Requests\Achievement;

use Illuminate\Foundation\Http\FormRequest;

class StoreAchievementRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'date' => 'required|date',
            'url' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:255',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title field must be a string.',
            'title.max' => 'The title field must not be greater than 255 characters.',

            'issuer.required' => 'The issuer field is required.',
            'issuer.string' => 'The issuer field must be a string.',
            'issuer.max' => 'The issuer field must not be greater than 255 characters.',

            'date.required' => 'The date field is required.',
            'date.date' => 'The date field must be a date.',

            'url.url' => 'The url field must be a URL.',
            'url.max' => 'The url field must not be greater than 255 characters.',

            'description.string' => 'The description field must be a string.',
            'description.max' => 'The description field must not be greater than 255 characters.',

            'file.mimes' => 'The file must be a JPEG, PNG, JPG, PDF, DOC, or DOCX file.',
            'file.max' => 'The file must not be greater than 2MB.',
        ];
    }
}
