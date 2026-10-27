<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DailyClass;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Trainee;
use App\Models\TrainingProgram;
use App\Services\EvaluationExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DailyClassController extends Controller
{
    public function index()
    {
        $programs = TrainingProgram::all();
        $dailyClasses = DailyClass::with('trainingProgram', 'attendances', 'tasks.submissions', 'traineeEvaluations')->get();

        return view('admin.daily_classes.index', compact('programs', 'dailyClasses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'trainer_name' => 'required|string|max:255',
        ]);

        $dailyClass = DailyClass::create($validated);

        // When a daily class is created, auto-create attendance records for all active trainees in that program with status 'alpha'
        $trainees = Trainee::where('training_program_id', $validated['training_program_id'])
            ->where('status', 'aktif')
            ->get();

        foreach ($trainees as $trainee) {
            Attendance::create([
                'daily_class_id' => $dailyClass->id,
                'trainee_id' => $trainee->id,
                'status' => 'belum',
            ]);
        }

        return redirect()->route('training-reguler.daily-classes.index')->with('success', 'Materi Sesi / Kelas Harian berhasil ditambahkan.');
    }

    public function update(Request $request, DailyClass $dailyClass)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'trainer_name' => 'required|string|max:255',
        ]);

        $dailyClass->update($validated);

        return redirect()->route('training-reguler.daily-classes.index')->with('success', 'Materi Sesi / Kelas Harian berhasil diperbarui.');
    }

    public function show(DailyClass $dailyClass)
    {
        $dailyClass->load(['trainingProgram', 'tasks', 'attendances.trainee', 'tasks.submissions.trainee']);
        $trainees = Trainee::where('training_program_id', $dailyClass->training_program_id)->where('status', 'aktif')->get();

        return view('admin.daily_classes.show', compact('dailyClass', 'trainees'));
    }

    public function updateAttendance(Request $request, DailyClass $dailyClass)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'status' => 'required|in:hadir,sakit,izin,alpha',
        ]);

        Attendance::updateOrCreate(
            ['daily_class_id' => $dailyClass->id, 'trainee_id' => $request->trainee_id],
            ['status' => $request->status]
        );

        return back()->with('success', 'Status absensi berhasil diperbarui.');
    }

    public function generateQr(DailyClass $dailyClass)
    {
        if (! $dailyClass->qr_code_token) {
            $dailyClass->update([
                'qr_code_token' => Str::random(32),
            ]);
        }

        return back()->with(['success' => 'QR Code tersedia.', 'auto_open_qr' => true]);
    }

    public function printQr(DailyClass $dailyClass)
    {
        abort_if(! $dailyClass->qr_code_token, 404, 'QR Code belum di-generate.');

        return view('admin.daily_classes.print_qr', compact('dailyClass'));
    }

    public function printTasks(DailyClass $dailyClass)
    {
        $dailyClass->load(['tasks', 'trainingProgram']);
        $trainees = Trainee::where('training_program_id', $dailyClass->training_program_id)
            ->where('status', 'aktif')
            ->get();
        // Load submissions for these tasks
        $submissions = TaskSubmission::whereIn('task_id', $dailyClass->tasks->pluck('id'))
            ->whereIn('trainee_id', $trainees->pluck('id'))
            ->get();

        return view('admin.daily_classes.print_tasks', compact('dailyClass', 'trainees', 'submissions'));
    }

    public function storeTask(Request $request, DailyClass $dailyClass)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'required|date',
        ]);

        $dailyClass->tasks()->create([
            'title' => $validated['title'],
            'deadline' => $validated['deadline'],
        ]);

        return back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function showTask(Task $task)
    {
        $task->load(['submissions.trainee', 'dailyClass.trainingProgram']);

        return view('admin.tasks.show', compact('task'));
    }

    public function destroyTask(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function toggleEvaluation(DailyClass $dailyClass)
    {
        $dailyClass->update([
            'is_evaluation_open' => ! $dailyClass->is_evaluation_open,
        ]);

        $message = $dailyClass->is_evaluation_open
            ? 'Form evaluasi berhasil dikirim dan dibuka untuk peserta!'
            : 'Akses evaluasi untuk sesi ini berhasil ditutup.';

        return back()->with('success', $message);
    }

    public function exportEvaluations(DailyClass $dailyClass)
    {
        return EvaluationExportService::export($dailyClass);
    }

    public function toggleCompleted(DailyClass $dailyClass)
    {
        $newStatus = ! $dailyClass->is_completed;
        $dailyClass->update([
            'is_completed' => $newStatus,
            'completed_at' => $newStatus ? now() : null,
        ]);

        $message = $newStatus
            ? 'Status pelatihan untuk materi "' . $dailyClass->title . '" berhasil diubah menjadi: Pelatihan Selesai!'
            : 'Status pelatihan untuk materi "' . $dailyClass->title . '" berhasil diubah kembali menjadi aktif/berjalan.';

        return back()->with('success', $message);
    }
}
