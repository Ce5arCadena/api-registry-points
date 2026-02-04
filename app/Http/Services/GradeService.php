<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\Grade;
use App\Http\Resources\GradeResource;
use App\Repositories\GradeRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GradeService {
    public function __construct(protected GradeRepository $gradeRepository) {}

    public function getAll(User $user) {
        $grades = Grade::where("school_id", $user->school_id)
            ->where('status', 'ACTIVE')
            ->paginate()
            ->toResourceCollection();

        return $grades->additional([
            'message' => 'Lista de cursos.',
            'errors' => []
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

    public function update(array $fields, int $grade, User $user) {
        $gradeExist = $this->gradeRepository->getGradeById($grade, $user->school_id);
        if (!$gradeExist) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No existe el curso especificado.'],
            ], JsonResponse::HTTP_CONFLICT);
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

    public function showGrade(int $grade, User $user) {
        $grade = Grade::where('id', $grade)->where('school_id', $user->school_id)->firstOr(function () {
            throw new NotFoundHttpException('No existe el curso especificado');
        });

        return response()->json([
            'message' => 'Curso listado.',
            'errors' => [],
            'data' => [
                $grade
            ]
        ]);
    }

    public function destroy(int $grade, User $user) {
        $grade = Grade::where('id', $grade)->where('school_id', $user->school_id)->firstOr(function () {
            throw new NotFoundHttpException('No existe el curso especificado');
        });
        
        $grade->update([
            'status' => 'INACTIVE'
        ]);

        return response()->json([
            'message' => 'Curso eliminado éxitosamente.',
            'errors' => []
        ]);
    }
}