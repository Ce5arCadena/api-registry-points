<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\TeacherRepository;
use App\Repositories\PointCategoryRepository;
use App\Repositories\TeacherSubjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repositories\PointCategoryContextRepository;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Requests\StorePointCategoryContextRequest;
use App\Http\Requests\UpdatePointCategoryContextRequest;

class PointCategoryContextService {
    public function __construct(
        protected TeacherRepository $teacherRepository,
        protected PointCategoryRepository $pointCategoryRepository,
        protected TeacherSubjectRepository $teacherSubjectRepository,
        protected PointCategoryContextRepository $pointCategoryContextRepository,
    ) {}

    public function savePointCategoryContext(StorePointCategoryContextRequest $request): JsonResponse | ResourceCollection {
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

        return $this->getPointsCategories('Categoría de puntos asignada.');
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

    public function updatePointCategoryContext(UpdatePointCategoryContextRequest $request, int $pointCategoryContextId): JsonResponse | ResourceCollection {
        $fieldsUpdated = [];
        $authUser = Auth::user();
        $data = $request->validated();
        $teacher = $this->teacherRepository->getTeacherByUserId($authUser->id, $authUser->school_id);

        // Verificar que la asignación de categoría de puntos exista y pertenezca al maestro autenticado
        $pointCategoryContext = $this->pointCategoryContextRepository->getPointCategoryContextById([
            "id" => $pointCategoryContextId,
            "teacher_id" => $teacher->id,
            "school_id" => $authUser->school_id
        ]);
        if (!$pointCategoryContext) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No se encontró la asignación de categoría de puntos.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        if(isset($data["pointCategoryId"])) {
            $fieldsUpdated["point_category_id"] = $data["pointCategoryId"];
        }

        if(isset($data["course"])) {
            $fieldsUpdated["grade_id"] = $data["course"];
        }

        if(isset($data["subject"])) {
            $fieldsUpdated["subject_id"] = $data["subject"];
        }

        $updated = $this->pointCategoryContextRepository->updatePointCategoryContext($pointCategoryContextId, $authUser->school_id, $fieldsUpdated);
        if (!$updated) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar la asignación de categoría de puntos.',
                'errors' => ['Inténtalo de nuevo más tarde.'],
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getPointsCategories('Asignación de categoría de puntos actualizada.');
    }

    public function changeStates(array $ids): JsonResponse | ResourceCollection {
        $user = Auth::user();
        [$pointCategoriesById] = $this->pointCategoryContextRepository->updateStates(array_values($ids["ids"]), $user->school_id);
        $unprocessedPointCategories = array_diff(array_values($ids["ids"]), $pointCategoriesById->pluck('id')->toArray());
        $message = count($unprocessedPointCategories) > 0 ? "Ids de registros que no existen => " . implode(",", $unprocessedPointCategories) : "Categoría de puntos cambiada de estado.";

        return $this->getPointsCategories($message);
    }
}