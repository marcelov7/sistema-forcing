<?php

/**
 * 🎨 Rotas de Acessibilidade para Daltonicos
 * Sistema de Forcing - Devaxis
 * 
 * Arquivo: routes/accessibility.php
 */

use App\Http\Controllers\UserController;
use App\Http\Controllers\AccessibilityController;
use Illuminate\Support\Facades\Route;

// 🎨 Grupo de rotas de acessibilidade
Route::middleware(['auth'])->prefix('accessibility')->name('accessibility.')->group(function () {
    
    // 🔧 Preferências de acessibilidade
    Route::post('/preferences', [UserController::class, 'updateAccessibilityPreferences'])
         ->name('preferences.update');
    
    Route::get('/preferences', [UserController::class, 'getAccessibilityPreferences'])
         ->name('preferences.get');
    
    // 🧪 Teste de daltonismo
    Route::post('/colorblind-test', [UserController::class, 'colorblindnessTest'])
         ->name('colorblind.test');
    
    Route::get('/colorblind-test', function () {
        return view('accessibility.colorblind-test');
    })->name('colorblind.test.form');
    
    // 📊 Simulador de daltonismo
    Route::get('/simulator', function () {
        return view('accessibility.simulator');
    })->name('simulator');
    
    // 📋 Guia de acessibilidade
    Route::get('/guide', function () {
        return view('accessibility.guide');
    })->name('guide');
    
    // 🎯 Toggle rápido de modo daltonico (AJAX)
    Route::post('/toggle-colorblind', function () {
        $user = auth()->user();
        $currentMode = $user->preferences['colorblind_mode'] ?? false;
        
        $user->preferences = array_merge($user->preferences ?? [], [
            'colorblind_mode' => !$currentMode
        ]);
        $user->save();
        
        return response()->json([
            'success' => true,
            'colorblind_mode' => !$currentMode,
            'message' => !$currentMode ? 
                '✅ Modo daltonico ativado!' : 
                '✅ Modo daltonico desativado!'
        ]);
    })->name('toggle.colorblind');
    
    // 🔆 Toggle de alto contraste (AJAX)
    Route::post('/toggle-contrast', function () {
        $user = auth()->user();
        $currentContrast = $user->preferences['high_contrast'] ?? false;
        
        $user->preferences = array_merge($user->preferences ?? [], [
            'high_contrast' => !$currentContrast
        ]);
        $user->save();
        
        return response()->json([
            'success' => true,
            'high_contrast' => !$currentContrast,
            'message' => !$currentContrast ? 
                '✅ Alto contraste ativado!' : 
                '✅ Alto contraste desativado!'
        ]);
    })->name('toggle.contrast');
    
    // 📏 Alterar tamanho da fonte (AJAX)
    Route::post('/font-size', function () {
        $size = request('size', 'normal');
        
        if (!in_array($size, ['small', 'normal', 'large'])) {
            return response()->json(['success' => false, 'message' => 'Tamanho inválido'], 400);
        }
        
        $user = auth()->user();
        $user->preferences = array_merge($user->preferences ?? [], [
            'font_size' => $size
        ]);
        $user->save();
        
        return response()->json([
            'success' => true,
            'font_size' => $size,
            'message' => "✅ Tamanho da fonte alterado para {$size}!"
        ]);
    })->name('font.size');
});

/**
 * 🎨 Adicionar ao web.php principal:
 * 
 * // Incluir rotas de acessibilidade
 * require __DIR__.'/accessibility.php';
 */
