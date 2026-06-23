<?php

namespace App\Repositories;

use App\Models\RegistryPoint;

class RegistryPointRepository {
    public function createRegistryPoint(array $fields) {
        return RegistryPoint::upsert(
            [$fields],
            ["student_id", "point_category_context_id", "academic_year"],
            ["points", "updated_at"]
        );
    }

    public function getByStudentsAndContexts(array $studentIds, array $contextIds, int $academicYear)
    {
        return RegistryPoint::whereIn('student_id', $studentIds)
            ->whereIn('point_category_context_id', $contextIds)
            ->where('academic_year', $academicYear)
            ->get(['student_id', 'point_category_context_id', 'points']);
    }
}