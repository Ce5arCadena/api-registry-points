<?php

namespace App\Repositories;

use App\Models\Teacher;

class TeacherRepository {
    public function getTeacherByName(string $fullName, int $schoolId) {
        return Teacher::active()
            ->where("full_name", $fullName)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function saveTeacher(array $teacher) {
        return Teacher::create($teacher);
    }
}