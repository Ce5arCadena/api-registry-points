<?php

namespace App\Repositories;

use App\Models\TeacherSubject;

class TeacherSubjectRepository {

    public function asignSubjectToTeacher(array $fields) {
        return TeacherSubject::create($fields);
    }
}