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
            'region' => ['required', 'string', Rule::in(['REGION 3 (CENTRAL LUZON)'])],
            'province' => ['required', 'string', Rule::in(['PAMPANGA'])],
            'city_municipality' => ['required', 'string', Rule::in(['SANTA ANA'])],
            'barangay' => ['required', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'height' => ['nullable', 'numeric', 'min:50', 'max:300'],
            'religion' => ['nullable', 'string', Rule::in(['roman_catholic', 'islam', 'iglesia_ni_cristo', 'born_again', 'baptist', 'seventh_day_adventist'])],
            'resume' => ['file', 'mimes:pdf', 'max:5120', 'nullable'],
            'employment_status' => [
                'required',
                Rule::in(['employed', 'unemployed']),
            ],
            'employment_type' => [
                'nullable',
                Rule::in(['full_time', 'part_time', 'contract', 'internship']),
                'required_if:employment_status,employed',
                'prohibited_if:employment_status,unemployed',
            ],
            'months_looking' => [
                'required',
                'nullable',
                'integer',
                'min:0',
                'max:60',
            ],
            'is_4ps' => [
                'nullable',
                'required',
                'boolean',
            ],
            'is_pwd' => [
                'nullable',
                'required',
                'boolean',
            ],
            'disability' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_ofw' => [
                'nullable',
                'required',
                'boolean',
            ],
            'work_experience' => [
                'nullable',
                'string',
                'max:255',
            ],
            'is_former_ofw' => [
                'nullable',
                'required',
                'boolean',
            ],
            'country' => [
                'nullable',
                'string',
                'max:255',
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
            'transaction_date' => [
                'nullable',
                'date',
            ],
            'event' => [
                'nullable',
                'string',
                'max:255',
            ],
            'program_service' => [
                'nullable',
                'string',
                'max:255',
            ],
            'transaction_reference' => [
                'nullable',
                'string',
                'max:255',
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
            'password' => ['sometimes', 'required', 'string', 'min:6'],

            // Skills (optional)
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id', 'distinct'],

            // Preferred Positions (optional)
            'preferred_positions' => ['nullable', 'array'],
            'preferred_positions.*' => ['integer', 'exists:preferred_positions,id'],

            // Courses (optional)
            'courses' => ['nullable', 'array'],
            'courses.*.education_level' => ['string', 'required'],
            'courses.*.name' => ['string', 'required'],
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
