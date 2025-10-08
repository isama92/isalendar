<?php

use App\Http\Controllers\Services\GoogleCalendar\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('/services/google')
    ->name('services.google.')
    ->middleware('auth')
    ->group(function (): void {

        Route::prefix('/auth')
            ->name('auth.')
            ->group(function (): void {
                Route::get('/connect', [AuthController::class, 'create'])
                    ->name('connect');
                Route::get('/redirect', [AuthController::class, 'store'])
                    ->name('redirect');
            });
    });
