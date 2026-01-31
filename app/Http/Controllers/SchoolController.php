<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Services\SchoolService;
use App\Http\Requests\StoreSchoolRequest;
use App\Http\Requests\UpdateSchoolRequest;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SchoolController extends Controller
{
    public function __construct(protected SchoolService $schoolService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse|ResourceCollection
    {
        try {
            return $this->schoolService->getAll();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar los colegios.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSchoolRequest $request): JsonResponse
    {
        try {
            return $this->schoolService->saveSchool($request->validated());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear el colegio.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($school): JsonResponse
    {
        try {
            return $this->schoolService->showSchool($school);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar el colegio.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSchoolRequest $request, int $school): JsonResponse
    {
        try {
            return $this->schoolService->updateSchool($request->validated(), $school);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar el colegio.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $school): JsonResponse
    {
        try {
            return $this->schoolService->deleteSchool($school);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar el colegio.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }
}
