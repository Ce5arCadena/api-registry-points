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

    public function getMyGrades(int $teacherId, int $schoolId, bool $hasSubjectsAssignment) {
        return Teacher::where('school_id', $schoolId)
            ->with([
                'grades' => function($query) {
                    $query->withCount('students');
                },
                'grades.subjects' => function($query) use ($hasSubjectsAssignment, $teacherId) {
                $query->wherePivot('teacher_id', $teacherId)
                    ->when($hasSubjectsAssignment, function($query) {
                        $query->whereExists(function ($sub) {
                            $sub->select('id')
                                ->from('point_category_context')
                                ->whereColumn('point_category_context.subject_id', 'subjects.id')
                                ->whereColumn('point_category_context.grade_id', 'teacher_subject.grade_id')
                                ->whereColumn('point_category_context.school_id', 'teacher_subject.school_id')
                                ->where('point_category_context.status', 'ACTIVE');
                        });
                    });
            }])
            ->where('id', $teacherId)
            ->first();
    }

    public function getMySubjects(int $teacherId, int $schoolId, int | null $courseId = null) {
        return Teacher::where('school_id', $schoolId)
            ->when($courseId, function($query) use ($courseId) {
                $query->with(['subjects' => function($query) use ($courseId) {
                    $query->where('grade_id', $courseId);
                }]);
            })
            ->when(!$courseId, function($query) use ($courseId) {
                $query->with('subjects');
            })
            ->where('id', $teacherId)
            ->first();
    }

    public function getAllTeachersActive(int $schoolId) {
        return Teacher::active()
            ->where('school_id', $schoolId)
            ->get();
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

    public function teacherSubjectsWithCourse(int $schoolId) {
        return Teacher::whereHas('subjectAssignments', function($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->with(['subjectAssignments' => function($q) use ($schoolId) {
            $q->active()
                ->where('school_id', $schoolId)
                ->with(['subject', 'grade']);
        }])->get();
    }

    public function searchTeacher(string $field, string $value, int $schoolId) {
        return Teacher::active()
            ->where('school_id', $schoolId)
            ->whereLike($field, '%'.$value.'%')
            ->get();
    }
}