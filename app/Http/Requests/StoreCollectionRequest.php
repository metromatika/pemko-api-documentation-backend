<?php

namespace App\Http\Requests;

use App\Models\Collection;
use App\Models\User;
use App\Rules\JsonFile;
use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionRequest extends FormRequest
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
        $accessTypes = [
            Collection::COLLECTION_ACCESS_TYPE_PUBLIC,
            Collection::COLLECTION_ACCESS_TYPE_PRIVATE
        ];

        return [
            'project_name' => ['required', 'max:255'],
            'access_type' => ['required', 'in:' . implode(',', $accessTypes)],
            'json_file' => ['required', 'file', new JsonFile],
            'source_code_file' => ['sometimes', 'required', 'array', 'max:3'],
            'source_code_file.*' => ['required', 'file', 'mimes:zip,rar', 'max:512000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'project_name.required' => 'Project name is required',
            'project_name.max' => 'Project name must be less than 255 characters',

            'access_type.required' => 'Access type is required',
            'access_type.in' => 'Access type is invalid',

            'json_file.required' => 'JSON file is required',
            'json_file.file' => 'JSON file is not valid',
            'json_file.mimetypes' => 'JSON file must be in JSON format',

            'source_code_file.required' => 'Source code file is required',
            'source_code_file.array' => 'Source code file should be array',
            'source_code_file.size' => 'Maximum uploaded file is 3',

            'source_code_file.*.required' => 'Source code file is required',
            'source_code_file.*.file' => 'Source code file must be a file',
            'source_code_file.*.mimes' => 'Source code file must be zip or rar file',
            'source_code_file.*.max' => 'Maximum source code file is 500MB'
        ];
    }
}
