<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ResetController;
use Illuminate\Support\Facades\Route;

Route::get('/', [Controller::class, 'index']);
Route::get('/balance', [BalanceController::class, 'index']);
Route::post('/event', [EventController::class, 'index']);
Route::post('/reset', [ResetController::class, 'index']);
