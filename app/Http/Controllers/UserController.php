<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function createSuperAdmin() {
        dump(config('services.admin.email'), config('services.admin.pass'));
        $userAdmin = new User;
        $userAdmin->email = config('services.admin.email');
        $userAdmin->password = Hash::make(config('services.admin.pass'));
        $userAdmin->role = "SUPERADMIN";
        $userAdmin->status = "ACTIVE";

        $userAdmin->save();

        return response()->json([
            'data' => collect($userAdmin->toArray()),
            'message' => 'SuperAdmin creado',
            'success' => 'ok'
        ]);
    }
}
