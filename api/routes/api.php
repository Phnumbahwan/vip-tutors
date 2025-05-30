<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckTaskAccessByRole;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store']);
        Route::put('/{id}', [TaskController::class, 'update']);
        Route::delete('/{id}', [TaskController::class, 'destroy']);
        Route::post('/reorder', [TaskController::class, 'reorder']);
    });

    Route::middleware([CheckTaskAccessByRole::class])->group(function () {
        Route::prefix('admin')->group(function () {
            Route::get('/users-tasks', [AdminController::class, 'usersWithTasks']);
            Route::get('/users-task-stats', [AdminController::class, 'usersWithTaskStats']);
        });
    });
});