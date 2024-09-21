<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompletedLessonsController;
use App\Http\Controllers\CoursesController;
use App\Http\Controllers\LessonsController;
use App\Http\Controllers\SetsController;
use App\Http\Middleware\CheckToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Auth Routes
Route::post('/register', [AuthController::class, "register"]);
Route::post('/login', [AuthController::class, "login"]);
Route::post('/logout', [AuthController::class, "logout"])->middleware(CheckToken::class);

// Main Routes
Route::apiResource('/courses', CoursesController::class)->middleware(CheckToken::class);
Route::apiResource('/courses/{courses:slug}/sets', SetsController::class)->middleware(CheckToken::class);
Route::post('/courses/{courses:slug}/register', [CoursesController::class, "register_course"])->middleware(CheckToken::class);
Route::apiResource('/lessons', LessonsController::class)->middleware(CheckToken::class);
Route::post('/lessons/{lesson_id}/contents/{content_id}/check', [LessonsController::class, "checkAnswer"])->middleware(CheckToken::class);
Route::get('/users/progress', [CompletedLessonsController::class, 'index'])->middleware(CheckToken::class);
Route::put('/lessons/{lesson_id}/complete', [CompletedLessonsController::class, "store"])->middleware(CheckToken::class);
