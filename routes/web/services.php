<?php

use App\Http\Controllers\Services\GoogleCalendar\AuthController;
use App\Http\Controllers\Services\GoogleCalendar\CalendarsController;
use Illuminate\Support\Facades\Route;

Route::prefix('/services/google')
    ->name('services.google.')
    ->middleware('auth')
    ->group(function (): void {

        Route::prefix('/auth')
            ->group(function (): void {
                Route::get('/connect', [AuthController::class, 'redirect'])
                    ->name('connect');
                Route::get('/redirect', [AuthController::class, 'callback'])
                    ->name('redirect');
                Route::post('/disconnect', [AuthController::class, 'disconnect'])
                    ->name('disconnect');
            });

        Route::prefix('/calendar')
            ->name('calendar.')
            ->group(function (): void {
                Route::get('/select', [CalendarsController::class, 'index'])
                    ->name('index');
                Route::post('/select', [CalendarsController::class, 'update'])
                    ->name('update');
            });
    });
