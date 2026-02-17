<?php

namespace App\Repositories;

use App\Models\RegistryPoint;

class RegistryPointRepository {
    public function createRegistryPoint(array $fields) {
        return RegistryPoint::upsert(
        [$fields], 
        ["student_id", "point_category_id", "subject_id", "school_id"], 
        ["points", "updated_at"]
        );
    }
}