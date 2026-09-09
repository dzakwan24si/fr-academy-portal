<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use App\Models\TrainingProgram;
use App\Models\Attendance;
use App\Models\QuizScore;
use App\Models\ReportParticipant;
use App\Models\ReportPhoto;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WeeklyReportController extends Controller
{
    public function index()
    {
        // For simplicity, just get all weekly reports. We could eager load trainer and trainingProgram
        $reports = WeeklyReport::with(['trainer', 'trainingProgram'])->latest()->paginate(10);
        return view('admin.weekly_reports.index', compact('reports'));
    }

    public function create()
    {
        // Load programs with their trainees
        $programs = TrainingProgram::with('trainees')->get();
        // Since this is for trainer, we should ideally get the logged in trainer.
        // For now, let's just get all trainers to allow selection, or assume the first trainer.
        // Assuming the logged in user is associated with a trainer record, or we just pass trainers for dropdown if needed.
        // The prompt says: "Buatkan fitur input laporan mingguan (weekly report) untuk trainer".
        // Let's pass all trainers for simplicity if they need to choose, or they can just use their own.
        // The prompt says "dropdown pilih training_program". No mention of trainer dropdown.
        // But trainer_id is required in weekly_reports table.
        // We will pass the currently logged-in user's trainer ID or a dummy if none.
        $trainer = Trainer::where('user_id', auth()->id())->first();

        return view('admin.weekly_reports.create', compact('programs', 'trainer'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'training_purpose' => 'required|string',
            'quote' => 'nullable|string',
            'trainees' => 'required|array', // This will contain trainee data
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            // 1. Create Weekly Report
            $report = WeeklyReport::create([
                'training_program_id' => $request->training_program_id,
                'trainer_id' => $request->trainer_id ?? 1, // Fallback to 1 if no trainer_id provided
                'week_start_date' => $request->week_start_date,
                'week_end_date' => $request->week_end_date,
                'training_purpose' => $request->training_purpose,
                'quote' => $request->quote,
                'status' => 'draft',
            ]);

            // 2. Process Trainees Data
            foreach ($request->trainees as $traineeId => $data) {
                // Attendance
                Attendance::create([
                    'weekly_report_id' => $report->id,
                    'trainee_id' => $traineeId,
                    'is_present' => isset($data['is_present']) ? true : false,
                ]);

                // Quiz Score
                $practiceScore = $data['practice_score'] ?? 0;
                $inClassScore = $data['in_class_score'] ?? 0;
                $averageScore = ($practiceScore + $inClassScore) / 2;

                QuizScore::create([
                    'weekly_report_id' => $report->id,
                    'trainee_id' => $traineeId,
                    'practice_score' => $practiceScore,
                    'in_class_score' => $inClassScore,
                    'average_score' => $averageScore,
                    'input_method' => 'manual',
                ]);

                // Report Participant
                ReportParticipant::create([
                    'weekly_report_id' => $report->id,
                    'trainee_id' => $traineeId,
                    'participation_type' => $data['participation_type'] ?? 'pasif',
                ]);
            }

            // 3. Process Photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('report_photos', 'public');
                    ReportPhoto::create([
                        'weekly_report_id' => $report->id,
                        'photo_path' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.weekly-reports.index')->with('success', 'Laporan mingguan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(WeeklyReport $weeklyReport)
    {
        $weeklyReport->load(['trainingProgram', 'trainer', 'attendances.trainee', 'quizScores.trainee', 'reportParticipants.trainee', 'reportPhotos']);
        return view('admin.weekly_reports.show', compact('weeklyReport'));
    }
}
