<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\TeacherRepository;
use App\Repositories\PointCategoryRepository;
use App\Repositories\TeacherSubjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repositories\PointCategoryContextRepository;
use App\Http\Requests\StorePointCategoryContextRequest;

class PointCategoryContextService {
    public function __construct(
        protected TeacherRepository $teacherRepository,
        protected PointCategoryRepository $pointCategoryRepository,
        protected TeacherSubjectRepository $teacherSubjectRepository,
        protected PointCategoryContextRepository $pointCategoryContextRepository,
    ) {}

    public function savePointCategoryContext(StorePointCategoryContextRequest $request): JsonResponse {
        $authUser = Auth::user();
        $data = $request->validated();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        if (!$teacher) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No estás autorizado para ejecutar esta acción.'],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $pointCategoryContext = $this->pointCategoryContextRepository->existCategoryContext([
            "point_category_id" => $data["pointCategoryId"],
            "grade_id" => $data["course"],
            "subject_id" => $data["subject"],
            "school_id" => $authUser->school_id
        ]);
        if ($pointCategoryContext) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Ya tienes una categoría asignada con el mismo curso y misma asignatura.'],
            ], JsonResponse::HTTP_CONFLICT);
        }

        $pointCategoryContext = $this->pointCategoryContextRepository->createPointCategoryContext([
            "point_category_id" => $data["pointCategoryId"],
            "grade_id" => $data["course"],
            "subject_id" => $data["subject"],
            "status" => 'ACTIVE',
            "school_id" => $authUser->school_id
        ]);

        return response()->json([
            'message' => 'Categoria de puntos asignada.',
            'data' => $pointCategoryContext
        ]);
    }

    public function getPointsCategories(string $message = "Lista de asignaciones de categorías.") {
        $authUser = Auth::user();
        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);

        $pointCategories = $this->pointCategoryRepository->getPointCategoriesByContext([
            "teacher_id" => $teacher->id,
            "school_id" => $authUser->school_id
        ]);

        return $pointCategories->additional([
            "message" => $message
        ]);
    }
}