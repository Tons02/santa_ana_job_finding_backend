<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicationDisplayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the authenticated user
        $authUser = auth()->user();

        // Check if user is authenticated
        if (!$authUser) {
            return false;
        }

        // Get the JobApplication from route parameter (if exists)
        $jobApplication = $this->route('job_application');

        // Check if there's a user_id query/request parameter
        $requestUserId = $this->input('user_id');

        // If user_id parameter exists, validate it matches auth user (unless admin)
        if ($requestUserId !== null) {
            // Only allow if:
            // 1. The user_id matches authenticated user's id
            // 2. OR user is admin
            return (int)$requestUserId === $authUser->id
                || $authUser->role_type === 'admin';
        }

        // If job_application route parameter exists, check ownership
        if ($jobApplication) {
            return $authUser->id === $jobApplication->user_id
                || $authUser->role_type === 'admin';
        }

        // If no parameters to validate against, allow only admins
        return $authUser->role_type === 'admin';
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
}
