<?php

namespace App\Http\Controllers;

use App\Http\Services\TeacherSubjectService;
use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Requests\UpdateTeacherSubjectRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TeacherSubjectController extends Controller
{
    public function __construct(protected TeacherSubjectService $teacherSubjectService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse|ResourceCollection
    {
        try {
            return $this->teacherSubjectService->getAllTeachersSubjects();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar la asignación de materias.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherSubjectRequest $request): JsonResponse
    {
        try {
            return $this->teacherSubjectService->asignSubjectToTeacher($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al asignar la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $teacherSubject): JsonResponse
    {
        try {
            return $this->teacherSubjectService->getTeacherSubject($teacherSubject);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar la asignación de la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherSubjectRequest $request, int $teacherSubject): JsonResponse
    {
        try {
            return $this->teacherSubjectService->updateAsignSubjectToTeacher($request, $teacherSubject);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar la asignación de la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $teacherSubject): JsonResponse
    {
        try {
            return $this->teacherSubjectService->deleteTeacherSubject($teacherSubject);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar la asignación de la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
