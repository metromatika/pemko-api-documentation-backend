<?php

namespace App\Http\Requests;

use App\Models\Collection;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCollectionRequest extends FormRequest
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
            'title' => ['sometimes', 'required', 'max:255'],
            'access_type' => ['sometimes', 'required', 'in:' . implode(',', $accessTypes)],
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
            'title.required' => 'Title is required',
            'title.max' => 'Title is too long',

            'access_type.required' => 'Access type is required',
            'access_type.in' => 'Access type is invalid',
        ];
    }
}
