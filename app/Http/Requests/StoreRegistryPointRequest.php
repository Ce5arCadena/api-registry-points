<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistryPointRequest extends FormRequest
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
        $authUser = Auth::user();
        return [
            "grade" => [
                "required",
                "integer",
                "min:1",
                Rule::exists("grades","id")
                    ->where('status', 'ACTIVE')
                    ->where("school_id", $authUser->school_id),
            ],
            "points" => "required|array|min:1",
            "points.*.id" => [
                "required",
                "integer",
                "min:1",
                Rule::exists("students","id")
                    ->where('status', 'ACTIVE')
                    ->where("school_id", $authUser->school_id),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'grade.required' => 'El grado es obligatorio.',
            'grade.integer' => 'El grado debe ser un número entero.',
            'grade.min' => 'El grado debe ser mayor a 0.',
            'grade.exists' => 'El grado seleccionado no es válido o no está activo.',

            'points.required' => 'Debe proporcionar al menos una categoría de puntos.',
            'points.array' => 'El formato de puntos no es válido.',
            'points.min' => 'Debe incluir al menos una categoría de puntos.',

            'points.*.id.required' => 'El estudiante es obligatorio.',
            'points.*.id.integer' => 'El id del estudiante debe ser un número entero.',
            'points.*.id.min' => 'El id del estudiante debe ser mayor a 0.',
            'points.*.id.exists' => 'El estudiante no es válido, no está activo.',
        ];
    }
}