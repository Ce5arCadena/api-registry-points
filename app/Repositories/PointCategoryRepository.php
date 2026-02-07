<?php

namespace App\Repositories;

use App\Models\PointCategory;

class PointCategoryRepository {
    public function createPointCategory(array $fields) {
        return PointCategory::create($fields);
    }
}