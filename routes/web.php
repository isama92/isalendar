<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthController::class, 'store'])
    ->name('login.submit');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')
    ->name('logout');

Route::post('/locale', [LocaleController::class, 'update'])
    ->name('locale.update');

Route::middleware('auth')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

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
