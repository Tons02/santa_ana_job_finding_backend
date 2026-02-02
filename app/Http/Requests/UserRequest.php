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
            'date_of_birth' => [
                'required',
                'date',
                'before_or_equal:' . now()->subYears(18)->toDateString(),
            ],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'landline' => ['nullable', 'string', 'max:50'],
            'civil_status' => ['required', Rule::in(['single', 'married', 'widowed', 'separated'])],
            'height' => ['nullable', 'numeric', 'min:50', 'max:300'],
            'religion' => ['nullable', 'string', 'max:255'],
            'region' => ['required', 'string', Rule::in(['REGION 3 (CENTRAL LUZON)'])],
            'province' => ['required', 'string', Rule::in(['PAMPANGA'])],
            'city_municipality' => ['required', 'string', Rule::in(['SANTA ANA'])],
            'barangay' => ['required', 'string', 'max:100'],
            'street_address' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_]+$/', // Optional: alphanumeric and underscore only
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
            // Skills (optional)
            'skills' => ['nullable', 'array'],
            'skills.*' => ['integer', 'exists:skills,id'],
        ];
    }
}
