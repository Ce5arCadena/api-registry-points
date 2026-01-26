<?php
namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;

class AuthService {
    public function login(array $data) {
        $user = $this->existUser($data);

        $token = '';
        if ($user->role === 'SUPERADMIN') {
            $token = $user->createToken('api-token', ['admin:schools'])->plainTextToken;    
        } else if( $user->role === 'SCHOOL') {
            $token = $user->createToken('api-token', [
                'school:students',
                'school:grades',
                'school:subjects',
                'school:teachers'
            ])->plainTextToken;
        } else {
            $token = $user->createToken('api-token')->plainTextToken;
        }

        return [
            'token' => $token
        ];
    }

    public function existUser(array $data): User | AuthenticationException  {
        $user = User::where('email', trim($data['email']))->where('status', 'ACTIVE')->first();
        if (!$user || !Hash::check(trim($data['password']), $user->password)) {
            throw new AuthenticationException('Usuario y/o contraseña incorrectos.');
        }

        return $user;
    }
}