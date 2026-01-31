<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Http\Resources\SchoolResource;
use App\Repositories\SchoolRepository;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            'errors' => [],
            'data' => new SchoolResource($newSchool)
        ]);
    }

    public function updateSchool(array $fields, School $school) {
        if (empty($fields)) {
            throw ValidationException::withMessages([
                'errors' => ['Debe enviar al menos un campo.']
            ]);
        }
        
        $user = User::findOrFail($school->user_id);
        if (isset($fields['email'])) {
            $email = trim($fields['email']);

            $exists = User::where('email', $email)
                ->where('school_id', $school->id)
                ->where('status', 'ACTIVE')
                ->where('id', '!=', $user->id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'email' => ['El correo ya está en uso.']
                ]);
            }

            $user->email = $email;
        }

        if (isset($fields['password'])) {
            $user->password = Hash::make($fields['password']);
        }

        $user->save();

        return new UserResource($user);
    }

    public function showSchool($school) {
        $school = School::where('status', 'ACTIVE')->where('id', $school)->with('user')->first();
        if (!$school) {
            throw new ModelNotFoundException('No se pudo encontrar el colegio especificado.');
        }

        return new SchoolResource($school);
    }

    public function deleteSchool($school) {
        $school = School::where('status', 'ACTIVE')->where('id', $school)->first();
        if (!$school) {
            throw new ModelNotFoundException('No se pudo encontrar el colegio especificado.');
        }

        $school->user->update(['status'=> 'INACTIVE']);
        $school->update([
            'status'=> 'INACTIVE'
        ]);

        return new SchoolResource($school);
    }

    public function getAll() {
        $schools = School::where('status', 'ACTIVE')->with('user')->paginate()->toResourceCollection();
        
        return $schools->additional([
            'message' => 'Lista de colegios.',
            'errors' => []
        ]);
    }
}