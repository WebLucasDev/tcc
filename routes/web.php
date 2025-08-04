<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::prefix('login')->group(function () {
        Route::get('/', [LoginController::class, 'index'])->name('login.index');
        Route::post('/', [LoginController::class, 'auth'])->name('login.auth');
    });

    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('forgotPassword');
});

// Rotas autenticadas (apenas para usuários logados)
Route::middleware('auth')->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    });

    // Rota de logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
