<?php

namespace App\Repositories;

use App\Models\Student;

class StudentRepository {
    
    public function getStudentByDocument(int $document, int $schoolId) {
        return Student::active()
            ->where("document", $document)
            ->where("school_id", $schoolId)
            ->first();
    }

    public function createStudent(array $data) {
        return Student::create($data);
    }
}