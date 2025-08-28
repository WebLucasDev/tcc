<?php

use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PositionController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'index'])->name('login.index');
    Route::post('/login', [LoginController::class, 'auth'])->name('login.auth');

    Route::prefix('forgot-password')->group(function () {
        Route::post('/send', [ForgotPasswordController::class, 'send'])->name('forgot-password.send');
        Route::get('/{token}', [ForgotPasswordController::class, 'openReset'])->name('forgot-password.open-reset');
        Route::post('/', [ForgotPasswordController::class, 'processReset'])->name('forgot-password.process-reset');
        Route::post('/check-current-password', [ForgotPasswordController::class, 'checkCurrentPassword'])->name('forgot-password.check-current-password');
    });

});

Route::middleware('auth')->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    });

    Route::prefix('cadastros')->group(function () {

        Route::prefix('colaboradores')->group(function () {
            Route::get('/', [CollaboratorController::class, 'index'])->name('collaborator.index');
            Route::get('/create', [CollaboratorController::class, 'create'])->name('collaborator.create');
            Route::post('/', [CollaboratorController::class, 'store'])->name('collaborator.store');
            Route::put('/{id}', [CollaboratorController::class, 'update'])->name('collaborator.update');
            Route::delete('/{id}', [CollaboratorController::class, 'destroy'])->name('collaborator.destroy');
        });

        Route::prefix('departamentos')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
            Route::get('/create', [DepartmentController::class, 'create'])->name('department.create');
            Route::post('/', [DepartmentController::class, 'store'])->name('department.store');
            Route::put('/{id}', [DepartmentController::class, 'update'])->name('department.update');
            Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
        });

        Route::prefix('cargos')->group(function () {
            Route::get('/', [PositionController::class, 'index'])->name('position.index');
            Route::get('/create', [PositionController::class, 'create'])->name('position.create');
            Route::post('/', [PositionController::class, 'store'])->name('position.store');
            Route::get('/{id}/edit', [PositionController::class, 'edit'])->name('position.edit');
            Route::put('/{id}', [PositionController::class, 'update'])->name('position.update');
            Route::delete('/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
        });
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
