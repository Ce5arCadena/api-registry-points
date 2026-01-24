<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request) {
        $messages = [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no tiene un formato válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' =>  Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
        ], $messages);

        if($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'errors' => $validator->errors()
            ], 401);
        }

        $user = User::where('email', trim($request->email))->first();
        if (!$user || !Hash::check(trim($request->password), $user->password)) {
            return response()->json([
                'message' => 'Usuario y/o contraseña incorrectos.',
                'errors' => []
            ], 401);
        }

        $token = '';
        if ($user->role === 'SUPERADMIN') {
            $token = $user->createToken('token', ['admin:schools'])->plainTextToken;    
        } else {
            $token = $user->createToken()->plainTextToken;
        }

        return response()->json([
            'message' => 'Login éxitoso',
            'errors' => [],
            'data' => [
                'token' => $token
            ]
        ]);
    }
}
