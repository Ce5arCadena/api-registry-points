<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\PointCategoryRepository;
use App\Http\Resources\PointCategoryResource;
use App\Http\Requests\StorePointCategoryRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class PointCategoryService {
    public function __construct(protected PointCategoryRepository $pointCategoryRepository) {}

    public function createPointCategory(StorePointCategoryRequest $request): JsonResponse {
        $authUser = Auth::user();
        $data = $request->validated();
        $pointCategory = $this->pointCategoryRepository->createPointCategory([
            ...$data,
            "subject_id" => $data["subject"],
            "school_id" => $authUser->school_id
        ]);

        return response()->json([
            'message' => 'Categoria de puntos creada.',
            'data' => new PointCategoryResource($pointCategory)
        ]);
    }
}