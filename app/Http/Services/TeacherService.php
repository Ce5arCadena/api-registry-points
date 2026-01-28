<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Resources\SchoolResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class TeacherService {
    public function saveTeacher(array $fields, User $user) {
        $teacherByName = Teacher::where("full_name", $fields["full_name"])
            ->where('school_id', $user->school_id)
            ->where('status', 'ACTIVE')
            ->first();
        if ($teacherByName) throw new ConflictHttpException('Ya existe un maestro con el mismo nombre. Use otro.');

        $userTeacher = new User;
        $userTeacher->email = trim($fields["email"]);
        $userTeacher->password = Hash::make($fields["password"]);
        $userTeacher->role = "TEACHER";
        $userTeacher->status = "ACTIVE";
        $userTeacher->school_id = $user->school_id;
        $userTeacher->save();

        $newTeacher = new Teacher;
        $newTeacher->full_name = $fields["full_name"];
        $newTeacher->document = $fields["document"];
        $newTeacher->phone = $fields["phone"];
        $newTeacher->status = 'ACTIVE';
        $newTeacher->school_id = $user->school_id;
        $newTeacher->user_id = $userTeacher->id;
        $newTeacher->save();

        return response()->json([
            'message' => 'Curso registrado éxitosamente.',
            'errors' => [],
            'data' => [
                'teacher' => $newTeacher
            ]
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