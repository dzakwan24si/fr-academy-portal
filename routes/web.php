<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/clear-opcache', function() {
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    \Artisan::call('optimize:clear');
    return 'Cache server berhasil dibersihkan! Silakan kembali ke halaman Edit dan Refresh (F5).';
});

Route::prefix('training-reguler')->group(function () {
    Route::get('/', function () {
        $totalTrainees = \App\Models\Trainee::where('status', 'aktif')->count();
        $totalPrograms = \App\Models\TrainingProgram::count();
        $draftReports = \App\Models\WeeklyReport::where('status', 'draft')->count();
        $recentReports = \App\Models\WeeklyReport::with('trainingProgram', 'trainer')->latest()->take(5)->get();
        return view('admin.training-dashboard', compact('totalTrainees', 'totalPrograms', 'draftReports', 'recentReports'));
    })->name('training-reguler.dashboard');

    Route::resource('training-programs', \App\Http\Controllers\Admin\TrainingProgramController::class)->names('training-reguler.training-programs');
    Route::resource('trainees', \App\Http\Controllers\Admin\TraineeController::class)->names('training-reguler.trainees');
    Route::post('trainees/import', [\App\Http\Controllers\Admin\TraineeController::class, 'import'])->name('training-reguler.trainees.import');
    Route::get('trainees/template', [\App\Http\Controllers\Admin\TraineeController::class, 'downloadTemplate'])->name('training-reguler.trainees.template');
    Route::resource('trainers', \App\Http\Controllers\Admin\TrainerController::class)->names('training-reguler.trainers');
    Route::post('weekly-reports/merge', [\App\Http\Controllers\Admin\WeeklyReportController::class, 'merge'])->name('training-reguler.weekly-reports.merge');
    Route::post('weekly-reports/{weekly_report}/unmerge', [\App\Http\Controllers\Admin\WeeklyReportController::class, 'unmerge'])->name('training-reguler.weekly-reports.unmerge');
    Route::resource('weekly-reports', \App\Http\Controllers\Admin\WeeklyReportController::class)->names('training-reguler.weekly-reports');
    Route::get('weekly-reports/{weekly_report}/export-pdf', [\App\Http\Controllers\Admin\WeeklyReportController::class, 'exportPdf'])->name('training-reguler.weekly-reports.export-pdf');
    Route::resource('moas', \App\Http\Controllers\Admin\MoaController::class)->names('training-reguler.moas');
    
    // Dummy Print Routes
    Route::get('print/attendance', function() {
        return view('admin.print_dummy.attendance');
    })->name('training-reguler.print.attendance');
    
    Route::get('print/tasks', function() {
        return view('admin.print_dummy.tasks');
    })->name('training-reguler.print.tasks.dummy');

    Route::post('daily-classes/{daily_class}/attendance', [\App\Http\Controllers\Admin\DailyClassController::class, 'updateAttendance'])->name('training-reguler.daily-classes.attendance.update');
    Route::post('daily-classes/{daily_class}/generate-qr', [\App\Http\Controllers\Admin\DailyClassController::class, 'generateQr'])->name('training-reguler.daily-classes.generate-qr');
    Route::post('daily-classes/{daily_class}/toggle-evaluation', [\App\Http\Controllers\Admin\DailyClassController::class, 'toggleEvaluation'])->name('training-reguler.daily-classes.toggle-evaluation');
    Route::get('daily-classes/{daily_class}/print-qr', [\App\Http\Controllers\Admin\DailyClassController::class, 'printQr'])->name('training-reguler.daily-classes.print-qr');
    Route::get('daily-classes/{daily_class}/print-tasks', [\App\Http\Controllers\Admin\DailyClassController::class, 'printTasks'])->name('training-reguler.daily-classes.print-tasks');
    Route::post('daily-classes/{daily_class}/tasks', [\App\Http\Controllers\Admin\DailyClassController::class, 'storeTask'])->name('training-reguler.daily-classes.tasks.store');
    Route::get('tasks/{task}', [\App\Http\Controllers\Admin\DailyClassController::class, 'showTask'])->name('training-reguler.tasks.show');
    Route::delete('tasks/{task}', [\App\Http\Controllers\Admin\DailyClassController::class, 'destroyTask'])->name('training-reguler.daily-classes.tasks.destroy');
    Route::resource('daily-classes', \App\Http\Controllers\Admin\DailyClassController::class)->names('training-reguler.daily-classes');
});



Route::prefix('training-non-reguler')->group(function () {
    Route::get('/', function () {
        $today = now()->format('Y-m-d');
        
        $totalTrainees = \App\Models\Trainee::count();
        $totalSesi = \App\Models\DailyClass::count();
        $totalPrograms = \App\Models\TrainingProgram::count();
        
        $recentSesi = \App\Models\DailyClass::with('trainingProgram')
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

    Route::resource('training-programs', \App\Http\Controllers\Admin\NonRegulerTrainingProgramController::class)->names('training-non-reguler.training-programs');
    Route::resource('trainees', \App\Http\Controllers\Admin\NonRegulerTraineeController::class)->names('training-non-reguler.trainees');
    Route::post('trainees/import', [\App\Http\Controllers\Admin\NonRegulerTraineeController::class, 'import'])->name('training-non-reguler.trainees.import');
    Route::get('trainees/template', [\App\Http\Controllers\Admin\NonRegulerTraineeController::class, 'downloadTemplate'])->name('training-non-reguler.trainees.template');
    Route::resource('trainers', \App\Http\Controllers\Admin\NonRegulerTrainerController::class)->names('training-non-reguler.trainers');
    Route::post('weekly-reports/merge', [\App\Http\Controllers\Admin\NonRegulerWeeklyReportController::class, 'merge'])->name('training-non-reguler.weekly-reports.merge');
    Route::post('weekly-reports/{weekly_report}/unmerge', [\App\Http\Controllers\Admin\NonRegulerWeeklyReportController::class, 'unmerge'])->name('training-non-reguler.weekly-reports.unmerge');
    Route::resource('weekly-reports', \App\Http\Controllers\Admin\NonRegulerWeeklyReportController::class)->names('training-non-reguler.weekly-reports');
    Route::get('weekly-reports/{weekly_report}/export-pdf', [\App\Http\Controllers\Admin\NonRegulerWeeklyReportController::class, 'exportPdf'])->name('training-non-reguler.weekly-reports.export-pdf');
    Route::resource('moas', \App\Http\Controllers\Admin\NonRegulerMoaController::class)->names('training-non-reguler.moas');
    
    // Dummy Print Routes
    Route::get('print/attendance', function() {
        return view('admin.print_dummy.attendance');
    })->name('training-non-reguler.print.attendance');
    
    Route::get('print/tasks', function() {
        return view('admin.print_dummy.tasks');
    })->name('training-non-reguler.print.tasks.dummy');

    Route::post('daily-classes/{daily_class}/import', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'importTrainees'])->name('training-non-reguler.daily-classes.import');
    Route::post('daily-classes/{daily_class}/attendance', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'updateAttendance'])->name('training-non-reguler.daily-classes.attendance.update');
    Route::post('daily-classes/{daily_class}/generate-qr', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'generateQr'])->name('training-non-reguler.daily-classes.generate-qr');
    Route::post('daily-classes/{daily_class}/generate-certificates', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'generateCertificates'])->name('training-non-reguler.daily-classes.generate-certificates');
    Route::post('daily-classes/{daily_class}/preview-certificate', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'previewCertificate'])->name('training-non-reguler.daily-classes.preview-certificate');
    Route::post('daily-classes/{daily_class}/toggle-evaluation', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'toggleEvaluation'])->name('training-non-reguler.daily-classes.toggle-evaluation');
      Route::delete('daily-classes/{daily_class}/clear-evaluation', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'clearEvaluation'])->name('training-non-reguler.daily-classes.clear-evaluation');
    Route::get('daily-classes/{daily_class}/print-qr', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'printQr'])->name('training-non-reguler.daily-classes.print-qr');
    Route::get('daily-classes/{daily_class}/print-tasks', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'printTasks'])->name('training-non-reguler.daily-classes.print-tasks');
    Route::get('daily-classes/{daily_class}/print-attendance', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'printAttendance'])->name('training-non-reguler.daily-classes.print-attendance');
    Route::get('daily-classes/{daily_class}/export-training-record', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'exportTrainingRecord'])->name('training-non-reguler.daily-classes.export-training-record');
    Route::post('daily-classes/{daily_class}/tasks', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'storeTask'])->name('training-non-reguler.daily-classes.tasks.store');
    Route::get('tasks/{task}', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'showTask'])->name('training-non-reguler.tasks.show');
    Route::delete('tasks/{task}', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'destroyTask'])->name('training-non-reguler.daily-classes.tasks.destroy');
    Route::delete('daily-classes/{daily_class}/trainees', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'removeAllTrainees'])->name('training-non-reguler.daily-classes.trainees.destroy-all');
    Route::delete('daily-classes/{daily_class}/trainees/{trainee}', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'removeTrainee'])->name('training-non-reguler.daily-classes.trainees.destroy');
    
Route::post('daily-classes/{daily_class}/notify', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'notifyTrainees'])->name('training-non-reguler.daily-classes.notify');
    
Route::get('history', [\App\Http\Controllers\Admin\NonRegulerDailyClassController::class, 'history'])->name('training-non-reguler.history.index');
    Route::resource('daily-classes', \App\Http\Controllers\Admin\NonRegulerDailyClassController::class)->names('training-non-reguler.daily-classes');
});


Route::middleware(['auth', 'verified'])->post('/admin/notifications/blast', function(Illuminate\Http\Request $request) {
    $request->validate([
        'title' => 'required|string',
        'body' => 'required|string',
    ]);
    
    $trainees = App\Models\Trainee::all();
    
    foreach ($trainees as $trainee) {
        $trainee->notify(new App\Notifications\WebPushBlastNotification($request->title, $request->body));
    }
    
    return back()->with('success', 'Notifikasi berhasil dikirim ke seluruh peserta!');
})->name('admin.notifications.blast');


