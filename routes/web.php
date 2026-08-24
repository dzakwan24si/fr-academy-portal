<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProgramController;

Route::get('/', function () {
    return redirect()->route('kalender-training.index');
})->name('home');

Route::get('/administrasi/kalender-training', [ProgramController::class, 'index'])->name('kalender-training.index');
Route::get('/dashboard', [ProgramController::class, 'dashboard'])->name('dashboard');
