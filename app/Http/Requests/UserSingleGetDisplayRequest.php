<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserSingleGetDisplayRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $authUser = auth()->user();

        $user = $this->route('user');

        // Check if user is authenticated
        if (!$authUser) {
            return false;
        }

        return $authUser->id === $user->id
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
}
