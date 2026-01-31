<?php

namespace App\Repositories;

use App\Models\School;

class SchoolRepository {
    public function saveSchools(array $fields) {
        return School::create($fields);
    }
}