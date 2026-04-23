<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\GradeService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreGradeRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GradeController extends Controller
{
    public function __construct(protected GradeService $gradeService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|ResourceCollection
    {
        try {
            $user = Auth::user();
            return $this->gradeService->getAll($user, $request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar los cursos.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGradeRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = Auth::user();
    
            return $this->gradeService->store($validated, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el curso.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $grade): JsonResponse
    {
        try {
            $user = Auth::user();
            return $this->gradeService->showGrade($grade, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar el curso.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreGradeRequest $request, int $grade): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = Auth::user();
    
            return $this->gradeService->update($validated, $grade, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al editar el curso.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $grade): JsonResponse
    {
        try {
            $user = Auth::user();
            return $this->gradeService->destroy($grade, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el curso.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }
}
