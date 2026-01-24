<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;
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
}