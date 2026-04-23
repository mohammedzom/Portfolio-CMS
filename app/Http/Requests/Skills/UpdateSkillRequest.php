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
                'sometimes',
                'string',
                'max:255',
                Rule::unique('skills', 'name')->ignore($this->id),
            ],
            'icon' => 'sometimes|url',
            'proficiency' => 'sometimes|integer|min:0|max:100',
            'type' => 'sometimes|string',
        ];
    }

    public function messages(): array
    {
        return [
            // Name
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot be longer than 255 characters.',
            'name.unique' => 'Skill name already exists.',

            // Icon
            'icon.url' => 'Icon must be a valid URL.',

            // Proficiency
            'proficiency.integer' => 'Proficiency must be an integer.',
            'proficiency.min' => 'Proficiency cannot be less than 0.',
            'proficiency.max' => 'Proficiency cannot be more than 100.',

            // Type
            'type.string' => 'Type must be a string.',
        ];
    }
}
