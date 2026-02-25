<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepository {
    public function saveSchools(array $fields) {
        return School::create($fields);
    }

    public function updateSchool(int $school, array $fields) {
        return School::where("id", $school)->update($fields);
    }

    public function findById(int $schoolId) {
        return School::active()->where('id', $schoolId)->first();
    }

    public function findByName(string $name) {
        return School::where('name', $name)->first();
    }

    public function getAllSchoolsWithPaginate() {
        return School::active()
            // ->with('user')
            ->paginate()
            ->toResourceCollection();
    }
}