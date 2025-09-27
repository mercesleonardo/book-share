<?php

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Definição de middleware continua aqui se necessário.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->booted(function (Application $app): void {
        // Registrar rate limiters somente após o boot para evitar "facade root has not been set".
        RateLimiter::for('moderation', function () {
            $userId = request()->user()?->id ?? 'guest';

            return [
                Limit::perMinute(20)->by('moderation:' . $userId),
            ];
        });
    })
    ->create();
