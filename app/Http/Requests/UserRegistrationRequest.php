<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRegistrationRequest extends FormRequest
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

            'date_of_birth' => [
                'sometimes',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateString(),
            ],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'landline' => ['nullable', 'string', 'max:50'],
            'mobile_number' => [
                'required',
                'string',
                'max:255',
                'regex:/^\+63\d{10}$/',
                $this->route()->user
                    ? "unique:users,mobile_number," . $this->route()->user
                    : "unique:users,mobile_number"
            ],
            'civil_status' => ['required', Rule::in(['single', 'married', 'widowed', 'separated'])],
            'height' => ['nullable', 'numeric', 'min:50', 'max:300'],
            'religion' => ['nullable', 'string', 'max:255'],
            'resume' => ['file', 'mimes:pdf', 'max:2048', 'required'],

            'full_address' => ['required', 'string'],
            'province' => ['required', 'string', 'max:255'],
            'lgu' => ['required', 'string', 'max:255'],
            'barangay' => ['required', 'string', 'max:255'],

            'employment_status' => [
                'required_if:role_type,user',
                Rule::in(['employed', 'unemployed']),
            ],

            'employment_type' => [
                'nullable',
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
                'nullable',
                'required_if:role_type,user',
                'boolean',
            ],

            'is_former_ofw' => [
                'nullable',
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
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/',
                $this->route()->user
                    ? "unique:users,username," . $this->route()->user
                    : "unique:users,username"
            ],
            'email' => [
                'required',
                'email',
                $this->route()->user
                    ? "unique:users,email," . $this->route()->user
                    : "unique:users,email"
            ],
            'password' => ['sometimes', 'required', 'string', 'min:4'],

            // Skills (optional)
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ];
    }

    public function messages()
    {
        return [
            'mobile_number.regex' => 'Mobile number must be in format +63XXXXXXXXXX',
            'mobile_number.unique' => 'This mobile number is already taken',
            'resume.mimes' => 'Resume must be a PDF file',
            'resume.max' => 'Resume must not exceed 2MB',
            'date_of_birth.before_or_equal' => 'You must be at least 15 years old.',
            'skills.*.exists' => 'Selected skill is invalid.',
        ];
    }
}
