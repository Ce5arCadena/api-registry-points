<?php

namespace App\Repositories;

use App\Models\TeacherSubject;

class TeacherSubjectRepository {
    public function asignSubjectToTeacher(array $fields) {
        return TeacherSubject::create($fields);
    }

    public function getAllTeachersSubjects(int $schoolId) {
        return TeacherSubject::active()
            ->where('school_id', $schoolId)
            ->paginate()
            ->toResourceCollection();
    }

    public function getTeacherSubjectById(int $id, int $schoolId) {
        return TeacherSubject::active()
            ->where("id", $id)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function getBySubjectAndteacher(int $teacherId, int $subjectId, int $schoolId) {
        return TeacherSubject::active()
            ->where("teacher_id", $teacherId)
            ->where("subject_id", $subjectId)
            ->where('school_id', $schoolId)
            ->first();
    }

    public function getTeacherSubjectBySubject(array $fields) {
        return TeacherSubject::active()
            ->where('id', '!=', $fields['id'])
            ->where('teacher_id', $fields['teacher_id'])
            ->where('grade_id', $fields['grade_id'])
            ->where('subject_id', $fields['subject_id'])
            ->where('school_id', $fields['school_id'])
            ->first();
    }

    public function updateTeacherSubject(int $id, int $schoolId, array $fields) {
        return TeacherSubject::active()
            ->where('id', $id)
            ->where('school_id', $schoolId)
            ->update($fields);
    }

    public function getTeacherSubjectByYear(array $fields) {
        return TeacherSubject::active()
            ->where('teacher_id', $fields['teacher_id'])
            ->where('grade_id', $fields['grade_id'])
            ->where('subject_id', $fields['subject_id'])
            ->where('school_id', $fields['school_id'])
            ->where('academic_year', $fields['year'])
            ->first();
    }

    public function getTeacherSubjects(array $filters = [])
    {
        $query = TeacherSubject::active()
            ->where('school_id', $filters['school_id'])
            ->where('teacher_id', $filters['teacher_id'])
            ->where('academic_year', $filters['year']);
        
        if (isset($filters['grade_id'])) {
            return $query->where('grade_id', $filters['grade_id'])
                ->first();
        }
        
        return $query->get();
    }
}