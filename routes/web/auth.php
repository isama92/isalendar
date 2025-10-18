<?php

use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Auth\OidcController;
use App\Http\Controllers\Web\Auth\PasswordResetLinkController;
use App\Http\Controllers\Web\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')
    ->group(function (): void {
        Route::get('/login', [AuthController::class, 'create'])
            ->name('login');
        Route::post('/login', [AuthController::class, 'store'])
            ->name('login.submit');

        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');
        Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])
            ->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'store'])
            ->name('password.update');

        Route::prefix('/auth/oidc')
            ->name('auth.oidc.')
            ->group(function (): void {
                Route::get('/connect', [OidcController::class, 'create'])
                    ->name('connect');
                Route::get('/redirect', [OidcController::class, 'store'])
                    ->name('redirect');
            });
    });

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')
    ->name('logout');
