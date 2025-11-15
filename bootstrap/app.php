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
        // Register custom middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'security.headers' => \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        // Apply security headers globally
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        // Exclude CSRF protection for external form submission endpoint
        $middleware->validateCsrfTokens(except: [
            'external/order',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
