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
            Route::get('/', [CollaboratorController::class, 'index'])->name('colaboradores.index');
        });

        Route::prefix('departamentos')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('departamentos.index');
        });

        Route::prefix('cargos')->group(function () {
            Route::get('/', [PositionController::class, 'index'])->name('cargos.index');
        });
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
