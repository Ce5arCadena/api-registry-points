<?php

namespace App\Http\Services;

use App\Http\Resources\SubjectResource;
use App\Models\User;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use App\Repositories\SubjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubjectService {
    public function __construct(protected SubjectRepository $subjectRepository) {}

    public function store(array $fields, User $user) {
        $subjectByNameExists = Subject::where('status', 'ACTIVE')
            ->where('name', trim($fields['name']))
            ->where('school_id', $user->school_id)
            ->exists();
        
        if ($subjectByNameExists) throw new ConflictHttpException('Ya existe una materia con el mismo nombre. Use otro.');

        $newSubject = new Subject;
        $newSubject->name = trim($fields['name']);
        $newSubject->school_id = $user->school_id;
        $newSubject->save();

        return response()->json([
            'message' => 'Materia creada éxitosamente.',
            'errors' => [],
            'data' => [
                'subject' => $newSubject
            ]
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

        $subject->update([
            'name' => trim($fields['name'])
        ]);
        return response()->json([
            'message' => 'Materia actualizada éxitosamente.',
            'errors' => [],
            'data' => [new SubjectResource($subject)]
        ], JsonResponse::HTTP_OK);
    }
}