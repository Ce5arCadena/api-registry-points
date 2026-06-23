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
use App\Repositories\PointCategoryContextRepository;

class RegistryPointService
{
    public function __construct(
        protected GradeRepository $gradeRepository,
        protected SubjectRepository $subjectRepository,
        protected StudentRepository $studentRepository,
        protected TeacherRepository $teacherRepository,
        protected PointCategoryRepository $pointCategoryRepository,
        protected RegistryPointRepository $registryPointRepository,
        protected TeacherSubjectRepository $teacherSubjectRepository,
        protected PointCategoryContextRepository $pointCategoryContextRepository
    ) {}

    public function registryPoints(StoreRegistryPointRequest $request)
    {
        $authUser = Auth::user();
        $fields = $request->validated();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $academicYear = Carbon::now()->year;

        $errors = [];
        foreach ($fields["points"] as $value) {
            $pointCategoryContext = $this->pointCategoryContextRepository->getPointCategoryContextById([
                "id" => $value["point_category_context"],
                "teacher_id" => $teacher->id,
                "school_id" => $authUser->school_id,
            ]);

            if (!$pointCategoryContext) {
                array_push($errors, "El contexto de categoría con identificador {$value['point_category_context']} no se procesó porque no existe o no es permitido.");
                continue;
            }

            foreach ($value["data"] as $points) {
                if ($points["points"] > $pointCategoryContext->pointCategory->max_points) {
                    array_push($errors, "El estudiante con ID {$points['student']} excede el límite de puntos permitidos en el contexto de categoría {$value['point_category_context']}.");
                    continue;
                }

                $this->registryPointRepository->createRegistryPoint([
                    "student_id" => $points["student"],
                    "point_category_context_id" => $pointCategoryContext->id,
                    "teacher_id" => $teacher->id,
                    "points" => $points["points"],
                    "academic_year" => $academicYear,
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
