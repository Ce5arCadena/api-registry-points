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
            "points.*.point_category_context" => [
                "required",
                "integer",
                "min:1",
                Rule::exists("point_category_context","id")
                    ->where('status', 'ACTIVE')
                    ->where("school_id", $authUser->school_id)
                    ->where("grade_id", $this->input("grade")),
            ],
            "points.*.data" => "required|array|min:1",
            "points.*.data.*.points" => "required|integer|min:1",
            "points.*.data.*.student" => [
                "required",
                "integer",
                "min:1",
                Rule::exists("students","id")
                    ->where('status', 'ACTIVE')
                    ->where("school_id", $authUser->school_id)
                    ->where("grade_id", $this->input("grade")),
            ],
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

            'points.*.point_category_context.required' => 'El contexto de categoría de puntos es obligatorio.',
            'points.*.point_category_context.integer' => 'El contexto de categoría de puntos debe ser un número entero.',
            'points.*.point_category_context.min' => 'El contexto de categoría de puntos debe ser mayor a 0.',
            'points.*.point_category_context.exists' => 'El contexto de categoría seleccionado no es válido, no está activo o no pertenece al grado especificado.',

            'points.*.data.required' => 'Debe proporcionar datos de estudiantes para esta categoría.',
            'points.*.data.array' => 'El formato de datos no es válido.',
            'points.*.data.min' => 'Debe incluir al menos un estudiante.',

            'points.*.data.*.points.required' => 'Los puntos del estudiante son obligatorios.',
            'points.*.data.*.points.integer' => 'Los puntos deben ser un número entero.',
            'points.*.data.*.points.min' => 'Los puntos deben ser mayor a 0.',

            'points.*.data.*.student.required' => 'El estudiante es obligatorio.',
            'points.*.data.*.student.integer' => 'El ID del estudiante debe ser un número entero.',
            'points.*.data.*.student.min' => 'El ID del estudiante debe ser mayor a 0.',
            'points.*.data.*.student.exists' => 'El estudiante seleccionado no es válido, no está activo o no pertenece al grado especificado.',
        ];
    }
}
