<?php

use App\Http\Middleware\AddSecurityHeaders;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RedirectIfSetupNeeded;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
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
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO |
                     Request::HEADER_X_FORWARDED_PREFIX
        );

        $middleware->web(
            append: [
                SetLocale::class,
                HandleInertiaRequests::class,
            ],
            prepend: [
                AddSecurityHeaders::class,
                AddCspHeaders::class,
                RedirectIfSetupNeeded::class,
            ]);

        // Horizon middleware group - same as web but without CSP
        $middleware->group(
            'horizon',
            collect($middleware->getMiddlewareGroups()['web'])
                ->filter(fn (string $m) => $m !== AddCspHeaders::class)
                ->toArray()
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
