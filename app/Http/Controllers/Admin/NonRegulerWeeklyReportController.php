<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReport;
use App\Models\TrainingProgram;
use App\Models\Trainee;
use App\Models\Trainer;
use App\Models\Resignation;
use App\Models\SessionEvaluation;
use App\Models\TrainingSession;
use App\Models\SessionParticipant;
use App\Models\OjtPlacement;
use App\Models\QuizScore;
use App\Models\Ranking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class NonRegulerWeeklyReportController extends Controller
{
    public function index(Request $request)
    {
        $query = WeeklyReport::with(['trainer', 'trainingProgram'])->latest();
        if ($request->filled('month_year')) {
            $my = explode('-', $request->month_year);
            if (count($my) == 2) {
                $query->whereYear('week_start_date', $my[0])->whereMonth('week_start_date', $my[1]);
            }
        }

        if ($request->filled('week')) {
            $week = $request->week;
            if ($week == '1') {
                $query->whereDay('week_start_date', '>=', 1)->whereDay('week_start_date', '<=', 7);
            } elseif ($week == '2') {
                $query->whereDay('week_start_date', '>=', 8)->whereDay('week_start_date', '<=', 14);
            } elseif ($week == '3') {
                $query->whereDay('week_start_date', '>=', 15)->whereDay('week_start_date', '<=', 21);
            } elseif ($week == '4') {
                $query->whereDay('week_start_date', '>=', 22);
            }
        }
        $reports = $query->paginate(10);
        $total = WeeklyReport::count();
        $draft = WeeklyReport::where('status', 'draft')->count();
        $submitted = WeeklyReport::where('status', '!=', 'draft')->count();
        
        $months = [];
        $distinctDates = WeeklyReport::select('week_start_date')->distinct()->orderBy('week_start_date', 'desc')->get();
        foreach ($distinctDates as $date) {
            if ($date->week_start_date) {
                $parsed = \Carbon\Carbon::parse($date->week_start_date);
                $key = $parsed->format('Y-m');
                if (!isset($months[$key])) {
                    $months[$key] = $parsed->translatedFormat('F Y');
                }
            }
        }
        $programs = TrainingProgram::with('trainees')->get();
        $trainers = Trainer::all();
        
        return view('admin.non-reguler.weekly_reports.index', compact('reports', 'programs', 'trainers', 'total', 'draft', 'submitted', 'months'));
    }

    public function create()
    {
        return redirect()->route('training-non-reguler.weekly-reports.index');
    }

    public function getTrainees(Request $request)
    {
        $programId = $request->get('program_id');
        $trainees = Trainee::where('training_program_id', $programId)
            ->where('status', '!=', 'resign')
            ->get(['id', 'name', 'origin', 'university']);
        return response()->json($trainees);
    }

    public function show(WeeklyReport $weeklyReport)
    {
        $weeklyReport->load([
            'trainingProgram',
            'trainer',
            'trainingSessions.sessionParticipants.trainee',
            'trainingSessions.evaluations',
            'trainingSessions.ojtPlacements.trainee',
            'quizScores.trainee',
            'rankings.trainee',
            'resignations.trainee'
        ]);
        
        $dailyClasses = \App\Models\DailyClass::with(['attendances.trainee', 'tasks.submissions.trainee'])
            ->where('training_program_id', $weeklyReport->training_program_id)
            ->whereBetween('date', [$weeklyReport->week_start_date, $weeklyReport->week_end_date])
            ->get();
            
        return view('admin.non-reguler.weekly_reports.show', compact('weeklyReport', 'dailyClasses'));
    }

    public function destroy(WeeklyReport $weeklyReport)
    {
        $weeklyReport->delete();
        return redirect()->route('training-non-reguler.weekly-reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    public function exportPdf(WeeklyReport $weeklyReport)
    {
        $weeklyReport->load([
            'trainingProgram.trainees',
            'trainer',
            'trainingSessions.sessionParticipants.trainee',
            'trainingSessions.evaluations',
            'trainingSessions.ojtPlacements.trainee',
            'quizScores.trainee',
            'rankings.trainee',
            'resignations.trainee'
        ]);
        
        $dailyClasses = \App\Models\DailyClass::with(['attendances.trainee', 'tasks.submissions.trainee'])
            ->where('training_program_id', $weeklyReport->training_program_id)
            ->whereBetween('date', [$weeklyReport->week_start_date, $weeklyReport->week_end_date])
            ->get();
        
        $pdf = Pdf::loadview('admin.non-reguler.weekly_reports.pdf', [
            'weeklyReport' => $weeklyReport,
            'dailyClasses' => $dailyClasses
        ])->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Mingguan.pdf');
    }

    public function exportMultiplePdf(Request $request)
    {
        $ids = $request->input('report_ids', []);
        $reports = WeeklyReport::whereIn('id', $ids)->with([
            'trainingProgram.trainees',
            'trainer',
            'trainingSessions.sessionParticipants.trainee',
            'trainingSessions.evaluations',
            'trainingSessions.ojtPlacements.trainee',
            'quizScores.trainee',
            'rankings.trainee',
            'resignations.trainee'
        ])->get();

        if ($reports->isEmpty()) {
            return back()->with('error', 'Tidak ada laporan yang dipilih.');
        }

        foreach($reports as $report) {
            $report->dailyClasses = \App\Models\DailyClass::with(['attendances.trainee', 'tasks.submissions.trainee'])
                ->where('training_program_id', $report->training_program_id)
                ->whereBetween('date', [$report->week_start_date, $report->week_end_date])
                ->get();
        }

        $pdf = Pdf::loadview('admin.non-reguler.weekly_reports.export_multiple', ['reports' => $reports])
            ->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan_Mingguan_Multiple.pdf');
    }

    public function store(Request $request)
    {
        $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'week_start_date'     => 'required|date',
            'week_end_date'       => 'required|date',
            'theme'               => 'nullable|string',
        ]);

        // Karena Trainer Utama diisi manual per-sesi, kita set default trainer_id agar tidak error di database
        $defaultTrainerId = \App\Models\Trainer::first()->id ?? 1;

        $report = WeeklyReport::create([
            'training_program_id' => $request->training_program_id,
            'trainer_id'          => $defaultTrainerId,
            'week_start_date'     => $request->week_start_date,
            'week_end_date'       => $request->week_end_date,
            'theme'               => $request->theme ?? 'reguler',
            'status'              => 'draft'
        ]);

        // Karena form wizard "Buat Laporan Baru" mengirimkan SEMUA data (sesi, foto, dsb) dalam satu waktu,
        // kita langsung lempar request-nya ke fungsi update untuk memproses semuanya dan redirect ke index.
        return $this->update($request, $report);
    }

    public function edit(WeeklyReport $weeklyReport)
    {
        $weeklyReport->load([
            'trainingProgram.trainees', 
            'trainer',
            'trainingSessions.sessionParticipants.trainee',
            'trainingSessions.evaluations',
            'trainingSessions.ojtPlacements.trainee',
            'quizScores.trainee',
            'rankings.trainee',
            'resignations.trainee'
        ]);
        
        $programs = TrainingProgram::with('trainees')->get();
        $trainers = Trainer::all();
        
        $resigneesData = \App\Models\Trainee::with('resignation')
            ->where('status', 'like', '%esign%')
            ->get()
            ->filter(function($t) use ($weeklyReport) {
                return $t->training_program_id == $weeklyReport->training_program_id;
            })
            ->map(function($t) {
                $r = $t->resignation;
                return [
                    'id' => $r ? $r->id : null,
                    'trainee_id' => $t->id,
                    'birth_place' => $r ? $r->birth_place : '',
                    'birth_date' => $r ? $r->birth_date : '',
                    'reason' => $r ? $r->reason : '',
                    'avg_inclass_score' => $r ? $r->avg_inclass_score : '',
                    'inclass_rank' => $r ? $r->inclass_rank : '',
                    'avg_practice_score' => $r ? $r->avg_practice_score : '',
                    'practice_rank' => $r ? $r->practice_rank : '',
                    'photoPreview' => ($r && $r->photo_path) ? '/storage/' . $r->photo_path : null,
                    'traineeObj' => $t
                ];
            })->values()->toArray();

        $uniqueSubjects = $weeklyReport->quizScores->pluck('materi_name')->unique()->values();
        $initialQuizScores = [];

        $initialRankings = [];
        $initialRemedialNotes = [];
        foreach($weeklyReport->rankings as $rank) {
            $initialRankings[$rank->trainee_id] = [
                'id' => $rank->id,
                'total_average' => $rank->total_average,
                'rank' => $rank->rank,
                'rank_group' => $rank->rank_group
            ];
            if ($rank->remedial_note) {
                $initialRemedialNotes[$rank->trainee_id] = $rank->remedial_note;
            }
        }

        $existingPhotos = [
            1 => $weeklyReport->cover_photo_1 ? '/storage/' . $weeklyReport->cover_photo_1 : null,
            2 => $weeklyReport->cover_photo_2 ? '/storage/' . $weeklyReport->cover_photo_2 : null,
            3 => $weeklyReport->cover_photo_3 ? '/storage/' . $weeklyReport->cover_photo_3 : null,
            4 => $weeklyReport->cover_photo_4 ? '/storage/' . $weeklyReport->cover_photo_4 : null,
            5 => $weeklyReport->cover_photo_5 ? '/storage/' . $weeklyReport->cover_photo_5 : null,
            6 => $weeklyReport->cover_photo_6 ? '/storage/' . $weeklyReport->cover_photo_6 : null,
        ];
        
        $stripPreviews = [
            1 => $weeklyReport->strip_photo_1 ? '/storage/' . $weeklyReport->strip_photo_1 : null,
            2 => $weeklyReport->strip_photo_2 ? '/storage/' . $weeklyReport->strip_photo_2 : null,
            3 => $weeklyReport->strip_photo_3 ? '/storage/' . $weeklyReport->strip_photo_3 : null,
            4 => $weeklyReport->strip_photo_4 ? '/storage/' . $weeklyReport->strip_photo_4 : null,
            5 => $weeklyReport->strip_photo_5 ? '/storage/' . $weeklyReport->strip_photo_5 : null,
            6 => $weeklyReport->strip_photo_6 ? '/storage/' . $weeklyReport->strip_photo_6 : null,
            7 => $weeklyReport->strip_photo_7 ? '/storage/' . $weeklyReport->strip_photo_7 : null,
            8 => $weeklyReport->strip_photo_8 ? '/storage/' . $weeklyReport->strip_photo_8 : null,
        ];

        $allTrainees = Trainee::where('training_program_id', $weeklyReport->training_program_id)->get();

        return view('admin.non-reguler.weekly_reports.edit', compact(
            'weeklyReport', 'programs', 'trainers', 'resigneesData', 'uniqueSubjects', 
            'initialQuizScores', 'initialRankings', 'initialRemedialNotes', 
            'existingPhotos', 'stripPreviews', 'allTrainees'
        ));
    }

    public function update(Request $request, WeeklyReport $weeklyReport)
    {
        \Illuminate\Support\Facades\Log::info('Update WeeklyReport Request:', $request->all());
        DB::beginTransaction();
        try {
            // Update Report Meta
            $weeklyReport->update([
                'training_program_id' => $request->training_program_id ?? $weeklyReport->training_program_id,
                'week_start_date' => $request->week_start_date ?? $weeklyReport->week_start_date,
                'week_end_date' => $request->week_end_date ?? $weeklyReport->week_end_date,
                'theme' => $request->theme ?? $weeklyReport->theme,
                'training_purpose' => $request->training_purpose ?? $weeklyReport->training_purpose,
                'quote' => $request->quote ?? $weeklyReport->quote,
                'quiz_notes' => $request->quiz_notes ?? $weeklyReport->quiz_notes,
                'status' => 'submitted', 
            ]);

            // Handle Cover Photos
            for ($i = 1; $i <= 6; $i++) {
                if ($request->hasFile("cover_photo_$i")) {
                    $file = $request->file("cover_photo_$i");
                    $path = $file->store('report_covers', 'public');
                    $col = "cover_photo_$i";
                    $weeklyReport->$col = $path;
                }
            }
            if ($request->hasFile('cover_photo_main')) {
                $file = $request->file('cover_photo_main');
                $path = $file->store('report_covers', 'public');
                $weeklyReport->cover_photo_main = $path;
            }
            if ($request->input('cover_photo_main_deleted') == '1' || $request->input('cover_main_deleted') == '1') {
                $weeklyReport->cover_photo_main = null;
            }

            // Handle Strip Photos
            $currentStrips = is_array($weeklyReport->strip_photos) ? $weeklyReport->strip_photos : [];
            
            if ($request->input('delete_all_strip_photos') == '1' || $request->input('skip_page_2') == '1') {
                $currentStrips = [];
            }

            if ($request->hasFile('page_2_strip_photos')) {
                foreach ($request->file('page_2_strip_photos') as $file) {
                    $path = $file->store('report_strips', 'public');
                    $currentStrips[] = $path;
                }
            }
            $weeklyReport->strip_photos = $currentStrips;
            $weeklyReport->save();

            // Update Sessions
            $requestSessionIds = collect($request->sessions ?? [])
                ->pluck('id')
                ->filter(function($id) { return is_numeric($id); })
                ->toArray();

            TrainingSession::where('weekly_report_id', $weeklyReport->id)
                ->whereNotIn('id', $requestSessionIds)
                ->delete();

            foreach ($request->sessions ?? [] as $sData) {
                if (empty($sData['title']) && empty($sData['date_start']) && empty($sData['date_end']) && empty($sData['location'])) continue;

                $title = !empty($sData['title']) ? $sData['title'] : 'Sesi Pelatihan';

                // Automatically detect session type
                $titleLower = strtolower($title);
                $inferredType = 'inclass';
                
                if (isset($sData['ojt']) && is_array($sData['ojt']) && count($sData['ojt']) > 0) {
                    if (str_contains($titleLower, 'supervisi') || str_contains($titleLower, 'zoom')) {
                        $inferredType = 'supervisi_ojt';
                    } else {
                        $inferredType = 'ojt';
                    }
                } else {
                    if (str_contains($titleLower, 'orientasi')) {
                        $inferredType = 'orientasi';
                    } elseif (str_contains($titleLower, 'moa')) {
                        $inferredType = 'moa';
                    }
                }

                $sessionId = (isset($sData['id']) && is_numeric($sData['id'])) ? $sData['id'] : null;

                $session = TrainingSession::updateOrCreate(
                    ['id' => $sessionId, 'weekly_report_id' => $weeklyReport->id],
                    [
                        'session_type' => $inferredType,
                        'title' => $title,
                        'banner_right_text' => $sData['banner_right_text'] ?? null,
                        'date_start' => $sData['date_start'] ?? null,
                        'date_end' => $sData['date_end'] ?? null,
                        'trainer_name' => $sData['trainer_name'] ?? null,
                        'location' => $sData['location'] ?? null,
                        'training_purpose' => $sData['training_purpose'] ?? null,
                        'training_result' => $sData['training_result'] ?? null,
                    ]
                );

                // Process Trainer Photo
                if (isset($sData['trainer_photo']) && $sData['trainer_photo'] instanceof \Illuminate\Http\UploadedFile) {
                    $path = $sData['trainer_photo']->store('session_photos', 'public');
                    $session->update(['trainer_photo_path' => $path]);
                } elseif (isset($sData['delete_trainer_photo']) && $sData['delete_trainer_photo'] == '1') {
                    $session->update(['trainer_photo_path' => null]);
                }
                
                // Process Banner Photos
                if (isset($sData['photos']) && is_array($sData['photos'])) {
                    // explicitly handle delete logic for banners
                    if (!empty($sData['delete_photo_0']) && $sData['delete_photo_0'] == '1') {
                        \App\Models\SessionPhoto::where('training_session_id', $session->id)->where('photo_type', 'banner_left')->delete();
                    }
                    if (!empty($sData['delete_photo_1']) && $sData['delete_photo_1'] == '1') {
                        \App\Models\SessionPhoto::where('training_session_id', $session->id)->where('photo_type', 'banner_right')->delete();
                    }

                    foreach ($sData['photos'] as $idx => $photo) {
                        if ($photo instanceof \Illuminate\Http\UploadedFile) {
                            $type = ($idx == 0) ? 'banner_left' : 'banner_right';
                            // delete existing if replacing
                            \App\Models\SessionPhoto::where('training_session_id', $session->id)->where('photo_type', $type)->delete();
                            
                            $path = $photo->store('session_photos', 'public');
                            \App\Models\SessionPhoto::create([
                                'training_session_id' => $session->id,
                                'photo_path' => $path,
                                'photo_type' => $type
                            ]);
                        }
                    }
                }

                // Process Grid (Dokumentasi) Photos
                if (isset($sData['deleted_grid_photos']) && is_array($sData['deleted_grid_photos'])) {
                    \App\Models\SessionPhoto::whereIn('id', $sData['deleted_grid_photos'])->delete();
                }

                if (isset($sData['dokumentasi_types']) && is_array($sData['dokumentasi_types'])) {
                    foreach ($sData['dokumentasi_types'] as $photoId => $type) {
                        if ($photoId !== 'undefined' && $photoId !== '') {
                            $photo = \App\Models\SessionPhoto::find($photoId);
                            if ($photo) {
                                $photo->update(['photo_type' => $type]);
                            }
                        }
                    }
                }

                if (isset($sData['dokumentasi']) && is_array($sData['dokumentasi'])) {
                    foreach (array_values($sData['dokumentasi']) as $photo) {
                        if ($photo instanceof \Illuminate\Http\UploadedFile) {
                            $path = $photo->store('session_photos', 'public');
                            \App\Models\SessionPhoto::create([
                                'training_session_id' => $session->id,
                                'photo_path' => $path,
                                'photo_type' => 'training'
                            ]);
                        }
                    }
                }
                
                // Evaluations
                SessionEvaluation::where('training_session_id', $session->id)->delete();
                $categories = ['baik_sekali' => 'Baik Sekali', 'baik' => 'Baik', 'cukup_baik' => 'Cukup Baik', 'kurang_baik' => 'Kurang Baik', 'tidak_baik' => 'Tidak Baik'];
                foreach ($categories as $key => $label) {
                    if (isset($sData['evaluations'][$key])) {
                        $m = (float)str_replace(',', '.', $sData['evaluations'][$key]['materi'] ?? 0);
                        $t = (float)str_replace(',', '.', $sData['evaluations'][$key]['trainer'] ?? 0);
                        $f = (float)str_replace(',', '.', $sData['evaluations'][$key]['fasilitas'] ?? 0);
                        
                        if ($m > 0 || $t > 0 || $f > 0) {
                            SessionEvaluation::create([
                                'training_session_id' => $session->id,
                                'category' => $label,
                                'materi_percentage' => $m,
                                'trainer_percentage' => $t,
                                'fasilitas_percentage' => $f,
                            ]);
                        }
                    }
                }

                // Participants
                SessionParticipant::where('training_session_id', $session->id)->delete();
                if (isset($sData['participants']) && is_array($sData['participants'])) {
                    foreach ($sData['participants'] as $tId => $pData) {
                        if (isset($pData['is_participating']) && $pData['is_participating'] == '1') {
                            if (isset($pData['participation_type']) && $pData['participation_type'] !== 'absen') {
                                SessionParticipant::create([
                                    'training_session_id' => $session->id,
                                    'trainee_id' => $tId,
                                    'participation_type' => $pData['participation_type'],
                                ]);
                            }
                        }
                    }
                }

                // OJT
                OjtPlacement::where('training_session_id', $session->id)->delete();
                if (isset($sData['ojt']) && is_array($sData['ojt'])) {
                    foreach ($sData['ojt'] as $tId => $oData) {
                        if (!empty($oData['location_code']) || !empty($oData['region'])) {
                            OjtPlacement::create([
                                'training_session_id' => $session->id,
                                'trainee_id' => $tId,
                                'location_code' => $oData['location_code'] ?? null,
                                'region' => $oData['region'] ?? null,
                            ]);
                        }
                    }
                }
            }

            // Update Quiz
            QuizScore::where('weekly_report_id', $weeklyReport->id)->delete();
            $subjects = $request->subjects ?? [];
            foreach ($request->quiz_scores ?? [] as $tId => $qData) {
                foreach ($qData as $sIdx => $score) {
                    if (isset($subjects[$sIdx]) && $score !== null && $score !== '') {
                        QuizScore::create([
                            'weekly_report_id' => $weeklyReport->id,
                            'trainee_id' => $tId,
                            'materi_name' => $subjects[$sIdx],
                            'score' => $score
                        ]);
                    }
                }
            }

            // Update Rankings
            Ranking::where('weekly_report_id', $weeklyReport->id)->delete();
            foreach ($request->rankings ?? [] as $tId => $rData) {
                if (isset($rData['rank'])) {
                    Ranking::create([
                        'weekly_report_id' => $weeklyReport->id,
                        'trainee_id' => $tId,
                        'average_score' => $rData['average_score'] ?? 0,
                        'rank' => $rData['rank'],
                        'rank_group' => $rData['rank_group'] ?? 'unranked',
                        'remedial_note' => $rData['remedial_note'] ?? null,
                    ]);
                }
            }

            // Update Resignations
            // Delete missing
            $requestResignIds = collect($request->resignees ?? [])->pluck('id')->filter(function($id) { return is_numeric($id) && $id < 1000000; })->toArray(); // 1000000 to filter JS date.now()
            
            $deletedResignations = Resignation::whereHas('trainee', function($q) use ($weeklyReport) {
                    $q->where('training_program_id', $weeklyReport->training_program_id);
                })
                ->whereNotIn('id', $requestResignIds)
                ->get();
                
            foreach ($deletedResignations as $delRes) {
                $delTrainee = Trainee::find($delRes->trainee_id);
                if ($delTrainee) {
                    $delTrainee->update(['status' => 'on_training']);
                }
                $delRes->delete();
            }

            foreach ($request->resignees ?? [] as $idx => $resData) {
                if (!empty($resData['trainee_id'])) {
                    $trainee = Trainee::find($resData['trainee_id']);
                    if ($trainee) {
                        $trainee->update(['status' => 'resign']);
                    }

                    $resig = null;
                    if (!empty($resData['id']) && is_numeric($resData['id']) && $resData['id'] < 1000000) {
                        $resig = Resignation::find($resData['id']);
                    }
                    
                    if (!$resig) {
                        $resig = new Resignation();
                        $resig->weekly_report_id = $weeklyReport->id;
                    }

                    $resig->trainee_id = $resData['trainee_id'];
                    $resig->birth_place = $resData['birth_place'] ?? null;
                    $resig->birth_date = $resData['birth_date'] ?? null;
                    $resig->reason = $resData['reason'] ?? null;
                    $resig->avg_inclass_score = $resData['avg_inclass_score'] ?: 0;
                    $resig->inclass_rank = $resData['inclass_rank'] ?: 0;
                    $resig->avg_practice_score = $resData['avg_practice_score'] ?: 0;
                    $resig->practice_rank = $resData['practice_rank'] ?: 0;

                    if (!empty($resData['photo']) && $resData['photo'] instanceof \Illuminate\Http\UploadedFile && $resData['photo']->isValid()) {
                        if ($resig->photo_path) {
                            Storage::disk('public')->delete($resig->photo_path);
                        }
                        $resig->photo_path = $resData['photo']->store('resignation_photos', 'public');
                    } elseif (!empty($resData['delete_photo'])) {
                        if ($resig->photo_path) {
                            Storage::disk('public')->delete($resig->photo_path);
                            $resig->photo_path = null;
                        }
                    }
                    
                    $resig->save();
                }
            }

            DB::commit();
            return redirect()->route('training-non-reguler.weekly-reports.index')->with('success', 'Laporan Mingguan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function merge(Request $request)
    {
        $ids = $request->input('report_ids', []);
        
        if (count($ids) < 2) {
            return back()->with('error', 'Pilih minimal 2 laporan untuk digabungkan.');
        }

        $reports = WeeklyReport::whereIn('id', $ids)->get();
        if ($reports->count() < 2) {
            return back()->with('error', 'Laporan tidak ditemukan atau tidak valid.');
        }

        DB::beginTransaction();
        try {
            $mergedProgramNames = $reports->map(function($r) {
                return $r->trainingProgram->name ?? 'Unknown';
            })->unique()->implode(' & ');

            $firstReport = $reports->first();

            $mergedReport = WeeklyReport::create([
                'training_program_id' => $firstReport->training_program_id,
                'trainer_id'          => $firstReport->trainer_id,
                'week_start_date'     => $firstReport->week_start_date,
                'week_end_date'       => $firstReport->week_end_date,
                'theme'               => $firstReport->theme,
                'status'              => 'submitted',
                'is_merged'           => true,
                'merged_program_names' => $mergedProgramNames,
                'training_purpose'    => $firstReport->training_purpose,
                'quote'               => $firstReport->quote,
                'cover_photo_1' => $firstReport->cover_photo_1,
                'cover_photo_2' => $firstReport->cover_photo_2,
                'cover_photo_3' => $firstReport->cover_photo_3,
                'cover_photo_4' => $firstReport->cover_photo_4,
                'cover_photo_5' => $firstReport->cover_photo_5,
                'cover_photo_6' => $firstReport->cover_photo_6,
                'strip_photos'  => $firstReport->strip_photos,
            ]);

            foreach ($reports as $report) {
                $report->parent_report_id = $mergedReport->id;
                $report->save();
                $report->delete(); 
            }

            DB::commit();
            return redirect()->route('training-non-reguler.weekly-reports.index')->with('success', 'Laporan berhasil digabungkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menggabungkan: ' . $e->getMessage());
        }
    }

    public function unmerge(WeeklyReport $weeklyReport)
    {
        if (!$weeklyReport->is_merged) {
            return back()->with('error', 'Laporan ini bukan hasil gabungan.');
        }

        DB::beginTransaction();
        try {
            $originalReports = WeeklyReport::onlyTrashed()
                ->where('parent_report_id', $weeklyReport->id)
                ->get();

            if ($originalReports->isEmpty()) {
                return back()->with('error', 'Data laporan asli tidak ditemukan. Tidak dapat membatalkan penggabungan.');
            }

            foreach ($originalReports as $oldReport) {
                $oldReport->restore();

                $sessions = $weeklyReport->trainingSessions()->with('sessionParticipants.trainee')->get()->filter(function($session) use ($oldReport) {
                    $programName = $oldReport->trainingProgram->name ?? null;
                    if ($session->program_source && $session->program_source === $programName) {
                        return true;
                    }
                    $firstParticipant = $session->sessionParticipants->first();
                    return $firstParticipant && $firstParticipant->trainee && $firstParticipant->trainee->training_program_id == $oldReport->training_program_id;
                });
                foreach ($sessions as $session) {
                    $session->update(['weekly_report_id' => $oldReport->id]);
                }

                $quizScores = $weeklyReport->quizScores()->whereHas('trainee', function($q) use ($oldReport) {
                    $q->where('training_program_id', $oldReport->training_program_id);
                })->get();
                foreach ($quizScores as $qs) {
                    $qs->update(['weekly_report_id' => $oldReport->id]);
                }

                $rankings = $weeklyReport->rankings()->whereHas('trainee', function($q) use ($oldReport) {
                    $q->where('training_program_id', $oldReport->training_program_id);
                })->get();
                foreach ($rankings as $rank) {
                    $rank->update(['weekly_report_id' => $oldReport->id]);
                }

                $resignations = $weeklyReport->resignations()->whereHas('trainee', function($q) use ($oldReport) {
                    $q->where('training_program_id', $oldReport->training_program_id);
                })->get();
                foreach ($resignations as $resig) {
                    $resig->update(['weekly_report_id' => $oldReport->id]);
                }

                $oldReport->parent_report_id = null;
                $oldReport->save();
            }

            $weeklyReport->forceDelete();

            DB::commit();
            return redirect()->route('training-non-reguler.weekly-reports.index')->with('success', 'Berhasil membatalkan penggabungan laporan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan penggabungan: ' . $e->getMessage());
        }
    }
}
