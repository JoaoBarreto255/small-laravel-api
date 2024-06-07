<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\ResetController;
use Illuminate\Support\Facades\Route;

Route::get('/balance', [MainController::class, 'index']);
Route::post('/event', [MainController::class, 'event']);
Route::post('/reset', [ResetController::class, 'index']);
