<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BallisticsController;

Route::get('/', [BallisticsController::class, 'index']);
Route::post('/analyze', [BallisticsController::class, 'analyze']);
