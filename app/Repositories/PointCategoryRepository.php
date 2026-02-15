<?php

namespace App\Repositories;

use App\Models\PointCategory;

class PointCategoryRepository {
    public function getPointsCategories(int $schoolId) {
        return PointCategory::active()
            ->where("school_id", $schoolId)
            ->paginate()
            ->toResourceCollection();
    }

    public function createPointCategory(array $fields) {
        return PointCategory::create($fields);
    }

    public function getPointCategoryByName(array $fields) {
        return PointCategory::active()
            ->where('name', $fields['name'])
            ->where('teacher_id', $fields['teacher_id'])
            ->where('subject_id', $fields['subject_id'])
            ->where('school_id', $fields['school_id'])
            ->first();
    }

    public function getPointCategoryById(array $fields) {
        return PointCategory::active()
            ->where('id', $fields['id'])
            ->where('teacher_id', $fields['teacher_id'])
            ->where('school_id', $fields['school_id'])
            ->first();
    }

    public function updateCategoryPoint(array $fieldsConditions, array $newData) {
        return PointCategory::active()
            ->where('id', $fieldsConditions['id'])
            ->where('teacher_id', $fieldsConditions['teacher_id'])
            ->where('school_id', $fieldsConditions['school_id'])
            ->update($newData);
    }
}