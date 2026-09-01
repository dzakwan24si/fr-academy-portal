<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Models\TvPlaylist;

Route::get('/', function () {
    $latest_articles = \App\Models\Article::where('status', 'published')->latest()->take(3)->get();
    return view('welcome', compact('latest_articles'));
});

Route::get('/tv-display', function () {
    $playlists = TvPlaylist::with('article.author', 'article.category')->orderBy('order')->get()->map(function($item) {
        $url = route('artikel.show', $item->article->slug);
        $item->qr_code = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(1)->generate($url)->toHtml();
        return $item;
    });
    return view('tv.display', compact('playlists'));
})->name('tv.display');

Route::get('/artikel', function () {
    $query = \App\Models\Article::where('status', 'published')->latest();
    
    if (request()->has('category')) {
        $query->where('category_id', request('category'));
    }
    
    $articles = $query->paginate(9)->withQueryString();
    $categories = \App\Models\Category::all();
    return view('article.index', compact('articles', 'categories'));
});

use App\Models\Article;
use App\Models\Category;

Route::get('/artikel/{slug}', function ($slug) {
    $article = Article::with(['author', 'category'])->where('slug', $slug)->firstOrFail();
    $categories = Category::all();
    $latest_articles = Article::where('status', 'published')->where('id', '!=', $article->id)->latest()->take(3)->get();
    return view('article.show', compact('article', 'categories', 'latest_articles'));
})->name('artikel.show');

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
