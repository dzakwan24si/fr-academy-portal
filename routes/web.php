<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/artikel', function () {
    return view('article.index');
});

Route::get('/artikel/{slug}', function ($slug) {
    return view('article.show');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;

// Admin Routes (Protected)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('artikel', AdminArticleController::class)->names('admin.artikel');
    
    Route::get('tv', function () {
        return view('admin.tv.index');
    })->name('admin.tv.index');
});
