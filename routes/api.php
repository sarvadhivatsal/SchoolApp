<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SessionApiController;
use App\Http\Controllers\Api\LoginApiController;
use App\Http\Controllers\Api\TeacherApiController;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\StudentGoalApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('/login', [LoginApiController::class, 'login']);
Route::post('/logout', [LoginApiController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sessions', [SessionApiController::class, 'index']);
    Route::post('/sessions', [SessionApiController::class, 'store']);
    Route::get('/sessions/{session}', [SessionApiController::class, 'show']);
    Route::put('/sessions/{session}', [SessionApiController::class, 'update']);
    Route::delete('/sessions/{session}', [SessionApiController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/teachers', [TeacherApiController::class, 'index']);
    Route::get('/teachers/{id}', [TeacherApiController::class, 'show']);
    Route::post('/teachers', [TeacherApiController::class, 'store']);
    Route::put('/teachers/{id}', [TeacherApiController::class, 'update']);
    Route::delete('/teachers/{id}', [TeacherApiController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [StudentApiController::class, 'index']);
    Route::get('/students/{id}', [StudentApiController::class, 'show']);
    Route::post('/students', [StudentApiController::class, 'store']);
    Route::put('/students/{id}', [StudentApiController::class, 'update']);
    Route::delete('/students/{id}', [StudentApiController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/student-goals', [StudentGoalApiController::class, 'index']);       
    Route::post('/student-goals', [StudentGoalApiController::class, 'store']);      
    Route::get('/student-goals/{id}', [StudentGoalApiController::class, 'show']);   
    Route::put('/student-goals/{id}', [StudentGoalApiController::class, 'update']); 
    Route::delete('/student-goals/{id}', [StudentGoalApiController::class, 'destroy']); 
});
