<?php

namespace App\Http\Requests\SkillCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'min:1',
                'max:100',
                Rule::unique('skill_categories', 'name')->ignore($this->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'The :attribute already exists.',
            'name.min' => 'The :attribute must be at least :min characters.',
            'name.max' => 'The :attribute must be at most :max characters.',
            'name.string' => 'The :attribute must be a string.',
        ];
    }
}
