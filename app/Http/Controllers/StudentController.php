<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\StudentService;
use App\Http\Requests\UpdateStatesRequest;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StudentController extends Controller
{
    public function __construct(protected StudentService $studentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            return $this->studentService->listStudents($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar los estudiantes.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
            return $this->studentService->createStudent($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el estudiante.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $student): JsonResponse
    {
        try {
            return $this->studentService->getStudent($student);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar el estudiante.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, int $student)
    {
        try {
            return $this->studentService->updateStudent($request, $student);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el estudiante.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $student): JsonResponse
    {
        try {
            return $this->studentService->deleteStudent($student);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el estudiante.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Updates teacher statuses.
     */
    public function changeStates(UpdateStatesRequest $request): JsonResponse|ResourceCollection
    {
        try {
            $validated = $request->validated();
            return $this->studentService->changeStates($validated);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar los estados de los estudiantes.',
                'errors' => [$e->getMessage(), $e->getLine(), $e->getFile()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
