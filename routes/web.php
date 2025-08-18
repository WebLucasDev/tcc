<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;

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

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
