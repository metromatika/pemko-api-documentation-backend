<?php

namespace App\Http\Requests;

use App\Models\Collection;
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
            'collection_id' => ['required', 'uuid', 'exists:' . Collection::class . ',id'],
            'source_code_file' => ['required', 'array', 'max:3'],
            'source_code_file.*' => ['required', 'file', 'mimes:zip,rar', 'max:512000'],
        ];
    }

    /**
     * @return array<string,string>
     */
    public function messages(): array
    {
        return [
            'collection_id.required' => 'The collection is required',
            'collection_id.uuid' => 'The collection is invalid',
            'collection_id.exist' => 'The collection does not exist',

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
