<?php

use App\Http\Middleware\ApiAuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
// ✅ Your middleware
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'api.auth' => ApiAuthMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetBusinessSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ✅ REQUIRED — do not remove
    })
    ->create();
