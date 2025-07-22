<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rotas de autenticação (públicas - apenas para usuários não logados)
Route::middleware('guest')->group(function () {

    Route::prefix('login')->group(function () {
        Route::get('/', [LoginController::class, 'index'])->name('login.index');
        Route::post('/', [LoginController::class, 'auth'])->name('login.auth');
    });
});

// Rotas autenticadas (apenas para usuários logados)
Route::middleware('auth')->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
