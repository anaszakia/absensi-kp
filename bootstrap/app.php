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
        // Global middleware
        $middleware->append([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // Middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'log.sensitive' => \App\Http\Middleware\LogSensitiveActions::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
