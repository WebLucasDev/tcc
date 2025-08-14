<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'index'])->name('login.index');
    Route::post('/login', [LoginController::class, 'auth'])->name('login.auth');

});

Route::middleware('auth')->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    });

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
