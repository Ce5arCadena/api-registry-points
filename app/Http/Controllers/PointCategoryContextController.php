<?php

namespace App\Http\Controllers;

use App\Models\PointCategoryContext;
use App\Http\Requests\UpdateStatesRequest;
use App\Http\Services\PointCategoryContextService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Http\Requests\StorePointCategoryContextRequest;
use App\Http\Requests\UpdatePointCategoryContextRequest;

class PointCategoryContextController extends Controller
{
    public function __construct(
        protected PointCategoryContextService $pointCategoryContextService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return $this->pointCategoryContextService->getPointsCategories();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al listar las asignaciones de categorías de puntos.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
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
    public function store(StorePointCategoryContextRequest $request): JsonResponse | ResourceCollection
    {
        try {
            return $this->pointCategoryContextService->savePointCategoryContext($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al asignar la categoría de puntos.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PointCategoryContext $pointCategoryContext)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PointCategoryContext $pointCategoryContext)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePointCategoryContextRequest $request, int $pointCategoryContext) : JsonResponse | ResourceCollection
    {
        try {
            return $this->pointCategoryContextService->updatePointCategoryContext($request, $pointCategoryContext);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar la asignación de categoría de puntos.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PointCategoryContext $pointCategoryContext)
    {
        //
    }

    /**
     * Updates point category context statuses.
     */
    public function changeStates(UpdateStatesRequest $request): JsonResponse | ResourceCollection
    {
        try {
            $validated = $request->validated();
            return $this->pointCategoryContextService->changeStates($validated);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al actualizar los estados de las categorías de puntos.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
