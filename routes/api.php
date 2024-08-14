<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    // Separate routes for each status
    Route::put('/tasks/{task}/todo', [TaskController::class, 'markAsTodo']);
    Route::put('/tasks/{task}/in-progress', [TaskController::class, 'markAsInProgress']);
    Route::put('/tasks/{task}/done', [TaskController::class, 'markAsDone']);

    Route::put('/tasks/{task}/change-parent', [TaskController::class, 'changeParent']);
});
