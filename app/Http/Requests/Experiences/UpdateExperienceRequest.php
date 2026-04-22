<?php

namespace App\Http\Requests\Experiences;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExperienceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'job_title' => 'sometimes|string|max:255',
            'company' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|integer',
            'end_date' => 'required_if:is_current,false,0|nullable|integer|after:start_date',
            'is_current' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            // job title
            'job_title.max' => 'Job title cannot be longer than 255 characters.',

            // company
            'company.max' => 'Company cannot be longer than 255 characters.',

            // description
            'description.max' => 'Description cannot be longer than 255 characters.',

            // start date
            'start_date.integer' => 'Start date must be a valid year.',

            // end date
            'end_date.required_if' => 'End date is required if is current is false.',
            'end_date.integer' => 'End date must be a valid year.',
            'end_date.after' => 'End date must be after start date.',

            // is current
            'is_current.boolean' => 'Is current must be a boolean.',
        ];
    }
}
