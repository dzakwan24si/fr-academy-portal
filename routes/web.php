<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\CareerProgramController;
use App\Http\Controllers\Admin\CompetencyController;
use App\Http\Controllers\Admin\DailyClassController;
use App\Http\Controllers\Admin\HomepageEventController;
use App\Http\Controllers\Admin\MoaController;
use App\Http\Controllers\Admin\NonRegulerDailyClassController;
use App\Http\Controllers\Admin\NonRegulerMoaController;
use App\Http\Controllers\Admin\NonRegulerTraineeController;
use App\Http\Controllers\Admin\NonRegulerTrainerController;
use App\Http\Controllers\Admin\NonRegulerTrainingProgramController;
use App\Http\Controllers\Admin\NonRegulerWeeklyReportController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\TraineeController;
use App\Http\Controllers\Admin\TrainerController;
use App\Http\Controllers\Admin\TrainingProgramController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WeeklyReportController;
use App\Http\Controllers\Administrasi\ActivityLogController;
use App\Http\Controllers\Administrasi\CutiController;
use App\Http\Controllers\Administrasi\MppController;
use App\Http\Controllers\Administrasi\RekrutmenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompetencyPublicController;
use App\Http\Controllers\Cost\BiayaRekruitController;
use App\Http\Controllers\Cost\CapexController;
use App\Http\Controllers\Cost\CostController;
use App\Http\Controllers\Cost\VraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventaris\InventarisController;
use App\Http\Controllers\Inventaris\InventarisDashboardController;
use App\Http\Controllers\Inventaris\InventarisGudangController;
use App\Http\Controllers\Inventaris\InventarisKantorController;
use App\Http\Controllers\Inventaris\InventarisMessController;
use App\Http\Controllers\Inventaris\InventarisSummaryController;
use App\Http\Controllers\Monitoring\AsistenController;
use App\Http\Controllers\Monitoring\MonitoringDashboardController;
use App\Http\Controllers\Monitoring\MonitoringTalentController;
use App\Http\Controllers\Monitoring\ResignedStaffController;
use App\Http\Controllers\Program\DashboardMonitoringTahunanController;
use App\Http\Controllers\Program\DashboardRealisasiController;
use App\Http\Controllers\Program\ProgramBatchController;
use App\Http\Controllers\Program\RealisasiVsBatchController;
use App\Http\Controllers\Program\TrainingRecordController;
use App\Models\Article;
use App\Models\Attendance;
use App\Models\CareerProgram;
use App\Models\Category;
use App\Models\Competency\CompetencyDictionary;
use App\Models\DailyClass;
use App\Models\HomepageEvent;
use App\Models\Trainee;
use App\Models\TrainingProgram;
use App\Models\TvPlaylist;
use App\Models\User;
use App\Models\WeeklyReport;
use App\Notifications\WebPushBlastNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/', function () {
    $latest_articles = Article::where('status', 'published')->latest()->take(3)->get();
    $active_event = HomepageEvent::where('is_active', true)->first();

    return view('welcome', compact('latest_articles', 'active_event'));
});

Route::get('/sitemap.xml', function () {
    $articles = Article::where('status', 'published')->get();
    $competencyDictionaries = CompetencyDictionary::published()
        ->get(['code', 'year', 'updated_at']);

    return response()->view('sitemap', compact('articles', 'competencyDictionaries'))
        ->header('Content-Type', 'text/xml');
})->name('sitemap');

Route::get('/program', function () {
    return view('program');
})->name('program');

Route::get('/standar-kompetensi/{code?}', [CompetencyPublicController::class, 'show'])
    ->where('code', 'estate|mill')
    ->name('competency.public');

Route::get('/tentang-kami', function () {
    return view('about');
})->name('about');

Route::get('/career', function () {
    $programs = CareerProgram::where('is_active', true)->get();

    return view('career.index', compact('programs'));
})->name('career.index');

Route::get('/career-qr', function (Request $request) {
    $url = $request->query('url', 'https://fr-academy.com');
    $qrCode = QrCode::size(200)->generate($url);

    return response($qrCode)->header('Content-Type', 'image/svg+xml');
})->name('career.qr');

Route::get('/download-app', function () {
    $path = public_path('downloads/LearningActivity.apk');
    if (! file_exists($path)) {
        $rootPath = base_path('LearningActivity.apk');
        if (file_exists($rootPath)) {
            copy($rootPath, $path);
        } else {
            abort(404, 'File APK belum tersedia.');
        }
    }

    return response()->download($path, 'FR_Learning_Activity.apk', [
        'Content-Type' => 'application/vnd.android.package-archive',
    ]);
})->name('download.app');

Route::get('/tv-display', function () {
    $playlists = TvPlaylist::with('article.author', 'article.category')->orderBy('order')->get();

    // Stable hash based on latest updates and count
    $playlistUpdate = TvPlaylist::max('updated_at');
    $articleUpdate = Article::max('updated_at');
    $playlistCount = TvPlaylist::count();
    $currentHash = md5($playlistUpdate.$articleUpdate.$playlistCount);

    $playlists->transform(function ($item) {
        $url = route('artikel.show', $item->article->slug);
        $item->qr_code = QrCode::size(120)->margin(1)->generate($url)->toHtml();

        return $item;
    });

    return view('tv.display', compact('playlists', 'currentHash'));
})->name('tv.display');

Route::get('/tv-display/check', function () {
    $playlistUpdate = TvPlaylist::max('updated_at');
    $articleUpdate = Article::max('updated_at');
    $playlistCount = TvPlaylist::count();
    $currentHash = md5($playlistUpdate.$articleUpdate.$playlistCount);

    return response()->json([
        'hash' => $currentHash,
    ]);
})->name('tv.check');

Route::get('/artikel', function () {
    $query = Article::where('status', 'published')->latest();

    if (request()->has('category')) {
        $query->where('category_id', request('category'));
    }

    $articles = $query->paginate(9)->withQueryString();
    $categories = Category::all();

    return view('article.index', compact('articles', 'categories'));
});

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

// Admin Routes (Protected)
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/', function () {
        $user = auth()->user();
        if ($user->hasRole('Top Management')) {
            return redirect('/Top_Management');
        }

        if (! $user->can('access_administrasi') && ($user->can('access_learning_activity') || $user->hasRole('Trainer'))) {
            return redirect()->route('training-non-reguler.dashboard');
        }

        return redirect()->route('administrasi.dashboard-realisasi.index');
    })->name('admin.dashboard');

    Route::resource('artikel', AdminArticleController::class)->names('admin.artikel')->middleware('can:manage_articles');

    // User Management Routes
    Route::resource('users', UserController::class)->names('admin.users')->middleware('can:manage_users');
    Route::resource('roles', RoleController::class)->names('admin.roles')->middleware('can:manage_roles');

    // Event Themes Management
    Route::resource('events', HomepageEventController::class)->names('admin.events')->middleware('can:manage_events');

    // Profile Management Routes
    Route::get('profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('admin.profile.update');

    Route::get('tv', function () {
        return view('admin.tv.index');
    })->name('admin.tv.index')->middleware('can:manage_tv_display');

    // Career Programs Management
    Route::resource('career-programs', CareerProgramController::class)
        ->names('admin.career_programs')
        ->middleware('can:manage_career');

    // Standar Kompetensi Management
    Route::middleware('can:manage_competency')->prefix('kompetensi')->name('admin.competency.')->group(function () {
        Route::get('/', [CompetencyController::class, 'index'])->name('index');
        Route::post('/', [CompetencyController::class, 'store'])->name('store');
        Route::get('/{dictionary}', [CompetencyController::class, 'show'])->name('show');
        Route::put('/{dictionary}', [CompetencyController::class, 'update'])->name('update');
        Route::delete('/{dictionary}', [CompetencyController::class, 'destroy'])->name('destroy');
        Route::patch('/{dictionary}/toggle-publish', [CompetencyController::class, 'togglePublish'])->name('toggle_publish');
        Route::post('/{dictionary}/copy', [CompetencyController::class, 'copy'])->name('copy');

        // Positions
        Route::post('/{dictionary}/positions', [CompetencyController::class, 'storePosition'])->name('positions.store');
        Route::put('/positions/{position}', [CompetencyController::class, 'updatePosition'])->name('positions.update');
        Route::delete('/positions/{position}', [CompetencyController::class, 'destroyPosition'])->name('positions.destroy');
        Route::post('/{dictionary}/positions/reorder', [CompetencyController::class, 'reorderPositions'])->name('positions.reorder');

        // Competencies
        Route::post('/{dictionary}/competencies', [CompetencyController::class, 'storeCompetency'])->name('competencies.store');
        Route::get('/competencies/{competency}/edit', [CompetencyController::class, 'editCompetency'])->name('competencies.edit');
        Route::put('/competencies/{competency}', [CompetencyController::class, 'updateCompetency'])->name('competencies.update');
        Route::delete('/competencies/{competency}', [CompetencyController::class, 'destroyCompetency'])->name('competencies.destroy');
        Route::post('/{dictionary}/competencies/reorder', [CompetencyController::class, 'reorderCompetencies'])->name('competencies.reorder');
    });
});

// Mengamankan rute dashboard & administrasi agar memerlukan auth dan permission access_administrasi
Route::middleware(['auth', 'can:access_administrasi'])->group(function () {
    Route::get('/internal-dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/administrasi/dashboard-realisasi', [DashboardRealisasiController::class, 'index'])->name('administrasi.dashboard-realisasi.index');
    Route::post('/administrasi/dashboard-realisasi/update-reguler-row', [DashboardRealisasiController::class, 'updateRegulerRow'])->name('administrasi.dashboard-realisasi.update-reguler-row');
    Route::get('/administrasi/dashboard-monitoring-tahunan', [DashboardMonitoringTahunanController::class, 'index'])->name('administrasi.dashboard-monitoring-tahunan.index');

    Route::prefix('administrasi/program-batch')->name('administrasi.program-batch.')->group(function () {
        Route::get('/', [ProgramBatchController::class, 'index'])->name('index');
        Route::get('/data', [ProgramBatchController::class, 'data'])->name('data');
        Route::get('/template', [ProgramBatchController::class, 'template'])->name('template');
        Route::post('/import', [ProgramBatchController::class, 'import'])->name('import');
        Route::delete('/destroy-all', [ProgramBatchController::class, 'destroyAll'])->name('destroy-all');
        Route::post('/{type}', [ProgramBatchController::class, 'store'])->whereIn('type', ['reguler', 'non_reguler'])->name('store');
        Route::put('/{type}/{id}', [ProgramBatchController::class, 'update'])->whereIn('type', ['reguler', 'non_reguler'])->name('update');
        Route::delete('/{type}/{id}', [ProgramBatchController::class, 'destroy'])->whereIn('type', ['reguler', 'non_reguler'])->name('destroy');
    });

    Route::redirect('administrasi/program', '/administrasi/program-batch')->name('administrasi.program.index');

    Route::prefix('administrasi/realisasi-vs-batch')->name('administrasi.realisasi-vs-batch.')->group(function () {
        Route::get('/', [RealisasiVsBatchController::class, 'index'])->name('index');
        Route::post('/', [RealisasiVsBatchController::class, 'store'])->name('store');
        Route::post('/update-batch', [RealisasiVsBatchController::class, 'updateBatch'])->name('update-batch');
        Route::post('/import', [RealisasiVsBatchController::class, 'import'])->name('import');
        Route::get('/export', [RealisasiVsBatchController::class, 'export'])->name('export');
        Route::delete('/destroy-all', [RealisasiVsBatchController::class, 'destroyAll'])->name('destroy-all');
        Route::delete('/{id}', [RealisasiVsBatchController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('administrasi/training-record')->name('administrasi.training-record.')->group(function () {
        Route::get('/', [TrainingRecordController::class, 'index'])->name('index');
        Route::post('/', [TrainingRecordController::class, 'store'])->name('store');
        Route::post('/import', [TrainingRecordController::class, 'import'])->name('import');
        Route::get('/export', [TrainingRecordController::class, 'export'])->name('export');
        Route::get('/template', [TrainingRecordController::class, 'template'])->name('template');
        Route::delete('/destroy-all', [TrainingRecordController::class, 'destroyAll'])->name('destroy-all');
        Route::get('/{trainingRecord}', [TrainingRecordController::class, 'show'])->name('show');
        Route::put('/{trainingRecord}', [TrainingRecordController::class, 'update'])->name('update');
        Route::delete('/{trainingRecord}', [TrainingRecordController::class, 'destroy'])->name('destroy');
    });

    // Administrasi - MPP Jabatan Routes
    Route::prefix('administrasi/mpp')->name('administrasi.mpp.')->group(function () {
        Route::get('/', [MppController::class, 'index'])->name('index');
        Route::post('/', [MppController::class, 'store'])->name('store');
        Route::post('/import', [MppController::class, 'importCsv'])->name('import');
        Route::get('/template/download', [MppController::class, 'downloadTemplate'])->name('template');
        Route::get('/export/report', [MppController::class, 'export'])->name('export');
        Route::get('/get-by-jabatan', [MppController::class, 'showByJabatan'])->name('showByJabatan');
        Route::put('/update-jabatan', [MppController::class, 'updateJabatan'])->name('updateJabatan');
        Route::delete('/destroy-jabatan', [MppController::class, 'destroyJabatan'])->name('destroyJabatan');
    });

    // Administrasi - Schedule Bulanan Route
    Route::view('/administrasi/schedule', 'Administrasi.Pages.Program.schedule')->name('administrasi.schedule.index');

    // Administrasi - Cuti (Employee Leave Management) Routes
    Route::prefix('administrasi/cuti')->name('administrasi.cuti.')->group(function () {
        Route::get('/', [CutiController::class, 'index'])->name('index');
        Route::post('/', [CutiController::class, 'store'])->name('store');
        Route::post('/import', [CutiController::class, 'import'])->name('import');
        Route::get('/template/download', [CutiController::class, 'downloadTemplate'])->name('template');
        Route::post('/year', [CutiController::class, 'storeYear'])->name('year');
        Route::get('/export/report', [CutiController::class, 'export'])->name('export');
        Route::get('/{id}', [CutiController::class, 'show'])->name('show');
        Route::put('/{id}', [CutiController::class, 'update'])->name('update');
        Route::delete('/{id}', [CutiController::class, 'destroy'])->name('destroy');
    });

    // Administrasi - VRA (Vehicle Running Account) Routes (Read: view_cost, Write: edit_cost)
    Route::prefix('administrasi/vra')->name('administrasi.vra.')->middleware('can:view_cost')->group(function () {
        Route::get('/', [VraController::class, 'index'])->name('index');
        Route::get('/template/download', [VraController::class, 'downloadTemplate'])->name('template');
        Route::get('/export/report', [VraController::class, 'export'])->name('export');
        Route::get('/{id}', [VraController::class, 'show'])->name('show');

        Route::middleware('can:edit_cost')->group(function () {
            Route::post('/', [VraController::class, 'store'])->name('store');
            Route::post('/import', [VraController::class, 'importCsv'])->name('import');
            Route::delete('/destroy-all', [VraController::class, 'destroyAll'])->name('destroyAll');
            Route::put('/{id}', [VraController::class, 'update'])->name('update');
            Route::delete('/{id}', [VraController::class, 'destroy'])->name('destroy');
        });
    });

    // Administrasi - Rekrutmen Karyawan Routes
    Route::prefix('administrasi/rekrutmen')->name('administrasi.rekrutmen.')->group(function () {
        Route::get('/', [RekrutmenController::class, 'index'])->name('index');
        Route::get('/api/data', [RekrutmenController::class, 'getData'])->name('api.data');
        Route::post('/api/store', [RekrutmenController::class, 'store'])->name('api.store');
        Route::put('/api/{id}', [RekrutmenController::class, 'update'])->name('api.update');
        Route::delete('/api/destroy-all', [RekrutmenController::class, 'destroyAll'])->name('api.destroyAll');
        Route::delete('/api/{id}', [RekrutmenController::class, 'destroy'])->name('api.destroy');
        Route::post('/api/import', [RekrutmenController::class, 'importCsv'])->name('api.import');
        Route::get('/api/export', [RekrutmenController::class, 'exportCsv'])->name('api.export');
    });

    // Administrasi - Database Asisten Routes
    Route::prefix('administrasi/asisten')->name('administrasi.asisten.')->group(function () {
        Route::get('/', [AsistenController::class, 'index'])->name('index');
        Route::get('/template', [AsistenController::class, 'downloadTemplate'])->name('template');
        Route::get('/api/template', [AsistenController::class, 'downloadTemplate'])->name('api.template');
        Route::get('/api/data', [AsistenController::class, 'getData'])->name('api.data');
        Route::get('/api/{id}', [AsistenController::class, 'show'])->name('api.show');
        Route::post('/api/store', [AsistenController::class, 'store'])->name('api.store');
        Route::put('/api/{id}', [AsistenController::class, 'update'])->name('api.update');
        Route::delete('/api/destroy-all', [AsistenController::class, 'destroyAll'])->name('api.destroyAll');
        Route::delete('/api/{id}', [AsistenController::class, 'destroy'])->name('api.destroy');
        Route::post('/api/import', [AsistenController::class, 'importCsv'])->name('api.import');
        Route::get('/api/export', [AsistenController::class, 'exportCsv'])->name('api.export');

        // Monitoring Program & Angkatan endpoints (backward compatibility)
        Route::get('/api/monitoring-program/data', [AsistenController::class, 'getMonitoringProgramData'])->name('api.monitoring-program.data');
        Route::post('/api/monitoring-program/store', [AsistenController::class, 'storeMonitoringProgram'])->name('api.monitoring-program.store');
        Route::put('/api/monitoring-program/{id}', [AsistenController::class, 'updateMonitoringProgram'])->name('api.monitoring-program.update');
        Route::delete('/api/monitoring-program/{id}', [AsistenController::class, 'destroyMonitoringProgram'])->name('api.monitoring-program.destroy');
        Route::get('/api/monitoring-program/drilldown', [AsistenController::class, 'getDrilldownAsisten'])->name('api.monitoring-program.drilldown');
        Route::post('/api/monitoring-program/import', [AsistenController::class, 'importMonitoringProgramCsv'])->name('api.monitoring-program.import');
        Route::get('/api/monitoring-program/export', [AsistenController::class, 'exportMonitoringProgramCsv'])->name('api.monitoring-program.export');
    });

    // Administrasi - Monitoring Summarized Routes (Agregasi Per Program & Tahun)
    Route::prefix('administrasi/monitoring-summarized')->name('administrasi.monitoring-summarized.')->group(function () {
        Route::get('/', [AsistenController::class, 'indexMonitoringSummarized'])->name('index');
        Route::get('/api/data', [AsistenController::class, 'getMonitoringProgramData'])->name('api.data');
        Route::get('/api/drilldown', [AsistenController::class, 'getDrilldownAsisten'])->name('api.drilldown');
        Route::get('/api/export', [AsistenController::class, 'exportMonitoringProgramCsv'])->name('api.export');
    });

    // Administrasi - Monitoring Alumni Routes (Detail Per Gelombang / Batch)
    Route::prefix('administrasi/monitoring-alumni')->name('administrasi.monitoring-alumni.')->group(function () {
        Route::get('/', [AsistenController::class, 'indexMonitoringAlumni'])->name('index');
        Route::get('/api/data', [AsistenController::class, 'getMonitoringProgramData'])->name('api.data');
        Route::post('/api/store', [AsistenController::class, 'storeMonitoringProgram'])->name('api.store');
        Route::put('/api/{id}', [AsistenController::class, 'updateMonitoringProgram'])->name('api.update');
        Route::delete('/api/{id}', [AsistenController::class, 'destroyMonitoringProgram'])->name('api.destroy');
        Route::get('/api/drilldown', [AsistenController::class, 'getDrilldownAsisten'])->name('api.drilldown');
        Route::get('/api/export', [AsistenController::class, 'exportMonitoringProgramCsv'])->name('api.export');
    });

    // Administrasi - Monitoring Lulusan Alumni Routes (backward compatible)
    Route::prefix('administrasi/monitoring-lulusan')->name('administrasi.monitoring-lulusan.')->group(function () {
        Route::get('/', [AsistenController::class, 'indexMonitoringAlumni'])->name('index');
        Route::get('/api/data', [AsistenController::class, 'getMonitoringProgramData'])->name('api.data');
        Route::post('/api/store', [AsistenController::class, 'storeMonitoringProgram'])->name('api.store');
        Route::put('/api/{id}', [AsistenController::class, 'updateMonitoringProgram'])->name('api.update');
        Route::delete('/api/{id}', [AsistenController::class, 'destroyMonitoringProgram'])->name('api.destroy');
        Route::get('/api/drilldown', [AsistenController::class, 'getDrilldownAsisten'])->name('api.drilldown');
        Route::post('/api/import', [AsistenController::class, 'importMonitoringProgramCsv'])->name('api.import');
        Route::get('/api/export', [AsistenController::class, 'exportMonitoringProgramCsv'])->name('api.export');
    });

    // Administrasi - Monitoring Talent Routes
    Route::prefix('administrasi/monitoring-talent')->name('administrasi.monitoring-talent.')->group(function () {
        Route::get('/', [MonitoringTalentController::class, 'index'])->name('index');
        Route::post('/store', [MonitoringTalentController::class, 'store'])->name('store');
        Route::post('/update-progress-materi', [MonitoringTalentController::class, 'updateProgressMateri'])->name('update-progress-materi');
        Route::put('/{id}', [MonitoringTalentController::class, 'update'])->name('update');
        Route::delete('/destroy-all', [MonitoringTalentController::class, 'destroyAll'])->name('destroyAll');
        Route::delete('/{id}', [MonitoringTalentController::class, 'destroy'])->name('destroy');
        Route::post('/import', [MonitoringTalentController::class, 'import'])->name('import');
        Route::get('/export', [MonitoringTalentController::class, 'export'])->name('export');
        Route::get('/template', [MonitoringTalentController::class, 'template'])->name('template');
    });

    // Administrasi - Biaya Rekruit Routes (Read: view_cost, Write: edit_cost)
    Route::prefix('administrasi/biaya-rekruit')->name('administrasi.biaya-rekruit.')->middleware('can:view_cost')->group(function () {
        Route::get('/', [BiayaRekruitController::class, 'index'])->name('index');
        Route::get('/template/download', [BiayaRekruitController::class, 'downloadTemplate'])->name('template');
        Route::get('/export/report', [BiayaRekruitController::class, 'export'])->name('export');
        Route::get('/{id}', [BiayaRekruitController::class, 'show'])->name('show');

        Route::middleware('can:edit_cost')->group(function () {
            Route::post('/', [BiayaRekruitController::class, 'store'])->name('store');
            Route::post('/import', [BiayaRekruitController::class, 'importCsv'])->name('import');
            Route::delete('/destroy-all', [BiayaRekruitController::class, 'destroyAll'])->name('destroyAll');
            Route::put('/{id}', [BiayaRekruitController::class, 'update'])->name('update');
            Route::delete('/{id}', [BiayaRekruitController::class, 'destroy'])->name('destroy');
        });
    });

    // Administrasi - CAPEX (Capital Expenditure) Routes (Read: view_cost, Write: edit_cost)
    Route::prefix('administrasi/capex')->name('administrasi.capex.')->middleware('can:view_cost')->group(function () {
        Route::get('/', [CapexController::class, 'index'])->name('index');
        Route::get('/rab/{id}', [CapexController::class, 'showRab'])->name('rab.show');
        Route::get('/realisasi/{id}', [CapexController::class, 'showRealisasi'])->name('realisasi.show');
        Route::get('/template/download', [CapexController::class, 'downloadTemplate'])->name('template');
        Route::get('/export/report', [CapexController::class, 'export'])->name('export');

        Route::middleware('can:edit_cost')->group(function () {
            Route::post('/rab', [CapexController::class, 'storeRab'])->name('rab.store');
            Route::post('/realisasi', [CapexController::class, 'storeRealisasi'])->name('realisasi.store');
            Route::put('/rab/{id}', [CapexController::class, 'updateRab'])->name('rab.update');
            Route::put('/realisasi/{id}', [CapexController::class, 'updateRealisasi'])->name('realisasi.update');
            Route::delete('/rab/{id}', [CapexController::class, 'destroyRab'])->name('rab.destroy');
            Route::delete('/realisasi/{id}', [CapexController::class, 'destroyRealisasi'])->name('realisasi.destroy');
            Route::post('/import', [CapexController::class, 'importCsv'])->name('import');
            Route::delete('/destroy-all', [CapexController::class, 'destroyAll'])->name('destroyAll');
        });
    });

    // Administrasi - Cost Routes (Read: view_cost, Write: edit_cost)
    Route::prefix('administrasi/cost')->name('administrasi.cost.')->middleware('can:view_cost')->group(function () {
        Route::get('/', [CostController::class, 'index'])->name('index');
        Route::get('/template/download', [CostController::class, 'downloadTemplate'])->name('template');
        Route::get('/export/report', [CostController::class, 'export'])->name('export');
        Route::get('/{id}', [CostController::class, 'show'])->name('show');

        Route::middleware('can:edit_cost')->group(function () {
            Route::post('/', [CostController::class, 'store'])->name('store');
            Route::post('/import', [CostController::class, 'importCsv'])->name('import');
            Route::post('/sync', [CostController::class, 'sync'])->name('sync');
            Route::delete('/destroy-all', [CostController::class, 'destroyAll'])->name('destroyAll');
            Route::put('/{id}', [CostController::class, 'update'])->name('update');
            Route::delete('/{id}', [CostController::class, 'destroy'])->name('destroy');
        });
    });

    // Histori Aktivitas / Audit Log Routes
    Route::prefix('administrasi/activity-logs')->name('administrasi.activity-logs.')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('index');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('show');
    });

    // Administrasi - Inventaris Routes
    Route::prefix('administrasi/inventaris')->name('administrasi.inventaris.')->group(function () {
        // Dashboard & Summary Overview Pages
        Route::get('/', [InventarisDashboardController::class, 'index'])->name('index');
        Route::get('/dashboard', [InventarisDashboardController::class, 'index'])->name('dashboard');
        Route::get('/summary', [InventarisSummaryController::class, 'index'])->name('summary');

        // Unified Routes (Single Controller)
        Route::delete('/barang/destroy-all', [InventarisController::class, 'barangDestroyAll'])->name('barang.destroyAll');
        Route::get('/barang', [InventarisController::class, 'barangIndex'])->name('barang');
        Route::get('/barang/template', [InventarisController::class, 'barangTemplate'])->name('barang.template');
        Route::post('/barang/import', [InventarisController::class, 'barangImport'])->name('barang.import');
        Route::post('/barang', [InventarisController::class, 'barangStore'])->name('barang.store');
        Route::put('/barang/{id}', [InventarisController::class, 'barangUpdate'])->name('barang.update');
        Route::delete('/barang/{id}', [InventarisController::class, 'barangDestroy'])->name('barang.destroy');

        Route::delete('/pembelian/destroy-all', [InventarisController::class, 'pembelianDestroyAll'])->name('pembelian.destroyAll');
        Route::get('/pembelian', [InventarisController::class, 'pembelianIndex'])->name('pembelian');
        Route::get('/pembelian/template', [InventarisController::class, 'pembelianTemplate'])->name('pembelian.template');
        Route::post('/pembelian/import', [InventarisController::class, 'pembelianImport'])->name('pembelian.import');
        Route::post('/pembelian', [InventarisController::class, 'pembelianStore'])->name('pembelian.store');
        Route::put('/pembelian/{id}', [InventarisController::class, 'pembelianUpdate'])->name('pembelian.update');
        Route::delete('/pembelian/{id}', [InventarisController::class, 'pembelianDestroy'])->name('pembelian.destroy');

        Route::delete('/stock-opname/destroy-all', [InventarisController::class, 'stockOpnameDestroyAll'])->name('stock-opname.destroyAll');
        Route::get('/stock-opname', [InventarisController::class, 'stockOpnameIndex'])->name('stock-opname');
        Route::get('/stock-opname/template', [InventarisController::class, 'stockOpnameTemplate'])->name('stock-opname.template');
        Route::post('/stock-opname/import', [InventarisController::class, 'stockOpnameImport'])->name('stock-opname.import');
        Route::post('/stock-opname/session', [InventarisController::class, 'stockOpnameSessionStore'])->name('stock-opname.session.store');
        Route::put('/stock-opname/session/{date}', [InventarisController::class, 'stockOpnameSessionUpdate'])->name('stock-opname.session.update');
        Route::delete('/stock-opname/session/{date}', [InventarisController::class, 'stockOpnameSessionDestroy'])->name('stock-opname.session.destroy');

        Route::get('/stock-opname/{date}', [InventarisController::class, 'stockOpnameDetail'])->name('stock-opname.detail');
        Route::get('/stock-opname/{date}/export', [InventarisController::class, 'stockOpnameExport'])->name('stock-opname.export');
        Route::post('/stock-opname/{date}/item', [InventarisController::class, 'stockOpnameItemStore'])->name('stock-opname.item.store');
        Route::put('/stock-opname/item/{id}', [InventarisController::class, 'stockOpnameItemUpdate'])->name('stock-opname.item.update');
        Route::delete('/stock-opname/item/{id}', [InventarisController::class, 'stockOpnameItemDestroy'])->name('stock-opname.item.destroy');

        // Gudang Subsystem (Aliases to InventarisController)
        Route::prefix('gudang')->name('gudang.')->group(function () {
            Route::delete('/barang/destroy-all', [InventarisGudangController::class, 'barangDestroyAll'])->name('barang.destroyAll');
            Route::get('/barang', [InventarisGudangController::class, 'barangIndex'])->name('barang');
            Route::get('/barang/template', [InventarisGudangController::class, 'barangTemplate'])->name('barang.template');
            Route::post('/barang/import', [InventarisGudangController::class, 'barangImport'])->name('barang.import');
            Route::post('/barang', [InventarisGudangController::class, 'barangStore'])->name('barang.store');
            Route::put('/barang/{id}', [InventarisGudangController::class, 'barangUpdate'])->name('barang.update');
            Route::delete('/barang/{id}', [InventarisGudangController::class, 'barangDestroy'])->name('barang.destroy');

            Route::delete('/pembelian/destroy-all', [InventarisGudangController::class, 'pembelianDestroyAll'])->name('pembelian.destroyAll');
            Route::get('/pembelian', [InventarisGudangController::class, 'pembelianIndex'])->name('pembelian');
            Route::get('/pembelian/template', [InventarisGudangController::class, 'pembelianTemplate'])->name('pembelian.template');
            Route::post('/pembelian/import', [InventarisGudangController::class, 'pembelianImport'])->name('pembelian.import');
            Route::post('/pembelian', [InventarisGudangController::class, 'pembelianStore'])->name('pembelian.store');
            Route::put('/pembelian/{id}', [InventarisGudangController::class, 'pembelianUpdate'])->name('pembelian.update');
            Route::delete('/pembelian/{id}', [InventarisGudangController::class, 'pembelianDestroy'])->name('pembelian.destroy');

            Route::delete('/stock-opname/destroy-all', [InventarisGudangController::class, 'stockOpnameDestroyAll'])->name('stock-opname.destroyAll');
            Route::get('/stock-opname', [InventarisGudangController::class, 'stockOpnameIndex'])->name('stock-opname');
            Route::get('/stock-opname/template', [InventarisGudangController::class, 'stockOpnameTemplate'])->name('stock-opname.template');
            Route::post('/stock-opname/import', [InventarisGudangController::class, 'stockOpnameImport'])->name('stock-opname.import');
            Route::post('/stock-opname/session', [InventarisGudangController::class, 'stockOpnameSessionStore'])->name('stock-opname.session.store');
            Route::put('/stock-opname/session/{date}', [InventarisGudangController::class, 'stockOpnameSessionUpdate'])->name('stock-opname.session.update');
            Route::delete('/stock-opname/session/{date}', [InventarisGudangController::class, 'stockOpnameSessionDestroy'])->name('stock-opname.session.destroy');

            Route::get('/stock-opname/{date}', [InventarisGudangController::class, 'stockOpnameDetail'])->name('stock-opname.detail');
            Route::get('/stock-opname/{date}/export', [InventarisGudangController::class, 'stockOpnameExport'])->name('stock-opname.export');
            Route::post('/stock-opname/{date}/item', [InventarisGudangController::class, 'stockOpnameItemStore'])->name('stock-opname.item.store');
            Route::put('/stock-opname/item/{id}', [InventarisGudangController::class, 'stockOpnameItemUpdate'])->name('stock-opname.item.update');
            Route::delete('/stock-opname/item/{id}', [InventarisGudangController::class, 'stockOpnameItemDestroy'])->name('stock-opname.item.destroy');
        });

        // Kantor Subsystem (Aliases to InventarisController)
        Route::prefix('kantor')->name('kantor.')->group(function () {
            Route::delete('/barang/destroy-all', [InventarisKantorController::class, 'barangDestroyAll'])->name('barang.destroyAll');
            Route::get('/barang', [InventarisKantorController::class, 'barangIndex'])->name('barang');
            Route::get('/barang/template', [InventarisKantorController::class, 'barangTemplate'])->name('barang.template');
            Route::post('/barang/import', [InventarisKantorController::class, 'barangImport'])->name('barang.import');
            Route::post('/barang', [InventarisKantorController::class, 'barangStore'])->name('barang.store');
            Route::put('/barang/{id}', [InventarisKantorController::class, 'barangUpdate'])->name('barang.update');
            Route::delete('/barang/{id}', [InventarisKantorController::class, 'barangDestroy'])->name('barang.destroy');

            Route::delete('/pembelian/destroy-all', [InventarisKantorController::class, 'pembelianDestroyAll'])->name('pembelian.destroyAll');
            Route::get('/pembelian', [InventarisKantorController::class, 'pembelianIndex'])->name('pembelian');
            Route::get('/pembelian/template', [InventarisKantorController::class, 'pembelianTemplate'])->name('pembelian.template');
            Route::post('/pembelian/import', [InventarisKantorController::class, 'pembelianImport'])->name('pembelian.import');
            Route::post('/pembelian', [InventarisKantorController::class, 'pembelianStore'])->name('pembelian.store');
            Route::put('/pembelian/{id}', [InventarisKantorController::class, 'pembelianUpdate'])->name('pembelian.update');
            Route::delete('/pembelian/{id}', [InventarisKantorController::class, 'pembelianDestroy'])->name('pembelian.destroy');

            Route::delete('/stock-opname/destroy-all', [InventarisKantorController::class, 'stockOpnameDestroyAll'])->name('stock-opname.destroyAll');
            Route::get('/stock-opname', [InventarisKantorController::class, 'stockOpnameIndex'])->name('stock-opname');
            Route::get('/stock-opname/template', [InventarisKantorController::class, 'stockOpnameTemplate'])->name('stock-opname.template');
            Route::post('/stock-opname/import', [InventarisController::class, 'stockOpnameImport'])->name('stock-opname.import');
            Route::post('/stock-opname/session', [InventarisKantorController::class, 'stockOpnameSessionStore'])->name('stock-opname.session.store');
            Route::put('/stock-opname/session/{date}', [InventarisKantorController::class, 'stockOpnameSessionUpdate'])->name('stock-opname.session.update');
            Route::delete('/stock-opname/session/{date}', [InventarisKantorController::class, 'stockOpnameSessionDestroy'])->name('stock-opname.session.destroy');

            Route::get('/stock-opname/{date}', [InventarisKantorController::class, 'stockOpnameDetail'])->name('stock-opname.detail');
            Route::get('/stock-opname/{date}/export', [InventarisKantorController::class, 'stockOpnameExport'])->name('stock-opname.export');
            Route::post('/stock-opname/{date}/item', [InventarisKantorController::class, 'stockOpnameItemStore'])->name('stock-opname.item.store');
            Route::put('/stock-opname/item/{id}', [InventarisKantorController::class, 'stockOpnameItemUpdate'])->name('stock-opname.item.update');
            Route::delete('/stock-opname/item/{id}', [InventarisKantorController::class, 'stockOpnameItemDestroy'])->name('stock-opname.item.destroy');
        });

        // Mess Subsystem (Read-Only)
        Route::prefix('mess')->name('mess.')->group(function () {
            // Tab 1: Barang
            Route::get('/barang', [InventarisMessController::class, 'barangIndex'])->name('barang');

            // Tab 2: Pembelian
            Route::get('/pembelian', [InventarisMessController::class, 'pembelianIndex'])->name('pembelian');

            // Tab 3: Stock Opname Sessions
            Route::get('/stock-opname', [InventarisMessController::class, 'stockOpnameIndex'])->name('stock-opname');

            // Tab 4: Stock Opname Detail
            Route::get('/stock-opname/{date}', [InventarisMessController::class, 'stockOpnameDetail'])->name('stock-opname.detail');
            Route::get('/stock-opname/{date}/export', [InventarisMessController::class, 'stockOpnameExport'])->name('stock-opname.export');
        });
    });

    // Top Management Module (React SPA) Routes
    Route::get('/Top_Management/{any?}', function () {
        return view('Top_Management.index');
    })->where('any', '.*')->name('Top_Management.index');

    Route::get('/top-management/{any?}', function () {
        return view('Top_Management.index');
    })->where('any', '.*')->name('top-management');

    // Administrasi - Monitoring Karyawan (Detailed) Backend API Routes
    Route::prefix('administrasi/monitoring/api')->name('administrasi.monitoring.api.')->group(function () {
        // Employees API
        Route::get('/employees', [MonitoringDashboardController::class, 'getEmployees'])->name('employees.index');
        Route::post('/employees', [MonitoringDashboardController::class, 'storeEmployee'])->name('employees.store');
        Route::post('/employees/import', [MonitoringDashboardController::class, 'importEmployees'])->name('employees.import');
        Route::get('/employees/export', [MonitoringDashboardController::class, 'exportEmployees'])->name('employees.export');
        Route::get('/employees/template', [MonitoringDashboardController::class, 'templateEmployees'])->name('employees.template');
        Route::delete('/employees/destroy-all', [MonitoringDashboardController::class, 'destroyAllEmployees'])->name('employees.destroyAll');
        Route::put('/employees/{id}', [MonitoringDashboardController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{id}', [MonitoringDashboardController::class, 'destroyEmployee'])->name('employees.destroy');

        // Angkatan API
        Route::get('/angkatan', [MonitoringDashboardController::class, 'getAngkatan'])->name('angkatan.index');
        Route::post('/angkatan', [MonitoringDashboardController::class, 'storeAngkatan'])->name('angkatan.store');
        Route::post('/angkatan/import', [MonitoringDashboardController::class, 'importAngkatan'])->name('angkatan.import');
        Route::get('/angkatan/export', [MonitoringDashboardController::class, 'exportAngkatan'])->name('angkatan.export');
        Route::get('/angkatan/template', [MonitoringDashboardController::class, 'templateAngkatan'])->name('angkatan.template');
        Route::delete('/angkatan/destroy-all', [MonitoringDashboardController::class, 'destroyAllAngkatan'])->name('angkatan.destroyAll');
        Route::put('/angkatan/{id}', [MonitoringDashboardController::class, 'updateAngkatan'])->name('angkatan.update');
        Route::delete('/angkatan/{id}', [MonitoringDashboardController::class, 'destroyAngkatan'])->name('angkatan.destroy');

        // Culled API
        Route::get('/culled', [MonitoringDashboardController::class, 'getCulled'])->name('culled.index');
        Route::post('/culled', [MonitoringDashboardController::class, 'storeCulled'])->name('culled.store');
        Route::post('/culled/import', [MonitoringDashboardController::class, 'importCulled'])->name('culled.import');
        Route::get('/culled/export', [MonitoringDashboardController::class, 'exportCulled'])->name('culled.export');
        Route::get('/culled/template', [MonitoringDashboardController::class, 'templateCulled'])->name('culled.template');
        Route::delete('/culled/destroy-all', [MonitoringDashboardController::class, 'destroyAllCulled'])->name('culled.destroyAll');
        Route::put('/culled/{id}', [MonitoringDashboardController::class, 'updateCulled'])->name('culled.update');
        Route::delete('/culled/{id}', [MonitoringDashboardController::class, 'destroyCulled'])->name('culled.destroy');

        // Afdeling API
        Route::get('/afdeling', [MonitoringDashboardController::class, 'getAfdeling'])->name('afdeling.index');
        Route::put('/afdeling', [MonitoringDashboardController::class, 'updateAfdeling'])->name('afdeling.update');

        // Reset Data to CSV Seed
        Route::post('/reset-data', [MonitoringDashboardController::class, 'resetData'])->name('reset-data');
    });

    // Administrasi - Staf Resign
    Route::get('/administrasi/staf-resign', [ResignedStaffController::class, 'index'])->name('administrasi.staf-resign.index');
    Route::post('/administrasi/staf-resign', [ResignedStaffController::class, 'store'])->name('administrasi.staf-resign.store');
    Route::put('/administrasi/staf-resign/{id}', [ResignedStaffController::class, 'update'])->name('administrasi.staf-resign.update');
    Route::delete('/administrasi/staf-resign/{id}', [ResignedStaffController::class, 'destroy'])->name('administrasi.staf-resign.destroy');
    Route::get('/administrasi/staf-resign/template', [ResignedStaffController::class, 'downloadTemplate'])->name('administrasi.staf-resign.template');
    Route::post('/administrasi/staf-resign/import/preview', [ResignedStaffController::class, 'previewImport'])->name('administrasi.staf-resign.import-preview');
    Route::post('/administrasi/staf-resign/import/process', [ResignedStaffController::class, 'processImport'])->name('administrasi.staf-resign.import-process');

    // Administrasi - Monitoring Karyawan (React SPA) Routes
    Route::get('/administrasi/monitoring/{any?}', function () {
        return view('Administrasi.Pages.Monitoring_Spesifik.index');
    })->where('any', '.*')->name('administrasi.monitoring.index');

    Route::get('/Administrasi/Monitoring/{any?}', function () {
        return view('Administrasi.Pages.Monitoring_Spesifik.index');
    })->where('any', '.*');
});

Route::get('/clear-opcache', function () {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    Artisan::call('optimize:clear');

    return 'Cache server berhasil dibersihkan! Silakan kembali ke halaman Edit dan Refresh (F5).';
});

Route::prefix('training-reguler')->group(function () {
    Route::get('/', function () {
        $totalTrainees = Trainee::where('status', 'aktif')->count();
        $totalPrograms = TrainingProgram::count();
        $draftReports = WeeklyReport::where('status', 'draft')->count();
        $recentReports = WeeklyReport::with('trainingProgram', 'trainer')->latest()->take(5)->get();

        return view('admin.training-dashboard', compact('totalTrainees', 'totalPrograms', 'draftReports', 'recentReports'));
    })->name('training-reguler.dashboard');

    Route::resource('training-programs', TrainingProgramController::class)->names('training-reguler.training-programs');
    Route::resource('trainees', TraineeController::class)->names('training-reguler.trainees');
    Route::post('trainees/import', [TraineeController::class, 'import'])->name('training-reguler.trainees.import');
    Route::get('trainees/template', [TraineeController::class, 'downloadTemplate'])->name('training-reguler.trainees.template');
    Route::resource('trainers', TrainerController::class)->names('training-reguler.trainers');
    Route::post('weekly-reports/merge', [WeeklyReportController::class, 'merge'])->name('training-reguler.weekly-reports.merge');
    Route::post('weekly-reports/{weekly_report}/unmerge', [WeeklyReportController::class, 'unmerge'])->name('training-reguler.weekly-reports.unmerge');
    Route::resource('weekly-reports', WeeklyReportController::class)->names('training-reguler.weekly-reports');
    Route::get('weekly-reports/{weekly_report}/export-pdf', [WeeklyReportController::class, 'exportPdf'])->name('training-reguler.weekly-reports.export-pdf');
    Route::resource('moas', MoaController::class)->names('training-reguler.moas');

    // Dummy Print Routes
    Route::get('print/attendance', function () {
        return view('admin.print_dummy.attendance');
    })->name('training-reguler.print.attendance');

    Route::get('print/tasks', function () {
        return view('admin.print_dummy.tasks');
    })->name('training-reguler.print.tasks.dummy');

    Route::post('daily-classes/{daily_class}/attendance', [DailyClassController::class, 'updateAttendance'])->name('training-reguler.daily-classes.attendance.update');
    Route::post('daily-classes/{daily_class}/generate-qr', [DailyClassController::class, 'generateQr'])->name('training-reguler.daily-classes.generate-qr');
    Route::post('daily-classes/{daily_class}/toggle-evaluation', [DailyClassController::class, 'toggleEvaluation'])->name('training-reguler.daily-classes.toggle-evaluation');
    Route::get('daily-classes/{daily_class}/print-qr', [DailyClassController::class, 'printQr'])->name('training-reguler.daily-classes.print-qr');
    Route::get('daily-classes/{daily_class}/print-tasks', [DailyClassController::class, 'printTasks'])->name('training-reguler.daily-classes.print-tasks');
    Route::post('daily-classes/{daily_class}/tasks', [DailyClassController::class, 'storeTask'])->name('training-reguler.daily-classes.tasks.store');
    Route::get('tasks/{task}', [DailyClassController::class, 'showTask'])->name('training-reguler.tasks.show');
    Route::delete('tasks/{task}', [DailyClassController::class, 'destroyTask'])->name('training-reguler.daily-classes.tasks.destroy');
    Route::resource('daily-classes', DailyClassController::class)->names('training-reguler.daily-classes');
});

Route::prefix('training-non-reguler')->group(function () {
    Route::get('/', function () {
        $today = now()->format('Y-m-d');

        // Menghitung total peserta yang materi/kelasnya berjalan hari ini
        $totalTrainees = Attendance::whereHas('dailyClass', function ($query) use ($today) {
            $query->where(function ($q) use ($today) {
                $q->whereNotNull('end_date')
                    ->whereDate('date', '<=', $today)
                    ->whereDate('end_date', '>=', $today);
            })->orWhere(function ($q) use ($today) {
                $q->whereNull('end_date')
                    ->whereDate('date', '=', $today);
            });
        })->distinct('trainee_id')->count('trainee_id');
        $totalSesi = DailyClass::count();
        $totalPrograms = TrainingProgram::count();

        $recentSesi = DailyClass::with('trainingProgram')
            ->whereDate('date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereDate('end_date', '>=', $today)
                    ->orWhere(function ($sq) use ($today) {
                        $sq->whereNull('end_date')->whereDate('date', '>=', $today);
                    });
            })
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return view('admin.non-reguler.training-dashboard', compact('totalTrainees', 'totalSesi', 'totalPrograms', 'recentSesi'));
    })->name('training-non-reguler.dashboard');

    Route::resource('training-programs', NonRegulerTrainingProgramController::class)->names('training-non-reguler.training-programs');
    Route::resource('trainees', NonRegulerTraineeController::class)->names('training-non-reguler.trainees');
    Route::post('trainees/import', [NonRegulerTraineeController::class, 'import'])->name('training-non-reguler.trainees.import');
    Route::get('trainees/template', [NonRegulerTraineeController::class, 'downloadTemplate'])->name('training-non-reguler.trainees.template');
    Route::resource('trainers', NonRegulerTrainerController::class)->names('training-non-reguler.trainers');
    Route::post('weekly-reports/merge', [NonRegulerWeeklyReportController::class, 'merge'])->name('training-non-reguler.weekly-reports.merge');
    Route::post('weekly-reports/{weekly_report}/unmerge', [NonRegulerWeeklyReportController::class, 'unmerge'])->name('training-non-reguler.weekly-reports.unmerge');
    Route::resource('weekly-reports', NonRegulerWeeklyReportController::class)->names('training-non-reguler.weekly-reports');
    Route::get('weekly-reports/{weekly_report}/export-pdf', [NonRegulerWeeklyReportController::class, 'exportPdf'])->name('training-non-reguler.weekly-reports.export-pdf');
    Route::resource('moas', NonRegulerMoaController::class)->names('training-non-reguler.moas');

    // Dummy Print Routes
    Route::get('print/attendance', function () {
        return view('admin.print_dummy.attendance');
    })->name('training-non-reguler.print.attendance');

    Route::get('print/tasks', function () {
        return view('admin.print_dummy.tasks');
    })->name('training-non-reguler.print.tasks.dummy');

    Route::post('daily-classes/{daily_class}/import', [NonRegulerDailyClassController::class, 'importTrainees'])->name('training-non-reguler.daily-classes.import');
    Route::post('daily-classes/{daily_class}/add-manual', [NonRegulerDailyClassController::class, 'addManualTrainee'])->name('training-non-reguler.daily-classes.add-manual');
    Route::post('daily-classes/{daily_class}/attendance', [NonRegulerDailyClassController::class, 'updateAttendance'])->name('training-non-reguler.daily-classes.attendance.update');
    Route::post('daily-classes/{daily_class}/generate-qr', [NonRegulerDailyClassController::class, 'generateQr'])->name('training-non-reguler.daily-classes.generate-qr');
    Route::post('daily-classes/{daily_class}/generate-certificates', [NonRegulerDailyClassController::class, 'generateCertificates'])->name('training-non-reguler.daily-classes.generate-certificates');
    Route::post('daily-classes/{daily_class}/preview-certificate', [NonRegulerDailyClassController::class, 'previewCertificate'])->name('training-non-reguler.daily-classes.preview-certificate');
    Route::post('daily-classes/{daily_class}/toggle-evaluation', [NonRegulerDailyClassController::class, 'toggleEvaluation'])->name('training-non-reguler.daily-classes.toggle-evaluation');
    Route::delete('daily-classes/{daily_class}/clear-evaluation', [NonRegulerDailyClassController::class, 'clearEvaluation'])->name('training-non-reguler.daily-classes.clear-evaluation');
    Route::get('daily-classes/{daily_class}/print-qr', [NonRegulerDailyClassController::class, 'printQr'])->name('training-non-reguler.daily-classes.print-qr');
    Route::get('daily-classes/{daily_class}/print-tasks', [NonRegulerDailyClassController::class, 'printTasks'])->name('training-non-reguler.daily-classes.print-tasks');
    Route::get('daily-classes/{daily_class}/print-attendance', [NonRegulerDailyClassController::class, 'printAttendance'])->name('training-non-reguler.daily-classes.print-attendance');
    Route::get('daily-classes/{daily_class}/export-training-record', [NonRegulerDailyClassController::class, 'exportTrainingRecord'])->name('training-non-reguler.daily-classes.export-training-record');
    Route::post('daily-classes/{daily_class}/tasks', [NonRegulerDailyClassController::class, 'storeTask'])->name('training-non-reguler.daily-classes.tasks.store');
    Route::get('tasks/{task}', [NonRegulerDailyClassController::class, 'showTask'])->name('training-non-reguler.tasks.show');
    Route::delete('tasks/{task}', [NonRegulerDailyClassController::class, 'destroyTask'])->name('training-non-reguler.daily-classes.tasks.destroy');
    Route::delete('daily-classes/{daily_class}/trainees', [NonRegulerDailyClassController::class, 'removeAllTrainees'])->name('training-non-reguler.daily-classes.trainees.destroy-all');
    Route::delete('daily-classes/{daily_class}/trainees/{trainee}', [NonRegulerDailyClassController::class, 'removeTrainee'])->name('training-non-reguler.daily-classes.trainees.destroy');

    Route::post('daily-classes/{daily_class}/notify', [NonRegulerDailyClassController::class, 'notifyTrainees'])->name('training-non-reguler.daily-classes.notify');

    Route::get('history', [NonRegulerDailyClassController::class, 'history'])->name('training-non-reguler.history.index');
    Route::resource('daily-classes', NonRegulerDailyClassController::class)->names('training-non-reguler.daily-classes');
});

Route::middleware(['auth', 'verified'])->post('/admin/notifications/blast', function (Request $request) {
    $request->validate([
        'title' => 'required|string',
        'body' => 'required|string',
    ]);

    $trainees = Trainee::all();

    foreach ($trainees as $trainee) {
        $trainee->notify(new WebPushBlastNotification($request->title, $request->body));
    }

    return back()->with('success', 'Notifikasi berhasil dikirim ke seluruh peserta!');
})->name('admin.notifications.blast');
