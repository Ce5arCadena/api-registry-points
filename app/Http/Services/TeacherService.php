<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\Teacher;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\TeacherResource;
use App\Repositories\TeacherRepository;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TeacherService {
    public function __construct(
        protected UserRepository $userRepository,
        protected TeacherRepository $teacherRepository
    ) {}

    public function saveTeacher(array $fields, User $user): JsonResponse {
        $teacherByName = $this->teacherRepository->getTeacherByName($fields["full_name"], $user->school_id);
        if ($teacherByName) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Ya existe un maestro con el mismo nombre. Use otro.'],
            ]);
        }

        $userExist = $this->userRepository->userByEmail($fields['email'], $user->school_id);
        if ($userExist) {
            if ($userExist->status === 'INACTIVE') {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['El usuario se encuentra inactivo. Puede activarlo cambiando su estado.'],
                ], JsonResponse::HTTP_CONFLICT);
            }

            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['No puede usar el correo especificado.'],
            ], JsonResponse::HTTP_CONFLICT);
        }

        $userTeacher = $this->userRepository->saveUsers([
            'email' => trim($fields["email"]),
            'password' => Hash::make($fields["password"]),
            'role' => 'TEACHER',
            'status' => 'ACTIVE',
            'school_id' => $user->school_id,
        ]);

        $newTeacher = $this->teacherRepository->saveTeacher([
            'full_name' => $fields["full_name"],
            'document' => $fields["document"],
            'phone' => $fields["phone"],
            'school_id' => $user->school_id,
            'user_id' => $userTeacher->id
        ]);

        return response()->json([
            'message' => 'Maestro registrado éxitosamente.',
            'data' => new TeacherResource($newTeacher)
        ]);
    }

    public function updateTeacher(array $fields, int $teacher, User $user) {
        if (empty($fields)) {
            throw ValidationException::withMessages([
                'errors' => ['Debe enviar al menos un campo.']
            ]);
        }
        
        $teacher = Teacher::where('id', $teacher)
            ->where('school_id', $user->school_id)
            ->where('status', 'ACTIVE')
            ->firstOr(function () {
                throw new NotFoundHttpException('No existe el maestro solicitado');
            });

        if (isset($fields['email'])) {
            $email = trim($fields['email']);

            $userExist = User::where('email', $email)
                ->where('school_id', $user->school_id)
                ->where('status', 'ACTIVE')
                ->first();
            
                if (isset($userExist) && $userExist->id !== $teacher->id) throw new ConflictHttpException('No puede usar el correo especificado.');

            $teacher->user->email = $email;
        }

        if (isset($fields['password'])) {
            $teacher->user->password = Hash::make($fields['password']);
        }

        if (isset($fields['full_name'])) {
            $teacherByName = Teacher::where("full_name", $fields["full_name"])
                ->where('school_id', $user->school_id)
                ->where('status', 'ACTIVE')
                ->first();
            if (isset($teacherByName) && $teacherByName->id !== $teacher->id) throw new ConflictHttpException('Ya existe un maestro con el mismo nombre. Use otro.');
            $teacher->full_name = trim($fields['full_name']);
        }

        if (isset($fields['document'])) {
            $teacher->document = trim($fields['document']);
        }

        if (isset($fields['phone'])) {
            $teacher->phone = trim($fields['phone']);
        }

        $teacher->save();

        return response()->json([
            'message' => 'Maestro actualizado éxitosamente.',
            'errors' => [],
            'data' => [
                'teacher' => new TeacherResource($teacher)
            ]
        ]);
    }

    public function showTeacher(int $teacher, User $user) {
        $teacher = Teacher::where('status', 'ACTIVE')
            ->where('id', $teacher)
            ->where('school_id', $user->school_id) 
            ->firstOr(function () {
                throw new ModelNotFoundException('No se pudo encontrar el maestro especificado.');
            });

        return response()->json([
            'message' => 'Maestro encontrado.',
            'errors' => [],
            'data' => [
                'teacher' => new TeacherResource($teacher)
            ]
        ]);
    }

    public function deleteTeacher(int $teacher, User $user) {
        $teacher = Teacher::where('status', 'ACTIVE')
            ->where('id', $teacher)
            ->where('school_id', $user->school_id)
            ->firstOr(function () {
                throw new ModelNotFoundException('No se pudo encontrar el maestro especificado.');
            });

        $teacher->user->update(['status'=> 'INACTIVE']);
        $teacher->update([
            'status'=> 'INACTIVE'
        ]);

        return response()->json([
            'message' => 'Maestro eliminado éxitosamente.',
            'errors' => [],
            'data' => [
                'teacher' => new TeacherResource($teacher)
            ]
        ]);
    }

    public function getAll(User $user) {
        $teachers = Teacher::where('status', 'ACTIVE')
            ->where('school_id', $user->school_id)
            ->paginate()
            ->toResourceCollection();
        
        return $teachers->additional([
            'message' => 'Lista de colegios.',
            'errors' => []
        ]);
    }
}