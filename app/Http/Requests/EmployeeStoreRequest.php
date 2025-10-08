<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employees,email',
            'phone' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
        ];
    }


    public function messages(): array
    {
        return [
            'first_name.required' => 'Employee first name is required.',
            'first_name.max' => 'Employee first name may not be greater than 255 characters.',
            'last_name.required' => 'Employee last name is required.',
            'last_name.max' => 'Employee last name may not be greater than 255 characters.',
            'email.required' => 'Employee email is required.',
            'email.email' => 'Employee email must be a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Employee email may not be greater than 255 characters.',
            'phone.required' => 'Employee phone number is required.',
            'phone.max' => 'Employee phone number may not be greater than 255 characters.',
            'company_id.required' => 'Company is required.',
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
