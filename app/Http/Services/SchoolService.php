<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\School;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;

class SchoolService {
    public function saveSchool(array $fields) {
        $userAdmin = new User;
        $userAdmin->email = trim($fields["email"]);
        $userAdmin->password = Hash::make($fields["password"]);
        $userAdmin->role = "SCHOOL";
        $userAdmin->status = "ACTIVE";
        $userAdmin->save();

        $newSchool = new School;
        $newSchool->name = $fields["name"];
        $newSchool->user_id = $userAdmin->id;
        $newSchool->status = 'ACTIVE';
        $newSchool->save();

        $userAdmin->school_id = $newSchool->id;
        $userAdmin->save();

        return [
            'school' => $newSchool
        ];
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

    public function deleteSchool($school) {
        $school = School::where('status', 'ACTIVE')->where('id', $school)->first();
        if (!$school) {
            throw new ModelNotFoundException('No se pudo encontrar el colegio especificado.');
        }

        $school->user->update(['status'=> 'INACTIVE']);
        $school->update([
            'status'=> 'INACTIVE'
        ]);

        return new UserResource($school);
    }
}