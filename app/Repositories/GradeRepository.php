<?php

namespace App\Repositories;

use App\Models\Grade;

class GradeRepository {
    public function getGradeByName(string $name, int $schoolId) {
        return Grade::active() 
            ->where('name', $name)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function createGrade(array $fields) {
        return Grade::create($fields);
    }
}