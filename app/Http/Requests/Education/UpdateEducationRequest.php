<?php

namespace App\Http\Requests\Education;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEducationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'degree' => 'sometimes|string|max:255',
            'institution' => 'sometimes|string|max:255',
            'field_of_study' => 'sometimes|string|max:255',
            'start_year' => 'sometimes|integer|min:1950|max:'.(date('Y') + 1),
            'end_year' => 'nullable|integer|after_or_equal:start_year',
            'gpa' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'degree.string' => 'The degree must be a string.',
            'degree.max' => 'The degree may not be greater than 255 characters.',

            'institution.string' => 'The institution must be a string.',
            'institution.max' => 'The institution may not be greater than 255 characters.',

            'field_of_study.string' => 'The field of study must be a string.',
            'field_of_study.max' => 'The field of study may not be greater than 255 characters.',

            'start_year.integer' => 'The start year must be an integer.',
            'start_year.min' => 'The start year must be at least 1950.',
            'start_year.max' => 'The start year may not be greater than :max.',

            'end_year.integer' => 'The end year must be an integer.',
            'end_year.after_or_equal' => 'The end year must be after or equal to the start year.',

            'gpa.numeric' => 'The GPA must be a number.',
            'gpa.min' => 'The GPA must be at least 0.',
            'gpa.max' => 'The GPA may not be greater than 100.',

            'description.string' => 'The description must be a string.',
        ];
    }
}
