<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\GradeRepository;
use App\Repositories\StudentRepository;
use App\Http\Resources\StudentResource;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class StudentService {
    public function __construct(
        protected GradeRepository $gradeRepository,
        protected StudentRepository $studentRepository
    ) {}

    public function createStudent(StoreStudentRequest $request): JsonResponse {
        $data = $request->validated();
        $userAuth = Auth::user();

        $student = $this->studentRepository->createStudent([
            ...$data, 
            "grade_id" => $data['grade'],
            "school_id" => $userAuth->school_id
        ]);

        return response()->json([
            'message' => 'Estudiante creado.',
            'data' => new StudentResource($student)
        ]);
    }

    public function updateStudent(UpdateStudentRequest $request, int $student) {
        $data = $request->validated();
        $userAuth = Auth::user();

        $this->studentRepository->updateStudent($student, $userAuth->school_id, [...$data, "grade_id" => $data['grade']]);
        $studentUpdated = $this->studentRepository->getStudentById($student, $userAuth->school_id);
        return response()->json([
            'message' => 'Estudiante actualizado.',
            'data' => new StudentResource( $studentUpdated)
        ]);
    }

    public function deleteStudent(int $student): JsonResponse {
        $authUser = Auth::user();

        $studentExists = $this->studentRepository->getStudentById($student, $authUser->school_id);
        if (!$studentExists) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['El estudiante no existe.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->studentRepository->updateStudent($student, $authUser->school_id, [
            'status' => 'INACTIVE'
        ]);

        return response()->json([
            'message' => 'Estudiante eliminado.',
            'data' => new StudentResource($studentExists->fresh())
        ]);
    }
}