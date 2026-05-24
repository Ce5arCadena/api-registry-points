<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PointCategoryContext;
use App\Http\Services\PointCategoryContextService;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Http\Requests\StorePointCategoryContextRequest;

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
        //
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
    public function store(StorePointCategoryContextRequest $request): JsonResponse
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
    public function update(Request $request, PointCategoryContext $pointCategoryContext)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PointCategoryContext $pointCategoryContext)
    {
        //
    }
}
