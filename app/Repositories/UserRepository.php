<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository {
    public function saveUsers(array $fields) {
        return User::create($fields);
    }

    public function updateUser(int $userId, array $fields) {
        return User::where("id", $userId)->update($fields);
    }
}