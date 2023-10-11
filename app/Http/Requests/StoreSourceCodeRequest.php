<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSourceCodeRequest extends FormRequest
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
            'name' => ['required', 'max:255'],
            'file' => ['required', 'file', 'mimes:zip,rar', 'max:512000'],
        ];
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.max' => 'Name must be less than 255 characters',

            'file.required' => 'Source code file is required',
            'file.file' => 'Source code file must be a file',
            'file.mimes' => 'Source code file must be zip or rar file',
            'file.max' => 'Maximum source code file is 500MB'
        ];
    }
}
