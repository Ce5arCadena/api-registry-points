<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\TeacherRepository;
use App\Repositories\PointCategoryRepository;
use App\Http\Resources\PointCategoryResource;
use App\Repositories\TeacherSubjectRepository;
use App\Http\Requests\StorePointCategoryRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class PointCategoryService {
    public function __construct(
        protected TeacherRepository $teacherRepository,
        protected PointCategoryRepository $pointCategoryRepository,
        protected TeacherSubjectRepository $teacherSubjectRepository
    ) {}

    public function createPointCategory(StorePointCategoryRequest $request): JsonResponse {
        $authUser = Auth::user();
        $data = $request->validated();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $subjectAssign = $this->teacherSubjectRepository->getBySubjectAndteacher($teacher->id, $data['subject'], $authUser->school_id);
        if (!$subjectAssign) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No puedes crear esta categoría de puntos para esta asignatura.'],
            ], JsonResponse::HTTP_CONFLICT);
        }

        $pointCategory = $this->pointCategoryRepository->getPointCategoryByName([
            "name" => $data["name"],
            "teacher_id" => $teacher->id,
            "subject_id" => $subjectAssign->subject_id,
            "school_id" => $authUser->school_id
        ]);
        if ($pointCategory) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Ya tienes una categoria con el mismo nombre.'],
            ], JsonResponse::HTTP_CONFLICT);
        }

        $pointCategory = $this->pointCategoryRepository->createPointCategory([
            ...$data,
            "teacher_id" => $teacher->id,
            "subject_id" => $data["subject"],
            "school_id" => $authUser->school_id
        ]);

        return response()->json([
            'message' => 'Categoria de puntos creada.',
            'data' => new PointCategoryResource($pointCategory)
        ]);
    }
}