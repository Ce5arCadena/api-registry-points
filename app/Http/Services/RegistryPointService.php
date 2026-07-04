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

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $academicYear = Carbon::now()->year;

        $errors = [];
        foreach ($request->input("points") as $value) {
            // válidamos que exista la categoría de puntos en cada elemento por cada estudiante
            foreach($value["registered_points"] as $idCategoryPoint => $valueCategoryPoint) {
                $pointCategoryContext = $this->pointCategoryContextRepository->getPointCategoryContextByCategoryId([
                    "point_category_id" => $idCategoryPoint,
                    "teacher_id" => $teacher->id,
                    "school_id" => $authUser->school_id,
                ]);

                if (!$pointCategoryContext) {
                    array_push($errors, "El contexto de categoría con identificador {$idCategoryPoint} no se procesó porque no existe o no es permitido.");
                    continue;
                }

                if ($valueCategoryPoint > $pointCategoryContext->pointCategory->max_points) {
                    array_push($errors, "El estudiante con ID {$value['id']} excede el límite de puntos permitidos en el contexto de categoría {$pointCategoryContext->pointCategory->name}.");
                    continue;
                }

                // Ajustar el guardado de los puntos con la nueva estructura. Puede ser insertar o actualizar
                $this->registryPointRepository->createRegistryPoint([
                    "points" => $valueCategoryPoint,
                    "academic_year" => $academicYear,
                    "student_id" => $value["id"],
                    "point_category_context_id" => $pointCategoryContext->id,
                    "teacher_id" => $teacher->id,
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

        $contexts = $this->pointCategoryContextRepository->getContextsByGradeAndSubject([
            'grade_id'   => $request->grade,
            'subject_id' => $request->subject,
            'school_id'  => $authUser->school_id,
            'teacher_id' => $teacher->id,
        ]);

        if ($contexts->isEmpty()) {
            return response()->json([
                'message' => 'No tienes acceso a este recurso.',
                'errors'  => ['No se encontraron categorías para este curso y asignatura.'],
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $students   = $this->studentRepository->getStudentsByGrade($request->grade, $authUser->school_id);
        $contextIds = $contexts->pluck('id')->all();
        $studentIds = $students->pluck('id')->all();

        $existingPoints = $this->registryPointRepository->getByStudentsAndContexts(
            $studentIds,
            $contextIds,
            Carbon::now()->year
        );

        // Lookup [student_id][context_id] => points
        $pointsMap = [];
        foreach ($existingPoints as $rp) {
            $pointsMap[$rp->student_id][$rp->point_category_context_id] = $rp->points;
        }

        $studentsData = $students->map(function ($student) use ($contextIds, $pointsMap) {
            $registered = [];
            foreach ($contextIds as $contextId) {
                $registered[$contextId] = $pointsMap[$student->id][$contextId] ?? null;
            }
            return [
                'id'               => $student->id,
                'name'             => $student->name,
                'last_name'        => $student->last_name,
                'registered_points' => $registered,
            ];
        });

        $contextsData = $contexts->map(fn($ctx) => [
            'id'         => $ctx->id,
            'name'       => $ctx->pointCategory->name,
            'max_points' => $ctx->pointCategory->max_points,
        ]);

        return response()->json([
            'message' => 'Datos de registro de puntos.',
            'data'    => [
                'students'               => $studentsData,
                'point_category_contexts' => $contextsData,
            ],
        ]);
    }
}
