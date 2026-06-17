<?php

namespace App\Http\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\GradeRepository;
use App\Repositories\SubjectRepository;
use App\Repositories\StudentRepository;
use App\Repositories\TeacherRepository;
use App\Repositories\RegistryPointRepository;
use App\Repositories\PointCategoryRepository;
use App\Repositories\TeacherSubjectRepository;
use App\Http\Requests\StoreRegistryPointRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistryPointService
{
    public function __construct(
        protected GradeRepository $gradeRepository,
        protected SubjectRepository $subjectRepository,
        protected StudentRepository $studentRepository,
        protected TeacherRepository $teacherRepository,
        protected PointCategoryRepository $pointCategoryRepository,
        protected RegistryPointRepository $registryPointRepository,
        protected TeacherSubjectRepository $teacherSubjectRepository
    ) {}

    public function registryPoints(StoreRegistryPointRequest $request)
    {
        $authUser = Auth::user();
        $fields = $request->validated();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        // La asignatura debe estarse impartiendo en el curso.
        $gradeSubject = $this->teacherSubjectRepository->getTeacherSubjectByYear([
            "teacher_id" => $teacher->id,
            "grade_id" => $fields["grade"],
            "subject_id" => $fields["subject"],
            "school_id" => $authUser->school_id,
            "year" => Carbon::now()->year
        ]);
        if (!$gradeSubject) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['La asignatura no se está impartiendo en el curso o está inactiva.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $errors = [];
        foreach ($fields["points"] as $key => $value) {
            $pointCategory = $this->pointCategoryRepository->getPointCategoryByIdAndSubject([
                "id" => $value["point_category"],
                "teacher_id" => $teacher->id,
                "subject_id" => $fields["subject"],
                "school_id" => $authUser->school_id
            ]);

            if (!$pointCategory) {
                array_push($errors, "La categoría de puntos con identificador" . $value["point_category"] . "no se procesó porque no existe o no es permitida para esta asignatura.");
                continue;
            }

            foreach ($value["data"] as $points) {
                if ($points["points"] > $pointCategory->max_points) {
                    array_push($errors, "El estudiante con ID {$points['student']} excede el límite de puntos permitidos en la categoría {$value['point_category']}.");
                    continue;
                }

                $this->registryPointRepository->createRegistryPoint([
                    "student_id" => $points["student"],
                    "points" => $points["points"],
                    "point_category_id" => $pointCategory->id,
                    "subject_id" => $fields["subject"],
                    "school_id" => $authUser->school_id,
                    "updated_at" => now(),
                ]);
            }
        }

        return response()->json([
            'message' => count($errors) > 0 ? 'Algunos registros no fueron procesados.' : 'Asignación de puntos procesada',
            'errors' => count($errors) > 0 ? $errors : [],
        ]);
    }

    public function getRegistryPoints(Request $request)
    {
        $authUser = Auth::user();
        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);

        $isAuthorized = $this->teacherSubjectRepository->getBySubjectAndteacher($teacher->id, $request->subject, $authUser->school_id);
        if (!$isAuthorized) {
            return response()->json([
                'message' => 'No tienes acceso a este recurso.',
                'errors' => ['No se encontró esa asignatura asignada a ti en ese curso.'],
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $students = $this->studentRepository->getStudentsByGrade($request->grade, $authUser->school_id);
        $categories = $this->pointCategoryRepository->getCategoriesBySubjectAndGrade($request->subject, $request->grade, $authUser->school_id);

        return response()->json([
            'message' => 'Datos de registro de puntos.',
            'data' => [
                'students' => $students,
                'categories' => $categories,
            ]
        ]);
    }
}
