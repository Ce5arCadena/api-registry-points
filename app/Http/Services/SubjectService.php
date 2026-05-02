<?php

namespace App\Http\Services;

use App\Models\User;
use App\Http\Resources\SubjectResource;
use App\Repositories\SubjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubjectService {
    public function __construct(protected SubjectRepository $subjectRepository) {}

    public function getAll(User $user): ResourceCollection {
        $data = $this->subjectRepository->getSubjects($user->school_id);
        return $data->additional([
            'message' => 'Lista de materias.',
        ]);
    }

    public function store(array $fields, User $user) {
        $subjectByName = $this->subjectRepository->getByName($fields['name'], $user->school_id);
        if ($subjectByName) {
            return response()->json([
                'message' => 'Error al crear la materia.',
                'errors' => ['Ya existe una materia con el mismo nombre. Use otro.'],
            ], JsonResponse::HTTP_CONFLICT);
        }
         
        $newSubject = $this->subjectRepository->create([
            'name'=> $fields['name'],
            'school_id' => $user->school_id
        ]);

        return response()->json([
            'message' => 'Materia creada éxitosamente.',
            'data' => new SubjectResource($newSubject)
        ]);
    }

    public function update(array $fields, int $subject, User $user): JsonResponse {
        $subject = $this->subjectRepository->getById($subject, $user->school_id);
        if (!$subject) {
            return response()->json([
                'message' => 'Error al consultar la materia.',
                'errors' => ['La materia no existe'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $subjectByName = $this->subjectRepository->getByName($fields['name'], $user->school_id);
        if ($subjectByName && $subject->id !== $subjectByName->id) {
            return response()->json([
                'message' => 'Error al actualizar la materia.',
                'errors' => ['Ya existe una materia con el mismo nombre. Use otro.'],
            ], JsonResponse::HTTP_CONFLICT);
        }

        $this->subjectRepository->update($subject->id, $user->school_id, [
            'name' => trim($fields['name'])
        ]);

        return response()->json([
            'message' => 'Materia actualizada éxitosamente.',
            'data' => new SubjectResource($subject->fresh())
        ], JsonResponse::HTTP_OK);
    }

    public function getSubject(int $subjectId, User $user): JsonResponse {
        $subject = $this->subjectRepository->getById($subjectId, $user->school_id);
        if (!$subject) {
            return response()->json([
                'message' => 'Error al consultar la materia.',
                'errors' => ['La materia no existe'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Materia encontrada.',
            'data' => new SubjectResource($subject)
        ], JsonResponse::HTTP_OK);
    }

    public function delete(int $subjectId, User $user): JsonResponse {
        $subject = $this->subjectRepository->getById($subjectId, $user->school_id);
        if (!$subject) {
            return response()->json([
                'message' => 'Error al consultar la materia.',
                'errors' => ['La materia no existe'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->subjectRepository->update($subjectId, $user->school_id, [
            'status' => 'INACTIVE'
        ]);
        return response()->json([
            'message' => 'Materia eliminada éxitosamente.',
            'data' => new SubjectResource($subject->fresh())
        ], JsonResponse::HTTP_OK);
    }

    public function searchSubject(array $validated, User $user) {
        $searchSubjects = $this->subjectRepository->searchSubject($validated['field'], $validated['value'], $user->school_id);
        return response()->json([
            'message' => 'Búsqueda de asignaturas.',
            'data' => $searchSubjects
        ]);
    }
}