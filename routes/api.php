<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::prefix('tournament')->group(function () {
    Route::get('/state', [TournamentController::class, 'getState']);
    Route::post('/verify-pin', [TournamentController::class, 'verifyPin']);
    Route::post('/state', [TournamentController::class, 'updateState']);
    Route::post('/change-pin', [TournamentController::class, 'changePin']);
    Route::post('/reset', [TournamentController::class, 'reset']);
});

