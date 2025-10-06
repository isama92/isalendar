<?php

use App\Http\Controllers\GoogleCalendarController;
use Illuminate\Support\Facades\Route;

Route::prefix('/services/google/auth')
    ->name('services.google.')
    ->middleware('auth')
    ->group(function (): void {
        Route::get('/connect', [GoogleCalendarController::class, 'redirect'])
            ->name('connect');
        Route::get('/redirect', [GoogleCalendarController::class, 'callback'])
            ->name('redirect');
        Route::post('/disconnect', [GoogleCalendarController::class, 'disconnect'])
            ->name('disconnect');
    });
