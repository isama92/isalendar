<?php

declare(strict_types=1);

use App\Http\Controllers\Web\BatchEventsController;
use App\Http\Controllers\Web\CalendarEventController;
use App\Http\Controllers\Web\CalendarsController;
use App\Http\Controllers\Web\CalendarSynchronizationController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\EventDurationSuggestionController;
use App\Http\Controllers\Web\EventsController;
use App\Http\Controllers\Web\EventTemplatesController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ProfilePhotoController;
use App\Http\Controllers\Web\ProvidersController;
use App\Http\Controllers\Web\SendUserPasswordResetController;
use App\Http\Controllers\Web\UsersController;
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
            Route::put('/{calendar}/synchronize', [CalendarSynchronizationController::class, 'update'])
                ->middleware('throttle:ui-calendar-sync')
                ->name('synchronize');
        });

    Route::prefix('/events')
        ->name('events.')
        ->group(function (): void {
            Route::get('/', [EventsController::class, 'index'])
                ->name('index');
            Route::get('/calendar', CalendarEventController::class)
                ->name('calendar');
            Route::get('/suggest-duration', EventDurationSuggestionController::class)
                ->name('suggest-duration');
            Route::get('/create', [EventsController::class, 'create'])
                ->name('create');
            Route::post('/', [EventsController::class, 'store'])
                ->name('store');
            Route::get('/batch/create', [BatchEventsController::class, 'create'])
                ->name('batch.create');
            Route::post('/batch', [BatchEventsController::class, 'store'])
                ->name('batch.store');
            Route::get('/{event}', [EventsController::class, 'show'])
                ->name('show');
            Route::get('/{event}/edit', [EventsController::class, 'edit'])
                ->name('edit');
            Route::put('/{event}', [EventsController::class, 'update'])
                ->name('update');
            Route::delete('/{event}', [EventsController::class, 'destroy'])
                ->name('destroy');
        });

    Route::prefix('/event-templates')
        ->name('event-templates.')
        ->group(function (): void {
            Route::get('/', [EventTemplatesController::class, 'index'])
                ->name('index');
            Route::get('/create', [EventTemplatesController::class, 'create'])
                ->name('create');
            Route::post('/', [EventTemplatesController::class, 'store'])
                ->name('store');
            Route::get('/{event_template}', [EventTemplatesController::class, 'show'])
                ->name('show');
            Route::get('/{event_template}/edit', [EventTemplatesController::class, 'edit'])
                ->name('edit');
            Route::put('/{event_template}', [EventTemplatesController::class, 'update'])
                ->name('update');
            Route::delete('/{event_template}', [EventTemplatesController::class, 'destroy'])
                ->name('destroy');
        });

    Route::prefix('/users')
        ->name('users.')
        ->group(function (): void {
            Route::get('/', [UsersController::class, 'index'])
                ->name('index');
            Route::get('/create', [UsersController::class, 'create'])
                ->name('create');
            Route::post('/', [UsersController::class, 'store'])
                ->name('store');
            Route::get('/{user}', [UsersController::class, 'show'])
                ->withTrashed()
                ->name('show');
            Route::get('/{user}/edit', [UsersController::class, 'edit'])
                ->name('edit');
            Route::put('/{user}', [UsersController::class, 'update'])
                ->name('update');
            Route::delete('/{user}', [UsersController::class, 'destroy'])
                ->withTrashed()
                ->name('destroy');
            Route::post('/{user}/send-password-reset', SendUserPasswordResetController::class)
                ->name('send-password-reset');
        });
});
