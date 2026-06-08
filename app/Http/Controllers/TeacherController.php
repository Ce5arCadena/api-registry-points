<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SearchRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\TeacherService;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateStatesRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TeacherController extends Controller
{
    public function __construct(protected TeacherService $teacherService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse|ResourceCollection
    {
        try {
            $user = Auth::user();
            return $this->teacherService->getAll($user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar los maestros.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function searchTeacher(SearchRequest $request) {
        try {
            return $this->teacherService->searchTeacher($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar el maestro.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $userAuth = Auth::user();
            return $this->teacherService->saveTeacher($validated, $userAuth);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al guardar el maestro.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $teacher): JsonResponse
    {
        try {
            $user = Auth::user();
            return $this->teacherService->showTeacher($teacher, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar el maestro.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, int $teacher): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = Auth::user();
            return $this->teacherService->updateTeacher($validated,  $teacher, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el maestro.',
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
            $user = Auth::user();
            return $this->teacherService->changeStates($validated, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar los estados de los maestros.',
                'errors' => [$e->getMessage(), $e->getLine(), $e->getFile()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $teacher): JsonResponse
    {
        try {
            $user = Auth::user();
            return $this->teacherService->deleteTeacher($teacher, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el maestro.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Lista de cursos asociados al maestro.
     */
    public function myGrades(): JsonResponse|ResourceCollection
    {
        try {
            $user = Auth::user();
            return $this->teacherService->getMyGrades($user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar tus cursos.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Lista de asignaturas asociadas al maestro.
     */
    public function mySubjects(Request $request): JsonResponse|ResourceCollection
    {
        try {
            $user = Auth::user();
            return $this->teacherService->getMySubjects($user, $request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar tus asignaturas.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
