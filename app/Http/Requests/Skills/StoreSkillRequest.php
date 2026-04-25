<?php

namespace App\Http\Requests\Skills;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:skills,name',
            'icon' => 'nullable|url',
            'proficiency' => 'required|integer|min:0|max:100',
            'skill_category_id' => 'required|exists:skill_categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot be longer than 255 characters.',
            'name.unique' => 'Skill name already exists.',

            // Icon
            'icon.url' => 'Icon must be a valid URL.',

            // Proficiency
            'proficiency.required' => 'Proficiency is required.',
            'proficiency.integer' => 'Proficiency must be an integer.',
            'proficiency.min' => 'Proficiency cannot be less than 0.',
            'proficiency.max' => 'Proficiency cannot be more than 100.',

            // Skill Category
            'skill_category_id.required' => 'Skill category is required.',
            'skill_category_id.exists' => 'Skill category does not exist.',
        ];
    }
}
