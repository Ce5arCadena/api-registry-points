<?php
namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;

class AuthService {
    public function login(array $data) {
        $user = User::where('email', trim($data['email']))->where('status', 'ACTIVE')->first();
        if (!$user || !Hash::check(trim($data['password']), $user->password)) {
            throw new AuthenticationException('Usuario y/o contraseña incorrectos.');
        }

        $token = '';
        if ($user->role === 'SUPERADMIN') {
            $token = $user->createToken('api-token', ['admin:schools'])->plainTextToken;    
        } else {
            $token = $user->createToken()->plainTextToken;
        }

        return [
            'token' => $token
        ];
    }
}