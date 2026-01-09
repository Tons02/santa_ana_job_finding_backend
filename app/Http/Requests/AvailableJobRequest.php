<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AvailableJobRequest extends FormRequest
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
            "title" => [
                "required",
                "string",
                $this->route()->job
                    ? "unique:available_jobs,title," . $this->route()->job
                    : "unique:available_jobs,title",
            ],
            'description' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'is_remote' => ['required', 'boolean'],
            'employment_type' => [
                Rule::in(['full_time', 'part_time', 'contract', 'internship']),
            ],
            'experience_level' => ['required', 'string', 'max:255'],
            'salary_min' => ['required', 'integer'],
            'salary_max' => ['required', 'integer'],
            'salary_currency' => [
                Rule::in(['PHP', 'USD']),
            ],
            'salary_period' => [
                Rule::in(['yearly', 'monthly', 'weekly', 'hourly', 'semi_monthly']),
            ],
            'hiring_status' => [
                Rule::in(['active', 'closed', 'paused']),
            ],
            'posted_at' => [
                'date'
            ],
            'expires_at' => [
                'date'
            ]
        ];
    }
}
