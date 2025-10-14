<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $env = $_ENV['APP_ENV'] ?? 'production';

        $middleware->trustHosts();
        $middleware->trustProxies();

        if ($env !== 'testing') {
            $middleware->throttleWithRedis();
        }

        $middleware->web(
            append: [
                SetLocale::class,
                HandleInertiaRequests::class,
            ],
            prepend: [
                AddSecurityHeaders::class,
                AddCspHeaders::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
