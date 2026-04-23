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

    public function getAll(int $schoolId, ?int $gradeId) {
        return Student::where('school_id', $schoolId)
            ->when($gradeId, function($query) use ($gradeId) {
                return $query->where('grade_id', $gradeId);
            })
            ->paginate(50)
            ->toResourceCollection();
    }

    public function updateStudent(int $id, int $schoolId, array $data) {
        return $this->getStudentById($id, $schoolId)->update($data);
    }

    public function updateStates(array $ids, int $grade, int $schoolId) {
        $studentsById = Student::whereIn('id', $ids)->where('school_id', $schoolId)->get();
        $studentsById->each(function ($student){
            $student->update(['status' => $student->status === "INACTIVE" ? "ACTIVE" : "INACTIVE"]);
        });

        $students = $this->getAll($schoolId, $grade);

        return [
            $students,
            $studentsById
        ];
    }
}