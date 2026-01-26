<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\Grade;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class GradeService {
    public function __construct() {}

    public function store(array $fields, User $user) {
        if (empty($fields) || !$user) throw new AuthorizationException("No está autorizado.");

        $gradeExist = Grade::where('name', trim($fields['name']))->where('school_id', $user->school_id)->first();
        if ($gradeExist) throw new ConflictHttpException('Ya existe un curso con el mismo nombre.');

        $grade = new Grade;
        $grade->name = trim($fields['name']);
        $grade->school_id = $user->school_id;
        $grade->save();

        return response()->json([
            'message' => 'Curso registrado éxitosamente.',
            'errors' => [],
            'data' => [
                'grade' => $grade
            ]
        ]);
    }
}