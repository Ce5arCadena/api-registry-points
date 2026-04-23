<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\GradeRepository;
use App\Repositories\StudentRepository;
use App\Http\Resources\StudentResource;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStatesRequest;
use App\Http\Requests\UpdateStudentRequest;
use Illuminate\Http\Request;
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

    public function listStudents(Request $request) {
        $authUser = Auth::user();

        return $this->studentRepository->getAll($authUser->school_id, $request->gradeId)->additional([
            'message' => 'Lista de estudiantes'
        ]);
    }

    public function getStudent(int $student): JsonResponse {
        $authUser = Auth::user();

        $studentExists = $this->studentRepository->getStudentById($student, $authUser->school_id);
        if (!$studentExists) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['El estudiante no existe.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Estudiante encontrado.',
            'data' => new StudentResource($studentExists)
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

    public function changeStates(UpdateStatesRequest $request) {
        $user = Auth::user();
        if (!$request->has("grade")) {
            return response()->json([
                'message' => 'Error al procesar la solicitud',
                'errors' => ['El curso al que pertenecen los estudiantes es requerido.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        [$data, $studentsById] = $this->studentRepository->updateStates(array_values($request["ids"]), $request["grade"], $user->school_id);
        $unprocessedStudents = array_diff(array_values($request["ids"]), $studentsById->pluck('id')->toArray());
        $message = count($unprocessedStudents) > 0 ? "Ids de registros que no existen => " . implode(",", $unprocessedStudents) : "Estudiantes actualizados";

        return $data->additional([
            "message" => $message
        ]);
    }
}