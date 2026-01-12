<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the authenticated user
        $authUser = auth()->user();

        // Get the user from route parameter
        $userToUpdate = $this->route('user');

        return $authUser && (
            $authUser->id === $userToUpdate->id
            // || $authUser->role_type === 'admin' // Uncomment if admins can update any user
        );
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            // Personal Information
            'first_name' => ['sometimes', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:50'],

            'date_of_birth' => [
                'sometimes',
                'date',
                'before_or_equal:' . now()->subYears(15)->toDateString(),
            ],
            'gender' => ['sometimes', Rule::in(['male', 'female'])],
            'landline' => ['nullable', 'string', 'max:50'],
            'mobile_number' => [
                'nullable',
                'regex:/^\+63\d{10}$/',
                $userId ? "unique:users,mobile_number,{$userId}" : 'unique:users,mobile_number',
            ],
            'civil_status' => ['sometimes', Rule::in(['single', 'married', 'widowed', 'separated'])],
            'height' => ['nullable', 'numeric', 'min:50', 'max:300'],
            'religion' => ['nullable', 'string', 'max:255'],

            // Resume - optional on update
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:2048'],

            // Address
            'full_address' => ['sometimes', 'string'],
            'province' => ['sometimes', 'string', 'max:255'],
            'lgu' => ['sometimes', 'string', 'max:255'],
            'barangay' => ['sometimes', 'string', 'max:255'],

            // Employment Information
            'employment_status' => [
                'sometimes',
                Rule::in(['employed', 'unemployed']),
            ],

            'employment_type' => [
                'nullable',
                Rule::in(['full_time', 'part_time', 'contract', 'internship']),
            ],

            'months_looking' => [
                'nullable',
                'integer',
                'min:0',
                'max:60',
            ],

            // OFW Information
            'is_ofw' => ['nullable', 'boolean'],
            'is_former_ofw' => ['nullable', 'boolean'],
            'last_deployment' => ['nullable', 'string', 'max:255'],
            'return_date' => ['nullable', 'date'],

            // Skills (optional)
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ];
    }

    /**
     * Custom messages for validation errors
     */
    public function messages(): array
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
