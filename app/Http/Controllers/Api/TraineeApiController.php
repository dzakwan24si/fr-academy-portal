<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateTrainingRecordJob;
use App\Models\Attendance;
use App\Models\DailyClass;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Trainee;
use App\Models\TraineeEvaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TraineeApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required',
        ]);

        // Cari trainee yang aktif berdasarkan NIK
        $trainee = Trainee::with('trainingProgram')->where('nik', $request->nik)->where('status', 'aktif')->first();

        if (! $trainee) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK salah atau akun tidak aktif.',
            ], 401);
        }

        // Hanya peserta yang terdaftar di kelas pelatihan yang boleh login
        if (! $trainee->attendances()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK Anda belum terdaftar dalam kelas program pelatihan.',
            ], 403);
        }

        // Token lama tidak dihapus agar sesi tetap stabil dan tidak ter-logout tiba-tiba
        $token = $trainee->createToken('trainee-auth-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'token' => $token,
            'trainee' => $trainee->ui_data,
        ]);
    }

    private function getRelevantAttendanceRecord($trainee, string $today)
    {
        // 1. Cek kelas yang aktif (belum ditandai selesai) tempat trainee terdaftar
        $record = Attendance::where('trainee_id', $trainee->id)
            ->whereHas('dailyClass', function ($q) {
                $q->whereNull('is_completed')->orWhere('is_completed', false);
            })
            ->latest('id')
            ->first();

        // 2. Jika tidak ada jadwal kelas aktif, ambil riwayat sesi terakhir yang diikuti trainee
        if (! $record) {
            $record = Attendance::where('trainee_id', $trainee->id)
                ->whereHas('dailyClass')
                ->latest('updated_at')
                ->first();
        }

        return $record;
    }

    public function profile(Request $request)
    {
        $trainee = $request->user()->load('trainingProgram');

        // Jika trainee tidak terdaftar di kelas manapun, revoke token sesi ini dan tolak akses dengan 401 (trigger auto logout)
        if (! $trainee->attendances()->exists()) {
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah tidak terdaftar dalam kelas program pelatihan apapun.',
            ], 401);
        }

        $today = now()->format('Y-m-d');
        $todayAttendanceRecord = $this->getRelevantAttendanceRecord($trainee, $today);

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
            'data' => $data,
        ]);
    }

    public function dashboard(Request $request)
    {
        $trainee = $request->user();

        // Jika trainee tidak terdaftar di kelas manapun, revoke token sesi ini dan tolak akses dengan 401 (trigger auto logout)
        if (! $trainee->attendances()->exists()) {
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah tidak terdaftar dalam kelas program pelatihan apapun.',
            ], 401);
        }

        $today = now()->format('Y-m-d');

        $todayAttendanceRecord = $this->getRelevantAttendanceRecord($trainee, $today);

        $isClassCompleted = (bool) ($todayAttendanceRecord && $todayAttendanceRecord->dailyClass && $todayAttendanceRecord->dailyClass->is_completed);

        $classStartDate = $todayAttendanceRecord && $todayAttendanceRecord->dailyClass ? \Carbon\Carbon::parse($todayAttendanceRecord->dailyClass->date)->format('Y-m-d') : null;
        $classEndDate = $todayAttendanceRecord && $todayAttendanceRecord->dailyClass && $todayAttendanceRecord->dailyClass->end_date ? \Carbon\Carbon::parse($todayAttendanceRecord->dailyClass->end_date)->format('Y-m-d') : $classStartDate;

        $isClassRunningToday = $classStartDate && ($today >= $classStartDate && $today <= $classEndDate);

        $hasActiveClassToday = $todayAttendanceRecord && $todayAttendanceRecord->dailyClass && ! $isClassCompleted && $isClassRunningToday;

        $todayAttendance = $hasActiveClassToday && in_array(strtolower($todayAttendanceRecord->status), ['hadir', 'izin', 'sakit']) && ($todayAttendanceRecord->attendance_date === $today);

        // Cek tugas pending (hanya untuk kelas di mana trainee terdaftar)
        $pendingTasks = Task::whereHas('dailyClass', function ($q) use ($trainee) {
            $q->whereHas('attendances', function ($attQ) use ($trainee) {
                $attQ->where('trainee_id', $trainee->id);
            });
        })->whereDoesntHave('submissions', function ($q) use ($trainee) {
            $q->where('trainee_id', $trainee->id)->where('status', 'sudah');
        })->count();

        // Cek evaluasi pending (hanya untuk kelas di mana trainee terdaftar)
        $pendingEvaluationsCount = DailyClass::where('is_evaluation_open', true)
            ->whereHas('attendances', function ($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id);
            })
            ->whereDoesntHave('traineeEvaluations', function ($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id);
            })->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'todayAttendance' => $todayAttendance,
                'pendingTasks' => $pendingTasks,
                'pendingEvaluations' => $pendingEvaluationsCount,
                'pendingEvaluation' => $pendingEvaluationsCount > 0,
                'trainee' => $trainee->ui_data,
                'todayClass' => $hasActiveClassToday ? $todayAttendanceRecord->dailyClass->title : null,
                'classInfo' => $todayAttendanceRecord && $todayAttendanceRecord->dailyClass ? [
                    'id' => $todayAttendanceRecord->dailyClass->id,
                    'title' => $todayAttendanceRecord->dailyClass->title,
                    'date' => $todayAttendanceRecord->dailyClass->date ? Carbon::parse($todayAttendanceRecord->dailyClass->date)->translatedFormat('d M Y') : 'Hari Ini',
                    'end_date' => $todayAttendanceRecord->dailyClass->end_date ? Carbon::parse($todayAttendanceRecord->dailyClass->end_date)->translatedFormat('d M Y') : null,
                    'start_time' => $todayAttendanceRecord->dailyClass->start_time ? substr($todayAttendanceRecord->dailyClass->start_time, 0, 5) : '08:00',
                    'end_time' => $todayAttendanceRecord->dailyClass->end_time ? substr($todayAttendanceRecord->dailyClass->end_time, 0, 5) : '17:00',
                    'room' => $todayAttendanceRecord->dailyClass->room,
                    'region' => $todayAttendanceRecord->dailyClass->region,
                    'mess_url' => $todayAttendanceRecord->dailyClass->mess_url,
                    'material_url' => $todayAttendanceRecord->dailyClass->material_url,
                    'certificate_link' => $todayAttendanceRecord->dailyClass->certificate_link,
                    'is_passed' => $todayAttendanceRecord->is_passed,
                    'trainer_name' => $todayAttendanceRecord->dailyClass->trainer_name,
                    'is_completed' => (bool) ($todayAttendanceRecord->dailyClass->is_completed ?? false),
                    'completed_at' => $todayAttendanceRecord->dailyClass->completed_at ? $todayAttendanceRecord->dailyClass->completed_at->toIso8601String() : null,
                ] : null,
            ],
        ]);
    }

    public function tasks(Request $request)
    {
        $trainee = $request->user();

        // Jika trainee tidak terdaftar di kelas manapun, revoke token sesi ini dan tolak akses dengan 401
        if (! $trainee->attendances()->exists()) {
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah tidak terdaftar dalam kelas program pelatihan apapun.',
            ], 401);
        }

        // Cek apakah trainee sudah melakukan absensi (hadir)
        $hasAttended = Attendance::where('trainee_id', $trainee->id)
            ->where('status', 'hadir')
            ->exists();

        if (! $hasAttended) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'Silakan lakukan absensi terlebih dahulu untuk melihat dan mengerjakan tugas.',
            ]);
        }

        // Ambil daftar tugas untuk program training trainee ini
        $dbTasks = Task::with(['dailyClass', 'submissions' => function ($q) use ($trainee) {
            $q->where('trainee_id', $trainee->id);
        }])->whereHas('dailyClass', function ($q) use ($trainee) {
            $q->whereHas('attendances', function ($attQ) use ($trainee) {
                $attQ->where('trainee_id', $trainee->id);
            });
        })->orderBy('deadline', 'desc')->get();

        $tasks = $dbTasks->map(function ($task) {
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
                    'file_paths' => is_array($sub->file_path) ? array_map(fn ($f) => Storage::url($f), $sub->file_path) : (is_string($sub->file_path) ? [Storage::url($sub->file_path)] : []),
                ] : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    public function submitTask(Request $request, Task $task)
    {
        $trainee = $request->user();

        // Cek apakah trainee sudah melakukan absensi hadir di kelas
        $hasAttended = Attendance::where('trainee_id', $trainee->id)
            ->where('daily_class_id', $task->daily_class_id)
            ->where('status', 'hadir')
            ->exists();

        if (! $hasAttended) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda harus melakukan absensi terlebih dahulu sebelum dapat mengumpulkan tugas.',
            ], 403);
        }

        // Periksa jika ada file yang ditolak oleh konfigurasi server (PHP upload_max_filesize)
        if ($request->hasFile('task_files') || isset($_FILES['task_files'])) {
            $rawFiles = $request->allFiles()['task_files'] ?? [];
            if (! is_array($rawFiles)) {
                $rawFiles = [$rawFiles];
            }
            foreach ($rawFiles as $f) {
                if ($f instanceof \Illuminate\Http\UploadedFile && ! $f->isValid()) {
                    if ($f->getError() === UPLOAD_ERR_INI_SIZE || $f->getError() === UPLOAD_ERR_FORM_SIZE) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Ukuran file ' . $f->getClientOriginalName() . ' melebihi kapasitas upload server PHP (upload_max_filesize). Maksimal file adalah 10MB.',
                        ], 422);
                    }
                }
            }
        }

        $request->validate([
            'task_files' => 'required|array',
            'task_files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,ppt,pptx|max:10240', // max 10MB per file
        ], [
            'task_files.required' => 'File tugas wajib diunggah.',
            'task_files.*.mimes' => 'Format file tugas harus berupa foto (JPG, PNG), PDF, Word (DOC, DOCX), atau PowerPoint (PPT, PPTX).',
            'task_files.*.max' => 'Ukuran file maksimal adalah 10MB per file.',
        ]);

        $paths = [];
        if ($request->hasFile('task_files')) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'ppt', 'pptx'];
            foreach ($request->file('task_files') as $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                if (! in_array($extension, $allowedExtensions)) {
                    continue;
                }

                $safeName = time().'_'.Str::random(20).'.'.$extension;
                $paths[] = $file->storeAs('task_submissions', $safeName, 'public');
            }
        }

        TaskSubmission::updateOrCreate(
            ['task_id' => $task->id, 'trainee_id' => $trainee->id],
            [
                'file_path' => $paths,
                'status' => 'sudah',
                'submitted_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Tugas berhasil diunggah.',
        ]);
    }

    public function submitAttendance(Request $request)
    {
        $request->validate([
            'qr_token' => 'nullable|string',
        ]);

        $trainee = $request->user();

        // Trim token untuk menghindari error spasi berlebih dari input manual
        $rawToken = trim($request->qr_token ?? '');

        if (empty($rawToken) || strtolower($rawToken) === 'manual') {
            $today = now()->format('Y-m-d');

            // Cari kelas aktif tempat trainee terdaftar (yang belum selesai dan sedang berlangsung hari ini)
            $todayAttendanceRecord = Attendance::where('trainee_id', $trainee->id)
                ->whereHas('dailyClass', function ($q) use ($today) {
                    $q->where(function ($sq) {
                        $sq->whereNull('is_completed')->orWhere('is_completed', false);
                    })
                    ->whereDate('date', '<=', $today)
                    ->where(function ($sq) use ($today) {
                        $sq->whereNull('end_date')->whereDate('date', '>=', $today)
                           ->orWhereDate('end_date', '>=', $today);
                    });
                })
                ->latest('id')
                ->first();

            if (! $todayAttendanceRecord) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak ada jadwal kelas aktif hari ini untuk Anda.',
                ], 404);
            }

            $dailyClass = $todayAttendanceRecord->dailyClass;
            $attendanceDate = $today;
        } else {
            $parts = explode('|', $rawToken);
            $token = $parts[0];
            $attendanceDate = isset($parts[1]) ? $parts[1] : date('Y-m-d');

            // Cari kelas harian berdasarkan token QR
            $dailyClass = DailyClass::where('qr_code_token', $token)
                ->orWhere('qr_code_token', 'like', $token.'%')
                ->first();

            if (! $dailyClass) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'QR Code atau token absensi tidak valid.',
                ], 404);
            }
        }

        if ($dailyClass->is_completed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kelas pelatihan ini sudah selesai dan dipindahkan ke riwayat.',
            ], 400);
        }

        // Validasi tanggal jadwal pelatihan (tidak boleh absen sebelum kelas dimulai)
        $todayStr = now()->format('Y-m-d');
        $classStartDate = \Carbon\Carbon::parse($dailyClass->date)->format('Y-m-d');
        $classEndDate = $dailyClass->end_date ? \Carbon\Carbon::parse($dailyClass->end_date)->format('Y-m-d') : $classStartDate;

        if ($todayStr < $classStartDate) {
            $formattedStart = \Carbon\Carbon::parse($dailyClass->date)->translatedFormat('d F Y');
            return response()->json([
                'status' => 'error',
                'message' => "Sesi absensi untuk kelas ini belum dibuka. Pelatihan baru akan dimulai pada tanggal {$formattedStart}.",
            ], 400);
        }

        if ($todayStr > $classEndDate) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi absensi untuk kelas pelatihan ini telah berakhir.',
            ], 400);
        }

        // Validasi jam pelaksanaan kelas (tanpa toleransi: hanya aktif dari start_time s/d end_time)
        $currentTimeStr = now()->format('H:i:s');
        if (! empty($dailyClass->start_time)) {
            $startTimeStr = strlen($dailyClass->start_time) === 5 ? $dailyClass->start_time.':00' : $dailyClass->start_time;
            if ($currentTimeStr < $startTimeStr) {
                $formattedStartTime = substr($dailyClass->start_time, 0, 5);
                return response()->json([
                    'status' => 'error',
                    'message' => "Sesi absensi belum dibuka. Kelas dimulai pukul {$formattedStartTime} WIB.",
                ], 400);
            }
        }

        if (! empty($dailyClass->end_time)) {
            $endTimeStr = strlen($dailyClass->end_time) === 5 ? $dailyClass->end_time.':00' : $dailyClass->end_time;
            if ($currentTimeStr > $endTimeStr) {
                $formattedEndTime = substr($dailyClass->end_time, 0, 5);
                return response()->json([
                    'status' => 'error',
                    'message' => "Sesi absensi mandiri telah ditutup karena waktu kelas telah berakhir (pukul {$formattedEndTime} WIB). Silakan hubungi Admin/Trainer untuk absensi manual.",
                ], 400);
            }
        }

        // Cek apakah trainee memiliki record absensi di kelas ini
        $isRegistered = Attendance::where('daily_class_id', $dailyClass->id)
            ->where('trainee_id', $trainee->id)
            ->exists();

        if (! $isRegistered) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak terdaftar dalam kelas ini.',
            ], 403);
        }

        // Ambil data absensi untuk hari ini ($todayStr)
        $attendance = Attendance::where('daily_class_id', $dailyClass->id)
            ->where('trainee_id', $trainee->id)
            ->where('attendance_date', $todayStr)
            ->first();

        if (! $attendance) {
            $attendance = Attendance::firstOrCreate(
                ['daily_class_id' => $dailyClass->id, 'trainee_id' => $trainee->id, 'attendance_date' => $todayStr],
                ['status' => 'belum']
            );
        }

        if ($attendance->status === 'hadir') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi untuk sesi hari ini.',
            ], 400);
        }

        // Update status absensi
        $attendance->update([
            'status' => 'hadir',
            'scanned_at' => now(),
        ]);

        // Dispatch job untuk update/create Training Record langsung secara sync
        UpdateTrainingRecordJob::dispatchSync($trainee->id, $dailyClass->id, $todayStr);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil! Anda tercatat hadir di kelas '.$dailyClass->title,
        ]);
    }

    public function attendances(Request $request)
    {
        $trainee = $request->user();

        // Jika trainee tidak terdaftar di kelas manapun, revoke token sesi ini dan tolak akses dengan 401
        if (! $trainee->attendances()->exists()) {
            if ($request->user() && $request->user()->currentAccessToken()) {
                $request->user()->currentAccessToken()->delete();
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah tidak terdaftar dalam kelas program pelatihan apapun.',
            ], 401);
        }

        // Bersihkan data absensi prematur yang dibuat sebelum tanggal kelas dimulai
        Attendance::where('trainee_id', $trainee->id)
            ->whereHas('dailyClass', function ($q) {
                $q->whereColumn('attendances.attendance_date', '<', 'daily_classes.date');
            })
            ->delete();

        $history = Attendance::with('dailyClass')
            ->where('trainee_id', $trainee->id)
            ->whereHas('dailyClass', function ($q) {
                // Hanya tampilkan riwayat kelas yang tanggal mulainya sudah dimulai atau lewat
                $q->whereDate('date', '<=', now()->toDateString());
            })
            ->orderBy('attendance_date', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get()->map(function ($att) {
                return [
                    'id' => $att->id,
                    'session' => $att->dailyClass ? $att->dailyClass->title : 'Materi',
                    'date' => $att->attendance_date ? Carbon::parse($att->attendance_date)->format('d M Y') : ($att->dailyClass ? Carbon::parse($att->dailyClass->date)->format('d M Y') : '-'),
                    'status' => ucfirst($att->status),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $history,
        ]);
    }

    public function deleteTaskSubmission(Request $request, Task $task)
    {
        $trainee = $request->user();

        $submission = TaskSubmission::where('task_id', $task->id)
            ->where('trainee_id', $trainee->id)
            ->first();
        if ($submission) {
            // (Opsional) Hapus file fisik dari storage jika ada
            if (is_array($submission->file_path)) {
                foreach ($submission->file_path as $path) {
                    Storage::disk('public')->delete($path);
                }
            } elseif (is_string($submission->file_path)) {
                Storage::disk('public')->delete($submission->file_path);
            }
            // Hapus data pengumpulan dari database
            $submission->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Pengumpulan tugas berhasil dibatalkan.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Tugas belum dikumpulkan.',
        ], 404);
    }

    public function pendingEvaluations(Request $request)
    {
        $trainee = $request->user();
        if (! $trainee) {
            $trainee = auth()->user();
        }

        // Cari materi/kelas yang evaluasinya sudah dibuka oleh Admin/Trainer
        // dan trainee terdaftar di kelas tersebut,
        // tapi trainee belum mengisi evaluasi.
        $pendingClasses = DailyClass::where('is_evaluation_open', true)
            ->whereHas('attendances', function ($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id);
            })
            ->whereDoesntHave('traineeEvaluations', function ($query) use ($trainee) {
                $query->where('trainee_id', $trainee->id);
            })
            ->get(['id', 'title', 'trainer_name', 'date']);

        return response()->json($pendingClasses);
    }

    public function submitEvaluation(Request $request, $dailyClassId)
    {
        $trainee = $request->user();
        if (! $trainee) {
            $trainee = auth()->user();
        }

        $dailyClass = DailyClass::findOrFail($dailyClassId);

        if (! $dailyClass->is_evaluation_open) {
            return response()->json(['message' => 'Sesi evaluasi untuk kelas ini belum dibuka atau sudah ditutup.'], 403);
        }

        $exists = TraineeEvaluation::where('trainee_id', $trainee->id)
            ->where('daily_class_id', $dailyClass->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah mengisi evaluasi untuk sesi ini.'], 400);
        }

        // Verifikasi bahwa trainee terdaftar di kelas ini
        $isRegistered = Attendance::where('trainee_id', $trainee->id)
            ->where('daily_class_id', $dailyClass->id)
            ->exists();

        if (! $isRegistered) {
            return response()->json(['message' => 'Anda tidak terdaftar di sesi pelatihan ini.'], 403);
        }

        // Pastikan record absensi tersedia
        Attendance::firstOrCreate([
            'daily_class_id' => $dailyClass->id,
            'trainee_id' => $trainee->id,
        ]);

        $validated = $request->validate([
            'answers' => 'required|array|min:12', // 12 questions
            'answers.*' => 'required|in:A,B,C,D,E',
            'explanation_de' => 'nullable|string',
            'kesan_pesan' => 'required|string',
        ]);

        $explanationDe = $validated['explanation_de'] ?? $request->input('explanation_de');

        // Validate explanation if D or E is present
        $needsExplanation = in_array('D', $validated['answers']) || in_array('E', $validated['answers']);
        if ($needsExplanation && empty($explanationDe)) {
            return response()->json(['message' => 'Penjelasan wajib diisi karena Anda memberikan nilai D atau E.'], 400);
        }

        $evaluation = TraineeEvaluation::create([
            'trainee_id' => $trainee->id,
            'daily_class_id' => $dailyClass->id,
            'answers' => $validated['answers'],
            'explanation_de' => $explanationDe,
            'kesan_pesan' => $validated['kesan_pesan'],
        ]);

        return response()->json([
            'message' => 'Evaluasi berhasil dikirim.',
            'data' => $evaluation,
        ], 201);
    }

    public function downloadCertificate(Request $request, $dailyClassId)
    {
        $user = $request->user();
        $dailyClass = DailyClass::findOrFail($dailyClassId);

        $attendance = $dailyClass->attendances()->where('trainee_id', $user->id)->first();

        if (! $attendance || ! $attendance->is_passed) {
            return response()->json(['message' => 'Sertifikat tidak tersedia.'], 403);
        }

        $pdf = Pdf::loadView('admin.non-reguler.daily_classes.certificate', [
            'traineeName' => $user->name,
            'className' => $dailyClass->title,
            'certDescription' => $dailyClass->cert_description,
            'certSignerName' => $dailyClass->cert_signer_name,
            'certSignerRole' => $dailyClass->cert_signer_role,
            'certDate' => $dailyClass->cert_date ? Carbon::parse($dailyClass->cert_date)->translatedFormat('d F Y') : Carbon::now()->translatedFormat('d F Y'),
        ])->setPaper('a4', 'landscape');

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Sertifikat_'.str_replace(' ', '_', $user->name).'.pdf"');
    }
}
