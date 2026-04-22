<?php

namespace App\Http\Requests\Skills;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('skills', 'name')->ignore($this->skill->id),
            ],
            'icon' => 'nullable|url',
            'proficiency' => 'required|integer|min:0|max:100',
            'type' => 'required|string',
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

            // Type
            'type.required' => 'Type is required.',
            'type.string' => 'Type must be a string.',
        ];
    }
}
