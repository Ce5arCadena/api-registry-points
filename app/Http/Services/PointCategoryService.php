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

    public function updatePointCategory(UpdatePointCategoryRequest $request, int $pointCategoryId) {
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
        if (isset($data['subject'])) {
            // Válido que pueda el maestro asignar esa materia a la categoria de puntos que trata de editar
            $subjectAssign = $this->teacherSubjectRepository->getBySubjectAndteacher($teacher->id, $data['subject'], $authUser->school_id);
            if (!$subjectAssign) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['No puedes asignar esta materia porque no estás autorizado.'],
                ], JsonResponse::HTTP_NOT_FOUND);
            }
            $fields["subject_id"] = $data["subject"];  
        }

        if (isset($data["name"]) && !isset($data["subject"])) {
            $pointCategoryByName = $this->pointCategoryRepository->getPointCategoryByName([
                "name" => $data["name"],
                "teacher_id" => $teacher->id,
                "subject_id" => $pointCategory->subject_id,
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

        if (isset($data["name"]) && isset($data["subject"])) {
            $pointCategoryByName = $this->pointCategoryRepository->getPointCategoryByName([
                "name" => $data["name"],
                "teacher_id" => $teacher->id,
                "subject_id" => $data["subject"],
                "school_id" => $authUser->school_id
            ]);
            if ($pointCategoryByName && $pointCategoryByName->id !== $pointCategory->id) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['Ya tienes una categoria con el mismo nombre.'],
                ], JsonResponse::HTTP_CONFLICT);
            }
            $fields["name"] = $data["name"];
            $fields["subject_id"] = $data["subject"];    
        }

        if (isset($data["max_points"])) $fields["max_pointse"] = $data["max_points"];
        
        $this->pointCategoryRepository->updateCategoryPoint([
            "id" => $pointCategoryId,
            "teacher_id" => $teacher->id,
            "school_id" => $authUser->school_id
        ], $fields);

        return response()->json([
            'message' => 'Categoria de puntos actualizada.',
            'data' => new PointCategoryResource($pointCategory->fresh()),
        ]);
    }
}