<?php

namespace App\Repositories;

use App\Models\PointCategory;

class PointCategoryRepository
{
    public function getPointsCategories(int $schoolId)
    {
        return PointCategory::active()
            ->where("school_id", $schoolId)
            ->paginate(150)
            ->toResourceCollection();
    }

    public function getPointCategoriesByContext(array $fields)
    {
        return PointCategory::with('pointCategoryContext.course', 'pointCategoryContext.subject')
            ->where('teacher_id', $fields['teacher_id'])
            ->where('school_id', $fields['school_id'])
            ->paginate(150)
            ->toResourceCollection();
    }

    public function createPointCategory(array $fields)
    {
        return PointCategory::create($fields);
    }

    public function getPointCategoryByName(array $fields)
    {
        return PointCategory::active()
            ->where('name', $fields['name'])
            ->where('teacher_id', $fields['teacher_id'])
            ->where('school_id', $fields['school_id'])
            ->first();
    }

    public function getPointCategoryById(array $fields)
    {
        return PointCategory::active()
            ->where('id', $fields['id'])
            ->where('teacher_id', $fields['teacher_id'])
            ->where('school_id', $fields['school_id'])
            ->first();
    }

    public function getPointCategoryByIdAndSubject(array $fields)
    {
        return PointCategory::active()
            ->where('id', $fields['id'])
            ->where('teacher_id', $fields['teacher_id'])
            ->where('subject_id', $fields['subject_id'])
            ->where('school_id', $fields['school_id'])
            ->first();
    }

    public function updateCategoryPoint(array $fieldsConditions, array $newData)
    {
        return PointCategory::active()
            ->where('id', $fieldsConditions['id'])
            ->where('teacher_id', $fieldsConditions['teacher_id'])
            ->where('school_id', $fieldsConditions['school_id'])
            ->update($newData);
    }

    public function updateStates(array $ids, int $schoolId)
    {
        $pointCategoriesId = PointCategory::whereIn('id', $ids)->where('school_id', $schoolId)->get();
        $pointCategoriesId->each(function ($teacher) {
            $teacher->update(['status' => $teacher->status === "INACTIVE" ? "ACTIVE" : "INACTIVE"]);
        });

        return [
            $pointCategoriesId
        ];
    }

    public function getCategoriesBySubjectAndGrade(int $subjectId, int $gradeId, int $schoolId)
    {
        return PointCategory::whereHas('pointCategoryContext', function ($query) use ($subjectId, $gradeId, $schoolId) {
            $query->where('subject_id', $subjectId)
                ->where('grade_id', $gradeId)
                ->where('school_id', $schoolId)
                ->where('status', 'ACTIVE');
        })
        ->where('status', 'ACTIVE')
        ->get();
    }
}
