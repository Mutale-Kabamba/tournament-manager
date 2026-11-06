<?php

use App\Http\Controllers\TournamentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TournamentController::class, 'index'])->name('tournament.index');
