<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:' . User::class . ',email'],
            'office' => ['required', 'string'],
            'password' => ['required'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',

            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is already registered',

            'office.required' => 'Office is required',

            'password.required' => 'Password is required',

            'password_confirmation.required' => 'Password confirmation is required',
            'password_confirmation.same' => 'Password confirmation does not match',
        ];
    }


}
