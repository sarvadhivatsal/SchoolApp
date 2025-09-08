<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SessionApiController;
use App\Http\Controllers\Api\LoginApiController;
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
