<?php
namespace App\Http\Services;

use App\Models\User;
use App\Models\Grade;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function update(array $fields, int $grade, User $user) {
        if (empty($fields) || !$user) throw new AuthorizationException("No está autorizado.");

        $gradeExist = Grade::where('id', $grade)
            ->where('status', 'ACTIVE')
            ->where('school_id', $user->school_id)
            ->firstOr(function () {
                throw new NotFoundHttpException('No existe el curso especificado');
            });
        
        $gradeByName = Grade::where('name', $fields['name'])->where('status', 'ACTIVE')->where("school_id", $user->school_id)->first();
        if (isset($gradeByName) && $gradeExist->id !== $gradeByName->id) throw new ConflictHttpException('Ya existe un curso con el mismo nombre. Use otro.');

        $gradeExist->update([
            'name' => $fields['name']
        ]);
        
        return response()->json([
            'message' => 'Curso actualizado éxitosamente.',
            'errors' => [],
            'data' => [
                'grade' => $gradeExist
            ]
        ]);
    }

    public function showGrade(int $grade, User $user) {
        $grade = Grade::where('id', $grade)->where('school_id', $user->school_id)->firstOr(function () {
            throw new NotFoundHttpException('No existe el curso especificado');
        });

        return response()->json([
            'message' => 'Curso listado.',
            'errors' => [],
            'data' => [
                $grade
            ]
        ]);
    }

    public function destroy(int $grade, User $user) {
        $grade = Grade::where('id', $grade)->where('school_id', $user->school_id)->firstOr(function () {
            throw new NotFoundHttpException('No existe el curso especificado');
        });
        
        $grade->update([
            'status' => 'INACTIVE'
        ]);

        return response()->json([
            'message' => 'Curso eliminado éxitosamente.',
            'errors' => []
        ]);
    }
}