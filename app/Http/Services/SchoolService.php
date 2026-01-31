<?php
namespace App\Http\Services;

use App\Models\School;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UserRepository;
use App\Http\Resources\SchoolResource;
use App\Repositories\SchoolRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class SchoolService {
    public function __construct(
        protected SchoolRepository $schoolRepository,
        protected UserRepository $userRepository
    ) {}

    public function saveSchool(array $fields): JsonResponse {
        $userAdmin = $this->userRepository->saveUsers([
            'email' => $fields['email'],
            'password' => Hash::make($fields["password"]),
            'role' => 'SCHOOL',
        ]);

        $newSchool = $this->schoolRepository->saveSchools([
            'name' => $fields["name"],
            'user_id' => $userAdmin->id,
            'status' => 'ACTIVE'
        ]);

        $this->userRepository->updateUser($userAdmin->id, [
            'school_id' => $newSchool->id
        ]);

        return response()->json([
            'message' => 'Colegio registrado éxitosamente.',
            'data' => new SchoolResource($newSchool)
        ]);
    }

    public function updateSchool(array $fields, int $school) {
        if (empty($fields)) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Debe enviar al menos un campo.'],
            ]);
        }
        
        $schoolById = $this->schoolRepository->findById($school);
        if (!$schoolById) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['El colegio no existe.'],
            ]);
        }    

        if(isset($fields['name'])) {
            $schoolByName = $this->schoolRepository->findByName($fields['name']);
            if ($schoolByName && $schoolByName->id !== $schoolById->id) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['El nombre ya se encuentra en uso.'],
                ]);
            }
            $this->schoolRepository->updateSchool($schoolById->id, [
                'name'=> $fields['name'],
            ]);
        }

        $dataUserUpdate = [];
        $user = $this->userRepository->findById($schoolById->user_id);
        if (isset($fields['email'])) {
            $email = trim($fields['email']);
            $exists = $this->userRepository->userExistsByName($email, $schoolById->id,$user->id);

            if ($exists) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['El correo ya está en uso.'],
                ]);
            }

            $dataUserUpdate['email'] = $email;
        }

        if (isset($fields['password'])) {
            $dataUserUpdate['password'] = Hash::make($fields['password']);
        }

        $this->userRepository->updateUser($user->id, $dataUserUpdate);

        return response()->json([
            'message' => 'Colegio actualizado.',
            'data' => new SchoolResource($schoolById->fresh())
        ]);
    }

    public function showSchool($school) {
        $schoolById = $this->schoolRepository->findById($school);
        if (!$schoolById) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['El colegio no existe.'],
            ]);
        }

        return response()->json([
            'message' => 'Colegio encontrado.',
            'data' => new SchoolResource($schoolById)
        ]);
    }

    public function deleteSchool(int $school) {
        $schoolById = $this->schoolRepository->findById($school);
        if (!$schoolById) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['El colegio no existe.'],
            ]);
        } 

        $this->schoolRepository->updateSchool($schoolById->id, [
            'status'=> 'INACTIVE',
        ]);
        $this->userRepository->updateUser($schoolById->user_id, [
            'status' => 'INACTIVE'
        ]);

        return response()->json([
            'message' => 'Colegio eliminado.',
            'data' => new SchoolResource($schoolById->fresh())
        ]);
    }

    public function getAll() {
        $schools = $this->schoolRepository->getAllSchoolsWithPaginate();
        
        return $schools->additional([
            'message' => 'Lista de colegios.'
        ]);
    }
}