<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])
    ->name('login');
Route::post('/login', [AuthController::class, 'store'])
    ->name('login.submit');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')
    ->name('logout');
