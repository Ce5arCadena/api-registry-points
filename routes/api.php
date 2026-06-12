<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PointCategoryController;
use App\Http\Controllers\RegistryPointController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\PointCategoryContextController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/create-admin', [UserController::class, 'createSuperAdmin']);

// Rutas de colegio
Route::middleware(['auth:sanctum', 'abilities:admin:schools'])
    ->prefix('schools')
    ->controller(SchoolController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/info/dashboard', 'getInfoSchool');
        Route::get('{school}', 'show');
        Route::patch('{school}', 'update');
        Route::delete('{school}', 'destroy');
    });
    
// Ruta de consulta dashboard
Route::middleware(['auth:sanctum','abilities:school:info'])
    ->prefix('info')
    ->controller(SchoolController::class)
    ->group(function() {
        Route::get('/', 'getInfoSchool');
    });

// Rutas de grados
Route::middleware(['auth:sanctum','abilities:school:courses'])
    ->prefix('courses')
    ->controller(GradeController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/search', 'searchCourse');
        Route::get('{grade}', 'show');
        Route::put('{grade}', 'update');
        Route::delete('{grade}', 'destroy');
    });

// Rutas de profesores
Route::middleware(['auth:sanctum','abilities:school:teachers'])
    ->prefix('teachers')
    ->controller(TeacherController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/search', 'searchTeacher');
        Route::get('{teacher}', 'show');
        Route::put('{teacher}', 'update');
        Route::delete('{teacher}', 'destroy');
        Route::patch('/state', 'changeStates');
    });

// Rutas de materias
Route::middleware(['auth:sanctum','abilities:school:teachers'])
    ->prefix('subjects')
    ->controller(SubjectController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('/search', 'searchSubject');
        Route::get('{subject}', 'show');
        Route::put('{subject}', 'update');
        Route::delete('{subject}', 'destroy');
    });

// Rutas de estudiantes
Route::middleware(['auth:sanctum','abilities:school:students'])
    ->prefix('students')
    ->controller(StudentController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{student}', 'show');
        Route::put('{student}', 'update');
        Route::delete('{student}', 'destroy');
        Route::patch('/state', 'changeStates');
    });

// Rutas de categoría de puntos
Route::middleware(['auth:sanctum','abilities:teacher:point_categories'])
    ->prefix('point-categories')
    ->controller(PointCategoryController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{category}', 'show');
        Route::put('{category}', 'update');
        Route::delete('{category}', 'destroy');
        Route::patch('/state', 'changeStates');
    });

// Rutas de asignación de categoría de puntos en asignaturas y cursos
Route::middleware(['auth:sanctum','abilities:teacher:point_categories'])
    ->prefix('point-category-contexts')
    ->controller(PointCategoryContextController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::patch('/state', 'changeStates');
        Route::get('{category}', 'show');
        Route::patch('{category}', 'update');
        Route::delete('{category}', 'destroy');
    });

// Rutas de asignación de materias a profesores
Route::middleware(['auth:sanctum','abilities:school:teachers_subjects'])
    ->prefix('teachers-subjects')
    ->controller(TeacherSubjectController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{teacherSubject}', 'show');
        Route::put('{teacherSubject}', 'update');
        Route::delete('{teacherSubject}', 'destroy');
    });

// Obtiene los cursos asociados al maestro
Route::middleware(['auth:sanctum','abilities:teacher:get_grades'])
    ->prefix('teacher')
    ->controller(TeacherController::class)
    ->group(function() {
        Route::get('/courses', 'myGrades');
        Route::get('/subjects', 'mySubjects');
    });

// Obtiene los estudiantes asociados a un curso según el maestro que consulta
Route::middleware(['auth:sanctum','abilities:teacher:grades.view_students'])
    ->prefix('teacher/grades')
    ->controller(TeacherSubjectController::class)
    ->group(function() {
        Route::get('/{gradeId}/subjects/{subjectId}/students', 'getStudentsWithPoints');
    });

// Rutas de asignación de puntos a estudiantes
Route::middleware(['auth:sanctum','abilities:teacher:registry_points'])
    ->prefix('registry-points')
    ->controller(RegistryPointController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{registryPoint}', 'show');
        Route::put('{registryPoint}', 'update');
        Route::delete('{registryPoint}', 'destroy');
    });