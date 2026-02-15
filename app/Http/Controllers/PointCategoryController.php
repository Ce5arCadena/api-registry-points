<?php

namespace App\Http\Controllers;

use App\Models\PointCategory;
use App\Http\Services\PointCategoryService;
use App\Http\Requests\StorePointCategoryRequest;
use App\Http\Requests\UpdatePointCategoryRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class PointCategoryController extends Controller
{
    public function __construct(protected PointCategoryService $pointCategoryService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return $this->pointCategoryService->getPointsCategories();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear listar las categorías de puntos.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePointCategoryRequest $request): JsonResponse
    {
        try {
            return $this->pointCategoryService->createPointCategory($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al crear la categoría de puntos.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $pointCategory): JsonResponse
    {
        try {
            return $this->pointCategoryService->getPointCategory($pointCategory);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al buscar la categoría de puntos.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PointCategory $pointCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePointCategoryRequest $request, int $pointCategory) 
    {
        try {
            return $this->pointCategoryService->updatePointCategory($request, $pointCategory);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al editar la categoría de puntos.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $pointCategory): JsonResponse
    {
        try {
            return $this->pointCategoryService->deletePointCategory($pointCategory);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al eliminar la categoría de puntos.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }
}
