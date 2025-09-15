<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AlteracaoEletricaController;
use App\Http\Controllers\ForcingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::get('/', [App\Http\Controllers\WebController::class, 'index'])->name('home');
Route::get('/mobile-suggestion', [App\Http\Controllers\WebController::class, 'mobileSuggestion'])->name('mobile-suggestion');
Route::get('/device-info', [App\Http\Controllers\WebController::class, 'deviceInfo'])->name('device-info');

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    
    // Dashboard - com detecção de dispositivo
    Route::get('/dashboard', [App\Http\Controllers\WebController::class, 'dashboard'])->name('dashboard');
    
    // Rotas de forcing (todos os usuários autenticados)
    Route::get('/forcing/terms', [ForcingController::class, 'showTerms'])->name('forcing.terms');
    Route::post('/forcing/refresh-table', [ForcingController::class, 'refreshTable'])->name('forcing.refresh-table');
    Route::resource('forcing', ForcingController::class);
    // Rotas para modais (desktop)
    Route::post('/forcing/{forcing}/liberar', [ForcingController::class, 'liberar'])->name('forcing.liberar');
    Route::post('/forcing/{forcing}/registrar-execucao', [ForcingController::class, 'registrarExecucao'])->name('forcing.registrar-execucao');
    Route::post('/forcing/{forcing}/solicitar-retirada', [ForcingController::class, 'solicitarRetirada'])->name('forcing.solicitar-retirada');
    Route::get('/forcing/{forcing}/retirar', [ForcingController::class, 'retirar'])->name('forcing.retirar-get');
    Route::post('/forcing/{forcing}/retirar', [ForcingController::class, 'retirar'])->name('forcing.retirar');
    
    // Rotas para páginas mobile (iOS/Android)
    Route::get('/forcing/{forcing}/liberar-page', [ForcingController::class, 'showLiberarPage'])->name('forcing.liberar-page');
    Route::get('/forcing/{forcing}/executar-page', [ForcingController::class, 'showExecutarPage'])->name('forcing.executar-page');
    Route::get('/forcing/{forcing}/solicitar-retirada-page', [ForcingController::class, 'showSolicitarRetiradaPage'])->name('forcing.solicitar-retirada-page');
    Route::get('/forcing/{forcing}/retirar-page', [ForcingController::class, 'showRetirarPage'])->name('forcing.retirar-page');
    Route::get('/forcing/from-email/{forcing}', [ForcingController::class, 'fromEmail'])->name('forcing.from-email');
    
    // Rotas de usuários (apenas admin)
    Route::resource('users', UserController::class)->middleware('check.profile:admin');
    
    // Perfil do usuário atual
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Rotas de alterações elétricas (todos os usuários autenticados)
    Route::resource('alteracoes', AlteracaoEletricaController::class)->parameters(['alteracoes' => 'alteracao']);
    Route::post('/alteracoes/{alteracao}/aprovar', [AlteracaoEletricaController::class, 'aprovar'])->name('alteracoes.aprovar');
    Route::post('/alteracoes/{alteracao}/rejeitar', [AlteracaoEletricaController::class, 'rejeitar'])->name('alteracoes.rejeitar');
    Route::post('/alteracoes/{alteracao}/implementar', [AlteracaoEletricaController::class, 'implementar'])->name('alteracoes.implementar');
    Route::get('/alteracoes/{alteracao}/pdf', [AlteracaoEletricaController::class, 'pdf'])->name('alteracoes.pdf');
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
