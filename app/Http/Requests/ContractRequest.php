<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contract = $this->route('contract');
        $contractRefRule = $contract
            ? ['required', 'string', 'max:100', 'unique:contracts,contract_ref,'.$contract->id]
            : ['required', 'string', 'unique:contracts', 'max:100'];

        return [
            'contract_available' => ['nullable', 'string', 'in:yes,no'],
            'customer_name' => ['required', 'string', 'max:255'],
            'job_ref' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'contract_ref' => $contractRefRule,
            'start_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date', 'after:start_date'],
            'frequency' => ['nullable', 'string', 'max:50'],
            'last_ppm_date' => ['nullable', 'date'],
            'next_ppm_due' => ['nullable', 'date'],
            'contract_value' => ['nullable', 'numeric', 'min:0'],
            'customer_contact' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email'],
            'status' => ['nullable', 'string', 'in:upcoming,active,expiring,expired,cancelled,overdue'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Customer name is required.',
            'customer_name.max' => 'Customer name must not exceed 255 characters.',
            'job_ref.max' => 'Job reference must not exceed 100 characters.',
            'area.max' => 'Area must not exceed 100 characters.',
            'site_name.max' => 'Site name must not exceed 255 characters.',
            'contract_ref.required' => 'Contract reference is required.',
            'contract_ref.unique' => 'Contract reference already exists.',
            'contract_ref.max' => 'Contract reference must not exceed 100 characters.',
            'start_date.date' => 'Start date must be a valid date.',
            'expiry_date.date' => 'Expiry date must be a valid date.',
            'expiry_date.after' => 'Expiry date must be after start date.',
            'frequency.max' => 'Frequency must not exceed 50 characters.',
            'last_ppm_date.date' => 'Last PPM date must be a valid date.',
            'next_ppm_due.date' => 'Next PPM due date must be a valid date.',
            'contract_value.numeric' => 'Contract value must be a number.',
            'contract_value.min' => 'Contract value must be at least 0.',
            'customer_contact.max' => 'Customer contact must not exceed 255 characters.',
            'customer_email.email' => 'Please provide a valid email address.',
            'status.in' => 'Status must be one of: upcoming, active, expiring, expired, cancelled, overdue.',
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
