<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies,email',
            'website' => 'required|string|url|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Company name is required.',
            'name.max' => 'Company name may not be greater than 255 characters.',
            'email.required' => 'Company email is required.',
            'email.email' => 'Company email must be a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Company email may not be greater than 255 characters.',
            'website.required' => 'Company website is required.',
            'website.url' => 'Company website must be a valid URL.',
            'website.max' => 'Company website may not be greater than 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'company name',
            'email' => 'company email',
            'website' => 'company website',
        ];
    }
}
