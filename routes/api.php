<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::get('/balance', [MainController::class, 'index']);
Route::post('/event', [MainController::class, 'event']);
Route::post('/reset', [MainController::class, 'reset']);
