<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\TeacherRepository;
use App\Repositories\PointCategoryRepository;
use App\Http\Resources\PointCategoryResource;
use App\Repositories\TeacherSubjectRepository;
use App\Http\Requests\StorePointCategoryRequest;
use App\Http\Requests\UpdatePointCategoryRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PointCategoryService {
    public function __construct(
        protected TeacherRepository $teacherRepository,
        protected PointCategoryRepository $pointCategoryRepository,
        protected TeacherSubjectRepository $teacherSubjectRepository
    ) {}

    public function createPointCategory(StorePointCategoryRequest $request): JsonResponse | ResourceCollection {
        $authUser = Auth::user();
        $data = $request->validated();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);

        $pointCategory = $this->pointCategoryRepository->getPointCategoryByName([
            "name" => $data["name"],
            "teacher_id" => $teacher->id,
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
            "school_id" => $authUser->school_id
        ]);

        return $this->getPointsCategories("Categoria de puntos creada.");
    }

    public function updatePointCategory(UpdatePointCategoryRequest $request, int $pointCategoryId): JsonResponse | ResourceCollection {
        $authUser = Auth::user();
        $data = $request->validated();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $pointCategory = $this->pointCategoryRepository->getPointCategoryById([
            'id' => $pointCategoryId,
            'teacher_id' => $teacher->id,
            'school_id' => $authUser->school_id
        ]);
        if (!$pointCategory) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe la categoría de puntos especificada.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $fields = [];
        if (isset($data["name"])) {
            $pointCategoryByName = $this->pointCategoryRepository->getPointCategoryByName([
                "name" => $data["name"],
                "teacher_id" => $teacher->id,
                "school_id" => $authUser->school_id
            ]);
            if ($pointCategoryByName && $pointCategoryByName->id !== $pointCategory->id) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['Ya tienes una categoria con el mismo nombre.'],
                ], JsonResponse::HTTP_CONFLICT);
            }
            $fields["name"] = $data["name"]; 
        }

        if (isset($data["max_points"])) $fields["max_points"] = $data["max_points"];
        
        $this->pointCategoryRepository->updateCategoryPoint([
            "id" => $pointCategoryId,
            "teacher_id" => $teacher->id,
            "school_id" => $authUser->school_id
        ], $fields);

        return $this->getPointsCategories("Categoria de puntos actualizada.");
    }

    public function getPointsCategories(string $message = "Lista de categorías de puntos.") {
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

    public function getPointCategory(int $pointCategoryId) {
        $authUser = Auth::user();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $pointCategory = $this->pointCategoryRepository->getPointCategoryById([
            'id' => $pointCategoryId,
            'teacher_id' => $teacher->id,
            'school_id' => $authUser->school_id
        ]);
        if (!$pointCategory) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe la categoría de puntos especificada.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Categoria de puntos eliminada.',
            'data' => new PointCategoryResource($pointCategory)
        ]);
    }

    public function deletePointCategory(int $pointCategoryId): JsonResponse {
        $authUser = Auth::user();

        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);
        $pointCategory = $this->pointCategoryRepository->getPointCategoryById([
            'id' => $pointCategoryId,
            'teacher_id' => $teacher->id,
            'school_id' => $authUser->school_id
        ]);
        if (!$pointCategory) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe la categoría de puntos especificada.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->pointCategoryRepository->updateCategoryPoint([
            "id" => $pointCategoryId,
            "teacher_id" => $teacher->id,
            "school_id" => $authUser->school_id
        ], [
            "status" => "INACTIVE"
        ]);

        return $this->getPointsCategories("Categoria de puntos eliminada.");
    }
}