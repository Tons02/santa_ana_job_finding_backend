<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:50'],

            'date_of_birth' => ['required', 'date'],
            'gender' => ['required', Rule::in(['male', 'female'])],

            'landline' => ['nullable', 'string', 'max:50'],
            'civil_status' => ['required', Rule::in(['single', 'married', 'widowed', 'separated'])],
            'height' => ['nullable', 'numeric', 'min:50', 'max:300'],
            'religion' => ['nullable', 'string', 'max:255'],

            'full_address' => ['required', 'string'],
            'province' => ['required', 'string', 'max:255'],
            'lgu' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],

            'employment_status' => [
                'required_if:role_type,user',
                Rule::in(['employed', 'unemployed']),
            ],

            'employment_type' => [
                'required_if:role_type,user',
                Rule::in(['full_time', 'part_time', 'contract', 'internship']),
            ],

            'months_looking' => [
                'required_if:role_type,user',
                'nullable',
                'integer',
                'min:0',
                'max:60',
            ],

            'is_ofw' => [
                'required_if:role_type,user',
                'boolean',
            ],

            'is_former_ofw' => [
                'required_if:role_type,user',
                'boolean',
            ],

            'last_deployment' => [
                'nullable',
                'string',
                'max:255',
            ],

            'return_date' => [
                'nullable',
                'date',
            ],

            'email' => ['required', 'email', $this->route()->user
                    ? "unique:users,email," . $this->route()->user
                    : "unique:users,email",],
            'password' => ['sometimes','required', 'string', 'min:8'],

            'role_type' => ['required', Rule::in(['admin'])],

            // Skills (optional)
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ];
    }
}
