<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ForcingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::get('/', function () {
    return redirect()->route('forcing.index');
});

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    
    // Rotas de forcing (todos os usuários autenticados)
    Route::get('/forcing/terms', [ForcingController::class, 'showTerms'])->name('forcing.terms');
    Route::post('/forcing/refresh-table', [ForcingController::class, 'refreshTable'])->name('forcing.refresh-table');
    Route::resource('forcing', ForcingController::class);
    Route::post('/forcing/{forcing}/liberar', [ForcingController::class, 'liberar'])->name('forcing.liberar');
    Route::post('/forcing/{forcing}/registrar-execucao', [ForcingController::class, 'registrarExecucao'])->name('forcing.registrar-execucao');
    Route::post('/forcing/{forcing}/solicitar-retirada', [ForcingController::class, 'solicitarRetirada'])->name('forcing.solicitar-retirada');
    Route::post('/forcing/{forcing}/retirar', [ForcingController::class, 'retirar'])->name('forcing.retirar');
    
    // Rotas de usuários (apenas admin)
    Route::resource('users', UserController::class)->middleware('check.profile:admin');
    
    // Perfil do usuário atual
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
});

// Rotas de Super Admin
Route::middleware(['auth', 'super.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('units', \App\Http\Controllers\Admin\UnitController::class);
    Route::get('units/{unit}/users', [\App\Http\Controllers\Admin\UnitController::class, 'users'])->name('units.users');
    Route::get('units/{unit}/forcings', [\App\Http\Controllers\Admin\UnitController::class, 'forcings'])->name('units.forcings');
    
    // Estatísticas de emails
    Route::get('email-stats', [\App\Http\Controllers\EmailStatsController::class, 'index'])->name('email-stats');
    Route::get('email-stats/api', [\App\Http\Controllers\EmailStatsController::class, 'api'])->name('email-stats.api');
    Route::post('email-stats/reset', [\App\Http\Controllers\EmailStatsController::class, 'reset'])->name('email-stats.reset');
});
