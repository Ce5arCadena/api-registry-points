<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\SubjectService;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

class SubjectController extends Controller
{
    public function __construct(protected SubjectService $subjectService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection | JsonResponse
    {
        try {
            $user = Auth::user();
            return $this->subjectService->getAll($user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar las materias.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function searchCourse(SearchRequest $request) {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            return $this->subjectService->searchSubject($validated, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar las asignaturas.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubjectRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = Auth::user();
            return $this->subjectService->store($validated, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear la materia.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $subject)
    {
        try {
            $user = Auth::user();
            return $this->subjectService->getSubject($subject, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubjectRequest $request, int $subject): JsonResponse
    {
        try {
            $user = Auth::user();
            $request = $request->validated();
            return $this->subjectService->update($request, $subject, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al editar la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $subject)
    {
        try {
            $user = Auth::user();
            return $this->subjectService->delete($subject, $user);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar la materia.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
