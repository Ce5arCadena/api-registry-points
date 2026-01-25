<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/auth/create-admin', [UserController::class, 'createSuperAdmin']);

// Rutas de colegio
Route::middleware(['auth:sanctum', 'abilities:admin:schools'])
    ->prefix('schools')
    ->controller(SchoolController::class)
    ->group(function () {
        Route::post('/', 'store');
        Route::patch('{school}', 'update');
        Route::delete('{school}', 'destroy');
    });