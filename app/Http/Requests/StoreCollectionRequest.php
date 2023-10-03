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
            'access_type' => ['required', 'in:' . implode(',', $accessTypes)],
            'json_file' => ['required', 'file', new JsonFile]
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
            'access_type.required' => 'Access type is required',
            'access_type.in' => 'Access type is invalid',

            'json_file.required' => 'JSON file is required',
            'json_file.file' => 'JSON file is not valid',
            'json_file.mimetypes' => 'JSON file must be in JSON format',
        ];
    }
}
