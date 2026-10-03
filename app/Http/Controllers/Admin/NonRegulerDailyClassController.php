<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DailyClass;
use App\Models\TrainingProgram;
use App\Models\Attendance;
use App\Models\Trainee;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NonRegulerDailyClassController extends Controller
{
    public function history(Request $request)
    {
        $today = now()->format('Y-m-d');
        $query = DailyClass::with('trainingProgram', 'tasks', 'attendances', 'traineeEvaluations')
            ->where(function ($q) use ($today) {
                $q->where(function ($sq) use ($today) {
                    $sq->whereNotNull('end_date')->whereDate('end_date', '<', $today);
                })->orWhere(function ($sq) use ($today) {
                    $sq->whereNull('end_date')->whereDate('date', '<', $today);
                });
            });
            
        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('training_program_id', $request->program_id);
        }
            
        $pastClasses = $query->orderBy('end_date', 'desc')->get();
        $programs = TrainingProgram::where('type', 'non-reguler')->get();
            
        return view('admin.non-reguler.history.index', compact('pastClasses', 'programs'));
    }

    public function index()
    {
        if (TrainingProgram::where('type', 'non-reguler')->count() == 0) {
            $currentYear = date('Y');
            $defaultPrograms = [
                ['name' => 'Managerial & Leadership', 'type' => 'non-reguler', 'year' => $currentYear, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Mill', 'type' => 'non-reguler', 'year' => $currentYear, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Traksi', 'type' => 'non-reguler', 'year' => $currentYear, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Estate', 'type' => 'non-reguler', 'year' => $currentYear, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Downstream', 'type' => 'non-reguler', 'year' => $currentYear, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Administrasi', 'type' => 'non-reguler', 'year' => $currentYear, 'created_at' => now(), 'updated_at' => now()],
            ];
            TrainingProgram::insert($defaultPrograms);
        }

        $programs = TrainingProgram::where('type', 'non-reguler')->get();
        
        $today = now()->format('Y-m-d');
        
        // Auto-clone past classes to keep an empty template in the active list
        $pastClasses = DailyClass::where(function ($q) use ($today) {
            $q->where(function ($sq) use ($today) {
                $sq->whereNotNull('end_date')->whereDate('end_date', '<', $today);
            })->orWhere(function ($sq) use ($today) {
                $sq->whereNull('end_date')->whereDate('date', '<', $today);
            });
        })->get();
        
        foreach ($pastClasses as $pastClass) {
            $hasActive = DailyClass::where('title', $pastClass->title)
                ->where('training_program_id', $pastClass->training_program_id)
                ->where(function ($q) use ($today) {
                    $q->whereNull('date')
                      ->orWhere(function ($sq) use ($today) {
                          $sq->whereNotNull('end_date')->whereDate('end_date', '>=', $today);
                      })
                      ->orWhere(function ($sq) use ($today) {
                          $sq->whereNull('end_date')->whereDate('date', '>=', $today);
                      });
                })->exists();
                
            if (!$hasActive) {
                $newClass = $pastClass->replicate();
                $newClass->date = null;
                $newClass->end_date = null;
                $newClass->start_time = null;
                $newClass->end_time = null;
                $newClass->location = null;
                $newClass->room = null;
                $newClass->trainer_name = null;
                $newClass->provider = null;
                $newClass->region = null;
                $newClass->qr_code_token = \Illuminate\Support\Str::random(32);
                $newClass->save();
            }
        }

        $dailyClasses = DailyClass::with('trainingProgram', 'attendances', 'tasks.submissions', 'traineeEvaluations')
            ->where(function ($q) use ($today) {
                $q->whereNull('date') // newly created, no date yet
                  ->orWhere(function ($sq) use ($today) {
                      $sq->whereNotNull('end_date')->whereDate('end_date', '>=', $today);
                  })
                  ->orWhere(function ($sq) use ($today) {
                      $sq->whereNull('end_date')->whereDate('date', '>=', $today);
                  });
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.non-reguler.daily_classes.index', compact('programs', 'dailyClasses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'title' => 'required|string|max:255',
            'trainer_name' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'room' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'material_url' => 'nullable|url|max:255',
            'mess_url' => 'nullable|url|max:255',
            'certificate_link' => 'nullable|url|max:500',
        ]);

        // date and trainer_name will be null initially
        $dailyClass = DailyClass::create($validated);
        
        $parentProgram = \App\Models\TrainingProgram::find($dailyClass->training_program_id);
        
        $startDateObj = \Carbon\Carbon::parse($dailyClass->date);
        $endDateObj = $dailyClass->end_date ? \Carbon\Carbon::parse($dailyClass->end_date) : $startDateObj;
        $totalHari = $startDateObj->diffInDays($endDateObj) + 1;
        
        $tanggalPelaksanaan = null;
        if ($dailyClass->date) {
            $tgl = $startDateObj->translatedFormat('d M Y');
            if ($dailyClass->end_date && $startDateObj->format('Y-m-d') !== $endDateObj->format('Y-m-d')) {
                $tgl .= ' - ' . $endDateObj->translatedFormat('d M Y');
            }
            $tanggalPelaksanaan = json_encode([$tgl]);
        }
        
        $totalJam = 0;
        if ($dailyClass->start_time && $dailyClass->end_time) {
            $startT = \Carbon\Carbon::parse($dailyClass->start_time);
            $endT = \Carbon\Carbon::parse($dailyClass->end_time);
            $totalJam = $startT->diffInHours($endT) * $totalHari;
        }
        
        \Illuminate\Support\Facades\DB::table('program_non_reguler')->updateOrInsert(
            ['jenis_pelatihan' => $dailyClass->title], // Group by title!
            [
                'id_nonreguler' => $dailyClass->id,
                'kategori_pelatihan' => $parentProgram ? $parentProgram->name : 'Non Reguler',
                'sub_kategori' => $parentProgram ? $parentProgram->name : 'Softskill',
                'region' => $dailyClass->region ?: 'Nasional',
                'trainer' => $dailyClass->trainer_name,
                'lokasi_training' => $dailyClass->location, // Mapped to location!
                'start_date' => $dailyClass->date,
                'end_date' => $dailyClass->end_date,
                'tanggal_pelaksanaan' => $tanggalPelaksanaan,
                'total_hari' => $totalHari,
                'total_jam' => $totalJam,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        
        return redirect()->route('training-non-reguler.daily-classes.index')->with('success', 'Wadah Materi Sesi berhasil ditambahkan.');
    }

    public function update(Request $request, DailyClass $dailyClass)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'title' => 'required|string|max:255',
            'trainer_name' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'room' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'material_url' => 'nullable|url|max:255',
            'mess_url' => 'nullable|url|max:255',
            'certificate_link' => 'nullable|url|max:500',
        ]);

        $dailyClass->update($validated);
        
        $startDateObj = \Carbon\Carbon::parse($dailyClass->date);
        $endDateObj = $dailyClass->end_date ? \Carbon\Carbon::parse($dailyClass->end_date) : $startDateObj;
        $totalHari = $startDateObj->diffInDays($endDateObj) + 1;
        
        $tanggalPelaksanaan = null;
        if ($dailyClass->date) {
            $tgl = $startDateObj->translatedFormat('d M Y');
            if ($dailyClass->end_date && $startDateObj->format('Y-m-d') !== $endDateObj->format('Y-m-d')) {
                $tgl .= ' - ' . $endDateObj->translatedFormat('d M Y');
            }
            $tanggalPelaksanaan = json_encode([$tgl]);
        }
        
        $totalJam = 0;
        if ($dailyClass->start_time && $dailyClass->end_time) {
            $startT = \Carbon\Carbon::parse($dailyClass->start_time);
            $endT = \Carbon\Carbon::parse($dailyClass->end_time);
            $totalJam = $startT->diffInHours($endT) * $totalHari;
        }
        
        \Illuminate\Support\Facades\DB::table('program_non_reguler')
            ->where('id_nonreguler', $dailyClass->id)
            ->update([
                'jenis_pelatihan' => $dailyClass->title,
                'region' => $dailyClass->region ?: 'Nasional',
                'trainer' => $dailyClass->trainer_name,
                'lokasi_training' => $dailyClass->location,
                'start_date' => $dailyClass->date,
                'end_date' => $dailyClass->end_date,
                'tanggal_pelaksanaan' => $tanggalPelaksanaan,
                'total_hari' => $totalHari,
                'total_jam' => $totalJam,
                'updated_at' => now()
            ]);
        
        return back()->with('success', 'Detail kelas berhasil diperbarui.');
    }

    public function show(DailyClass $dailyClass)
    {
        $dailyClass->load(['trainingProgram', 'tasks', 'attendances.trainee', 'tasks.submissions.trainee']);
        // Fetch trainees that have attendances tied to this class
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id')->unique();
        $trainees = Trainee::whereIn('id', $traineeIds)->get();
        
        // Build dates array
        $dates = [];
        if ($dailyClass->date) {
            $startDate = Carbon::parse($dailyClass->date);
            $endDate = $dailyClass->end_date ? Carbon::parse($dailyClass->end_date) : $startDate->copy();
            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                $dates[] = $d->format('Y-m-d');
            }
        }

        return view('admin.non-reguler.daily_classes.show', compact('dailyClass', 'trainees', 'dates'));
    }
    
    public function importTrainees(Request $request, DailyClass $dailyClass)
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'trainer_name' => 'required|string|max:255',
            'room' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'provider' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
        ], [
            'file.required' => 'File Excel wajib diunggah.',
            'file.max' => 'Ukuran file maksimal 5MB.'
        ]);

        try {
            // Save the batch information to the DailyClass (Wadah)
            $dailyClass->update([
                'date' => $request->date,
                'end_date' => $request->end_date,
                'trainer_name' => $request->trainer_name,
                'room' => $request->room,
                'location' => $request->location,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'provider' => $request->provider,
                'region' => $request->region,
            ]);

            $startDateObj = \Carbon\Carbon::parse($request->date);
            $endDateObj = $request->end_date ? \Carbon\Carbon::parse($request->end_date) : $startDateObj;
            $totalHari = $startDateObj->diffInDays($endDateObj) + 1;
            
            $tanggalPelaksanaan = null;
            if ($request->date) {
                $tgl = $startDateObj->translatedFormat('d M Y');
                if ($request->end_date && $startDateObj->format('Y-m-d') !== $endDateObj->format('Y-m-d')) {
                    $tgl .= ' - ' . $endDateObj->translatedFormat('d M Y');
                }
                $tanggalPelaksanaan = json_encode([$tgl]);
            }
            
            $totalJam = 0;
            if ($request->start_time && $request->end_time) {
                $startT = \Carbon\Carbon::parse($request->start_time);
                $endT = \Carbon\Carbon::parse($request->end_time);
                $totalJam = $startT->diffInHours($endT) * $totalHari;
            }

            \Illuminate\Support\Facades\DB::table('program_non_reguler')
                ->where('id_nonreguler', $dailyClass->id)
                ->update([
                    'region' => $request->region ?: 'Nasional',
                    'trainer' => $request->trainer_name,
                    'lokasi_training' => $request->location,
                    'start_date' => $request->date,
                    'end_date' => $request->end_date,
                    'tanggal_pelaksanaan' => $tanggalPelaksanaan,
                    'total_hari' => $totalHari,
                    'total_jam' => $totalJam,
                    'updated_at' => now()
                ]);

            $path = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Find header row dynamically (some templates have titles in rows 1-4)
            $headerRowIndex = 0;
            $headerMap = ['nik' => 1, 'name' => 2, 'talent_to' => 3, 'unit' => 4, 'region' => 5]; // default map
            foreach ($rows as $index => $row) {
                // Strip all spaces and newlines for robust checking
                $rowString = strtolower(preg_replace('/\s+/', '', implode('', array_filter(array_map(fn($v) => trim((string)$v), $row)))));
                
                if (str_contains($rowString, 'employeeid') || str_contains($rowString, 'nik') || str_contains($rowString, 'employeename')) {
                    $headerRowIndex = $index;
                    // Map headers dynamically
                    foreach ($row as $colIdx => $colName) {
                        $colClean = strtolower(preg_replace('/\s+/', '', (string)$colName));
                        if (str_contains($colClean, 'employeeid') || str_contains($colClean, 'nik')) {
                            $headerMap['nik'] = $colIdx;
                        } elseif (str_contains($colClean, 'name') || str_contains($colClean, 'nama')) {
                            $headerMap['name'] = $colIdx;
                        } elseif (str_contains($colClean, 'talent')) {
                            $headerMap['talent_to'] = $colIdx;
                        } elseif (str_contains($colClean, 'unit')) {
                            $headerMap['unit'] = $colIdx;
                        } elseif (str_contains($colClean, 'region') || str_contains($colClean, 'asal')) {
                            $headerMap['region'] = $colIdx;
                        }
                    }
                    break;
                }
            }

            // Remove all rows up to and including the header row
            for ($i = 0; $i <= $headerRowIndex; $i++) {
                unset($rows[$i]);
            }

            $importedCount = 0;
            
            // Build dates array for attendances based on the inputted date
            $startDate = Carbon::parse($request->date);
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy();
            $dates = [];
            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                $dates[] = $d->format('Y-m-d');
            }

            DB::beginTransaction();
            foreach ($rows as $index => $row) {
                // If it's empty row, skip (check the dynamic nik and name indices)
                if (empty(trim($row[$headerMap['nik']] ?? '')) && empty(trim($row[$headerMap['name']] ?? ''))) {
                    continue;
                }
                
                $employeeId = trim($row[$headerMap['nik']] ?? '');
                $employeeName = trim($row[$headerMap['name']] ?? '');
                $talentTo = trim($row[$headerMap['talent_to']] ?? '');
                $unit = trim($row[$headerMap['unit']] ?? '');
                $region = trim($row[$headerMap['region']] ?? '');

                if (empty($employeeId) || empty($employeeName)) {
                    continue; // Skip if mandatory fields missing
                }

                // Cari NIK di tabel training_records untuk meng-copy data profil
                $record = \Illuminate\Support\Facades\DB::table('training_records')
                            ->where('participant_identifier', $employeeId)
                            ->first();

                // Kalau ketemu, timpa data Excel yang kosong dengan data dari database
                if ($record) {
                    $unit = !empty(trim($unit)) ? $unit : ($record->company ?? $record->work_location ?? '');
                    $region = !empty(trim($region)) ? $region : ($record->region ?? '');
                    $talentTo = !empty(trim($talentTo)) ? $talentTo : ($record->gol ?? '');
                }

                $trainee = Trainee::where('nik', $employeeId)->first();
                if ($trainee) {
                    // Update jika ada field yang masih kosong
                    $trainee->update([
                        'name' => $employeeName, // Update nama terbaru dari Excel/Record
                        'unit' => empty(trim($trainee->unit)) ? $unit : $trainee->unit,
                        'origin' => empty(trim($trainee->origin)) ? $region : $trainee->origin,
                        'talent_to' => empty(trim($trainee->talent_to)) ? $talentTo : $trainee->talent_to,
                    ]);
                } else {
                    // Buat baru dengan data hasil copy
                    $trainee = Trainee::create([
                        'nik' => $employeeId,
                        'name' => $employeeName,
                        'training_program_id' => $dailyClass->training_program_id,
                        'talent_to' => $talentTo,
                        'unit' => $unit,
                        'origin' => $region,
                        'status' => 'aktif',
                        'access_token' => \Illuminate\Support\Str::random(32)
                    ]);
                }
                
                // Make sure they have a training_program_id and access_token correctly assigned
                if (!$trainee->training_program_id) {
                    $trainee->update(['training_program_id' => $dailyClass->training_program_id]);
                }
                if (!$trainee->access_token) {
                    $trainee->update(['access_token' => \Illuminate\Support\Str::random(32)]);
                }

                // Create attendance rows for each date
                foreach ($dates as $d) {
                    Attendance::firstOrCreate([
                        'daily_class_id' => $dailyClass->id,
                        'trainee_id' => $trainee->id,
                        'attendance_date' => $d
                    ], [
                        'status' => 'belum'
                    ]);
                }
                
                $importedCount++;
            }
            DB::commit();

            return back()->with('success', "{$importedCount} peserta berhasil di-import ke materi ini!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Import Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return back()->with('error', 'Gagal import file: ' . $e->getMessage());
        }
    }
    public function addManualTrainee(Request $request, DailyClass $dailyClass)
    {
        $request->validate([
            'nik' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'talent_to' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $trainee = Trainee::where('nik', $request->nik)->first();
            
            if ($trainee) {
                // Update jika ada field yang kosong
                $trainee->update([
                    'name' => $request->name,
                    'unit' => empty(trim($trainee->unit)) ? $request->unit : $trainee->unit,
                    'origin' => empty(trim($trainee->origin)) ? $request->region : $trainee->origin,
                    'talent_to' => empty(trim($trainee->talent_to)) ? $request->talent_to : $trainee->talent_to,
                ]);
            } else {
                $trainee = Trainee::create([
                    'nik' => $request->nik,
                    'name' => $request->name,
                    'training_program_id' => $dailyClass->training_program_id,
                    'talent_to' => $request->talent_to,
                    'unit' => $request->unit,
                    'origin' => $request->region,
                    'status' => 'aktif',
                    'access_token' => \Illuminate\Support\Str::random(32)
                ]);
            }

            if (!$trainee->training_program_id) {
                $trainee->update(['training_program_id' => $dailyClass->training_program_id]);
            }
            if (!$trainee->access_token) {
                $trainee->update(['access_token' => \Illuminate\Support\Str::random(32)]);
            }

            // Build dates array for attendances based on the inputted date
            $startDate = Carbon::parse($dailyClass->date);
            $endDate = $dailyClass->end_date ? Carbon::parse($dailyClass->end_date) : $startDate->copy();
            
            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                Attendance::firstOrCreate([
                    'daily_class_id' => $dailyClass->id,
                    'trainee_id' => $trainee->id,
                    'attendance_date' => $d->format('Y-m-d')
                ], [
                    'status' => 'belum'
                ]);
            }

            DB::commit();
            return back()->with('success', "Peserta {$trainee->name} berhasil ditambahkan ke kelas ini!");
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Add Manual Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menambah peserta: ' . $e->getMessage());
        }
    }
    public function updateAttendance(Request $request, DailyClass $dailyClass)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,sakit,izin,alpha'
        ]);
        
        Attendance::updateOrCreate(
            ['daily_class_id' => $dailyClass->id, 'trainee_id' => $request->trainee_id, 'attendance_date' => $request->attendance_date],
            ['status' => $request->status]
        );

        if ($request->status === 'hadir') {
            \App\Jobs\UpdateTrainingRecordJob::dispatchSync($request->trainee_id, $dailyClass->id, $request->attendance_date);
        }
        
        return back()->with('success', 'Status absensi berhasil diperbarui.');
    }
    
    public function generateQr(DailyClass $dailyClass)
    {
        if (!$dailyClass->qr_code_token) {
            $dailyClass->update([
                'qr_code_token' => Str::random(32)
            ]);
        }
        
        return back()->with(['success' => 'QR Code tersedia.', 'auto_open_qr' => true]);
    }
    
    public function notifyTrainees(DailyClass $dailyClass)
    {
        $trainees = $dailyClass->attendances()->with('trainee')->get()->pluck('trainee')->unique('id')->filter();
        
        $dateString = $dailyClass->date ? \Carbon\Carbon::parse($dailyClass->date)->translatedFormat('d F Y') : 'Waktu belum ditentukan';
        $title = "Jadwal Training: " . $dailyClass->title;
        
        $sentCount = 0;
        foreach ($trainees as $trainee) {
            $body = "Halo {$trainee->name}, kamu akan melaksanakan training materi {$dailyClass->title} di ruang " . ($dailyClass->room ?: 'belum ditentukan') . " pada {$dateString}.";
            $trainee->notify(new \App\Notifications\WebPushBlastNotification($title, $body));
            $sentCount++;
        }
        
        return back()->with('success', "Notifikasi undangan jadwal kelas telah dikirim ke {$sentCount} peserta!");
    }

    public function printQr(Request $request, DailyClass $dailyClass)
    {
        abort_if(!$dailyClass->qr_code_token, 404, 'QR Code belum di-generate.');
        
        $printDate = $request->query('date', $dailyClass->date->format('Y-m-d'));
        
        return view('admin.non-reguler.daily_classes.print_qr', compact('dailyClass', 'printDate'));
    }
    
    public function printAttendance(Request $request, DailyClass $dailyClass)
    {
        $printDate = $request->query('date', $dailyClass->date ? \Carbon\Carbon::parse($dailyClass->date)->format('Y-m-d') : now()->format('Y-m-d'));
        
        $dailyClass->load(['trainingProgram', 'attendances' => function($query) use ($printDate) {
            $query->where('attendance_date', $printDate)->with('trainee');
        }]);
        
        return view('admin.non-reguler.daily_classes.print_attendance', compact('dailyClass', 'printDate'));
    }
    
    public function printTasks(DailyClass $dailyClass)
    {
        $dailyClass->load(['tasks', 'trainingProgram']);
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id')->unique();
        $trainees = Trainee::whereIn('id', $traineeIds)->get();

        $submissions = \App\Models\TaskSubmission::whereIn('task_id', $dailyClass->tasks->pluck('id'))
                        ->whereIn('trainee_id', $trainees->pluck('id'))
                        ->get();
                        
        return view('admin.non-reguler.daily_classes.print_tasks', compact('dailyClass', 'trainees', 'submissions'));
    }
    
    public function storeTask(Request $request, DailyClass $dailyClass)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'required|date'
        ]);
        
        $dailyClass->tasks()->create([
            'title' => $validated['title'],
            'deadline' => $validated['deadline']
        ]);
        
        return back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function showTask(\App\Models\Task $task)
    {
        $task->load(['submissions.trainee', 'dailyClass.trainingProgram']);
        return view('admin.non-reguler.tasks.show', compact('task'));
    }
    
    public function destroyTask(\App\Models\Task $task)
    {
        $task->delete();
        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function removeTrainee(DailyClass $dailyClass, Trainee $trainee)
    {
        // Delete all attendances for this trainee in this daily class
        Attendance::where('daily_class_id', $dailyClass->id)
                  ->where('trainee_id', $trainee->id)
                  ->delete();
                  
        // Also remove any task submissions by this trainee for this class
        $taskIds = $dailyClass->tasks()->pluck('id');
        \App\Models\TaskSubmission::whereIn('task_id', $taskIds)
                                  ->where('trainee_id', $trainee->id)
                                  ->delete();
                  
        return back()->with('success', "Trainee {$trainee->name} berhasil dihapus dari sesi ini.");
    }
    
    public function removeAllTrainees(DailyClass $dailyClass)
    {
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id');
        Attendance::where('daily_class_id', $dailyClass->id)->delete();
        $taskIds = $dailyClass->tasks()->pluck('id');
        \App\Models\TaskSubmission::whereIn('task_id', $taskIds)->whereIn('trainee_id', $traineeIds)->delete();
        
        $dailyClass->update([
            'date' => null,
            'end_date' => null,
            'trainer_name' => null,
            'room' => null,
            'start_time' => null,
            'end_time' => null,
            'provider' => null,
            'region' => null
        ]);
        
        \Illuminate\Support\Facades\DB::table('program_non_reguler')
            ->where('id_nonreguler', $dailyClass->id)
            ->update([
                'start_date' => null,
                'end_date' => null,
                'trainer' => null,
                'lokasi_training' => null,
                'region' => 'Nasional'
            ]);
        
        return back()->with('success', 'Semua peserta berhasil dihapus. Kelas kembali kosong.');
    }

    public function toggleEvaluation(DailyClass $dailyClass)
    {
        $dailyClass->update([
            'is_evaluation_open' => !$dailyClass->is_evaluation_open
        ]);
        
        $status = $dailyClass->is_evaluation_open ? 'dibuka' : 'ditutup';
        return back()->with('success', "Evaluasi untuk sesi ini berhasil {$status}.");
    }

    public function clearEvaluation(DailyClass $dailyClass)
    {
        $dailyClass->traineeEvaluations()->delete();
        return back()->with('success', 'Semua respons evaluasi untuk sesi ini berhasil dihapus.');
    }

    public function destroy(DailyClass $dailyClass)
    {
        // Get all trainee IDs from this class before we delete their attendances
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id')->unique();
        
        // Delete all attendances for this class
        Attendance::where('daily_class_id', $dailyClass->id)->delete();
        
        // Delete all task submissions and tasks for this class
        $taskIds = $dailyClass->tasks()->pluck('id');
        \App\Models\TaskSubmission::whereIn('task_id', $taskIds)->delete();
        $dailyClass->tasks()->delete();
        
        // Clean up orphaned trainees (who have no other attendances)
        foreach ($traineeIds as $tId) {
            $hasOtherClasses = Attendance::where('trainee_id', $tId)->exists();
            if (!$hasOtherClasses) {
                Trainee::where('id', $tId)->delete();
            }
        }
        
        // Finally, delete the class
        $classId = $dailyClass->id;
        $dailyClass->delete();
        
        \Illuminate\Support\Facades\DB::table('program_non_reguler')->where('id_nonreguler', $classId)->delete();
        
        return back()->with('success', 'Materi beserta pesertanya berhasil dihapus permanen.');
    }

    public function previewCertificate(Request $request, DailyClass $dailyClass)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.non-reguler.daily_classes.certificate', [
            'traineeName' => 'NAMA PESERTA (PREVIEW)',
            'className' => $dailyClass->title,
            'certDescription' => $request->input('cert_description'),
            'certSignerName' => $request->input('cert_signer_name'),
            'certSignerRole' => $request->input('cert_signer_role'),
            'certDate' => $request->input('cert_date') ? \Carbon\Carbon::parse($request->input('cert_date'))->translatedFormat('d F Y') : \Carbon\Carbon::now()->translatedFormat('d F Y')
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('Preview_Sertifikat.pdf');
    }

    public function generateCertificates(Request $request, DailyClass $dailyClass)
    {
        $passedTraineeIds = $request->input('passed_trainees', []);
        
        $dailyClass->update([
            'cert_description' => $request->input('cert_description'),
            'cert_signer_name' => $request->input('cert_signer_name'),
            'cert_signer_role' => $request->input('cert_signer_role'),
            'cert_date' => $request->input('cert_date'),
        ]);

        // Update all to not passed first (to handle unchecked ones)
        $dailyClass->attendances()->update(['is_passed' => false]);

        if (!empty($passedTraineeIds)) {
            // Update selected to passed
            $dailyClass->attendances()->whereIn('trainee_id', $passedTraineeIds)->update(['is_passed' => true]);
        }

        return back()->with('success', 'Sertifikat berhasil diterbitkan untuk ' . count($passedTraineeIds) . ' peserta.');
    }

    public function exportTrainingRecord(DailyClass $dailyClass)
    {
        $attendances = $dailyClass->attendances()->where('status', 'hadir')->with('trainee')->get();
        $parentProgram = \Illuminate\Support\Facades\DB::table('program_non_reguler')->where('id_nonreguler', $dailyClass->training_program_id)->first();
        
        $headers = ['NIK', 'Nama', 'Jenis Kelamin', 'Nama PT', 'Lokasi Kerja', 'Group', 'REGION', 'GOL', 'Jabatan', 'Nama Pelatihan', 'Internal/Eksternal', 'Level', 'Periode', 'Tanggal', 'Bulan', 'Tahun', 'Keterangan', 'program_type', 'program_id'];
        
        $filename = 'TrainingRecord_' . \Illuminate\Support\Str::slug($dailyClass->title) . '_' . date('Ymd') . '.xlsx';
        
        $dateObj = $dailyClass->date ? \Carbon\Carbon::parse($dailyClass->date) : now();
        $hariAbsen = $dateObj->format('d');
        $bulanAbsen = strtoupper($dateObj->translatedFormat('M'));
        $tahunAbsen = $dateObj->format('Y');
        $periode = 'Q' . ceil($dateObj->month / 3);
        
        $namaPelatihan = $dailyClass->title ?: ($parentProgram->jenis_pelatihan ?? '-');

        $dataRows = [];
        foreach ($attendances as $attendance) {
            $trainee = $attendance->trainee;
            if ($trainee) {
                // Coba cari record di tabel training_records yang sudah dibuat oleh Job
                $trRecord = \Illuminate\Support\Facades\DB::table('training_records')
                    ->where('participant_identifier', $trainee->nik)
                    ->where('program_id', $dailyClass->training_program_id)
                    ->where('program_type', 'Non reguler')
                    ->first();

                if ($trRecord) {
                    $gender = $trRecord->gender;
                    $gol = $trRecord->gol;
                    $company = $trRecord->company;
                    $workLoc = $trRecord->work_location;
                    $group = $trRecord->group_name;
                    $region = $trRecord->region;
                    $internalExternal = $trRecord->internal_external;
                    $level = $trRecord->level;
                } else {

                    $pastRecord = \Illuminate\Support\Facades\DB::table('training_records')
                        ->where('participant_identifier', $trainee->nik)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $gender = ($pastRecord && $pastRecord->gender && $pastRecord->gender !== '-') ? $pastRecord->gender : ($trainee->gender ?: '-');
                    $company = ($pastRecord && $pastRecord->company && $pastRecord->company !== '-') ? $pastRecord->company : ($trainee->unit ?: '-');
                    $workLoc = ($pastRecord && $pastRecord->work_location && $pastRecord->work_location !== '-') ? $pastRecord->work_location : ($trainee->unit ?: '-');
                    $group = ($pastRecord && $pastRecord->group_name && $pastRecord->group_name !== '-') ? $pastRecord->group_name : ($trainee->group ?: '-');
                    $region = ($pastRecord && $pastRecord->region && $pastRecord->region !== '-') ? $pastRecord->region : ($trainee->origin ?: '-');
                    $level = ($pastRecord && $pastRecord->level && $pastRecord->level !== '-') ? $pastRecord->level : '-';
                    $internalExternal = $dailyClass->provider ?: 'Internal';
                    
                    $jabatan = strtolower($trainee->talent_to ?? '');
                    if (str_contains($jabatan, 'askep') || str_contains($jabatan, 'asst') || str_contains($jabatan, 'manager') || str_contains($jabatan, 'staff') || str_contains($jabatan, 'ktu')) {
                        $gol = 'Staf';
                    } elseif (str_contains($jabatan, 'mandor') || str_contains($jabatan, 'kerani') || str_contains($jabatan, 'pekerja')) {
                        $gol = 'Non Staf';
                    } else {
                        $gol = '-';
                    }
                }

                $dataRows[] = [
                    $trainee->nik ?? '-',
                    $trainee->name ?? '-',
                    $gender ?? '-',
                    $company,
                    $workLoc,
                    $group,
                    $region,
                    $gol,
                    $trainee->talent_to ?? '-',
                    $namaPelatihan,
                    $internalExternal,
                    $level,
                    $periode,
                    $hariAbsen,
                    $bulanAbsen,
                    $tahunAbsen,
                    '-',
                    'non_reguler',
                    ''
                ];
            }
        }
        
        return \App\Services\ExcelExporter::download($filename, $headers, $dataRows, 'Training Record');
    }
}



