<?php

use App\Http\Controllers\Web\LocaleController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/web/setup.php';
require __DIR__.'/web/auth.php';
require __DIR__.'/web/app.php';
require __DIR__.'/web/services.php';

Route::post('/locale', [LocaleController::class, 'update'])
    ->name('locale.update');
