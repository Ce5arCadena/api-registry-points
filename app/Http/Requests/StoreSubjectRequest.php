<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "teacher" => "required|integer|min:1",
            "grade" => "required|integer|min:1"
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "El nombre es obligatorio",
            "name.string" => "El nombre debe ser un texto válido",
            "name.max" => "El nombre no puede exceder los 255 caracteres",
            "teacher.required" => "El ID del profesor es obligatorio",
            "teacher.integer" => "El ID del profesor debe ser un número entero",
            "teacher.min" => "El ID del profesor debe ser mayor a 0",
            "grade.required" => "El ID del grado es obligatorio",
            "grade.integer" => "El ID del grado debe ser un número entero",
            "grade.min" => "El ID del grado debe ser mayor a 0"
        ];
    }
}
