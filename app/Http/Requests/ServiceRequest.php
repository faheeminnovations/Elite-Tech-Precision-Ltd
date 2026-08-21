<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $service = $this->route('service');
        $uniqueRule = $service
            ? ['required', 'string', 'unique:services,job_ref,'.$service->id]
            : ['required', 'string', 'unique:services'];

        return [
            'job_ref' => array_merge($uniqueRule, ['max:100']),
            'customer_name' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'in:' . implode(',', \App\Models\Customer::AREAS)],
            'service_type' => ['required', 'in:PPM,Repair,Installation,Service Call,Inspection'],
            'engineer_id' => ['nullable', 'exists:users,id'],
            'engineer_name' => ['nullable', 'string', 'max:255'],
            'visit_date' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,in-progress,completed,cancelled'],
            'site_installation' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'job_details' => ['nullable', 'string'],
            'work_completed' => ['nullable', 'string'],
            'job_notes' => ['nullable', 'string'],
            'recommendations' => ['nullable', 'string'],
            'remedial_required' => ['nullable', 'in:yes,no'],
            'remedial_details' => ['nullable', 'string'],
            'next_ppm_due' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'job_ref.required' => 'Job reference is required.',
            'job_ref.unique' => 'Job reference already exists.',
            'job_ref.max' => 'Job reference must not exceed 100 characters.',
            'customer_name.required' => 'Customer name is required.',
            'customer_name.max' => 'Customer name must not exceed 255 characters.',
            'area.in' => 'Selected area is invalid.',
            'service_type.required' => 'Service type is required.',
            'service_type.in' => 'Service type must be one of: PPM, Repair, Installation, Service Call, Inspection.',
            'engineer_id.exists' => 'Selected engineer does not exist.',
            'visit_date.date' => 'Visit date must be a valid date.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be one of: scheduled, in-progress, completed, cancelled.',
            'site_installation.max' => 'Site installation must not exceed 255 characters.',
            'remedial_required.in' => 'Remedial required must be either yes or no.',
            'next_ppm_due.date' => 'Next PPM due date must be a valid date.',
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
