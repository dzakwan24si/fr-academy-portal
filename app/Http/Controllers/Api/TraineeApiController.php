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
        $trainee = $request->user()->load('trainingProgram'); // via Sanctum
        
        return response()->json([
            'status' => 'success',
            'data' => $trainee->ui_data
        ]);
    }

    public function dashboard(Request $request)
    {
        $trainee = $request->user();
        $today = now()->format('Y-m-d');
        
        // Cek absensi hari ini
        $todayAttendanceRecord = \App\Models\Attendance::where('trainee_id', $trainee->id)
            ->whereHas('dailyClass', function($q) use ($today) {
                $q->whereDate('date', $today);
            })->first();
            
        $todayAttendance = $todayAttendanceRecord && in_array(strtolower($todayAttendanceRecord->status), ['hadir', 'izin', 'sakit']);

        // Cek tugas pending
        $pendingTasks = \App\Models\Task::whereHas('dailyClass', function($q) use ($trainee) {
            $q->where('training_program_id', $trainee->training_program_id);
        })->whereDoesntHave('submissions', function($q) use ($trainee) {
            $q->where('trainee_id', $trainee->id)->where('status', 'sudah');
        })->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'todayAttendance' => $todayAttendance,
                'pendingTasks' => $pendingTasks,
                'trainee' => $trainee->ui_data
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
            $q->where('training_program_id', $trainee->training_program_id);
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
            'task_files.*' => 'required|file|max:5120' // max 5mb per file
        ], [
            'task_files.required' => 'File tugas wajib diunggah.',
            'task_files.*.max' => 'Ukuran file maksimal adalah 5MB.',
        ]);
        
        $paths = [];
        if ($request->hasFile('task_files')) {
            foreach ($request->file('task_files') as $file) {
                $paths[] = $file->store('task_submissions', 'public');
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
            'qr_token' => 'required|string'
        ]);

        $trainee = $request->user();

        // Trim token untuk menghindari error spasi berlebih dari input manual
        $token = trim($request->qr_token);

        // Cari kelas harian berdasarkan token QR (bisa token utuh 32 karakter dari scan, atau 8 karakter awal dari input manual)
        $dailyClass = \App\Models\DailyClass::where('qr_code_token', $token)
            ->orWhere('qr_code_token', 'like', $token . '%')
            ->first();

        if (!$dailyClass) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid atau sudah kadaluarsa.'
            ], 404);
        }

        // Pastikan trainee terdaftar pada program pelatihan kelas ini
        if ($dailyClass->training_program_id !== $trainee->training_program_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak terdaftar dalam kelas ini.'
            ], 403);
        }

        // Ambil data absensi, atau BUAT OTOMATIS jika belum ada di database
        $attendance = \App\Models\Attendance::firstOrCreate(
            ['daily_class_id' => $dailyClass->id, 'trainee_id' => $trainee->id],
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
        $pendingClasses = DailyClass::where('is_evaluation_open', true)
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
        
        $dailyClass = DailyClass::findOrFail($dailyClassId);

        $exists = TraineeEvaluation::where('trainee_id', $trainee->id)
            ->where('daily_class_id', $dailyClass->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Anda sudah mengisi evaluasi untuk sesi ini.'], 400);
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

        $evaluation = TraineeEvaluation::create([
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
}
