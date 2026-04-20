<?php

namespace App\Http\Requests\Messages;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            // name
            'name.required' => 'Name is required.',
            'name.max' => 'Name cannot be longer than 255 characters.',

            // email
            'email.required' => 'Email is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email cannot be longer than 255 characters.',

            // subject
            'subject.required' => 'Subject is required.',
            'subject.max' => 'Subject cannot be longer than 255 characters.',

            // body
            'body.required' => 'Body is required.',
        ];
    }
}
