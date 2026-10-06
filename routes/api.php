<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TraineeApiController;

use App\Http\Controllers\Api\V1\TrainingRecordExportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Trainee API Routes
Route::post('/trainee/login', [TraineeApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // V1 External Integration API Routes (e.g. Peta SDM System)
    Route::get('/v1/training-records/export', [TrainingRecordExportController::class, 'export']);

    Route::get('/trainee/dashboard', [TraineeApiController::class, 'dashboard']);
    Route::get('/trainee/profile', [TraineeApiController::class, 'profile']);
    Route::get('/trainee/tasks', [TraineeApiController::class, 'tasks']);
    Route::post('/trainee/tasks/{task}/submit', [TraineeApiController::class, 'submitTask']);
    Route::delete('/trainee/tasks/{task}/submit', [TraineeApiController::class, 'deleteTaskSubmission']);
    Route::post('/trainee/submit-attendance', [TraineeApiController::class, 'submitAttendance']);
    Route::get('/trainee/attendances', [TraineeApiController::class, 'attendances']);
    Route::get('trainee/evaluations/pending', [App\Http\Controllers\Api\TraineeApiController::class, 'pendingEvaluations']);
    Route::post('trainee/evaluations/{dailyClassId}', [App\Http\Controllers\Api\TraineeApiController::class, 'submitEvaluation']);
    Route::get('trainee/certificate/{dailyClassId}', [App\Http\Controllers\Api\TraineeApiController::class, 'downloadCertificate']);
});

Route::middleware('auth:sanctum')->post('/push-subscribe', [App\Http\Controllers\Api\PushNotificationController::class, 'subscribe']);
