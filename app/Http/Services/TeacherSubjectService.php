<?php

namespace App\Http\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\TeacherRepository;
use App\Http\Resources\GradeStudentResource;
use App\Repositories\TeacherSubjectRepository;
use App\Http\Resources\TeacherSubjectResource;
use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Requests\UpdateTeacherSubjectRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeacherSubjectService {

    public function __construct(
        protected TeacherRepository $teacherRepository,
        protected TeacherSubjectRepository $teacherSubjectRepository,
    ) {}

    public function asignSubjectToTeacher(StoreTeacherSubjectRequest $requests): JsonResponse {
        $authUser = Auth::user();
        $fields = $requests->validated();

        $teacherSubject = $this->teacherSubjectRepository->asignSubjectToTeacher([
            ...$fields,
            "teacher_id" => $fields["teacher"],
            "subject_id" => $fields["subject"],
            "grade_id" => $fields["grade"],
            "school_id" => $authUser->school_id
        ]);

        return response()->json([
            'message' => 'Materia asignada.',
            'data' => new TeacherSubjectResource($teacherSubject)
        ]);
    }

    public function updateAsignSubjectToTeacher(UpdateTeacherSubjectRequest $request, int $teacherSubjectId): JsonResponse {
        $authUser = Auth::user();
        $fields = $request->validated();

        $teacherSubject = $this->teacherSubjectRepository->getTeacherSubjectById($teacherSubjectId, $authUser->school_id);
        if (!$teacherSubject) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el registro especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $fieldsUpdate = [];
        $gradeId = $fields['grade'] ?? $teacherSubject->grade_id;
        $teacherId = $fields['teacher'] ?? $teacherSubject->teacher_id;
        $subjectId = $fields['subject'] ?? $teacherSubject->subject_id;

        $getTeacherSubjectBySubject = $this->teacherSubjectRepository->getTeacherSubjectBySubject([
            'id' => $teacherSubject->id,
            'teacher_id' => $teacherId,
            'subject_id' => $subjectId,
            'grade_id' => $gradeId,
            'school_id' => $authUser->school_id,
        ]);

        if ($getTeacherSubjectBySubject) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Esta materia ya está asignada a este profesor.'],
            ], JsonResponse::HTTP_CONFLICT);
        }

        if (isset($fields['teacher'])) {
            $fieldsUpdate['teacher_id'] = $fields['teacher'];
        }
        if (isset($fields['subject'])) {
            $fieldsUpdate['subject_id'] = $fields['subject'];
        }
        if (isset($fields['grade'])) {
            $fieldsUpdate['grade_id'] = $fields['grade'];
        }


        if (isset($fields['academic_year'])) $fieldsUpdate['academic_year'] = $fields['academic_year'];
        $this->teacherSubjectRepository->updateTeacherSubject($teacherSubject->id, $authUser->school_id, $fieldsUpdate);

        return response()->json([
            'message' => 'Asignación de materia actualizada.',
            'data' => new TeacherSubjectResource($teacherSubject->fresh())
        ]);
    }

    public function getAllTeachersSubjects() {
        $authUser = Auth::user();
        $teachers = $this->teacherSubjectRepository->getAllTeachersSubjects($authUser->school_id);

        return $teachers->additional([
            'message' => 'Lista de asignación de materias'
        ]);
    }

    public function getTeacherSubject(int $teacherSubjectId): JsonResponse {
        $authUser = Auth::user();

        $teacherSubject = $this->teacherSubjectRepository->getTeacherSubjectById($teacherSubjectId, $authUser->school_id);
        if (!$teacherSubject) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el registro especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Asignación de materia.',
            'data' => new TeacherSubjectResource($teacherSubject)
        ]);
    }

    public function deleteTeacherSubject(int $teacherSubjectId): JsonResponse {
        $authUser = Auth::user();

        $teacherSubject = $this->teacherSubjectRepository->getTeacherSubjectById($teacherSubjectId, $authUser->school_id);
        if (!$teacherSubject) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el registro especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->teacherSubjectRepository->updateTeacherSubject($teacherSubject->id, $authUser->school_id, [
            'status' => 'INACTIVE'
        ]);

        return response()->json([
            'message' => 'Asignación de materia eliminada.',
            'data' => new TeacherSubjectResource($teacherSubject->fresh())
        ]);
    }

    public function getGradesByTeacher(): JsonResponse {
        $authUser = Auth::user();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $grades = $this->teacherSubjectRepository->getTeacherSubjects([
            "year" => Carbon::now()->year,
            "teacher_id" => $teacher->id,
            "school_id" => $authUser->school_id
        ]);
        
        return response()->json([
            'message' => 'Tus cursos asignados.',
            'data' => TeacherSubjectResource::collection($grades)
        ]);
    }

    public function getStudentsWithPoints(int $gradeId, int $subjectId): JsonResponse {
        $authUser = Auth::user();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $gradeWithStudents = $this->teacherSubjectRepository->getTeacherSubjects([
            "year" => Carbon::now()->year,
            "teacher_id" => $teacher->id,
            "school_id" => $authUser->school_id,
            "grade_id" => $gradeId,
            "subject_id" => $subjectId
        ]);

        if (!$gradeWithStudents) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el registro especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'message' => 'Estudiante del curso.',
            'data' => new GradeStudentResource($gradeWithStudents)
        ]);
    }
}