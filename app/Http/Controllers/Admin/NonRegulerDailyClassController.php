<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateTrainingRecordJob;
use App\Models\Attendance;
use App\Models\DailyClass;
use App\Models\Program\ProgramNonReguler;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Trainee;
use App\Models\TraineeEvaluation;
use App\Models\TrainingProgram;
use App\Notifications\WebPushBlastNotification;
use App\Services\EvaluationExportService;
use App\Services\ExcelExporter;
use App\Services\FileArchiveService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class NonRegulerDailyClassController extends Controller
{
    public function history(Request $request)
    {
        $query = DailyClass::with('trainingProgram', 'tasks', 'attendances', 'traineeEvaluations')
            ->where('is_completed', true);

        if ($request->has('program_id') && $request->program_id != '') {
            $query->where('training_program_id', $request->program_id);
        }

        $pastClasses = $query->orderBy('completed_at', 'desc')->orderBy('end_date', 'desc')->paginate(15)->withQueryString();
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

        // Hanya tampilkan materi yang belum selesai di daftar materi aktif
        $dailyClasses = DailyClass::with('trainingProgram', 'attendances', 'tasks.submissions', 'traineeEvaluations')
            ->where(function ($q) {
                $q->whereNull('is_completed')
                    ->orWhere('is_completed', false);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $pnrQuery = ProgramNonReguler::select('id_nonreguler', 'jenis_pelatihan', 'tahun', 'region', 'kategori_pelatihan', 'sub_kategori')->get();

        $trainingProgramCatalog = $pnrQuery->map(function ($item) {
            return [
                'id' => $item->id_nonreguler,
                'title' => trim((string) $item->jenis_pelatihan),
                'year' => (string) ($item->tahun ?: '2026'),
                'region' => trim((string) ($item->region ?: '-')),
                'kategori' => trim((string) ($item->kategori_pelatihan ?? '')),
                'sub_kategori' => trim((string) ($item->sub_kategori ?? '')),
            ];
        })->filter(fn ($item) => ! empty($item['title']))->values();

        $availableYears = $trainingProgramCatalog->pluck('year')->filter()->unique()->sort()->values();
        $availableRegions = $trainingProgramCatalog->pluck('region')->filter(fn ($r) => $r !== '-' && ! empty($r))->unique()->sort()->values();

        return view('admin.non-reguler.daily_classes.index', compact('programs', 'dailyClasses', 'trainingProgramCatalog', 'availableYears', 'availableRegions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'title' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'trainer_name' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'room' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
            'material_url' => 'nullable|url|max:255',
            'mess_url' => 'nullable|url|max:255',
            'certificate_link' => 'nullable|url|max:500',
        ], [
            'region.required' => 'Region pelatihan wajib dipilih.',
            'title.required' => 'Nama pelatihan wajib dipilih.',
        ]);

        // Cegah program duplikat jika nama pelatihan dan region-nya sama pada kategori ini
        $exists = DailyClass::where('training_program_id', $validated['training_program_id'])
            ->where('title', $validated['title'])
            ->where('region', $validated['region'])
            ->where(function ($q) {
                $q->whereNull('is_completed')->orWhere('is_completed', false);
            })
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', "Program \"{$validated['title']}\" untuk region \"{$validated['region']}\" sudah ada dan masih aktif. Tidak boleh membuat program yang sama.");
        }

        // date and trainer_name will be null initially
        $dailyClass = DailyClass::create($validated);

        return redirect()->route('training-non-reguler.daily-classes.index')->with('success', 'Program pelatihan berhasil ditambahkan.');
    }

    public function update(Request $request, DailyClass $dailyClass)
    {
        $rules = [
            'training_program_id' => 'required|exists:training_programs,id',
            'title' => 'required|string|max:255',
            'material_url' => 'nullable|url|max:255',
            'mess_url' => 'nullable|url|max:255',
            'certificate_link' => 'nullable|url|max:500',
        ];

        // Jika request dari form Edit Detail Pelatihan (memiliki field jadwal)
        if ($request->has('trainer_name') || $request->has('date')) {
            $rules['trainer_name'] = 'required|string|max:255';
            $rules['date'] = 'required|date';
            $rules['end_date'] = 'required|date|after_or_equal:date';
            $rules['start_time'] = 'required|string';
            $rules['end_time'] = 'required|string';
            $rules['room'] = 'required|string|max:255';
            $rules['location'] = 'required|string|max:255';
            $rules['provider'] = 'required|string|max:255';
            $rules['region'] = 'required|string|max:255';
        } else {
            $rules['trainer_name'] = 'nullable|string|max:255';
            $rules['date'] = 'nullable|date';
            $rules['end_date'] = 'nullable|date|after_or_equal:date';
            $rules['start_time'] = 'nullable|string';
            $rules['end_time'] = 'nullable|string';
            $rules['room'] = 'nullable|string|max:255';
            $rules['location'] = 'nullable|string|max:255';
            $rules['provider'] = 'nullable|string|max:255';
            $rules['region'] = 'nullable|string|max:255';
        }

        $messages = [
            'date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'trainer_name.required' => 'Nama instruktur wajib diisi.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required' => 'Waktu selesai wajib diisi.',
            'location.required' => 'Lokasi training wajib diisi.',
            'room.required' => 'Ruangan wajib diisi.',
            'provider.required' => 'Provider wajib diisi.',
            'region.required' => 'Region wajib diisi.',
        ];

        $validated = $request->validate($rules, $messages);

        $validated['location'] = $request->input('location') ?: null;
        $validated['room'] = $request->input('room') ?: null;
        $validated['provider'] = $request->input('provider') ?: null;
        $validated['region'] = $request->input('region') ?: null;
        $validated['start_time'] = $request->input('start_time') ?: null;
        $validated['end_time'] = $request->input('end_time') ?: null;
        $validated['trainer_name'] = $request->input('trainer_name') ?: null;
        $validated['date'] = $request->input('date') ?: null;
        $validated['end_date'] = $request->input('end_date') ?: null;
        $validated['material_url'] = $request->input('material_url') ?: null;
        $validated['mess_url'] = $request->input('mess_url') ?: null;
        $validated['certificate_link'] = $request->input('certificate_link') ?: null;

        // Cegah duplikasi saat edit: jika judul dan region sudah ada di kelas lain yang aktif
        if (! empty($validated['title']) && ! empty($validated['region'])) {
            $progId = $validated['training_program_id'] ?? $dailyClass->training_program_id;
            $exists = DailyClass::where('training_program_id', $progId)
                ->where('title', $validated['title'])
                ->where('region', $validated['region'])
                ->where('id', '!=', $dailyClass->id)
                ->where(function ($q) {
                    $q->whereNull('is_completed')->orWhere('is_completed', false);
                })
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', "Program \"{$validated['title']}\" untuk region \"{$validated['region']}\" sudah ada dan masih aktif. Tidak boleh duplikat.");
            }
        }

        $dailyClass->update($validated);

        // Sinkronkan ke training records untuk peserta yang hadir jika ada perubahan jadwal
        if ($dailyClass->date) {
            $hadirTrainees = Attendance::where('daily_class_id', $dailyClass->id)
                ->where('status', 'hadir')
                ->get();

            foreach ($hadirTrainees as $att) {
                UpdateTrainingRecordJob::dispatchSync($att->trainee_id, $dailyClass->id, $att->attendance_date ?: $dailyClass->date);
            }
        }

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

    /**
     * Download CSV template for import peserta
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_peserta.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 compatibility with Excel
            fwrite($file, "\xEF\xBB\xBF");
            // Header row
            fputcsv($file, ['No', 'Employee ID', 'Employee Name', 'Jabatan', 'Unit', 'Region']);
            // Sample row
            fputcsv($file, ['1', '123456789', 'Test', 'Group Manager Kebun', 'ATS1', 'Riau']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importTrainees(Request $request, DailyClass $dailyClass)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
            'date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:date',
            'trainer_name' => 'required|string|max:255',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'location' => 'required|string|max:255',
            'room' => 'required|string|max:255',
            'provider' => 'required|string|max:255',
            'region' => 'required|string|max:255',
        ], [
            'file.required' => 'File CSV wajib diunggah.',
            'file.mimes' => 'Format file yang diunggah harus berupa file .csv.',
            'file.max' => 'Ukuran file maksimal 5MB.',
            'date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'trainer_name.required' => 'Nama instruktur wajib diisi.',
            'start_time.required' => 'Waktu mulai wajib diisi.',
            'end_time.required' => 'Waktu selesai wajib diisi.',
            'location.required' => 'Lokasi training wajib diisi.',
            'room.required' => 'Ruangan wajib diisi.',
            'provider.required' => 'Provider wajib diisi.',
            'region.required' => 'Region wajib diisi.',
        ]);

        try {
            $path = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                return back()->with('error', 'Gagal import: File CSV kosong.');
            }

            // Cari baris header dan cocokkan kolom
            $headerRowIndex = null;
            $headerMap = [];

            foreach ($rows as $index => $row) {
                $tempMap = [];
                foreach ($row as $colIdx => $colName) {
                    if ($colName === null) {
                        continue;
                    }

                    $colClean = strtolower(trim((string) $colName));
                    $colAlnum = preg_replace('/[^a-z0-9]/', '', $colClean);

                    if ($colAlnum === 'no' || $colAlnum === 'nomor') {
                        $tempMap['no'] = $colIdx;
                    } elseif ($colAlnum === 'employeeid' || $colAlnum === 'nik' || str_contains($colAlnum, 'employeeid') || str_contains($colAlnum, 'idkaryawan')) {
                        $tempMap['nik'] = $colIdx;
                    } elseif ($colAlnum === 'employeename' || $colAlnum === 'nama' || str_contains($colAlnum, 'employeename') || str_contains($colAlnum, 'namakaryawan') || str_contains($colAlnum, 'namapeserta')) {
                        $tempMap['name'] = $colIdx;
                    } elseif ($colAlnum === 'jabatan' || $colAlnum === 'talentto' || str_contains($colAlnum, 'jabatan') || str_contains($colAlnum, 'talent')) {
                        $tempMap['talent_to'] = $colIdx;
                    } elseif ($colAlnum === 'unit' || str_contains($colAlnum, 'unit')) {
                        $tempMap['unit'] = $colIdx;
                    } elseif ($colAlnum === 'region' || str_contains($colAlnum, 'region')) {
                        $tempMap['region'] = $colIdx;
                    }
                }

                // Baris header terdeteksi jika mengandung Employee ID dan Employee Name
                if (isset($tempMap['nik']) && isset($tempMap['name'])) {
                    $headerRowIndex = $index;
                    $headerMap = $tempMap;
                    break;
                }
            }

            // Daftar kolom wajib
            $requiredColumns = [
                'no' => 'No',
                'nik' => 'Employee ID',
                'name' => 'Employee Name',
                'talent_to' => 'Jabatan',
                'unit' => 'Unit',
                'region' => 'Region',
            ];

            if ($headerRowIndex === null) {
                return back()->with('error', 'Gagal import: Format kolom file CSV tidak sesuai. File wajib memiliki kolom: No, Employee ID, Employee Name, Jabatan, Unit, Region.');
            }

            // Validasi apakah semua kolom wajib ada di file
            $missingColumns = [];
            foreach ($requiredColumns as $key => $label) {
                if (! isset($headerMap[$key])) {
                    $missingColumns[] = $label;
                }
            }

            if (! empty($missingColumns)) {
                return back()->with('error', 'Gagal import: Kolom berikut tidak ditemukan pada file CSV: '.implode(', ', $missingColumns).'. Harap gunakan format template yang sesuai: No, Employee ID, Employee Name, Jabatan, Unit, Region.');
            }

            // Hapus baris sampai dengan baris header
            for ($i = 0; $i <= $headerRowIndex; $i++) {
                unset($rows[$i]);
            }

            // Filter baris data yang valid (memiliki NIK dan Nama)
            $validDataRows = [];
            foreach ($rows as $r) {
                $checkNik = trim((string) ($r[$headerMap['nik']] ?? ''));
                $checkName = trim((string) ($r[$headerMap['name']] ?? ''));
                if (! empty($checkNik) || ! empty($checkName)) {
                    $validDataRows[] = $r;
                }
            }

            if (empty($validDataRows)) {
                return back()->with('error', 'Gagal import: File CSV tidak memiliki baris data peserta yang valid.');
            }

            // Simpan detail batch ke DailyClass hanya jika file CSV sudah tervalidasi dengan benar
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

            $importedCount = 0;

            // Build dates array for attendances based on the inputted date
            $startDate = Carbon::parse($request->date);
            $endDate = $request->end_date ? Carbon::parse($request->end_date) : $startDate->copy();
            $dates = [];
            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                $dates[] = $d->format('Y-m-d');
            }

            DB::beginTransaction();
            foreach ($validDataRows as $row) {
                $employeeId = trim((string) ($row[$headerMap['nik']] ?? ''));
                $employeeName = trim((string) ($row[$headerMap['name']] ?? ''));
                $talentTo = trim((string) ($row[$headerMap['talent_to']] ?? ''));
                $unit = trim((string) ($row[$headerMap['unit']] ?? ''));
                $region = trim((string) ($row[$headerMap['region']] ?? ''));

                if (empty($employeeId) || empty($employeeName)) {
                    continue; // Skip if mandatory fields missing
                }

                // Cari NIK di tabel training_records untuk meng-copy data profil (prioritas NIK, fallback Nama persis)
                $record = DB::table('training_records')
                    ->where('participant_identifier', $employeeId)
                    ->first();

                if (! $record && ! empty($employeeName)) {
                    $record = DB::table('training_records')
                        ->where(DB::raw('LOWER(TRIM(participant_name))'), strtolower(trim($employeeName)))
                        ->orderBy('created_at', 'desc')
                        ->first();
                }

                // Kalau ketemu, timpa data Excel yang kosong dengan data dari database
                if ($record) {
                    $unit = ! empty(trim($unit)) ? $unit : ($record->company ?? $record->work_location ?? '');
                    $region = ! empty(trim($region)) ? $region : ($record->region ?? '');
                    $talentTo = ! empty(trim($talentTo)) ? $talentTo : ($record->gol ?? '');
                }

                $trainee = Trainee::where('nik', $employeeId)->first();
                if (! $trainee && ! empty($employeeName)) {
                    $trainee = Trainee::where(DB::raw('LOWER(TRIM(name))'), strtolower(trim($employeeName)))->first();
                    if ($trainee) {
                        $trainee->update(['nik' => $employeeId]);
                    }
                }
                if ($trainee) {
                    // Update jika ada field yang masih kosong dan pastikan status aktif
                    $trainee->update([
                        'name' => $employeeName, // Update nama terbaru dari Excel/Record
                        'unit' => empty(trim($trainee->unit)) ? $unit : $trainee->unit,
                        'origin' => empty(trim($trainee->origin)) ? $region : $trainee->origin,
                        'talent_to' => empty(trim($trainee->talent_to)) ? $talentTo : $trainee->talent_to,
                        'status' => 'aktif',
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
                        'access_token' => Str::random(32),
                    ]);
                }

                // Make sure they have a training_program_id and access_token correctly assigned
                if (! $trainee->training_program_id) {
                    $trainee->update(['training_program_id' => $dailyClass->training_program_id]);
                }
                if (! $trainee->access_token) {
                    $trainee->update(['access_token' => Str::random(32)]);
                }

                // Create attendance rows for each date
                foreach ($dates as $d) {
                    Attendance::firstOrCreate([
                        'daily_class_id' => $dailyClass->id,
                        'trainee_id' => $trainee->id,
                        'attendance_date' => $d,
                    ], [
                        'status' => 'tidak_hadir',
                    ]);
                }

                $importedCount++;
            }
            DB::commit();

            return back()->with('success', "{$importedCount} peserta berhasil di-import ke materi ini!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Error: '.$e->getMessage()."\n".$e->getTraceAsString());

            return back()->with('error', 'Gagal import file: '.$e->getMessage());
        }
    }

    public function addManualTrainee(Request $request, DailyClass $dailyClass)
    {
        $request->validate([
            'nik' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'talent_to' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'region' => 'required|string|max:255',
        ], [
            'nik.required' => 'NIK / Employee ID wajib diisi.',
            'name.required' => 'Nama peserta wajib diisi.',
            'talent_to.required' => 'Jabatan wajib diisi.',
            'unit.required' => 'Unit/PT wajib diisi.',
            'region.required' => 'Region wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            $trainee = Trainee::where('nik', $request->nik)->first();

            if ($trainee) {
                $trainee->update([
                    'name' => $request->name,
                    'unit' => $request->unit,
                    'origin' => $request->region,
                    'talent_to' => $request->talent_to,
                    'status' => 'aktif',
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
                    'access_token' => Str::random(32),
                ]);
            }

            if (! $trainee->training_program_id) {
                $trainee->update(['training_program_id' => $dailyClass->training_program_id]);
            }
            if (! $trainee->access_token) {
                $trainee->update(['access_token' => Str::random(32)]);
            }

            // Build dates array for attendances based on the inputted date
            $startDate = Carbon::parse($dailyClass->date);
            $endDate = $dailyClass->end_date ? Carbon::parse($dailyClass->end_date) : $startDate->copy();

            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                Attendance::firstOrCreate([
                    'daily_class_id' => $dailyClass->id,
                    'trainee_id' => $trainee->id,
                    'attendance_date' => $d->format('Y-m-d'),
                ], [
                    'status' => 'tidak_hadir',
                ]);
            }

            DB::commit();

            return back()->with('success', "Peserta {$trainee->name} berhasil ditambahkan ke kelas ini!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Add Manual Error: '.$e->getMessage());

            return back()->with('error', 'Gagal menambah peserta: '.$e->getMessage());
        }
    }

    public function updateAttendance(Request $request, DailyClass $dailyClass)
    {
        $request->validate([
            'trainee_id' => 'required|exists:trainees,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:hadir,tidak_hadir',
        ]);

        $updateData = ['status' => $request->status];
        if ($request->status === 'tidak_hadir') {
            $updateData['scanned_at'] = null;
        }

        Attendance::updateOrCreate(
            ['daily_class_id' => $dailyClass->id, 'trainee_id' => $request->trainee_id, 'attendance_date' => $request->attendance_date],
            $updateData
        );

        $trainee = Trainee::find($request->trainee_id);

        if ($request->status === 'hadir') {
            UpdateTrainingRecordJob::dispatchSync($request->trainee_id, $dailyClass->id, $request->attendance_date);
        } else {
            // Jika status diubah menjadi Tidak Hadir:
            if ($trainee) {
                $hasOtherHadir = Attendance::where('daily_class_id', $dailyClass->id)
                    ->where('trainee_id', $request->trainee_id)
                    ->where('status', 'hadir')
                    ->where('attendance_date', '!=', $request->attendance_date)
                    ->exists();

                if (! $hasOtherHadir) {
                    // Jika sama sekali tidak ada hari hadir di kelas ini, hapus dari training_records
                    DB::table('training_records')
                        ->where('participant_identifier', $trainee->nik)
                        ->where('nama_pelatihan', $dailyClass->title)
                        ->delete();
                } else {
                    // Masih ada hari lain yang berstatus hadir, sinkronkan ulang
                    $otherDate = Attendance::where('daily_class_id', $dailyClass->id)
                        ->where('trainee_id', $request->trainee_id)
                        ->where('status', 'hadir')
                        ->value('attendance_date');
                    UpdateTrainingRecordJob::dispatchSync($request->trainee_id, $dailyClass->id, $otherDate);
                }

                Cache::forever('tr_cache_version', ((int) Cache::get('tr_cache_version', 1)) + 1);
                Cache::forget('tr_years');
                Cache::forever('dashboard_cache_version', ((int) Cache::get('dashboard_cache_version', 1)) + 1);
                Cache::forever('rvb_cache_version', ((int) Cache::get('rvb_cache_version', 1)) + 1);
            }
        }

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

    public function notifyTrainees(DailyClass $dailyClass)
    {
        $trainees = $dailyClass->attendances()->with('trainee')->get()->pluck('trainee')->unique('id')->filter();

        $dateString = $dailyClass->date ? Carbon::parse($dailyClass->date)->translatedFormat('d F Y') : 'Waktu belum ditentukan';
        $title = 'Jadwal Training: '.$dailyClass->title;

        $sentCount = 0;
        foreach ($trainees as $trainee) {
            $body = "Halo {$trainee->name}, kamu akan melaksanakan training materi {$dailyClass->title} di ruang ".($dailyClass->room ?: 'belum ditentukan')." pada {$dateString}.";
            $trainee->notify(new WebPushBlastNotification($title, $body));
            $sentCount++;
        }

        return back()->with('success', "Notifikasi undangan jadwal kelas telah dikirim ke {$sentCount} peserta!");
    }

    public function printQr(Request $request, DailyClass $dailyClass)
    {
        abort_if(! $dailyClass->qr_code_token, 404, 'QR Code belum di-generate.');

        $printDate = $request->query('date', $dailyClass->date->format('Y-m-d'));

        return view('admin.non-reguler.daily_classes.print_qr', compact('dailyClass', 'printDate'));
    }

    public function printAttendance(Request $request, DailyClass $dailyClass)
    {
        $printDate = $request->query('date', $dailyClass->date ? Carbon::parse($dailyClass->date)->format('Y-m-d') : now()->format('Y-m-d'));

        // Ambil seluruh peserta yang terdaftar pada kelas ini (sama seperti tampilan tabel di halaman show)
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id')->unique();
        $trainees = Trainee::whereIn('id', $traineeIds)->orderBy('id', 'asc')->get();

        // Jika ada parameter format=print atau view, tampilkan halaman cetak HTML biasa
        if ($request->query('format') === 'print' || $request->query('format') === 'html') {
            $dailyClass->load('trainingProgram');

            return view('admin.non-reguler.daily_classes.print_attendance', compact('dailyClass', 'printDate', 'trainees'));
        }

        $dailyClass->load('trainingProgram');

        // Header Metadata Pelatihan (Nama Program, Trainer, Waktu, Lokasi, dll)
        $waktuStr = ($dailyClass->start_time ? Carbon::parse($dailyClass->start_time)->format('H:i') : '?')
            . ' - ' . ($dailyClass->end_time ? Carbon::parse($dailyClass->end_time)->format('H:i') : 'Selesai') . ' WIB';

        $lokasiStr = ($dailyClass->room ?: '-') . ($dailyClass->region ? ' (' . $dailyClass->region . ')' : '');

        $tglFormattedIndo = Carbon::parse($printDate)->locale('id')->isoFormat('dddd, DD MMMM Y');

        $details = [
            ['Nama Program', ': ' . ($dailyClass->trainingProgram->name ?? '-'), 'Tanggal Pelatihan', ': ' . $tglFormattedIndo],
            ['Program / Kelas', ': ' . $dailyClass->title, 'Waktu', ': ' . $waktuStr],
            ['Instruktur', ': ' . ($dailyClass->trainer_name ?: '-'), 'Lokasi / Ruang', ': ' . $lokasiStr],
            ['Provider', ': ' . ($dailyClass->provider ?: 'Internal'), 'Total Peserta', ': ' . count($trainees) . ' Orang'],
        ];

        // Default: Export ke format Excel (.xlsx) dengan Header Lengkap
        $headers = [
            'No',
            'Employee ID',
            'Employee Name',
            'Jabatan',
            'Unit',
            'Region',
            'Tanggal Pelatihan',
            'Status Kehadiran',
        ];

        $tglSesiFormatted = Carbon::parse($printDate)->translatedFormat('d F Y');

        $dataRows = [];
        foreach ($trainees as $index => $trainee) {
            $attendance = Attendance::where('daily_class_id', $dailyClass->id)
                ->where('trainee_id', $trainee->id)
                ->where('attendance_date', $printDate)
                ->first();

            $statusRaw = strtolower($attendance->status ?? 'tidak_hadir');
            $statusText = match ($statusRaw) {
                'hadir' => 'Hadir',
                'sakit' => 'Sakit',
                'izin' => 'Izin',
                'alpha', 'tidak_hadir' => 'Tidak Hadir',
                'belum' => 'Belum Absen',
                default => ucwords(str_replace('_', ' ', $statusRaw)),
            };

            $dataRows[] = [
                $index + 1,
                $trainee->nik ?? '-',
                $trainee->name ?? '-',
                $trainee->talent_to ?? '-',
                $trainee->unit ?? '-',
                $trainee->origin ?: ($dailyClass->region ?: '-'),
                $tglSesiFormatted,
                $statusText,
            ];
        }

        $dateSuffix = ($printDate && $printDate !== 'all') ? Carbon::parse($printDate)->format('Ymd') : date('Ymd');
        $filename = 'Absensi_'.Str::slug($dailyClass->title).'_'.$dateSuffix.'.xlsx';

        return ExcelExporter::downloadWithHeader(
            $filename,
            'DAFTAR HADIR PESERTA PELATIHAN',
            'FR Academy - Program Non Reguler',
            $details,
            $headers,
            $dataRows,
            'Daftar Hadir'
        );
    }

    public function printTasks(DailyClass $dailyClass)
    {
        $dailyClass->load(['tasks', 'trainingProgram']);
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id')->unique();
        $trainees = Trainee::whereIn('id', $traineeIds)->get();

        $submissions = TaskSubmission::whereIn('task_id', $dailyClass->tasks->pluck('id'))
            ->whereIn('trainee_id', $trainees->pluck('id'))
            ->get();

        return view('admin.non-reguler.daily_classes.print_tasks', compact('dailyClass', 'trainees', 'submissions'));
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

        return view('admin.non-reguler.tasks.show', compact('task'));
    }

    public function destroyTask(Task $task)
    {
        $task->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function destroyTaskSubmission(Task $task, TaskSubmission $submission)
    {
        if ($submission->file_path) {
            $files = is_array($submission->file_path) ? $submission->file_path : [$submission->file_path];
            foreach ($files as $filePath) {
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
        }

        $traineeName = $submission->trainee ? $submission->trainee->name : 'Peserta';
        $submission->delete();

        return back()->with('success', "Pengumpulan tugas dari {$traineeName} berhasil dihapus.");
    }

    public function downloadSubmission(Task $task, TaskSubmission $submission)
    {
        try {
            $rawFiles = $submission->file_path;
            $files = is_array($rawFiles) ? $rawFiles : (is_string($rawFiles) ? [$rawFiles] : []);
            $existingFiles = [];
            foreach ($files as $f) {
                if (Storage::disk('public')->exists($f)) {
                    $existingFiles[] = $f;
                }
            }

            if (empty($existingFiles)) {
                return back()->with('error', 'File pengumpulan tugas tidak ditemukan di server.');
            }

            $traineeName = $submission->trainee ? Str::slug($submission->trainee->name.'_'.$submission->trainee->nik) : 'tugas_'.$submission->id;

            // Jika hanya 1 file, unduh langsung tanpa zip
            if (count($existingFiles) === 1) {
                $ext = pathinfo($existingFiles[0], PATHINFO_EXTENSION);
                $singleName = 'Tugas_'.$traineeName.($ext ? ('.'.$ext) : '');

                return Storage::disk('public')->download($existingFiles[0], $singleName);
            }

            // Lebih dari 1 file: kemas dalam file ZIP
            $zipFileName = 'Tugas_'.$traineeName.'.zip';
            $tempDir = storage_path('app/temp');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            $tempPath = $tempDir.'/'.uniqid('sub_').'.zip';

            $filesToArchive = [];
            foreach ($existingFiles as $idx => $f) {
                $absPath = Storage::disk('public')->path($f);
                $cleanName = ($idx + 1).'_'.basename($f);
                $filesToArchive[] = [
                    'path' => $absPath,
                    'name' => $cleanName,
                ];
            }

            $created = FileArchiveService::createZip($filesToArchive, $tempPath);
            if ($created && file_exists($tempPath) && filesize($tempPath) > 0) {
                return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
            }

            return back()->with('error', 'Gagal membuat arsip ZIP. Silakan unduh file satu per satu melalui tombol Lihat.');
        } catch (\Throwable $e) {
            Log::error('Error downloading non-reguler task submission: '.$e->getMessage(), [
                'task_id' => $task->id,
                'submission_id' => $submission->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kendala saat mengunduh tugas: '.$e->getMessage());
        }
    }

    public function downloadAllSubmissions(Task $task)
    {
        try {
            $task->load(['submissions.trainee']);
            $validSubmissions = $task->submissions->filter(function ($sub) {
                return $sub->status == 'sudah' && ! empty($sub->file_path);
            });

            if ($validSubmissions->isEmpty()) {
                return back()->with('error', 'Belum ada pengumpulan tugas dari peserta.');
            }

            $tempDir = storage_path('app/temp');
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0755, true);
            }
            $taskSlug = Str::slug($task->title ?: 'tugas');
            $zipFileName = 'Semua_Tugas_'.$taskSlug.'.zip';
            $tempPath = $tempDir.'/'.uniqid('all_').'.zip';

            $filesToArchive = [];
            foreach ($validSubmissions as $sub) {
                $rawFiles = $sub->file_path;
                $files = is_array($rawFiles) ? $rawFiles : (is_string($rawFiles) ? [$rawFiles] : []);
                $folderName = $sub->trainee ? Str::slug($sub->trainee->nik.'_'.$sub->trainee->name) : 'peserta_'.$sub->id;

                foreach ($files as $idx => $f) {
                    if (Storage::disk('public')->exists($f)) {
                        $absPath = Storage::disk('public')->path($f);
                        $fileNameInZip = $folderName.'/'.($idx + 1).'_'.basename($f);
                        $filesToArchive[] = [
                            'path' => $absPath,
                            'name' => $fileNameInZip,
                        ];
                    }
                }
            }

            if (empty($filesToArchive)) {
                return back()->with('error', 'Tidak ada file fisik tugas yang tersimpan di server untuk diunduh.');
            }

            $created = FileArchiveService::createZip($filesToArchive, $tempPath);
            if ($created && file_exists($tempPath) && filesize($tempPath) > 0) {
                return response()->download($tempPath, $zipFileName)->deleteFileAfterSend(true);
            }

            return back()->with('error', 'Gagal membuat file ZIP gabungan tugas.');
        } catch (\Throwable $e) {
            Log::error('Error downloading all non-reguler task submissions: '.$e->getMessage(), [
                'task_id' => $task->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kendala saat mengunduh seluruh tugas: '.$e->getMessage());
        }
    }

    public function removeTrainee(DailyClass $dailyClass, Trainee $trainee)
    {
        // Delete all attendances for this trainee in this daily class
        Attendance::where('daily_class_id', $dailyClass->id)
            ->where('trainee_id', $trainee->id)
            ->delete();

        // Also remove any task submissions by this trainee for this class
        $taskIds = $dailyClass->tasks()->pluck('id');
        TaskSubmission::whereIn('task_id', $taskIds)
            ->where('trainee_id', $trainee->id)
            ->delete();

        // Also remove any evaluation submissions by this trainee for this class
        TraineeEvaluation::where('daily_class_id', $dailyClass->id)
            ->where('trainee_id', $trainee->id)
            ->delete();

        // Jika trainee tidak lagi terdaftar di kelas manapun, ubah status jadi nonaktif & revoke token login mobile
        if (! $trainee->attendances()->exists()) {
            $trainee->update(['status' => 'nonaktif']);
            $trainee->tokens()->delete();
        }

        return back()->with('success', "Trainee {$trainee->name} berhasil dihapus dari sesi ini.");
    }

    public function updateTrainee(Request $request, DailyClass $dailyClass, Trainee $trainee)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:255|unique:trainees,nik,'.$trainee->id,
            'name' => 'required|string|max:255',
            'talent_to' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'region' => 'required|string|max:255',
        ], [
            'nik.required' => 'NIK / Employee ID wajib diisi.',
            'nik.unique' => 'NIK / Employee ID ini sudah digunakan oleh peserta lain.',
            'name.required' => 'Nama peserta wajib diisi.',
            'talent_to.required' => 'Jabatan wajib diisi.',
            'unit.required' => 'Unit/PT wajib diisi.',
            'region.required' => 'Region wajib diisi.',
        ]);

        $trainee->update([
            'nik' => $validated['nik'],
            'name' => $validated['name'],
            'talent_to' => $validated['talent_to'],
            'unit' => $validated['unit'],
            'origin' => $validated['region'],
        ]);

        return back()->with('success', "Data peserta {$trainee->name} berhasil diperbarui.");
    }

    public function removeAllTrainees(DailyClass $dailyClass)
    {
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id')->unique();
        Attendance::where('daily_class_id', $dailyClass->id)->delete();
        $taskIds = $dailyClass->tasks()->pluck('id');
        TaskSubmission::whereIn('task_id', $taskIds)->whereIn('trainee_id', $traineeIds)->delete();
        TraineeEvaluation::where('daily_class_id', $dailyClass->id)->whereIn('trainee_id', $traineeIds)->delete();

        foreach ($traineeIds as $tId) {
            $t = Trainee::find($tId);
            if ($t && ! $t->attendances()->exists()) {
                $t->update(['status' => 'nonaktif']);
                $t->tokens()->delete();
            }
        }

        $dailyClass->update([
            'date' => null,
            'end_date' => null,
            'trainer_name' => null,
            'room' => null,
            'location' => null,
            'start_time' => null,
            'end_time' => null,
            'provider' => null,
            'region' => null,
        ]);

        return back()->with('success', 'Semua peserta berhasil dihapus. Kelas kembali kosong.');
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

    public function clearEvaluation(DailyClass $dailyClass)
    {
        $dailyClass->traineeEvaluations()->delete();

        return back()->with('success', 'Semua respons evaluasi untuk sesi ini berhasil dihapus.');
    }

    public function toggleCompleted(DailyClass $dailyClass)
    {
        $newStatus = ! $dailyClass->is_completed;

        if ($newStatus) {
            // Selesai: Tandai batch/sesi kelas ini selesai (masuk ke Riwayat Pelatihan)
            $dailyClass->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            // Pastikan card wadah materi di "Daftar Materi" TETAP ADA (dalam keadaan kosong seperti awal)
            $activeExists = DailyClass::where('training_program_id', $dailyClass->training_program_id)
                ->where('title', $dailyClass->title)
                ->where(function ($q) {
                    $q->whereNull('is_completed')->orWhere('is_completed', false);
                })
                ->exists();

            if (! $activeExists) {
                DailyClass::create([
                    'training_program_id' => $dailyClass->training_program_id,
                    'title' => $dailyClass->title,
                    'description' => $dailyClass->description,
                    'trainer_name' => null,
                    'date' => null,
                    'end_date' => null,
                    'start_time' => null,
                    'end_time' => null,
                    'room' => null,
                    'location' => null,
                    'provider' => null,
                    'region' => null,
                    'material_url' => null,
                    'mess_url' => null,
                    'certificate_link' => null,
                    'is_completed' => false,
                    'completed_at' => null,
                ]);
            }

            $message = 'Program "'.$dailyClass->title.'" telah selesai dan dipindahkan ke Riwayat Program.';
        } else {
            // Aktifkan kembali dari riwayat:
            // Jika ada card placeholder kosong yang belum terpakai untuk materi ini, hapus dulu agar tidak double
            $placeholder = DailyClass::where('training_program_id', $dailyClass->training_program_id)
                ->where('title', $dailyClass->title)
                ->where(function ($q) {
                    $q->whereNull('is_completed')->orWhere('is_completed', false);
                })
                ->whereNull('date')
                ->whereDoesntHave('attendances')
                ->whereDoesntHave('tasks')
                ->first();

            if ($placeholder) {
                $placeholder->delete();
            }

            $dailyClass->update([
                'is_completed' => false,
                'completed_at' => null,
            ]);

            $message = 'Program "'.$dailyClass->title.'" berhasil dikembalikan ke Daftar Program Aktif.';
        }

        if (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'daily-classes/'.$dailyClass->id)) {
            return redirect()->route('training-non-reguler.daily-classes.index')->with('success', $message);
        }

        return back()->with('success', $message);
    }

    public function destroy(DailyClass $dailyClass)
    {
        // Ambil daftar trainee yang terhubung ke kelas ini
        $traineeIds = Attendance::where('daily_class_id', $dailyClass->id)->pluck('trainee_id')->unique();

        // Hapus absensi dan tugas di kelas ini (Learning Activity)
        // CATATAN: Data di tabel `training_records` sengaja TIDAK dihapus agar riwayat rekam jejak pelatihan karyawan tetap aman tersimpan secara permanen.
        Attendance::where('daily_class_id', $dailyClass->id)->delete();

        // Delete all task submissions and tasks for this class
        $taskIds = $dailyClass->tasks()->pluck('id');
        TaskSubmission::whereIn('task_id', $taskIds)->delete();
        $dailyClass->tasks()->delete();

        // Clean up orphaned trainees (who have no other attendances)
        foreach ($traineeIds as $tId) {
            $hasOtherClasses = Attendance::where('trainee_id', $tId)->exists();
            if (! $hasOtherClasses) {
                Trainee::where('id', $tId)->delete();
            }
        }

        // Finally, delete the class
        $dailyClass->delete();

        return back()->with('success', 'Program riwayat berhasil dihapus permanen. Data histori di Training Record tetap aman tersimpan.');
    }

    public function previewCertificate(Request $request, DailyClass $dailyClass)
    {
        $pdf = Pdf::loadView('admin.non-reguler.daily_classes.certificate', [
            'traineeName' => 'NAMA PESERTA (PREVIEW)',
            'className' => $dailyClass->title,
            'certDescription' => $request->input('cert_description'),
            'certSignerName' => $request->input('cert_signer_name'),
            'certSignerRole' => $request->input('cert_signer_role'),
            'certDate' => $request->input('cert_date') ? Carbon::parse($request->input('cert_date'))->translatedFormat('d F Y') : Carbon::now()->translatedFormat('d F Y'),
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

        if (! empty($passedTraineeIds)) {
            // Update selected to passed
            $dailyClass->attendances()->whereIn('trainee_id', $passedTraineeIds)->update(['is_passed' => true]);
        }

        return back()->with('success', 'Sertifikat berhasil diterbitkan untuk '.count($passedTraineeIds).' peserta.');
    }

    public function exportTrainingRecord(DailyClass $dailyClass)
    {
        $attendances = $dailyClass->attendances()->where('status', 'hadir')->with('trainee')->get();
        $parentProgram = DB::table('program_non_reguler')->where('jenis_pelatihan', $dailyClass->title)->first();

        $headers = ['NIK', 'Nama', 'Jenis Kelamin', 'Nama PT', 'Lokasi Kerja', 'Group', 'REGION', 'GOL', 'Jabatan', 'Nama Pelatihan', 'Internal/Eksternal', 'Level', 'Periode', 'Tanggal', 'Bulan', 'Tahun', 'Keterangan', 'program_type', 'program_id'];

        $filename = 'TrainingRecord_'.Str::slug($dailyClass->title).'_'.date('Ymd').'.xlsx';

        $dateObj = $dailyClass->date ? Carbon::parse($dailyClass->date) : now();
        $hariAbsen = $dateObj->format('d');
        $bulanAbsen = strtoupper($dateObj->translatedFormat('M'));
        $tahunAbsen = $dateObj->format('Y');
        $periode = 'Q'.ceil($dateObj->month / 3);

        $namaPelatihan = $dailyClass->title ?: ($parentProgram->jenis_pelatihan ?? '-');

        $dataRows = [];
        foreach ($attendances as $attendance) {
            $trainee = $attendance->trainee;
            if ($trainee) {
                // Coba cari record di tabel training_records yang sudah dibuat oleh Job (prioritas NIK, fallback Nama persis) untuk bulan & tahun yang sama
                $trQuery = DB::table('training_records')
                    ->where(function ($q) use ($trainee) {
                        $q->where('participant_identifier', $trainee->nik);
                        if (! empty(trim($trainee->name))) {
                            $q->orWhere(DB::raw('LOWER(TRIM(participant_name))'), strtolower(trim($trainee->name)));
                        }
                    })
                    ->where('nama_pelatihan', $namaPelatihan)
                    ->where('bulan', $bulanAbsen)
                    ->where('tahun', $tahunAbsen);

                $trRecord = $trQuery->first();

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
                    $gender = '-';
                    $gol = null;
                    $company = $trainee->unit ?: '-';
                    $workLoc = $trainee->unit ?: '-';
                    $group = $trainee->group ?: 'FR';
                    $region = $trainee->origin ?: '-';
                    $internalExternal = $dailyClass->provider ?: 'Internal';
                    $level = null;
                }

                // Jika data masih '-' atau null, cari dari riwayat peserta sebelumnya
                $pastRecord = null;
                if (! empty($trainee->nik)) {
                    $pastRecord = DB::table('training_records')
                        ->where('participant_identifier', $trainee->nik)
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
                if (! $pastRecord && ! empty(trim($trainee->name))) {
                    $pastRecord = DB::table('training_records')
                        ->where(DB::raw('LOWER(TRIM(participant_name))'), strtolower(trim($trainee->name)))
                        ->orderBy('created_at', 'desc')
                        ->first();
                }

                if ($pastRecord) {
                    if (empty($gender) || $gender === '-') $gender = $pastRecord->gender ?: '-';
                    if (empty($company) || $company === '-') $company = $pastRecord->company ?: ($trainee->unit ?: '-');
                    if (empty($workLoc) || $workLoc === '-') $workLoc = $pastRecord->work_location ?: ($trainee->unit ?: '-');
                    if (empty($group) || $group === '-') $group = $pastRecord->group_name ?: 'FR';
                    if (empty($region) || $region === '-') $region = $pastRecord->region ?: ($trainee->origin ?: '-');
                    if (empty($gol) || $gol === '-') $gol = $pastRecord->gol;
                    if (empty($level) || $level === '-') $level = $pastRecord->level;
                }

                // Dynamic GOL
                if (empty($gol) || $gol === '-') {
                    if (! empty($trainee->talent_to)) {
                        $gol = DB::table('training_records')
                            ->where('position', $trainee->talent_to)
                            ->whereNotNull('gol')
                            ->where('gol', '!=', '-')
                            ->orderBy('id', 'desc')
                            ->value('gol');
                    }
                    if (! $gol) {
                        $jabatanLower = strtolower($trainee->talent_to ?? '');
                        if (str_contains($jabatanLower, 'askep') || str_contains($jabatanLower, 'asst') || str_contains($jabatanLower, 'asisten') || str_contains($jabatanLower, 'manager') || str_contains($jabatanLower, 'staff') || str_contains($jabatanLower, 'staf') || str_contains($jabatanLower, 'ktu') || str_contains($jabatanLower, 'gmk') || str_contains($jabatanLower, 'head')) {
                            $gol = 'Staf';
                        } else {
                            $gol = 'Non Staf';
                        }
                    }
                }

                // Dynamic LEVEL
                if (empty($level) || $level === '-') {
                    if (! empty($trainee->talent_to)) {
                        $level = DB::table('training_records')
                            ->where('position', $trainee->talent_to)
                            ->whereNotNull('level')
                            ->where('level', '!=', '-')
                            ->orderBy('id', 'desc')
                            ->value('level');
                    }
                    if (! $level) {
                        $jabatanLower = strtolower($trainee->talent_to ?? '');
                        if (str_contains($jabatanLower, 'direktur') || str_contains($jabatanLower, 'director') || str_contains($jabatanLower, 'head')) {
                            $level = 'Senior';
                        } elseif (str_contains($jabatanLower, 'manager') || str_contains($jabatanLower, 'gm') || str_contains($jabatanLower, 'manajer')) {
                            $level = 'Middle';
                        } else {
                            $level = 'workers';
                        }
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
                    '',
                ];
            }
        }

        return ExcelExporter::download($filename, $headers, $dataRows, 'Training Record');
    }

    public function exportEvaluations(DailyClass $dailyClass)
    {
        return EvaluationExportService::export($dailyClass);
    }
}
