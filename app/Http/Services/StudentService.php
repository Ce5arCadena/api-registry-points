<?php

namespace App\Http\Services;

use App\Repositories\StudentRepository;

class StudentService {
    public function __construct(protected StudentRepository $studentRepository) {}
}