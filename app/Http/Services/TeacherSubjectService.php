<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Auth;
use App\Repositories\TeacherSubjectRepository;
use App\Http\Resources\TeacherSubjectResource;
use App\Http\Requests\StoreTeacherSubjectRequest;

class TeacherSubjectService {

    public function __construct(protected TeacherSubjectRepository $teacherSubjectRepository) {}

    public function asignSubjectToTeacher(StoreTeacherSubjectRequest $requests) {
        $fields = $requests->validated();
        $authUser = Auth::user();
        $teacherSubject = $this->teacherSubjectRepository->asignSubjectToTeacher([
            ...$fields,
            "teacher_id" => $fields["teacher"],
            "subject_id" => $fields["subject"],
            "school_id" => $authUser->school_id
        ]);

        return response()->json([
            'message' => 'Materia asignada.',
            'data' => new TeacherSubjectResource($teacherSubject)
        ]);
    }
}