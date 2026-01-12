<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicationSingleGetDisplayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the authenticated user
        $authUser = auth()->user();

        // Get the JobApplication from route parameter
        $jobApplication = $this->route('job_application');

        // Check if user is authenticated
        if (!$authUser) {
            return false;
        }

        return $authUser->id === $jobApplication->user_id
            || $authUser->role_type === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the validation error message when authorization fails.
     */
}
