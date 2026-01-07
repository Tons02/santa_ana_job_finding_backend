<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JobApplicationRequest extends FormRequest
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
            'job_id' => [
                'required',
                'exists:available_jobs,id',
                Rule::unique('job_applications')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'cover_letter' => [
                'required',
                'string',
                'min:10',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'job_id.required' => 'The job ID is required.',
            'job_id.exists' => 'The selected job does not exist.',
            'job_id.unique' => 'You have already applied for this job.',

            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
        ];
    }
}
