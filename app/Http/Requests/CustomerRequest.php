<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'job_ref' => ['nullable', 'string', 'max:100'],
            'job_details' => ['nullable', 'string'],
            'completion_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]*$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'job_notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'in:chain,new'],
            'region' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'name.max' => 'Customer name must not exceed 255 characters.',
            'job_ref.max' => 'Job reference must not exceed 100 characters.',
            'completion_date.date' => 'Completion date must be a valid date.',
            'phone.regex' => 'Phone number format is invalid.',
            'phone.max' => 'Phone number must not exceed 50 characters.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'category.in' => 'Category must be either chain or new.',
            'region.max' => 'Region must not exceed 100 characters.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
