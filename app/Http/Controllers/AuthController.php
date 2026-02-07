<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthService;
use App\Http\Requests\LoginRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request): JsonResponse {
        try {
            return $this->authService->login($request->validated());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al ejecutar la petición.',
                'errors' => [$e->getMessage()]
            ]);
        }
    }
}
