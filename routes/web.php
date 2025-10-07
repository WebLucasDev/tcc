<?php

use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\CompTimeController;
use App\Http\Controllers\CompTimeEmployessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardEmployessController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\RegistrationsEmployeesController;
use App\Http\Controllers\SolicitationController;
use App\Http\Controllers\SolicitationEmployeesController;
use App\Http\Controllers\TimeTrackingController;
use App\Http\Controllers\TimeTrackingEmployeesController;
use App\Http\Controllers\WorkHoursController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.welcome.index');
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

Route::middleware('user.auth')->group(function () {

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    });

    Route::prefix('cadastros')->group(function () {

        Route::prefix('cargos')->group(function () {
            Route::get('/', [PositionController::class, 'index'])->name('position.index');
            Route::get('/create', [PositionController::class, 'create'])->name('position.create');
            Route::post('/', [PositionController::class, 'store'])->name('position.store');
            Route::get('/{id}/edit', [PositionController::class, 'edit'])->name('position.edit');
            Route::put('/{id}', [PositionController::class, 'update'])->name('position.update');
            Route::delete('/{id}', [PositionController::class, 'destroy'])->name('position.destroy');
        });

        Route::prefix('colaboradores')->group(function () {
            Route::get('/', [CollaboratorController::class, 'index'])->name('collaborator.index');
            Route::get('/create', [CollaboratorController::class, 'create'])->name('collaborator.create');
            Route::post('/', [CollaboratorController::class, 'store'])->name('collaborator.store');
            Route::get('/{id}/edit', [CollaboratorController::class, 'edit'])->name('collaborator.edit');
            Route::put('/{id}', [CollaboratorController::class, 'update'])->name('collaborator.update');
            Route::delete('/{id}', [CollaboratorController::class, 'destroy'])->name('collaborator.destroy');
        });

        Route::prefix('departamentos')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('department.index');
            Route::get('/create', [DepartmentController::class, 'create'])->name('department.create');
            Route::post('/', [DepartmentController::class, 'store'])->name('department.store');
            Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('department.edit');
            Route::put('/{id}', [DepartmentController::class, 'update'])->name('department.update');
            Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
        });

        Route::prefix('jornadas-trabalho')->group(function () {
            Route::get('/', [WorkHoursController::class, 'index'])->name('work-hours.index');
            Route::get('/create', [WorkHoursController::class, 'create'])->name('work-hours.create');
            Route::post('/', [WorkHoursController::class, 'store'])->name('work-hours.store');
            Route::get('/{id}/edit', [WorkHoursController::class, 'edit'])->name('work-hours.edit');
            Route::put('/{id}', [WorkHoursController::class, 'update'])->name('work-hours.update');
            Route::delete('/{id}', [WorkHoursController::class, 'destroy'])->name('work-hours.destroy');
        });
    });

    Route::prefix('gestao-ponto')->group(function () {

        Route::prefix('registro-ponto')->group(function () {
            Route::get('/', [TimeTrackingController::class , 'index'])->name('time-tracking.index');
            Route::post('/', [TimeTrackingController::class, 'store'])->name('time-tracking.store');
            Route::get('/next-tracking-info', [TimeTrackingController::class, 'getNextTrackingInfo'])->name('time-tracking.next-info');
            Route::get('/{id}', [TimeTrackingController::class, 'show'])->name('time-tracking.show');
            Route::patch('/update', [TimeTrackingController::class, 'update'])->name('time-tracking.update');
            Route::patch('/{id}/cancel', [TimeTrackingController::class, 'cancel'])->name('time-tracking.cancel');
            Route::patch('/{id}/restore', [TimeTrackingController::class, 'restore'])->name('time-tracking.restore');
        });

        Route::prefix('solicitacoes')->group(function () {
            Route::get('/', [SolicitationController::class, 'index'])->name('solicitation.index');
            Route::patch('/{id}/approve', [SolicitationController::class, 'approve'])->name('solicitation.approve');
            Route::patch('/{id}/reject', [SolicitationController::class, 'reject'])->name('solicitation.reject');
            Route::patch('/{id}/cancel', [SolicitationController::class, 'cancel'])->name('solicitation.cancel');
        });

        Route::prefix('banco-horas')->group(function () {
            Route::get('/', [CompTimeController::class, 'index'])->name('comp-time.index');
        });
    });
});

Route::middleware('collaborator.auth')->group(function () {
    Route::prefix('sistema-colaboradores')->group(function () {

        Route::prefix('dashboard')->group(function () {
            Route::get('/', [DashboardEmployessController::class, 'index'])->name('system-for-employees.dashboard.index');
        });

        Route::prefix('bater-ponto')->group(function () {
            Route::get('/', [TimeTrackingEmployeesController::class, 'index'])->name('system-for-employees.time-tracking.index');
            Route::post('/', [TimeTrackingEmployeesController::class, 'store'])->name('system-for-employees.time-tracking.store');
            Route::get('/next-tracking-info', [TimeTrackingEmployeesController::class, 'getNextTrackingInfo'])->name('system-for-employees.time-tracking.next-info');
            Route::get('/{id}', [TimeTrackingEmployeesController::class, 'show'])->name('system-for-employees.time-tracking.show');
            Route::patch('/update', [TimeTrackingEmployeesController::class, 'update'])->name('system-for-employees.time-tracking.update');
            Route::patch('/{id}/cancel', [TimeTrackingEmployeesController::class, 'cancel'])->name('system-for-employees.time-tracking.cancel');
            Route::patch('/{id}/restore', [TimeTrackingEmployeesController::class, 'restore'])->name('system-for-employees.time-tracking.restore');
        });

        Route::prefix('solicitations')->group(function () {
            Route::get('/', [SolicitationEmployeesController::class, 'index'])->name('system-for-employees.solicitation.index');
        });

        Route::prefix('banco-horas')->group(function () {
            Route::get('/', [CompTimeEmployessController::class, 'index'])->name('system-for-employees.comp-time.index');
        });

        Route::prefix('cadastro')->group(function () {
            Route::get('/', [RegistrationsEmployeesController::class, 'index'])->name('system-for-employees.registrations.index');
            Route::put('/', [RegistrationsEmployeesController::class, 'update'])->name('system-for-employees.registrations.update');
        });
    });
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
