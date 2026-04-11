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

    public function updateTeacher(int $teacherId, array $fields) {
        return Teacher::where("id", $teacherId)->update($fields);
    }

    public function saveTeacher(array $teacher) {
        return Teacher::create($teacher);
    }

    public function getTeacherById(int $teacherId, int $schoolId) {
        return Teacher::active()
            ->where('id', $teacherId)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function getTeacherByUserId(int $userId, int $schoolId) {
        return Teacher::active()
            ->where('user_id', $userId)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function getAllTeachers(int $schoolId) {
        return Teacher::where('school_id', $schoolId)
            ->paginate(50)
            ->toResourceCollection();
    }

    public function updateStates(array $ids, int $schoolId) {
        $teachersById = Teacher::whereIn('id', $ids)->where('school_id', $schoolId)->get();
        $teachersById->each(function ($teacher){
            $teacher->update(['status' => $teacher->status === "INACTIVE" ? "ACTIVE" : "INACTIVE"]);
        });

        $teachers = $this->getAllTeachers($schoolId);

        return [
            $teachers,
            $teachersById
        ];
    }
}