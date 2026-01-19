<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the authenticated user
        $authUser = auth()->user();

        if (!$authUser) {
            return false;
        }

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
            "education_level" => [
                "required",
                "string",
            ],
            "name" => [
                "required",
                "string",
                $this->route()->course
                    ? "unique:courses,name," . $this->route()->course
                    : "unique:courses,name",
            ],
        ];
    }
}
