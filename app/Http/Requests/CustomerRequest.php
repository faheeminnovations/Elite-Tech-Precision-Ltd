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
        // Get customer ID for update operations
        $customer = $this->route('customer');
        $customerId = ($customer && is_object($customer)) ? $customer->id : (is_numeric($customer) ? $customer : null);

        // Email validation with unique constraint
        $emailRule = ['nullable', 'email', 'max:255'];
        if ($customerId) {
            $emailRule[] = 'unique:customers,email,' . $customerId;
        } else {
            $emailRule[] = 'unique:customers,email';
        }

        // Job Ref validation with unique constraint
        $jobRefRule = ['nullable', 'string', 'max:100'];
        if ($customerId) {
            $jobRefRule[] = 'unique:customers,job_ref,' . $customerId;
        } else {
            $jobRefRule[] = 'unique:customers,job_ref';
        }

        // Phone validation with unique constraint
        $phoneRule = ['nullable', 'string', 'max:50', 'regex:/^[0-9+\-\s()]*$/'];
        if ($customerId) {
            $phoneRule[] = 'unique:customers,phone,' . $customerId;
        } else {
            $phoneRule[] = 'unique:customers,phone';
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'job_ref' => $jobRefRule,
            'job_details' => ['nullable', 'string'],
            'completion_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string'],
            'phone' => $phoneRule,
            'email' => $emailRule,
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
            'job_ref.unique' => 'This job reference is already in use by another customer.',
            'completion_date.date' => 'Completion date must be a valid date.',
            'phone.regex' => 'Phone number format is invalid.',
            'phone.max' => 'Phone number must not exceed 50 characters.',
            'phone.unique' => 'This phone number is already in use by another customer.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email must not exceed 255 characters.',
            'email.unique' => 'This email is already in use by another customer.',
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
