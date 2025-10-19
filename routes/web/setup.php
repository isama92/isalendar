<?php

use App\Http\Controllers\Web\SetupController;
use App\Http\Middleware\RedirectIfSetupComplete;
use Illuminate\Support\Facades\Route;

Route::middleware(RedirectIfSetupComplete::class)
    ->group(function (): void {
        Route::get('/setup', [SetupController::class, 'create'])
            ->name('setup');
        Route::post('/setup', [SetupController::class, 'store'])
            ->name('setup.submit');
    });
