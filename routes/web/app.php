<?php

use App\Http\Controllers\Web\CalendarsController;
use App\Http\Controllers\Web\ConnectorsController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EventsController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ProfilePhotoController;
use App\Http\Controllers\Web\ProvidersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::get('/profile/photo', [ProfilePhotoController::class, 'show'])
        ->name('profile.photo');

    Route::prefix('/providers')
        ->name('providers.')
        ->group(function (): void {
            Route::get('/choose', [ProvidersController::class, 'create'])
                ->name('create');
            Route::post('/choose', [ProvidersController::class, 'store'])
                ->name('store');
        });

    Route::prefix('/connectors')
        ->name('connectors.')
        ->group(function (): void {
            Route::put('/{connector}/synchronize', [ConnectorsController::class, 'update'])
                ->middleware('throttle:connector-sync')
                ->name('synchronize');
        });

    Route::prefix('/calendars')
        ->name('calendars.')
        ->group(function (): void {
            Route::get('/', [CalendarsController::class, 'index'])
                ->name('index');
            Route::get('/create/{connector}', [CalendarsController::class, 'create'])
                ->name('create');
            Route::post('/create/{connector}', [CalendarsController::class, 'store'])
                ->name('store');
            Route::put('/{calendar}', [CalendarsController::class, 'update'])
                ->name('update');
            Route::delete('/{calendar}', [CalendarsController::class, 'destroy'])
                ->name('destroy');
        });

    Route::prefix('/events')
        ->name('events.')
        ->group(function (): void {
            Route::get('/', [EventsController::class, 'index'])
                ->name('index');
            Route::get('/create', [EventsController::class, 'create'])
                ->name('create');
            Route::post('/', [EventsController::class, 'store'])
                ->name('store');
            Route::get('/{event}', [EventsController::class, 'show'])
                ->name('show');
            Route::get('/{event}/edit', [EventsController::class, 'edit'])
                ->name('edit');
            Route::put('/{event}', [EventsController::class, 'update'])
                ->name('update');
            Route::delete('/{event}', [EventsController::class, 'destroy'])
                ->name('destroy');
        });
});
