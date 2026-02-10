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
use App\Http\Controllers\TeacherSubjectController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/create-admin', [UserController::class, 'createSuperAdmin']);

// Rutas de colegio
Route::middleware(['auth:sanctum', 'abilities:admin:schools'])
    ->prefix('schools')
    ->controller(SchoolController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{school}', 'show');
        Route::patch('{school}', 'update');
        Route::delete('{school}', 'destroy');
    });
    
// Rutas de grados
Route::middleware(['auth:sanctum','abilities:school:grades'])
    ->prefix('grades')
    ->controller(GradeController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
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
        Route::get('{teacher}', 'show');
        Route::put('{teacher}', 'update');
        Route::delete('{teacher}', 'destroy');
    });

// Rutas de materias
Route::middleware(['auth:sanctum','abilities:school:teachers'])
    ->prefix('subjects')
    ->controller(SubjectController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
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
    });

// Rutas de categoría de puntos
Route::middleware(['auth:sanctum','abilities:teacher:point-categories'])
    ->prefix('point-categories')
    ->controller(PointCategoryController::class)
    ->group(function() {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::get('{category}', 'show');
        Route::put('{category}', 'update');
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