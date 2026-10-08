<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use App\Services\ExcelExporter;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainingRecordController extends Controller
{
    private static ?Collection $cachedActivities = null;

    private const MONTHS = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

    public function index(Request $request)
    {
        $rawYear = $request->input('year', 'all');
        $year = ($rawYear === 'all' || $rawYear === '' || $rawYear === '0') ? 'all' : (int) $rawYear;
        $month = (int) $request->input('month', 0);
        $type = $request->input('type', 'all');
        $category = trim((string) $request->input('category', 'all'));

        if (! in_array($type, ['all', 'reguler', 'non_reguler', 'non_reguler_soft_skill', 'non_reguler_teknikal', 'soft_skill', 'teknikal'], true)) {
            $type = 'all';
        }

        $activities = $this->activities();
        $filteredRecords = $this->filteredRecords($request);

        $trYears = TrainingRecord::whereNotNull('tahun')->pluck('tahun')->map(fn ($y) => (int) $y)->filter()->all();
        $regYears = ProgramReguler::whereNotNull('Mulai_program')->get()->map(fn ($p) => (int) optional($p->Mulai_program)->year)->filter()->all();
        $nonRegYears = ProgramNonReguler::whereNotNull('start_date')->get()->map(fn ($p) => (int) optional($p->start_date)->year)->filter()->all();

        $years = collect([2024, 2025, 2026, 2027])
            ->merge($trYears)
            ->merge($regYears)
            ->merge($nonRegYears)
            ->filter(fn ($y) => $y >= 2000 && $y <= 2100)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $nonRegulerSubCategories = ProgramNonReguler::distinct()->pluck('sub_kategori')
            ->filter()
            ->map(fn ($c) => trim((string) $c))
            ->unique()
            ->values()
            ->all();

        $perPage = 15;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $records = new LengthAwarePaginator(
            $filteredRecords->values()->forPage($currentPage, $perPage),
            $filteredRecords->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        $months = self::MONTHS;

        return view('Administrasi.Pages.Program.training_record', compact('records', 'activities', 'year', 'rawYear', 'month', 'type', 'category', 'years', 'months', 'nonRegulerSubCategories'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->ensureActivity($data['program_type'], $data['program_id']);
        $this->ensureUnique($data['program_type'], $data['program_id'], $data['participant_identifier']);
        TrainingRecord::create($data + ['status' => 'valid']);

        return back()->with('success', 'Training Record berhasil ditambahkan.');
    }

    public function show(TrainingRecord $trainingRecord)
    {
        return view('Administrasi.Pages.Program.training_record_detail', ['record' => $trainingRecord, 'activity' => $trainingRecord->activity()]);
    }

    public function update(Request $request, TrainingRecord $trainingRecord)
    {
        $data = $this->validated($request, $trainingRecord);
        $this->ensureActivity($data['program_type'], $data['program_id']);
        $this->ensureUnique($data['program_type'], $data['program_id'], $data['participant_identifier'], null, null, null, $trainingRecord->id);
        $trainingRecord->update($data);

        return back()->with('success', 'Training Record berhasil diperbarui.');
    }

    public function destroy(TrainingRecord $trainingRecord)
    {
        $trainingRecord->delete();

        return back()->with('success', 'Training Record berhasil dihapus.');
    }

    public function destroyAll()
    {
        $count = TrainingRecord::count();
        TrainingRecord::truncate();

        ActivityLog::log(
            'Hapus',
            'Training Record',
            "Menghapus seluruh data Training Record ({$count} data)",
            ['count' => $count]
        );

        return back()->with('success', "Seluruh data Training Record ({$count} data) berhasil dihapus.");
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx|max:20480']);
        $file = $request->file('file');
        $rows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());
        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'File tidak memiliki data.']);
        }

        $rawHeaders = array_shift($rows);
        $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);

        // Map raw headers to standardized keys
        $headerMap = [];
        foreach ($rawHeaders as $idx => $h) {
            $cleaned = $cleanStr($h);
            if (in_array($cleaned, ['nik', 'nip', 'id peserta', 'no id', 'badge'])) {
                $headerMap['nik'] = $idx;
            } elseif (in_array($cleaned, ['nama', 'nama peserta', 'peserta', 'employee name'])) {
                $headerMap['nama'] = $idx;
            } elseif (in_array($cleaned, ['department', 'departemen', 'dept', 'divisi'])) {
                $headerMap['department'] = $idx;
            } elseif (in_array($cleaned, ['nama pelatihan', 'training program', 'judul training', 'program', 'nama program', 'jenis pelatihan', 'pelatihan'])) {
                $headerMap['nama pelatihan'] = $idx;
            } elseif (in_array($cleaned, ['region', 'wilayah', 'reg'])) {
                $headerMap['region'] = $idx;
            } elseif (in_array($cleaned, ['nama pt', 'pt', 'company', 'perusahaan', 'estate'])) {
                $headerMap['nama pt'] = $idx;
            } elseif (in_array($cleaned, ['lokasi kerja', 'lokasi', 'kebun/pabrik', 'unit', 'work location'])) {
                $headerMap['lokasi kerja'] = $idx;
            } elseif (in_array($cleaned, ['group', 'group unit'])) {
                $headerMap['group'] = $idx;
            } elseif (in_array($cleaned, ['jenis kelamin', 'jk', 'gender'])) {
                $headerMap['jenis kelamin'] = $idx;
            } elseif (in_array($cleaned, ['gol', 'golongan'])) {
                $headerMap['gol'] = $idx;
            } elseif (in_array($cleaned, ['jabatan', 'position', 'posisi'])) {
                $headerMap['jabatan'] = $idx;
            } elseif (in_array($cleaned, ['instruktur', 'trainer', 'instructor'])) {
                $headerMap['instruktur'] = $idx;
            } elseif (in_array($cleaned, ['internal/eksternal', 'internal_eksternal', 'internal eksternal'])) {
                $headerMap['internal/eksternal'] = $idx;
            } elseif (in_array($cleaned, ['level'])) {
                $headerMap['level'] = $idx;
            } elseif (in_array($cleaned, ['periode', 'period'])) {
                $headerMap['periode'] = $idx;
            } elseif (in_array($cleaned, ['tanggal', 'tgl', 'date'])) {
                $headerMap['tanggal'] = $idx;
            } elseif (in_array($cleaned, ['bulan', 'bln', 'month'])) {
                $headerMap['bulan'] = $idx;
            } elseif (in_array($cleaned, ['tahun', 'thn', 'year'])) {
                $headerMap['tahun'] = $idx;
            } elseif (in_array($cleaned, ['jml hari', 'jumlah hari', 'jml_hari', 'total hari', 'total_hari', 'hari'])) {
                $headerMap['jml hari'] = $idx;
            } elseif (in_array($cleaned, ['jam pelatihan', 'jam_pelatihan', 'total jam', 'total_jam', 'jam', 'jam/peserta'])) {
                $headerMap['jam pelatihan'] = $idx;
            } elseif (in_array($cleaned, ['keterangan', 'notes', 'note'])) {
                $headerMap['keterangan'] = $idx;
            } elseif (in_array($cleaned, ['program_type', 'tipe program'])) {
                $headerMap['program_type'] = $idx;
            } elseif (in_array($cleaned, ['program_id', 'id_nonreguler', 'program reguler id'])) {
                $headerMap['program_id'] = $idx;
            } else {
                $headerMap[$cleaned] = $idx;
            }
        }

        if (! isset($headerMap['nama pelatihan']) || (! isset($headerMap['nik']) && ! isset($headerMap['nama']))) {
            return back()->withErrors(['file' => 'Header file tidak lengkap. Kolom wajib: NIK/Nama dan Nama Pelatihan.']);
        }

        // Clear cache so newly added programs are included
        self::$cachedActivities = null;
        $activities = $this->activities();

        $count = 0;
        $skipped = 0;
        $insertBatch = [];
        $now = now();

        DB::transaction(function () use ($rows, $headerMap, &$activities, &$count, &$skipped, &$insertBatch, $now) {
            foreach ($rows as $row) {
                if (empty(array_filter($row, fn ($val) => trim((string) $val) !== ''))) {
                    continue;
                }

                $item = [];
                foreach ($headerMap as $key => $idx) {
                    $item[$key] = trim((string) ($row[$idx] ?? ''));
                }

                $nama = $item['nama'] ?? '';
                $nik = $item['nik'] ?? '';
                $namaPelatihan = $item['nama pelatihan'] ?? '';

                if ($nama === '' && $nik === '' && empty($item['jabatan']) && empty($item['nama pt'])) {
                    continue;
                }

                $data = $this->importData($item);
                
                // Cek NIK atau Nama
                $condition = [];
                if (!empty($data['participant_identifier'])) {
                    $condition['participant_identifier'] = $data['participant_identifier'];
                } else {
                    $condition['participant_name'] = $data['participant_name'];
                }
                $condition['nama_pelatihan'] = $data['nama_pelatihan'];

                $data['status'] = 'valid';
                $data['updated_at'] = $now;

                $existing = TrainingRecord::where($condition)->first();
                if ($existing) {
                    $existing->update($data);
                } else {
                    $data['created_at'] = $now;
                    $insertBatch[] = $data;
                }
                
                $count++;

                // Insert in chunks of 500
                if (count($insertBatch) >= 500) {
                    TrainingRecord::insert($insertBatch);
                    $insertBatch = [];
                }
            }

            if (! empty($insertBatch)) {
                TrainingRecord::insert($insertBatch);
                $insertBatch = [];
            }
        });

        ActivityLog::log(
            'Import',
            'Training Record',
            "Mengimport {$count} data Training Record dari file ".$file->getClientOriginalName(),
            ['filename' => $file->getClientOriginalName(), 'count' => $count, 'skipped' => $skipped]
        );

        $message = "{$count} data Training Record berhasil diimpor.";
        if ($skipped > 0) {
            $message .= " ({$skipped} data dilewati karena aktivitas program tidak ditemukan).";
        }

        return back()->with('success', $message);
    }

    public function export(Request $request): StreamedResponse
    {
        $records = $this->filteredRecords($request);
        $headers = ['No', 'NIK', 'Nama', 'Jenis Kelamin', 'Nama PT', 'Department', 'lokasi Kerja', 'Group', 'REGION', 'GOL', 'Jabatan', 'Tgl', 'Bln', 'Thn', 'Jml Hari', 'Jam Pelatihan', 'Nama Pelatihan', 'Instruktur', 'Tempat Pelatihan', 'Reg / Non Reg', 'Keterangan', 'Internal/Eksternal', 'Level', 'Periode'];

        $dataRows = [];
        foreach ($records as $index => $record) {
            $act = $record->activity();
            $dataRows[] = [
                $index + 1,
                $record->displayNik(),
                $record->participant_name,
                $record->gender,
                $record->company,
                $record->department,
                $record->work_location,
                $record->group_name,
                $record->region,
                $record->gol,
                $record->position,
                $record->tanggal,
                $record->bulan,
                $record->tahun,
                $record->jml_hari ?: ($act?->jumlah_hari ?? $act?->total_hari),
                $record->total_jam ?: $record->activityHoursPerParticipant(),
                $record->activityName(),
                $record->instructor ?: $record->activityTrainer(),
                $record->program_type === 'reguler' ? $act?->Lokasi_training : $act?->lokasi_training,
                $record->program_type === 'reguler' ? 'Reguler' : 'Non Reguler',
                $record->notes,
                $record->internal_external,
                $record->level,
                $record->periode,
            ];
        }

        return ExcelExporter::download('training_records.xlsx', $headers, $dataRows, 'Training Record');
    }

    public function template(): StreamedResponse
    {
        $headers = ['No', 'NIK', 'Nama', 'Jenis Kelamin', 'Nama PT', 'Department', 'lokasi Kerja', 'Group', 'REGION', 'GOL', 'Jabatan', 'Tgl', 'Bln', 'Thn', 'Jml Hari', 'Jam Pelatihan', 'Nama Pelatihan', 'Instruktur', 'Tempat Pelatihan', 'Reg / Non Reg', 'Keterangan', 'Internal/Eksternal', 'Level', 'Periode'];
        $exampleRow = ['1', '12345678', 'Contoh Peserta', 'Male', 'PT FR', 'Agronomy', 'Estate A', 'Group 1', 'Kalbar', 'Staf', 'Asisten', '15', 'JAN', 2026, '2', '16', 'Training Leadership', 'Budi Santoso', 'Training Center Riau', 'Reguler', 'Contoh Catatan', 'Internal', 'Staff', '2026-1'];

        return ExcelExporter::download('training_record_template.xlsx', $headers, [$exampleRow], 'Template TR');
    }

    private function validated(Request $request, ?TrainingRecord $record = null): array
    {
        $validated = $request->validate([
            'program_type' => 'required|in:reguler,non_reguler',
            'program_id' => 'required|integer',
            'participant_identifier' => 'nullable|string|max:100',
            'participant_name' => 'required|string|max:255',
            'gender' => 'nullable|string|max:50', 'company' => 'nullable|string|max:255', 'department' => 'nullable|string|max:255', 'work_location' => 'nullable|string|max:255',
            'group_name' => 'nullable|string|max:100', 'region' => 'nullable|string|max:100', 'gol' => 'nullable|string|max:100', 'position' => 'nullable|string|max:255', 'instructor' => 'nullable|string|max:255',
            'internal_external' => 'nullable|string|max:50', 'level' => 'nullable|string|max:100', 'periode' => 'nullable|string|max:100', 'notes' => 'nullable|string',
            'tanggal' => 'nullable|string|max:50',
            'bulan' => 'nullable|string|max:50',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'jml_hari' => 'nullable|string|max:100',
            'total_jam' => 'nullable|string|max:100',
        ]);

        if (! empty($validated['bulan'])) {
            $validated['bulan'] = strtoupper(substr(trim($validated['bulan']), 0, 3));
        }

        return $validated;
    }

    private function ensureActivity(string $type, int $id): void
    {
        if (! $this->activities()->has($type.':'.$id)) {
            abort(422, 'Training activity tidak ditemukan.');
        }
    }

    private function ensureUnique(string $type, int $id, ?string $identifier, ?string $tanggal = null, ?string $bulan = null, ?int $tahun = null, ?int $ignore = null): void
    {
        if (empty($identifier)) {
            return;
        }
        $query = TrainingRecord::where('program_type', $type)->where('program_id', $id)->where('participant_identifier', $identifier);
        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($ignore) {
            $query->whereKey('!=', $ignore);
        }
        if ($query->exists()) {
            abort(422, 'Peserta dengan NIK ini sudah terdaftar pada jadwal training activity ini.');
        }
    }

    private function activities()
    {
        if (self::$cachedActivities !== null) {
            return self::$cachedActivities;
        }

        $cleanStr = fn ($str) => strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $str)));

        $reguler = ProgramReguler::all()->mapWithKeys(fn ($item) => [
            'reguler:'.$item->getKey() => [
                'type' => 'reguler',
                'sub_type' => 'reguler',
                'type_label' => 'Reguler',
                'id' => $item->getKey(),
                'name' => $this->activityLabel($item, 'reguler'),
                'model' => $item,
            ],
        ]);

        $nonReguler = ProgramNonReguler::all()->mapWithKeys(function ($item) use ($cleanStr) {
            $kat = $cleanStr(($item->kategori_pelatihan ?? '').' '.($item->sub_kategori ?? '').' '.($item->jenis_pelatihan ?? ''));
            $isSoft = str_contains($kat, 'managerial') || str_contains($kat, 'leadership') || str_contains($kat, 'soft');

            return [
                'non_reguler:'.$item->getKey() => [
                    'type' => 'non_reguler',
                    'sub_type' => $isSoft ? 'soft_skill' : 'teknikal',
                    'type_label' => $isSoft ? 'Non Reguler - Soft Skill' : 'Non Reguler - Teknikal',
                    'id' => $item->getKey(),
                    'name' => $item->jenis_pelatihan,
                    'model' => $item,
                ],
            ];
        });

        return self::$cachedActivities = $reguler->union($nonReguler);
    }

    private function filteredRecords(Request $request)
    {
        $clone = $request->duplicate();
        $activities = $this->activities();
        $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);

        return TrainingRecord::latest()->get()->filter(function ($record) use ($clone, $activities, $cleanStr) {
            $type = $clone->input('type', 'all');
            $category = trim((string) $clone->input('category', 'all'));
            $rawYear = $clone->input('year', 'all');
            $year = ($rawYear === 'all' || $rawYear === '' || $rawYear === '0') ? null : (int) $rawYear;
            $month = (int) $clone->input('month', 0);

            $activity = null;
            if ($record->program_type && $record->program_id) {
                $activity = $activities->get($record->program_type.':'.$record->program_id);
            }

            // 1. Filter Type
            if ($type === 'reguler' && $record->program_type !== 'reguler') {
                return false;
            }
            if ($type === 'non_reguler' && $record->program_type !== 'non_reguler') {
                return false;
            }
            if (in_array($type, ['non_reguler_soft_skill', 'soft_skill'], true)) {
                if ($record->program_type !== 'non_reguler' || ($activity['sub_type'] ?? '') !== 'soft_skill') {
                    return false;
                }
            }
            if (in_array($type, ['non_reguler_teknikal', 'teknikal'], true)) {
                if ($record->program_type !== 'non_reguler' || ($activity['sub_type'] ?? '') !== 'teknikal') {
                    return false;
                }
            }

            // 2. Filter Category (Detail Kategori)
            if ($category !== 'all' && $category !== '') {
                $pModel = $activity['model'] ?? null;
                if ($record->program_type === 'reguler') {
                    $pName = trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $activity['name'] ?? ''));
                    $isMandor = ($pModel && str_contains($cleanStr($pModel->jenis_program_reguler ?? ''), 'mandor'))
                        || str_starts_with(strtoupper($pName), 'PMT')
                        || str_starts_with(strtoupper($pName), 'PKT');

                    if ($category === 'staf' && $isMandor) {
                        return false;
                    }
                    if ($category === 'mandor' && ! $isMandor) {
                        return false;
                    }
                } else {
                    // Non Reguler
                    $kat = $cleanStr(($pModel->kategori_pelatihan ?? '').' '.($pModel->sub_kategori ?? '').' '.($activity['name'] ?? ''));
                    if ($category === 'managerial' || strcasecmp($category, 'softskill') === 0) {
                        if (! str_contains($kat, 'managerial') && ! str_contains($kat, 'soft')) {
                            return false;
                        }
                    } elseif ($category === 'technical') {
                        if (! str_contains($kat, 'technical') || str_contains($kat, 'soft')) {
                            return false;
                        }
                    } else {
                        $searchCat = $cleanStr($category);
                        if (! str_contains($kat, $searchCat)) {
                            return false;
                        }
                    }
                }
            }

            // 3. Filter Year
            if ($year !== null) {
                if ($record->tahun || $record->bulan) {
                    if ($record->tahun && (int) $record->tahun !== $year) {
                        return false;
                    }
                } else {
                    if (! collect($record->activityDates())->contains(fn ($date) => $date && (int) date('Y', strtotime($date)) === $year)) {
                        return false;
                    }
                }
            }

            // 4. Filter Month
            if ($month > 0) {
                if ($record->bulan) {
                    $monthShort = strtoupper(self::MONTHS[$month] ?? '');
                    if ($record->bulan !== $monthShort) {
                        return false;
                    }
                } else {
                    if (! collect($record->activityDates())->contains(fn ($date) => $date && (int) date('n', strtotime($date)) === $month)) {
                        return false;
                    }
                }
            }

            // 5. Search keyword
            $search = strtolower(trim((string) $clone->input('search')));
            if ($search !== '' && ! str_contains(strtolower(implode(' ', [$record->participant_identifier, $record->participant_name, $record->company, $record->department, $record->work_location, $record->group_name, $record->region, $record->position, $record->bulan, $record->tahun, $record->jml_hari, $record->total_jam, $record->activityName()])), $search)) {
                return false;
            }
            foreach (['company', 'work_location', 'group_name', 'region', 'position', 'internal_external', 'level'] as $field) {
                if ($clone->filled($field) && strtolower((string) $record->{$field}) !== strtolower((string) $clone->input($field))) {
                    return false;
                }
            }

            return true;
        });
    }

    private function activityDates($activity, string $type): array
    {
        return $activity->tanggal_pelaksanaan ?: ($type === 'reguler' ? array_filter([$activity->Mulai_program, $activity->Selesai_program]) : array_filter([$activity->start_date, $activity->end_date]));
    }

    private function findActivity(array $item): ?array
    {
        return $this->findActivityCached($this->activities(), $item);
    }

    private function findActivityCached(Collection &$activities, array $item): ?array
    {
        $type = str_replace('-', '_', strtolower(trim((string) ($item['program_type'] ?? ''))));
        $type = $type === 'nonreguler' ? 'non_reguler' : $type;
        $id = $item['program_id'] ?? ($type === 'reguler' ? ($item['program_id'] ?? null) : ($item['id_nonreguler'] ?? null));
        $rowRegion = $this->inferRowRegion($item);

        // 1. If explicit ID was provided, check with or without explicit type
        if ($id) {
            $keysToTry = [];
            if ($type) {
                $keysToTry[] = $type.':'.(int) $id;
            }
            $keysToTry[] = 'non_reguler:'.(int) $id;
            $keysToTry[] = 'reguler:'.(int) $id;

            foreach ($keysToTry as $k) {
                if (isset($activities[$k])) {
                    $matchedAct = $activities[$k];
                    $actRegion = $this->normalizeRegion($matchedAct['model']->Region ?? $matchedAct['model']->region ?? '');
                    if ($rowRegion === '' || $actRegion === '' || $actRegion === $rowRegion) {
                        return $matchedAct;
                    }

                    // Region mismatch on provided ID! Look for sister program in the correct region
                    $targetProgName = trim($matchedAct['name']);
                    $actType = $matchedAct['type'];
                    $sister = $activities->first(function ($act) use ($actType, $targetProgName, $rowRegion) {
                        if ($act['type'] !== $actType) {
                            return false;
                        }
                        $actReg = $this->normalizeRegion($act['model']->Region ?? $act['model']->region ?? '');
                        if ($actReg !== $rowRegion) {
                            return false;
                        }

                        return strcasecmp(trim($act['name']), $targetProgName) === 0 || $this->matchActivityNames($targetProgName, $act['name']);
                    });
                    if ($sister) {
                        return $sister;
                    }

                    return $matchedAct;
                }
            }
        }

        $rawPelatihanName = trim((string) ($item['nama pelatihan'] ?? ''));
        $name = strtolower($rawPelatihanName);
        if ($name === '') {
            return null;
        }

        // 2. Exact match
        $matches = $activities->filter(fn ($activity) => strtolower(trim($activity['name'])) === $name);

        // 3. Flexible match (prefix, word matching) if no exact match
        if ($matches->isEmpty()) {
            $matches = $activities->filter(function ($activity) use ($name) {
                return $this->matchActivityNames($name, $activity['name']);
            });
        }

        // 4. Filter by region if multiple matches exist or rowRegion is known
        if ($matches->count() > 1 && $rowRegion !== '') {
            $regionalMatches = $matches->filter(function ($activity) use ($rowRegion) {
                $model = $activity['model'];
                $activityRegion = $this->normalizeRegion($model->Region ?? $model->region ?? '');

                return $activityRegion === $rowRegion;
            });
            if ($regionalMatches->isNotEmpty()) {
                $matches = $regionalMatches;
            }
        }

        if ($matches->count() === 1) {
            return $matches->first();
        }

        // Fallback: alias matching or pick first match from flexible results
        if ($matches->isNotEmpty()) {
            return $matches->first();
        }

        $aliasMatch = $activities->first(function ($activity) use ($name, $rowRegion) {
            $model = $activity['model'];
            $aliases = [$model->Training_Program ?? null, $model->jenis_pelatihan ?? null, $model->Batch ?? null];
            $nameMatches = collect($aliases)->filter()->contains(fn ($alias) => $this->matchActivityNames($name, (string) $alias));
            if (! $nameMatches) {
                return false;
            }
            if ($rowRegion !== '') {
                $actReg = $this->normalizeRegion($model->Region ?? $model->region ?? '');
                if ($actReg !== '' && $actReg !== $rowRegion) {
                    return false;
                }
            }

            return true;
        });

        if ($aliasMatch) {
            return $aliasMatch;
        }

        // Fallback 5: Auto-create missing unbudgeted ProgramNonReguler entry with 0 target
        $rawPelatihanName = trim((string) ($item['nama pelatihan'] ?? $item['training program'] ?? $item['program'] ?? ''));
        if ($rawPelatihanName === '') {
            return null;
        }

        try {
            $formattedRegion = $rowRegion ? ($rowRegion === 'riau' ? 'Riau' : ($rowRegion === 'kalbar' ? 'Kalbar' : 'Kaltim FR')) : 'Riau';
            $isSoft = str_contains(strtolower($rawPelatihanName), 'soft') || str_contains(strtolower($rawPelatihanName), 'leadership') || str_contains(strtolower($rawPelatihanName), 'managerial');

            $newNonReg = ProgramNonReguler::create([
                'jenis_pelatihan' => $rawPelatihanName,
                'kategori_pelatihan' => $isSoft ? 'Managerial and Leadership' : 'Technical',
                'sub_kategori' => $isSoft ? 'Softskill' : 'Technical',
                'region' => $formattedRegion,
                'trainer' => $item['instruktur'] ?? 'Internal',
                'lokasi_training' => $item['lokasi kerja'] ?? 'FR Academy',
                'TARGET_BATCH' => 0,
                'TARGET_PESERTA' => 0,
                'target_total' => 0,
                'total_batch' => 0,
            ]);

            $id = $newNonReg->getKey();
            $newAct = [
                'type' => 'non_reguler',
                'sub_type' => $isSoft ? 'soft_skill' : 'teknikal',
                'type_label' => $isSoft ? 'Non Reguler - Soft Skill' : 'Non Reguler - Teknikal',
                'id' => $id,
                'name' => $newNonReg->jenis_pelatihan,
                'model' => $newNonReg,
            ];

            $activities->put('non_reguler:'.$id, $newAct);

            return $newAct;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function matchActivityNames(string $inputName, string $activityName): bool
    {
        $inputName = strtolower(trim(preg_replace('/\s+/', ' ', $inputName)));
        $activityName = strtolower(trim(preg_replace('/\s+/', ' ', $activityName)));
        if ($inputName === '' || $activityName === '') {
            return false;
        }

        if ($inputName === $activityName) {
            return true;
        }

        // Check reguler acronyms: FAT, AAT, MAT, TAT, DMT, PMT, PKT, OAT
        $regAcronyms = ['fat', 'aat', 'mat', 'tat', 'dmt', 'pmt', 'pkt', 'oat'];
        $inWords = explode(' ', $inputName);
        $actWords = explode(' ', $activityName);
        $inFirst = $inWords[0] ?? '';
        $actFirst = $actWords[0] ?? '';

        $inIsReg = in_array($inFirst, $regAcronyms, true);
        $actIsReg = in_array($actFirst, $regAcronyms, true);

        if ($inIsReg || $actIsReg) {
            // Must have the exact same acronym
            if ($inFirst !== $actFirst) {
                return false;
            }
            // Check batch roman numeral (I, II, III, IV, etc.)
            $inRoman = $inWords[1] ?? '';
            $actRoman = $actWords[1] ?? '';
            if ($inRoman !== '' && $actRoman !== '' && $inRoman !== $actRoman) {
                return false;
            }

            return true;
        }

        if (str_starts_with($inputName, $activityName) || str_starts_with($activityName, $inputName)) {
            return true;
        }

        $cleanInput = preg_replace('/[^\w\s]/u', ' ', $inputName);
        $cleanActivity = preg_replace('/[^\w\s]/u', ' ', $activityName);

        $inputWords = array_values(array_filter(explode(' ', $cleanInput)));
        $activityWords = array_values(array_filter(explode(' ', $cleanActivity)));

        if (empty($inputWords) || empty($activityWords)) {
            return false;
        }

        $noise = ['training', 'pelatihan', 'program', 'unbudget', 'refreshment', 'refresh', 'of', 'by', 'vendor', 'dan', '&'];
        $filteredInWords = array_values(array_diff($inputWords, $noise));
        $filteredActWords = array_values(array_diff($activityWords, $noise));

        if (! empty($filteredInWords) && ! empty($filteredActWords)) {
            $intersect = array_intersect($filteredInWords, $filteredActWords);
            if (count($intersect) === count($filteredInWords) || count($intersect) === count($filteredActWords)) {
                return true;
            }
        }

        // If activityName is 1 word (e.g. "Supervisory", "Damkarhutlabun")
        if (count($activityWords) === 1) {
            return in_array($activityWords[0], $inputWords, true) || str_contains($cleanInput, $activityWords[0]);
        }

        // If activityName is 2+ words
        if (count($inputWords) >= 2 && count($activityWords) >= 2) {
            if ($inputWords[0] === $activityWords[0] && $inputWords[1] === $activityWords[1]) {
                return true;
            }
        }

        $firstTwoActivityWords = implode(' ', array_slice($activityWords, 0, 2));
        if (str_contains($cleanInput, $firstTwoActivityWords)) {
            return true;
        }

        return false;
    }

    private function activityLabel($activity, string $type): string
    {
        if ($type !== 'reguler') {
            return trim((string) $activity->jenis_pelatihan);
        }

        $name = trim((string) $activity->Training_Program);
        $batch = trim((string) $activity->Batch);
        if ($batch !== '' && ! str_contains(strtolower($name), strtolower($batch))) {
            $name .= ' - '.$batch;
        }

        return $name;
    }

    private function importData(array $item): array
    {
        $tgl = trim((string) ($item['tgl'] ?? $item['tanggal'] ?? ''));
        $bulan = trim((string) ($item['bulan'] ?? $item['bln'] ?? $item['month'] ?? ''));
        $tahun = trim((string) ($item['tahun'] ?? $item['thn'] ?? $item['year'] ?? ''));
        $nik = trim((string) ($item['nik'] ?? ''));
        $nama = trim((string) ($item['nama'] ?? ''));

        // Check if NIK is a valid specific identifier or just a placeholder/name
        $cleanNik = strtoupper(trim($nik));
        $cleanNama = strtoupper(trim($nama));
        $isPlaceholderNik = ($nik === '' || in_array($cleanNik, ['-', '#N/A', 'PROMOSI', 'TRAINEE', '0', 'NULL', 'N/A']) || ($cleanNama !== '' && $cleanNik === $cleanNama));
        $identifier = $isPlaceholderNik ? null : $nik;

        $finalNama = $nama !== '' ? $nama : (! empty($item['jabatan']) ? $item['jabatan'] : '-');

        $inferred = $this->inferRowRegion($item);
        $finalRegion = $item['region'] ?? null;
        if (empty($finalRegion) && $inferred !== '') {
            $finalRegion = $inferred === 'riau' ? 'Riau' : ($inferred === 'kalbar' ? 'Kalbar' : 'Kaltim FR');
        }

        $tahunVal = null;
        if ($tahun !== '' && is_numeric($tahun)) {
            $yInt = (int) $tahun;
            $tahunVal = ($yInt > 2030) ? 2026 : $yInt;
        }

        return [
            'program_type' => null,
            'program_id' => null,
            'nama_pelatihan' => $item['nama pelatihan'] ?? null,
            'tempat_pelatihan' => $item['tempat pelatihan'] ?? null,
            'reg_non_reg' => $item['reg / non reg'] ?? null,
            'participant_identifier' => $identifier,
            'participant_name' => $finalNama,
            'gender' => $item['jenis kelamin'] ?? null,
            'company' => $item['nama pt'] ?? null,
            'department' => $item['department'] ?? null,
            'work_location' => $item['lokasi kerja'] ?? null,
            'group_name' => $item['group'] ?? null,
            'region' => $finalRegion,
            'gol' => $item['gol'] ?? null,
            'position' => $item['jabatan'] ?? null,
            'tanggal' => $tgl !== '' ? $tgl : null,
            'instructor' => $item['instruktur'] ?? null,
            'internal_external' => $item['internal/eksternal'] ?? null,
            'level' => $item['level'] ?? null,
            'periode' => $item['periode'] ?? null,
            'bulan' => $bulan !== '' ? strtoupper(substr($bulan, 0, 3)) : null,
            'tahun' => $tahunVal,
            'jml_hari' => $item['jml hari'] ?? null,
            'total_jam' => $item['jam pelatihan'] ?? null,
            'notes' => $item['keterangan'] ?? null,
        ];
    }

    private function inferRowRegion(array $item): string
    {
        $rawRegion = $this->normalizeRegion($item['region'] ?? '');
        if ($rawRegion === 'riau' || $rawRegion === 'kalbar' || $rawRegion === 'kaltim') {
            return $rawRegion;
        }

        $text = strtoupper(trim(($item['nama pt'] ?? '').' '.($item['lokasi kerja'] ?? '').' '.($item['group'] ?? '')));
        if (preg_match('/\b(BDL|CAK|CAK1|CAK2|CAKB|CAKM|BPPJ|BPJJ|BPEJ|BSMJ|MKBB|MKB|MKB2)\b/', $text)) {
            return 'kaltim';
        }
        if (preg_match('/\b(USP|FAPE|LS|LS1|LS2|SMP|SMP1|SMP2|MKS|MKS1|MKS2|MKSK|KALE|PTLJ|BKP)\b/', $text)) {
            return 'kalbar';
        }
        if (preg_match('/\b(SAM|SAM1|SAM2|SIR|SIR1|SIR2|SAGM|PSA|SAL|CPK|ATS|ATS1|ATS2|CLP|GSI|PISP1|PISP2|MSSP|MSSP1|MII|PTR|ANJA|ANJAS|MCM|SMM|KTBM)\b/', $text)) {
            return 'riau';
        }

        return $rawRegion;
    }

    private function normalizeRegion($value): string
    {
        $v = strtolower(trim(str_replace(' fr', '', (string) $value)));
        if (str_contains($v, 'riau')) {
            return 'riau';
        }
        if (str_contains($v, 'kalbar')) {
            return 'kalbar';
        }
        if (str_contains($v, 'kaltim')) {
            return 'kaltim';
        }

        return $v;
    }
}
