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
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'errors' => ['Debe especificar al menos un campo.'],
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $teacher = $this->teacherRepository->getTeacherById($teacher, $user->school_id);
        if (!$teacher) {
            return response()->json([
            'message' => 'Error al procesar la solicitud.',
            'errors' => ['No existe el maestro especificado.'],
            ], JsonResponse::HTTP_NOT_FOUND);
        }
                
        $dataUser = [];
        $dataTeacher = [];
        if (isset($fields['email'])) {
            $email = trim($fields['email']);

            $userExist = $this->userRepository->getUserByEmailWithStatus($email, $user->school_id);
            if (isset($userExist) && $userExist->id !== $teacher->user_id) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['No puede usar el correo especificado.'],
                ], JsonResponse::HTTP_CONFLICT);
            };

            $dataUser['email'] = $email;
        }

        if (isset($fields['password'])) {
            $dataUser['password'] = Hash::make($fields['password']);
        }

        if (isset($fields['full_name'])) {
            $teacherByName = $this->teacherRepository->getTeacherByName($teacher, $user->school_id);
            if ($teacherByName && $teacherByName->id !== $teacher->id) {
                return response()->json([
                    'message' => 'Error al procesar la solicitud.',
                    'errors' => ['Ya existe un maestro con el mismo nombre. Use otro.'],
                ], JsonResponse::HTTP_CONFLICT);
            };

            $dataTeacher['full_name'] = trim($fields['full_name']);
        }

        if (isset($fields['document'])) {
            $dataTeacher['document'] = trim($fields['document']);
        }

        if (isset($fields['phone'])) {
            $dataTeacher['phone'] = trim($fields['phone']);
        }

        $this->userRepository->updateUser($teacher->user_id, $dataUser);
        $teacherUpdate = $this->teacherRepository->updateTeacher($teacher->id, $dataTeacher);

        return response()->json([
            'message' => 'Maestro actualizado éxitosamente.',
            'errors' => [],
            'data' => [ new TeacherResource($teacher->fresh()) ]
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