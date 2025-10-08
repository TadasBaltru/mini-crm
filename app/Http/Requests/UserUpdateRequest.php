<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

 
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? $this->route('user');

        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => 'sometimes|string|min:8|confirmed',
            'role' => ['sometimes', Rule::in(['admin', 'company'])],
            'company_id' => 'nullable|exists:companies,id',
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Ensure admin users don't have a company_id
            if ($this->input('role') === 'admin' && $this->input('company_id')) {
                $validator->errors()->add('company_id', 'Admin users cannot be assigned to a company.');
            }

            // Ensure company users have a company_id
            if ($this->input('role') === 'company' && !$this->input('company_id')) {
                $validator->errors()->add('company_id', 'Company users must be assigned to a company.');
            }
        });
    }


    public function messages(): array
    {
        return [
            'name.max' => 'User name may not be greater than 255 characters.',
            'email.email' => 'User email must be a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'User email may not be greater than 255 characters.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.in' => 'User role must be either admin or company.',
            'company_id.exists' => 'The selected company does not exist.',
        ];
    }


    public function attributes(): array
    {
        return [
            'name' => 'user name',
            'email' => 'email address',
            'password' => 'password',
            'role' => 'user role',
            'company_id' => 'company',
        ];
    }
}