<?php

namespace App\Http\Controllers;

use App\Models\RegistryPoint;
use App\Http\Services\RegistryPointService;
use App\Http\Requests\StoreRegistryPointRequest;
use App\Http\Requests\UpdateRegistryPointRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistryPointController extends Controller
{

    public function __construct(protected RegistryPointService $registryPointService) {}

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
    public function store(StoreRegistryPointRequest $request): JsonResponse
    {
        try {
            return $this->registryPointService->registryPoints($request);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al asignar los puntos.',
                'errors' => [$e->getMessage()]
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RegistryPoint $registryPoint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegistryPoint $registryPoint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegistryPointRequest $request, RegistryPoint $registryPoint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistryPoint $registryPoint)
    {
        //
    }
}
