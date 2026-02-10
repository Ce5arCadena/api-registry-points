<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherSubjectRequest extends FormRequest
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
        $userAuth = Auth::user();
        $year = date('Y');
        return [
            "teacher" => [
                "sometimes",
                "required",
                "numeric",
                "min:1",
                Rule::exists('teachers', 'id')->where(function ($query) use ($userAuth) {
                    $query->where('school_id', $userAuth->school_id);
                })
            ],
            "subject" => [
                "sometimes",
                "required",
                "numeric",
                "min:1",
                Rule::exists('subjects', 'id')->where(function ($query) use ($userAuth) {
                    $query->where('school_id', $userAuth->school_id);
                }),
            ],
            "academic_year"=> "sometimes|nullable|in:". $year,
        ];
    }

    public function messages()
    {
        $year = date('Y');
        return [
            'teacher.required' => 'El profesor es obligatorio',
            'teacher.numeric' => 'El profesor debe ser un valor numérico',
            'teacher.exists' => 'El profesor seleccionado no existe',
            'subject.required' => 'La materia es obligatoria',
            'subject.numeric' => 'La materia debe ser un valor numérico',
            'subject.exists' => 'La materia seleccionada no existe',
            'subject.unique' => 'Esta materia ya está asignada a este profesor en tu escuela',
            'academic_year.in' => 'El año académico debe ser ' . $year,
        ];
    }
}
