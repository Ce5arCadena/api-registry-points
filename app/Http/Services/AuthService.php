<?php
namespace App\Http\Services;

use App\Models\User;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthService {
    public function __construct(protected AuthRepository $authRepository) {}

    public function login(array $data): JsonResponse {
        $user = $this->existUser($data);

        $token = '';
        $nameUser = '';
        if ($user->role === 'SUPERADMIN') {
            $token = $user->createToken('api-token', ['admin:schools'])->plainTextToken;
            $nameUser = 'SUPERADMIN';
        } else if( $user->role === 'SCHOOL') {
            $token = $user->createToken('api-token', [
                'school:students',
                'school:courses',
                'school:subjects',
                'school:teachers',
                'school:teachers_subjects',
                'school:info'
            ])->plainTextToken;
            $nameUser = $user->school->name;
        } else if( $user->role === 'TEACHER') {
            $token = $user->createToken('api-token', [
                'teacher:get_grades',
                'teacher:registry_points',
                'teacher:point_categories',
                'teacher:grades.view_students',
            ])->plainTextToken;
            $nameUser = $user->teacher->full_name;
        } else {
            $token = $user->createToken('api-token')->plainTextToken;
        }

        return response()->json([
            'message' => 'Login éxitoso',
            'data' => [
                'token' => $token,
                'rol' => $user->role,
                'name' => $nameUser
            ]
        ]);
    }

    public function existUser(array $data): User | AuthenticationException  {
        $user = $this->authRepository->existUserByEmail($data['email']);
        if (!$user || !Hash::check(trim($data['password']), $user->password)) {
            throw new AuthenticationException('Usuario y/o contraseña incorrectos.');
        }

        return $user;
    }
}