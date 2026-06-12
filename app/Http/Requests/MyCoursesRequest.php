<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MyCoursesRequest extends FormRequest
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
            "hasSubjectsAssignment" => "sometimes|boolean"
        ];
    }

    public function messages(): array
    {
        return [
            'hasSubjectsAssignment.boolean' => 'El campo hasSubjectsAssignment debe ser verdadero o falso.',
        ];
    }
}
