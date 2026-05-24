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
}