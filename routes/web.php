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


