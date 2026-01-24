<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthService;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request) {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login éxitoso',
            'errors' => [],
            'data' => [
                'token' => $result['token']
            ]
        ]);
    }
}
