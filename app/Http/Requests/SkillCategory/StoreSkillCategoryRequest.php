<?php

namespace App\Http\Requests\SkillCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:1|max:100|unique:skill_categories,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The :attribute is required.',
            'name.unique' => 'The :attribute already exists.',
            'name.min' => 'The :attribute must be at least :min characters.',
            'name.max' => 'The :attribute must be at most :max characters.',
            'name.string' => 'The :attribute must be a string.',
        ];
    }
}
