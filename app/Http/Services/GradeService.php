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
        if (empty($fields) || !$user) throw new AuthorizationException("No está autorizado.");

        $gradeExist = Grade::where('id', $grade)
            ->where('status', 'ACTIVE')
            ->where('school_id', $user->school_id)
            ->firstOr(function () {
                throw new NotFoundHttpException('No existe el curso especificado');
            });
        
        $gradeByName = Grade::where('name', $fields['name'])->where('status', 'ACTIVE')->where("school_id", $user->school_id)->first();
        if (isset($gradeByName) && $gradeExist->id !== $gradeByName->id) throw new ConflictHttpException('Ya existe un curso con el mismo nombre. Use otro.');

        $gradeExist->update([
            'name' => $fields['name']
        ]);
        
        return response()->json([
            'message' => 'Curso actualizado éxitosamente.',
            'errors' => [],
            'data' => [
                'grade' => $gradeExist
            ]
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