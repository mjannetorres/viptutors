<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks');
        Route::get('/search', [TaskController::class, 'search'])->name('tasks.search');
        Route::post('store', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('show/{id}', [TaskController::class, 'show'])->name('tasks.show');
        Route::put('update/{id}', [TaskController::class, 'update'])->name('tasks.update');
        Route::put('reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
        Route::delete('delete/{id}', [TaskController::class, 'destroy'])
            ->middleware('admin')
            ->name('tasks.delete');
    });

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    });
});
