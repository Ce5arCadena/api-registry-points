<?php

namespace App\Repositories;

use App\Models\User;

class AuthRepository {

    public function existUserByEmail(string $email) {
        return User::active()->where('email', trim($email))->first();
    }
}