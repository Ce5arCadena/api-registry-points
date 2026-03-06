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

    public function getStudentById(int $id, int $schoolId) {
        return Student::active()->where('id', $id)->where('school_id', $schoolId)->first();
    }

    public function getAll(int $schoolId) {
        return Student::active()->where('school_id', $schoolId)->paginate(50)->toResourceCollection();
    }

    public function updateStudent(int $id, int $schoolId, array $data) {
        return $this->getStudentById($id, $schoolId)->update($data);
    }
}