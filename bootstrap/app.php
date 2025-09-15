<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'check.profile' => \App\Http\Middleware\CheckProfile::class,
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'detect.device' => \App\Http\Middleware\DetectDevice::class,
        ]);
        
        // Adicionar middleware global
        $middleware->append(\App\Http\Middleware\DetectDevice::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
