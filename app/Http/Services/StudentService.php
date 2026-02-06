<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\GradeRepository;
use App\Repositories\StudentRepository;
use App\Http\Resources\StudentResource;
use App\Http\Requests\StoreStudentRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class StudentService {
    public function __construct(
        protected GradeRepository $gradeRepository,
        protected StudentRepository $studentRepository
    ) {}

    public function createStudent(StoreStudentRequest $request): JsonResponse {
        $data = $request->validated();
        $userAuth = Auth::user();

        $gradeExist = $this->gradeRepository->getGradeById($data["grade"], $userAuth->school_id);
        if (!$gradeExist) {
            return response()->json([
                'message' => 'Error al crear el estudiante.',
                'errors' => ['No existe el curso al que desea asociarlo.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $student = $this->studentRepository->createStudent([
            ...$data, 
            "grade_id" => $gradeExist->id,
            "school_id" => $userAuth->school_id
        ]);

        return response()->json([
            'message' => 'Estudiante creado.',
            'data' => new StudentResource($student)
        ]);
    }
}