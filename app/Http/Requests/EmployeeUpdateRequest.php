<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $employeeId = $this->route('employee')->id ?? $this->route('employee');

        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('employees', 'email')->ignore($employeeId)
            ],
            'phone' => 'sometimes|string|max:255',
            'company_id' => 'sometimes|exists:companies,id',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.max' => 'Employee first name may not be greater than 255 characters.',
            'last_name.max' => 'Employee last name may not be greater than 255 characters.',
            'email.email' => 'Employee email must be a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Employee email may not be greater than 255 characters.',
            'phone.max' => 'Employee phone number may not be greater than 255 characters.',
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }

    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email address',
            'phone' => 'phone number',
            'company_id' => 'company',
        ];
    }
}