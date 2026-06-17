<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class RegistryPointsRequest extends FormRequest
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
        $schoolId = $this->user()->school_id;

        return [
            'grade_id' => [
                'required',
                'integer',
                Rule::exists('grades', 'id')->where('school_id', $schoolId),
            ],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'grade_id.exists' => 'El curso no existe o no pertenece a tu colegio.',
            'subject_id.exists' => 'La asignatura no existe.',
        ];
    }
}
