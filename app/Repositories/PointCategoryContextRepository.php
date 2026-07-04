<?php

namespace App\Repositories;

use App\Models\PointCategoryContext;

class PointCategoryContextRepository {
    public function createPointCategoryContext(array $fields) {
        return PointCategoryContext::create($fields);
    }

    public function existCategoryContext(array $fields) {
        return PointCategoryContext::active()
            ->where('point_category_id', $fields['point_category_id'])
            ->where('grade_id', $fields['grade_id'])
            ->where('subject_id', $fields['subject_id'])
            ->where('school_id', $fields['school_id'])
            ->first();
    }

    public function getPointCategoryContextById(array $fields) {
        return PointCategoryContext::active()
            ->where('id', $fields['id'])
            ->where('school_id', $fields['school_id'])
            ->whereHas('pointCategory', function($query) use ($fields) {
                $query->where('teacher_id', $fields['teacher_id'])
                    ->where('school_id', $fields['school_id'])
                    ->where('status', 'ACTIVE');
            })
            ->first();
    }

    public function getPointCategoryContextByCategoryId(array $fields) {
        return PointCategoryContext::active()
            ->where('id', $fields['point_category_id'])
            ->where('school_id', $fields['school_id'])
            ->whereHas('pointCategory', function($query) use ($fields) {
                $query->where('teacher_id', $fields['teacher_id'])
                    ->where('school_id', $fields['school_id'])
                    ->where('status', 'ACTIVE');
            })
            ->first();
    }

    public function updatePointCategoryContext(int $id, int $school_id, array $fields) {
        return PointCategoryContext::where('id', $id)->where('school_id', $school_id)->update($fields);
    }

    public function getContextsByGradeAndSubject(array $fields)
    {
        return PointCategoryContext::active()
            ->where('grade_id', $fields['grade_id'])
            ->where('subject_id', $fields['subject_id'])
            ->where('school_id', $fields['school_id'])
            ->whereHas('pointCategory', function ($query) use ($fields) {
                $query->where('teacher_id', $fields['teacher_id'])
                    ->where('status', 'ACTIVE');
            })
            ->with('pointCategory:id,name,max_points')
            ->get();
    }

    public function updateStates(array $ids, int $schoolId) {
        $pointCategoriesId = PointCategoryContext::whereIn('id', $ids)->where('school_id', $schoolId)->get();
        $pointCategoriesId->each(function ($pointCategoryContext) {
            $pointCategoryContext->update(['status' => $pointCategoryContext->status === "INACTIVE" ? "ACTIVE" : "INACTIVE"]);
        });

        return [
            $pointCategoriesId
        ];
    }
}