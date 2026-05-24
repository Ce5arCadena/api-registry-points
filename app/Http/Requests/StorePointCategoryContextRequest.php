<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TeacherRepository;
use Illuminate\Foundation\Http\FormRequest;

class StorePointCategoryContextRequest extends FormRequest
{
    public function __construct(
        protected TeacherRepository $teacherRepository,
    ) {}
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
        $auth = Auth::user();
        $teacherSearch = $this->teacherRepository->getTeacherByUserId($auth->id, $auth->school_id);

        return [
            "pointCategoryId" => [
                "required",
                "integer",
                "min:1",
                Rule::exists("point_categories", "id")
                    ->where('teacher_id', $teacherSearch->id)
                    ->where('status', 'ACTIVE')
                    ->where("school_id", $auth->school_id),
            ],
            "course" => [
                "required",
                "integer",
                "min:1",
                Rule::exists("teacher_subject", "grade_id")
                    ->where('teacher_id', $teacherSearch->id)
                    ->where('status', 'ACTIVE')
                    ->where("school_id", $auth->school_id),
            ],
            "subject" => [
                "required",
                "integer",
                "min:1",
                Rule::exists("teacher_subject", "subject_id")
                    ->where('teacher_id', $teacherSearch->id)
                    ->where('grade_id', $this->course)
                    ->where('status', 'ACTIVE')
                    ->where("school_id", $auth->school_id),
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'pointCategoryId.required' => 'La categoría de puntos es obligatoria.',
            'pointCategoryId.integer' => 'La categoría de puntos debe ser un número entero.',
            'pointCategoryId.min' => 'La categoría de puntos debe ser un número positivo.',
            'pointCategoryId.exists' => 'La categoría de puntos no existe o no te pertenece.',
            'course.required' => 'El curso es obligatorio.',
            'course.integer' => 'El curso debe ser un número entero.',
            'course.min' => 'El curso debe ser un número positivo.',
            'course.exists' => 'El curso no está asignado a ti.',
            'subject.required' => 'La materia es obligatoria.',
            'subject.integer' => 'La materia debe ser un número entero.',
            'subject.min' => 'La materia debe ser un número positivo.',
            'subject.exists' => 'La materia no está asignada a ti en ese curso.',
        ];
    }
}
