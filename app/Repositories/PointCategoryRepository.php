<?php

namespace App\Repositories;

use App\Models\PointCategory;

class PointCategoryRepository {
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
}