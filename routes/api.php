<?php

use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('users', [UserController::class, 'store']);
    Route::post('login', [UserController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('projects/trashed', [ProjectController::class, 'trashed'])->name('projects.trashed');
        Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])->name('projects.restore')->withTrashed();
        Route::apiResource('projects', ProjectController::class);
        Route::get('tasks/trashed', [TaskController::class, 'trashed'])->name('tasks.trashed');
        Route::post('tasks/{task}/restore', [TaskController::class, 'restore'])->name('tasks.restore')->withTrashed();
        Route::apiResource('tasks', TaskController::class);

        Route::apiResource('users', UserController::class)->except(['store']);
    });
});