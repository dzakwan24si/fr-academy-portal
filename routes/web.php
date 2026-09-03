<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Models\TvPlaylist;

Route::get('/', function () {
    $latest_articles = \App\Models\Article::where('status', 'published')->latest()->take(3)->get();
    $active_event = \App\Models\HomepageEvent::where('is_active', true)->first();
    return view('welcome', compact('latest_articles', 'active_event'));
});

Route::get('/sitemap.xml', function () {
    $articles = \App\Models\Article::where('status', 'published')->get();
    return response()->view('sitemap', compact('articles'))
                     ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::get('/tentang-kami', function () {
    return view('about');
})->name('about');

Route::get('/tv-display', function () {
    $playlists = TvPlaylist::with('article.author', 'article.category')->orderBy('order')->get();
    
    // Stable hash based on latest updates and count
    $playlistUpdate = TvPlaylist::max('updated_at');
    $articleUpdate = \App\Models\Article::max('updated_at');
    $playlistCount = TvPlaylist::count();
    $currentHash = md5($playlistUpdate . $articleUpdate . $playlistCount);
    
    $playlists->transform(function($item) {
        $url = route('artikel.show', $item->article->slug);
        $item->qr_code = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(1)->generate($url)->toHtml();
        return $item;
    });
    
    return view('tv.display', compact('playlists', 'currentHash'));
})->name('tv.display');

Route::get('/tv-display/check', function () {
    $playlistUpdate = TvPlaylist::max('updated_at');
    $articleUpdate = \App\Models\Article::max('updated_at');
    $playlistCount = TvPlaylist::count();
    $currentHash = md5($playlistUpdate . $articleUpdate . $playlistCount);
    
    return response()->json([
        'hash' => $currentHash
    ]);
})->name('tv.check');

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

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProfileController;

// Admin Routes (Protected)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', function () {
        $totalArticles = \App\Models\Article::count();
        $totalPlaylists = \App\Models\TvPlaylist::count();
        $totalUsers = \App\Models\User::count();
        $totalEvents = \App\Models\HomepageEvent::count();
        return view('admin.dashboard', compact('totalArticles', 'totalPlaylists', 'totalUsers', 'totalEvents'));
    })->name('admin.dashboard');

    Route::resource('artikel', AdminArticleController::class)->names('admin.artikel')->middleware('can:manage_articles');
    
    // User Management Routes
    Route::resource('users', UserController::class)->names('admin.users')->middleware('can:manage_users');
    Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class)->names('admin.roles')->middleware('can:manage_roles');

    // Event Themes Management
    Route::resource('events', \App\Http\Controllers\Admin\HomepageEventController::class)->names('admin.events')->middleware('can:manage_events');

    // Profile Management Routes
    Route::get('profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    
    Route::get('tv', function () {
        return view('admin.tv.index');
    })->name('admin.tv.index')->middleware('can:manage_tv_display');
});
