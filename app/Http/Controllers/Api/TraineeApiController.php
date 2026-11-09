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

        if ($trainee) {
            // Token lama tidak dihapus agar sesi tetap stabil dan tidak ter-logout tiba-tiba

            // Buat token baru
            $token = $trainee->createToken('trainee-auth-token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'trainee' => $trainee->ui_data,
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'NIK salah atau akun tidak aktif.',
        ], 401);
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

        // 2. Jika belum ada di attendance, cek apakah ada kelas aktif di program pelatihan peserta
        if (! $record && $trainee->training_program_id) {
            $class = DailyClass::where('training_program_id', $trainee->training_program_id)
                ->where(function ($sq) {
                    $sq->whereNull('is_completed')->orWhere('is_completed', false);
                })
                ->latest('id')
                ->first();

            if ($class) {
                // Objek in-memory untuk membaca info kelas tanpa memasukkan trainee ke database attendances
                $record = new Attendance([
                    'daily_class_id' => $class->id,
                    'trainee_id' => $trainee->id,
                    'status' => 'belum',
                    'attendance_date' => $today,
                ]);
                $record->setRelation('dailyClass', $class);
            }
        }

        // 3. Jika tidak ada jadwal kelas aktif, ambil riwayat sesi terakhir yang diikuti trainee
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
        $today = now()->format('Y-m-d');

        $todayAttendanceRecord = $this->getRelevantAttendanceRecord($trainee, $today);

        $isClassCompleted = (bool) ($todayAttendanceRecord && $todayAttendanceRecord->dailyClass && $todayAttendanceRecord->dailyClass->is_completed);

        $hasActiveClassToday = $todayAttendanceRecord && $todayAttendanceRecord->dailyClass && ! $isClassCompleted;

        $todayAttendance = $todayAttendanceRecord && ! $isClassCompleted && in_array(strtolower($todayAttendanceRecord->status), ['hadir', 'izin', 'sakit']);

        // Cek tugas pending
        $pendingTasks = Task::whereHas('dailyClass', function ($q) use ($trainee) {
            $q->where(function ($subQ) use ($trainee) {
                $subQ->whereHas('attendances', function ($attQ) use ($trainee) {
                    $attQ->where('trainee_id', $trainee->id);
                })->orWhere('training_program_id', $trainee->training_program_id);
            });
        })->whereDoesntHave('submissions', function ($q) use ($trainee) {
            $q->where('trainee_id', $trainee->id)->where('status', 'sudah');
        })->count();

        $pendingEvaluationsCount = DailyClass::where('is_evaluation_open', true)
            ->where(function ($q) use ($trainee) {
                $q->whereHas('attendances', function ($query) use ($trainee) {
                    $query->where('trainee_id', $trainee->id);
                })->orWhere('training_program_id', $trainee->training_program_id);
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
            $q->where(function ($subQ) use ($trainee) {
                $subQ->whereHas('attendances', function ($attQ) use ($trainee) {
                    $attQ->where('trainee_id', $trainee->id);
                })->orWhere('training_program_id', $trainee->training_program_id);
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

            // Cari kelas aktif tempat trainee terdaftar (yang belum selesai)
            $todayAttendanceRecord = Attendance::where('trainee_id', $trainee->id)
                ->whereHas('dailyClass', function ($q) {
                    $q->whereNull('is_completed')->orWhere('is_completed', false);
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
                    'message' => 'QR Code tidak valid atau sudah kadaluarsa.',
                ], 404);
            }
        }

        if ($dailyClass->is_completed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kelas pelatihan ini sudah selesai dan dipindahkan ke riwayat.',
            ], 400);
        }

        // Cek apakah trainee memiliki record absensi di kelas ini atau terdaftar di program kelas ini
        $isRegistered = Attendance::where('daily_class_id', $dailyClass->id)
            ->where('trainee_id', $trainee->id)
            ->exists() || ($dailyClass->training_program_id && $dailyClass->training_program_id == $trainee->training_program_id);

        if (! $isRegistered) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak terdaftar dalam kelas ini.',
            ], 403);
        }

        // Ambil data absensi, atau BUAT OTOMATIS jika belum ada di database
        $attendance = Attendance::firstOrCreate(
            ['daily_class_id' => $dailyClass->id, 'trainee_id' => $trainee->id, 'attendance_date' => $attendanceDate],
            ['status' => 'belum']
        );

        if ($attendance->status === 'hadir') {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah melakukan absensi untuk kelas ini sebelumnya.',
            ], 400);
        }

        // Update status absensi
        $attendance->update([
            'status' => 'hadir',
            'scanned_at' => now(),
        ]);

        // Dispatch job untuk update/create Training Record langsung secara sync
        UpdateTrainingRecordJob::dispatchSync($trainee->id, $dailyClass->id, $attendanceDate);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil! Anda tercatat hadir di kelas '.$dailyClass->title,
        ]);
    }

    public function attendances(Request $request)
    {
        $trainee = $request->user();

        $history = Attendance::with('dailyClass')
            ->where('trainee_id', $trainee->id)
            ->orderBy('updated_at', 'desc')
            ->get()->map(function ($att) {
                return [
                    'id' => $att->id,
                    'session' => $att->dailyClass ? $att->dailyClass->title : 'Materi',
                    'date' => $att->dailyClass ? Carbon::parse($att->dailyClass->date)->format('d M Y') : '-',
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
        // dan trainee terdaftar di kelas atau program pelatihan tersebut,
        // tapi trainee belum mengisi evaluasi.
        $pendingClasses = DailyClass::where('is_evaluation_open', true)
            ->where(function ($q) use ($trainee) {
                $q->whereHas('attendances', function ($query) use ($trainee) {
                    $query->where('trainee_id', $trainee->id);
                })->orWhere('training_program_id', $trainee->training_program_id);
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

        // Verifikasi bahwa trainee terdaftar di kelas atau program pelatihan ini
        $isRegistered = Attendance::where('trainee_id', $trainee->id)
            ->where('daily_class_id', $dailyClass->id)
            ->exists() || ($dailyClass->training_program_id && $dailyClass->training_program_id == $trainee->training_program_id);

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
