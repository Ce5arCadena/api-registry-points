<?php

namespace App\Http\Services;

use App\Models\User;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubjectService {
    public function store(array $fields, User $user) {
        $subjectByNameExists = Subject::where('status', 'ACTIVE')
            ->where('name', trim($fields['name']))
            ->where('school_id', $user->school_id)
            ->exists();
        
        if ($subjectByNameExists) throw new ConflictHttpException('Ya existe una materia con el mismo nombre. Use otro.');

        $teacherExists = Teacher::where('id', $fields['teacher'])
            ->where('school_id', $user->school_id)
            ->where('status', 'ACTIVE')
            ->first();
        if (!$teacherExists) throw new NotFoundHttpException('No existe el maestro especificado');

        $gradeExists = Grade::where('id', $fields['grade'])
            ->where('school_id', $user->school_id)
            ->where('status', 'ACTIVE')
            ->first();
        if (!$gradeExists) throw new NotFoundHttpException('No existe el curso especificado');

        $newSubject = new Subject;
        $newSubject->name = trim($fields['name']);
        $newSubject->teacher_id = $teacherExists->id;
        $newSubject->grade_id = $gradeExists->id;
        $newSubject->school_id = $user->school_id;
        $newSubject->save();

        return response()->json([
            'message' => 'Materia creada éxitosamente.',
            'errors' => [],
            'data' => [
                'subject' => $newSubject
            ]
        ]);
    }
}