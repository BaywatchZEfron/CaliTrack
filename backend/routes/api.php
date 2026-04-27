<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\WorkoutController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProgressController;
use Illuminate\Support\Facades\Route;

// Rutas públicas — no necesitan token
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    // prefix('auth') → todas las rutas de este grupo empiezan por /api/auth/
    // Queda: POST /api/auth/register y POST /api/auth/login
});

// Rutas protegidas — requieren token en el header
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',         [AuthController::class, 'user']);
    Route::put('/user/profile', [ProfileController::class, 'update']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::get('/progress', [ProgressController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/workouts',         [WorkoutController::class, 'index']);
    Route::post('/workouts',        [WorkoutController::class, 'store']);
    Route::get('/workouts/{id}',    [WorkoutController::class, 'show']);
    Route::delete('/workouts/{id}', [WorkoutController::class, 'destroy']);
    // middleware('auth:sanctum') → Sanctum verifica el token antes de entrar
    // Si no hay token o es inválido, devuelve 401 automáticamente
});