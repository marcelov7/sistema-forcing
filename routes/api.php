<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForcingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas públicas (sem autenticação)
Route::group(['prefix' => 'v1'], function () {
    // Autenticação
    Route::post('auth/login', [AuthController::class, 'login']);
    
    // Rotas protegidas por autenticação JWT
    Route::middleware('auth:api')->group(function () {
        // Autenticação
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        
        // Forcings
        Route::apiResource('forcings', ForcingController::class);
        
        // Ações específicas dos forcings
        Route::post('forcings/{id}/liberar', [ForcingController::class, 'liberar']);
        Route::post('forcings/{id}/executar', [ForcingController::class, 'executar']);
        Route::post('forcings/{id}/solicitar-retirada', [ForcingController::class, 'solicitarRetirada']);
        Route::post('forcings/{id}/retirar', [ForcingController::class, 'retirar']);
        
        // Dashboard
        Route::get('dashboard', [ForcingController::class, 'dashboard']);
    });
});

// Rota de teste para verificar se a API está funcionando
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API do Sistema de Forcing funcionando',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

