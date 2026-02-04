<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\TeacherService;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeacherController extends Controller
{
    public function __construct(protected TeacherService $teacherService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request)
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
    public function show(int $teacher)
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
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTeacherRequest $request, int $teacher)
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
     * Remove the specified resource from storage.
     */
    public function destroy(int $teacher)
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
}
