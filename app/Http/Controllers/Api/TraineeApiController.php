<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trainee;
use App\Models\Task;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TraineeApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required'
        ]);

        // Cari trainee yang aktif berdasarkan NIK
        $trainee = Trainee::with('trainingProgram')->where('nik', $request->nik)->where('status', 'aktif')->first();

        if ($trainee) {
            // Hapus token lama agar hanya bisa login di 1 perangkat (opsional)
            $trainee->tokens()->delete();
            
            // Buat token baru
            $token = $trainee->createToken('trainee-auth-token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'trainee' => $trainee->ui_data
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'NIK salah atau akun tidak aktif.'
        ], 401);
    }

        public function profile(Request $request)
    {
        $trainee = $request->user()->load('trainingProgram');
        
                $today = now()->format('Y-m-d');
        // Fetch the most relevant class: current, upcoming, or latest past
        $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
            ->whereHas('dailyClass', function($q) use ($today) {
                $q->where(function ($query) use ($today) {
                    $query->whereDate('date', '<=', $today)
                          ->whereDate('end_date', '>=', $today);
                })->orWhere(function ($query) use ($today) {
                    $query->whereDate('date', $today)
                          ->whereNull('end_date');
                });
            })->first();
            
                if (!$todayAttendanceRecord) {
            // If no active class today, get the next upcoming one
            $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
                ->join('daily_classes', 'attendances.daily_class_id', '=', 'daily_classes.id')
                ->whereDate('daily_classes.date', '>', $today)
                ->orderBy('daily_classes.date', 'asc')
                ->select('attendances.*')
                ->first();
        }
        
        if (!$todayAttendanceRecord) {
            // If no upcoming class, get the latest past one
            $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
                ->join('daily_classes', 'attendances.daily_class_id', '=', 'daily_classes.id')
                ->whereDate('daily_classes.end_date', '<', $today)
                ->orderBy('daily_classes.date', 'desc')
                ->first();
        }

        $data = $trainee->ui_data;
        if ($todayAttendanceRecord && $todayAttendanceRecord->dailyClass) {
            $data['program'] = $todayAttendanceRecord->dailyClass->trainingProgram->name ?? $data['program'];
            $data['materi'] = $todayAttendanceRecord->dailyClass->title;
        } else {
            $data['program'] = 'Peserta Pelatihan';
            $data['materi'] = 'Tidak ada jadwal hari ini';
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

        public function dashboard(Request $request)
    {
        $trainee = $request->user();
        $today = now()->format('Y-m-d');
        
        $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
            ->whereHas('dailyClass', function($q) use ($today) {
                $q->where(function ($query) use ($today) {
                    $query->whereDate('date', '<=', $today)
                          ->whereDate('end_date', '>=', $today);
                })->orWhere(function ($query) use ($today) {
                    $query->whereDate('date', $today)
                          ->whereNull('end_date');
                });
            })->first();
            
        if (!$todayAttendanceRecord) {
            $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
                ->join('daily_classes', 'attendances.daily_class_id', '=', 'daily_classes.id')
                ->whereDate('daily_classes.date', '>', $today)
                ->orderBy('daily_classes.date', 'asc')
                ->select('attendances.*')
                ->first();
        }
        
        if (!$todayAttendanceRecord) {
            $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
                ->join('daily_classes', 'attendances.daily_class_id', '=', 'daily_classes.id')
                ->whereDate('daily_classes.end_date', '<', $today)
                ->orderBy('daily_classes.date', 'desc')
                ->select('attendances.*')
                ->first();
        }
            
        $todayAttendance = $todayAttendanceRecord && in_array(strtolower($todayAttendanceRecord->status), ['hadir', 'izin', 'sakit']);

        // Cek tugas pending
        $pendingTasks = \App\Models\Task::whereHas('dailyClass', function($q) use ($trainee) {
            $q->whereHas('attendances', function($subQ) use ($trainee) {
                $subQ->where('trainee_id', $trainee->id)
                     ->whereNotIn('status', ['alpha', 'belum']);
            });
        })->whereDoesntHave('submissions', function($q) use ($trainee) {
            $q->where('trainee_id', $trainee->id)->where('status', 'sudah');
        })->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'todayAttendance' => $todayAttendance,
                'pendingTasks' => $pendingTasks,
                'pendingEvaluation' => $todayAttendanceRecord && $todayAttendanceRecord->dailyClass->is_evaluation_open && !\App\Models\TraineeEvaluation::where('daily_class_id', $todayAttendanceRecord->daily_class_id)->where('trainee_id', $trainee->id)->exists(),
                'trainee' => $trainee->ui_data,
                'todayClass' => $todayAttendanceRecord && $todayAttendanceRecord->dailyClass ? $todayAttendanceRecord->dailyClass->title : null,
                'classInfo' => $todayAttendanceRecord && $todayAttendanceRecord->dailyClass ? [
                    'id' => $todayAttendanceRecord->dailyClass->id,
                    'title' => $todayAttendanceRecord->dailyClass->title,
                    'date' => $todayAttendanceRecord->dailyClass->date,
                    'end_date' => $todayAttendanceRecord->dailyClass->end_date,
                    'start_time' => $todayAttendanceRecord->dailyClass->start_time,
                    'end_time' => $todayAttendanceRecord->dailyClass->end_time,
                    'room' => $todayAttendanceRecord->dailyClass->room,
                    'region' => $todayAttendanceRecord->dailyClass->region,
                    'mess_url' => $todayAttendanceRecord->dailyClass->mess_url,
                    'material_url' => $todayAttendanceRecord->dailyClass->material_url,
                    'certificate_link' => $todayAttendanceRecord->dailyClass->certificate_link,
                    'is_passed' => $todayAttendanceRecord->is_passed,
                    'trainer_name' => $todayAttendanceRecord->dailyClass->trainer_name,
                ] : null
            ]
        ]);
    }

    public function tasks(Request $request)
    {
        $trainee = $request->user();

        // Ambil daftar tugas untuk program training trainee ini
        $dbTasks = Task::with(['dailyClass', 'submissions' => function($q) use ($trainee) {
            $q->where('trainee_id', $trainee->id);
        }])->whereHas('dailyClass', function($q) use ($trainee) {
            $q->whereHas('attendances', function($subQ) use ($trainee) {
                $subQ->where('trainee_id', $trainee->id)
                     ->whereNotIn('status', ['alpha', 'belum']);
            });
        })->orderBy('deadline', 'desc')->get();
        
        $tasks = $dbTasks->map(function($task) {
            $sub = $task->submissions->first();
            return [
                'id' => $task->id,
                'title' => $task->title,
                'session' => $task->dailyClass->title,
                'deadline_formatted' => $task->deadline ? $task->deadline->format('d M Y H:i') : '-',
                'deadline_raw' => $task->deadline,
                'status' => $sub && $sub->status == 'sudah' ? 'Sudah' : 'Belum',
                'submission' => $sub ? [
                    'submitted_at' => $sub->submitted_at ? $sub->submitted_at->format('d M Y H:i') : null,
                    'file_paths' => is_array($sub->file_path) ? array_map(fn($f) => Storage::url($f), $sub->file_path) : (is_string($sub->file_path) ? [Storage::url($sub->file_path)] : [])
                ] : null
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }

    public function submitTask(Request $request, Task $task)
    {
        $trainee = $request->user();
        
        $request->validate([
            'task_files' => 'required|array',
            'task_files.*' => 'required|file|max:2048' // max 2mb per file
        ], [
            'task_files.required' => 'File tugas wajib diunggah.',
            'task_files.*.max' => 'Ukuran file maksimal adalah 2MB, silakan kompres file Anda.',
        ]);
        
        $paths = [];
        if ($request->hasFile('task_files')) {
            foreach ($request->file('task_files') as $file) {
                // Check if file is an image
                if (str_starts_with($file->getMimeType(), 'image/')) {
                    $imagePath = $file->getRealPath();
                    $mime = $file->getMimeType();
                    
                    // Create image resource based on type
                    $image = null;
                    if ($mime == 'image/jpeg' || $mime == 'image/jpg') {
                        $image = @imagecreatefromjpeg($imagePath);
                    } elseif ($mime == 'image/png') {
                        $image = @imagecreatefrompng($imagePath);
                    }
                    
                    if ($image) {
                        // Create target directory if not exists
                        $targetDir = storage_path('app/public/task_submissions');
                        if (!file_exists($targetDir)) {
                            mkdir($targetDir, 0755, true);
                        }
                        
                        // Save as highly compressed JPEG
                        $filename = 'task_submissions/' . \Illuminate\Support\Str::random(40) . '.jpg';
                        imagejpeg($image, storage_path('app/public/' . $filename), 50); // 50% quality for high compression
                        imagedestroy($image);
                        
                        $paths[] = $filename;
                        continue;
                    }
                }
                
                // Fallback for non-images or if GD fails
                $originalName = $file->getClientOriginalName();
                $safeName = time() . '_' . rand(1000, 9999) . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $originalName);
                $paths[] = $file->storeAs('task_submissions', $safeName, 'public');
            }
        }
        
        TaskSubmission::updateOrCreate(
            ['task_id' => $task->id, 'trainee_id' => $trainee->id],
            [
                'file_path' => $paths,
                'status' => 'sudah',
                'submitted_at' => now()
            ]
        );
        
        return response()->json([
            'status' => 'success',
            'message' => 'Tugas berhasil diunggah.'
        ]);
    }

    public function submitAttendance(Request $request)
    {
        $request->validate([
            'qr_token' => 'nullable|string'
        ]);

        $trainee = $request->user();

        // Trim token untuk menghindari error spasi berlebih dari input manual
        $rawToken = trim($request->qr_token ?? '');
        
        if (empty($rawToken) || strtolower($rawToken) === 'manual') {
            $today = now()->format('Y-m-d');
            
            // Cari kelas aktif hari ini seperti di dashboard
            $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
                ->whereHas('dailyClass', function($q) use ($today) {
                    $q->where(function ($query) use ($today) {
                        $query->whereDate('date', '<=', $today)
                              ->whereDate('end_date', '>=', $today);
                    })->orWhere(function ($query) use ($today) {
                        $query->whereDate('date', $today)
                              ->whereNull('end_date');
                    });
                })->first();
                
            if (!$todayAttendanceRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada jadwal kelas aktif hari ini untuk Anda.'
                ], 404);
            }
            
            $dailyClass = $todayAttendanceRecord->dailyClass;
            $attendanceDate = $today;
        } else {
            $parts = explode('|', $rawToken);
            $token = $parts[0];
            $attendanceDate = isset($parts[1]) ? $parts[1] : date('Y-m-d');

            // Cari kelas harian berdasarkan token QR
            $dailyClass = \App\Models\DailyClass::where('qr_code_token', $token)
                ->orWhere('qr_code_token', 'like', $token . '%')
                ->first();

            if (!$dailyClass) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code tidak valid atau sudah kadaluarsa.'
                ], 404);
            }
        }

        // Cek apakah trainee memiliki record absensi di kelas ini (menandakan dia terdaftar di kelas)
        $isRegistered = \App\Models\Attendance::where('daily_class_id', $dailyClass->id)
            ->where('trainee_id', $trainee->id)
            ->exists();

        if (!$isRegistered) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak terdaftar dalam kelas ini.'
            ], 403);
        }

        // Ambil data absensi, atau BUAT OTOMATIS jika belum ada di database
        $attendance = \App\Models\Attendance::firstOrCreate(
            ['daily_class_id' => $dailyClass->id, 'trainee_id' => $trainee->id, 'attendance_date' => $attendanceDate],
            ['status' => 'belum']
        );

        if ($attendance->status === 'hadir') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi untuk kelas ini sebelumnya.'
            ], 400);
        }

        // Update status absensi
        $attendance->update([
            'status' => 'hadir',
            'scanned_at' => now()
        ]);

        // Dispatch job untuk update/create Training Record di background
        \App\Jobs\UpdateTrainingRecordJob::dispatch($trainee->id, $dailyClass->id, $attendanceDate);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil! Anda tercatat hadir di kelas ' . $dailyClass->title
        ]);
    }
    public function attendances(Request $request) 
    { 
        $trainee = $request->user(); 
        
        $history = \App\Models\Attendance::with('dailyClass')
            ->where('trainee_id', $trainee->id)
            ->orderBy('updated_at', 'desc')
            ->get()->map(function($att) {
                return [
                    'id' => $att->id,
                    'session' => $att->dailyClass ? $att->dailyClass->title : 'Materi',
                    'date' => $att->dailyClass ? \Carbon\Carbon::parse($att->dailyClass->date)->format('d M Y') : '-',
                    'status' => ucfirst($att->status)
                ];
            });
        return response()->json([
            'status' => 'success',
            'data' => $history
        ]);
    }
    public function deleteTaskSubmission(Request $request, \App\Models\Task $task)
    {
        $trainee = $request->user();
        
        $submission = \App\Models\TaskSubmission::where('task_id', $task->id)
            ->where('trainee_id', $trainee->id)
            ->first();
        if ($submission) {
            // (Opsional) Hapus file fisik dari storage jika ada
            if (is_array($submission->file_path)) {
                foreach ($submission->file_path as $path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                }
            } else if (is_string($submission->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($submission->file_path);
            }
            // Hapus data pengumpulan dari database
            $submission->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Pengumpulan tugas berhasil dibatalkan.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Tugas belum dikumpulkan.'
        ], 404);
    }

    public function pendingEvaluations()
    {
        $trainee = auth()->user();
        $today = date('Y-m-d');

        // Cari materi/kelas yang evaluasinya sudah dibuka oleh Admin
        // dan trainee hadir (status != alpha/belum),
        // tapi trainee belum mengisi evaluasi.
        $pendingClasses = \App\Models\DailyClass::where('is_evaluation_open', true)
            ->whereHas('attendances', function ($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id)
                      ->whereNotIn('status', ['alpha', 'belum']);
            })
            ->whereDoesntHave('traineeEvaluations', function ($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id);
            })
            ->get(['id', 'title', 'trainer_name', 'date']);

        return response()->json($pendingClasses);
    }

    public function submitEvaluation(Request $request, $dailyClassId)
    {
        $trainee = auth()->user();
        
        $dailyClass = \App\Models\DailyClass::findOrFail($dailyClassId);

        $exists = \App\Models\TraineeEvaluation::where('trainee_id', $trainee->id)
            ->where('daily_class_id', $dailyClass->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah mengisi evaluasi untuk sesi ini.'], 400);
        }

        // Verifikasi kehadiran: Pastikan Trainee benar-benar HADIR di setidaknya 1 hari sesi kelas ini
        $hasAttended = \App\Models\Attendance::where('trainee_id', $trainee->id)
            ->where('daily_class_id', $dailyClass->id)
            ->whereNotIn('status', ['alpha', 'belum'])
            ->exists();

        if (!$hasAttended) {
            return response()->json(['message' => 'Anda tidak dapat mengisi evaluasi karena status Anda tidak hadir di kelas ini.'], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array|min:12', // 12 questions
            'answers.*' => 'required|in:A,B,C,D,E',
            'explanation_de' => 'nullable|string',
            'kesan_pesan' => 'required|string',
        ]);

        // Validate explanation if D or E is present
        $needsExplanation = in_array('D', $validated['answers']) || in_array('E', $validated['answers']);
        if ($needsExplanation && empty($validated['explanation_de'])) {
            return response()->json(['message' => 'Penjelasan wajib diisi karena Anda memberikan nilai D atau E.'], 400);
        }

        $evaluation = \App\Models\TraineeEvaluation::create([
            'trainee_id' => $trainee->id,
            'daily_class_id' => $dailyClass->id,
            'answers' => $validated['answers'],
            'explanation_de' => $validated['explanation_de'],
            'kesan_pesan' => $validated['kesan_pesan'],
        ]);

        return response()->json([
            'message' => 'Evaluasi berhasil dikirim.',
            'data' => $evaluation
        ], 201);
    }

    public function downloadCertificate(Request $request, $dailyClassId)
    {
        $user = $request->user();
        $dailyClass = \App\Models\DailyClass::findOrFail($dailyClassId);
        
        $attendance = $dailyClass->attendances()->where('trainee_id', $user->id)->first();
        
        if (!$attendance || !$attendance->is_passed) {
            return response()->json(['message' => 'Sertifikat tidak tersedia.'], 403);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.non-reguler.daily_classes.certificate', [
            'traineeName' => $user->name,
            'className' => $dailyClass->title,
            'certDescription' => $dailyClass->cert_description,
            'certSignerName' => $dailyClass->cert_signer_name,
            'certSignerRole' => $dailyClass->cert_signer_role,
            'certDate' => $dailyClass->cert_date ? \Carbon\Carbon::parse($dailyClass->cert_date)->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y')
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Sertifikat_' . str_replace(' ', '_', $user->name) . '.pdf"');
    }
}










