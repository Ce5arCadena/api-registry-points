<?php

namespace App\Repositories;

use App\Http\Resources\SubjectResource;
use App\Models\Subject;

class SubjectRepository {
    public function getSubjects(int $schoolId) {
        return Subject::where('status', 'ACTIVE')->where('school_id', $schoolId)->paginate()->toResourceCollection();
    }
    public function getById(int $id, int $schoolId) {
        return Subject::where('status', 'ACTIVE')
            ->where('id', trim($id))
            ->where('school_id', $schoolId)
            ->first();
    }

    public function getByName(string $name, int $schoolId) {
        return Subject::where('status', 'ACTIVE')
            ->where('name', trim($name))
            ->where('school_id', $schoolId)
            ->first();
    }

    public function update(int $subjectId, int $schoolId, array $data) {
        return $this->getById($subjectId, $schoolId)->update($data);
    }

    public function create(array $data): Subject {
        return Subject::create($data);
    }
}