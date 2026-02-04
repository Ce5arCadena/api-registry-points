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

    public function getGradeById(int $grade, int $schoolId) {
        return Grade::active()
            ->where('id', $grade)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function updateGrade(int $grade, int $schoolId, array $fields) {
        return $this->getGradeById($grade, $schoolId)->update($fields);
    }
}