<?php

namespace App\Http\Requests\Experiences;

use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            // job title
            'job_title.required' => 'Job title is required.',
            'job_title.max' => 'Job title cannot be longer than 255 characters.',

            // company
            'company.required' => 'Company is required.',
            'company.max' => 'Company cannot be longer than 255 characters.',

            // description
            'description.required' => 'Description is required.',
            'description.max' => 'Description cannot be longer than 255 characters.',

            // start date
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',

            // end date
            'end_date.date' => 'End date must be a valid date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',

            // is current
            'is_current.required' => 'Is current is required.',
            'is_current.boolean' => 'Is current must be a boolean.',
        ];
    }
}
