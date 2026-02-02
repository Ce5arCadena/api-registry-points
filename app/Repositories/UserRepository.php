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

    public function findById(int $id) {
        return User::where('status', 'ACTIVE')
            ->where('id', $id)
            ->first();
    }

    public function userExistsByEmail(string $email, int $schoolId, int $userId) {
        return User::where('email', $email)
            ->where('school_id', $schoolId)
            ->where('status', 'ACTIVE')
            ->where('id', '!=', $userId)
            ->exists();
    }

    public function userByEmail(string $email, int $schoolId) {
        return User::where('email', $email)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function getUserByEmailWithStatus(string $email, int $schoolId) {
        return User::active()
            ->where('email', $email)
            ->where('school_id', $schoolId)
            ->first();
    }
}