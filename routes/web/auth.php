<?php

use App\Http\Controllers\Auth\OidcController;
use App\Http\Controllers\Web\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthController::class, 'store'])
    ->name('login.submit');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')
    ->name('logout');

Route::prefix('/auth/oidc')
    ->name('auth.oidc.')
    ->group(function (): void {
        Route::get('/connect', [OidcController::class, 'create'])
            ->name('connect');
        Route::get('/redirect', [OidcController::class, 'store'])
            ->name('redirect');
    });
