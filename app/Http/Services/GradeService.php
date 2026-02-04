<?php
namespace App\Http\Services;

use App\Models\User;
use App\Http\Resources\GradeResource;
use App\Repositories\GradeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class GradeService {
    public function __construct(protected GradeRepository $gradeRepository) {}

    public function getAll(User $user) {
        $grades = $this->gradeRepository->getGrades($user->school_id);

        return $grades->additional([
            'message' => 'Lista de cursos.'
        ]);
    }

    public function store(array $fields, User $user): JsonResponse {
        $gradeExist = $this->gradeRepository->getGradeByName($fields['name'], $user->school_id);
        if ($gradeExist) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Ya existe un curso con el mismo nombre.'],
            ], JsonResponse::HTTP_CONFLICT);
        };

        $grade = $this->gradeRepository->createGrade([
            'name' => trim($fields['name']),
            'school_id' => $user->school_id
        ]);

        return response()->json([
            'message' => 'Curso registrado éxitosamente.',
            'data' => new GradeResource($grade)
        ]);
    }

    public function update(array $fields, int $grade, User $user): JsonResponse {
        $gradeExist = $this->gradeRepository->getGradeById($grade, $user->school_id);
        if (!$gradeExist) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el curso especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        };
        
        $gradeByName = $this->gradeRepository->getGradeByName($fields['name'], $user->school_id);
        if ($gradeByName && $gradeExist->id !== $gradeByName->id) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Ya existe un curso con el mismo nombre.'],
            ], JsonResponse::HTTP_CONFLICT);
        };

        $this->gradeRepository->updateGrade($grade, $user->school_id, [
            'name' => $fields['name']
        ]);
        
        return response()->json([
            'message' => 'Curso actualizado éxitosamente.',
            'data' => $gradeExist->fresh()
        ]);
    }

    public function showGrade(int $grade, User $user): JsonResponse {
        $grade = $this->gradeRepository->getGradeById($grade, $user->school_id);
        if (!$grade) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el curso especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        };

        return response()->json([
            'message' => 'Curso listado.',
            'data' => new GradeResource($grade)
        ]);
    }

    public function destroy(int $grade, User $user): JsonResponse {
        $gradeExist = $this->gradeRepository->getGradeById($grade, $user->school_id);
        if (!$gradeExist) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el curso especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        };

        $this->gradeRepository->updateGrade($grade, $user->school_id, [
            'status' => 'INACTIVE'
        ]);

        return response()->json([
            'message' => 'Curso eliminado éxitosamente.',
            'data' => new GradeResource($gradeExist->fresh())
        ]);
    }
}