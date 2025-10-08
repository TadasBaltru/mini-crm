<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $companyId = $this->route('company')->id ?? $this->route('company');

        return [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('companies', 'email')->ignore($companyId)
            ],
            'website' => 'sometimes|string|url|max:255',
        ];
    }


    public function messages(): array
    {
        return [
            'name.max' => 'Company name may not be greater than 255 characters.',
            'email.email' => 'Company email must be a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Company email may not be greater than 255 characters.',
            'website.url' => 'Company website must be a valid URL.',
            'website.max' => 'Company website may not be greater than 255 characters.',
        ];
    }


    public function attributes(): array
    {
        return [
            'name' => 'company name',
            'email' => 'company email',
            'website' => 'company website',
        ];
    }
}