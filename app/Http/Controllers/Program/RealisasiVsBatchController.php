<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\RealisasiVsBatch;
use App\Models\Program\TrainingRecord;
use App\Services\ExcelExporter;
use App\Services\ProgramFilterHelper;
use App\Services\RegionHelper;
use App\Services\SpreadsheetReader;
use App\Services\TrainingMetricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RealisasiVsBatchController extends Controller
{
    public const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public const MONTH_3_LETTERS = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
        5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
        9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
    ];

    public function index(Request $request)
    {
        $availableYears = $this->availableYears();
        $year = (int) $request->input('year', $availableYears[0] ?? date('Y'));
        $rawMonth = $request->input('month');
        $isAllMonths = ($rawMonth === 'all' || strcasecmp((string) $rawMonth, 'semua') === 0 || strcasecmp((string) $rawMonth, 'semua bulan') === 0);
        $month = $isAllMonths ? null : ($this->monthNumber($rawMonth) ?? (int) date('n'));
        $monthCode = $isAllMonths ? 'all' : self::MONTH_3_LETTERS[$month];
        $type = $request->input('type', 'all');
        $region = trim((string) $request->input('region', ''));
        $subKategori = trim((string) $request->input('sub_kategori', ''));

        $rows = $this->buildRows($year, $month, $type, $region, $subKategori);
        $regions = collect(Cache::remember('rvb_regions', 300, function () {
            return ProgramNonReguler::pluck('region')->merge(ProgramReguler::pluck('Region'))->filter()->map(fn ($value) => trim((string) $value))->unique()->sort()->values()->all();
        }));
        $subCategories = collect(Cache::remember('rvb_sub_categories', 300, function () {
            return ProgramNonReguler::whereNotNull('sub_kategori')->where('sub_kategori', '!=', '')->distinct()->pluck('sub_kategori')->filter()->map(fn ($value) => trim((string) $value))->unique()->sort()->values()->all();
        }));
        $activities = collect(Cache::remember('rvb_activities', 300, function () {
            return ProgramReguler::select('Training_Program', 'Program_reguler_id')->get()->map(fn ($item) => ['type' => 'reguler', 'id' => $item->getKey(), 'name' => $item->Training_Program])->concat(ProgramNonReguler::select('jenis_pelatihan', 'region', 'id_nonreguler')->get()->map(fn ($item) => ['type' => 'non_reguler', 'id' => $item->getKey(), 'name' => $item->jenis_pelatihan.' - '.$item->region]))->values()->all();
        }));
        $summary = [
            'planned_batch' => $rows->sum('planned_batch'),
            'realized_batch' => $rows->sum('realized_batch'),
            'planned_participants' => $rows->sum('planned_participants'),
            'realized_participants' => $rows->sum('realized_participants'),
        ];
        $summary['batch_percent'] = $this->percent($summary['realized_batch'], $summary['planned_batch']);
        $summary['participant_percent'] = $this->percent($summary['realized_participants'], $summary['planned_participants']);

        return view('Administrasi.Pages.Program.realisasi_vs_batch', compact('rows', 'summary', 'year', 'month', 'monthCode', 'isAllMonths', 'type', 'region', 'regions', 'subKategori', 'subCategories', 'activities', 'availableYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_type' => 'required|in:reguler,non_reguler',
            'program_id' => 'required|integer',
            'tahun' => 'required|integer|min:2000|max:2099',
            'bulan' => 'required',
            'legacy_realized_batch' => 'nullable|integer|min:0',
            'legacy_realized_participants' => 'nullable|integer|min:0',
        ]);
        $month = $this->monthNumber($data['bulan']) ?? 1;
        RealisasiVsBatch::updateOrCreate(
            ['program_type' => $data['program_type'], 'program_id' => $data['program_id'], 'tahun' => $data['tahun'], 'bulan' => $month],
            ['legacy_realized_batch' => $data['legacy_realized_batch'] ?? 0, 'legacy_realized_participants' => $data['legacy_realized_participants'] ?? 0]
        );
        $this->clearRvbCache();

        return back()->with('success', 'Realisasi berhasil dibuat tanpa mengubah nilai hasil perhitungan.');
    }

    public function destroy($id)
    {
        RealisasiVsBatch::findOrFail($id)->delete();
        $this->clearRvbCache();

        return back()->with('success', 'Data realisasi dihapus.');
    }

    public function destroyAll()
    {
        $count = RealisasiVsBatch::count();
        RealisasiVsBatch::truncate();

        ActivityLog::log(
            'Hapus',
            'Realisasi Vs Target',
            "Menghapus semua data realisasi vs batch ({$count} data)",
            ['count' => $count]
        );
        $this->clearRvbCache();

        return back()->with('success', "Semua data realisasi vs batch ({$count} data) berhasil dihapus.");
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx|max:20480']);
        $rows = SpreadsheetReader::read($request->file('file')->getRealPath(), $request->file('file')->getClientOriginalExtension());
        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'File tidak memiliki data.']);
        }
        $headers = array_map(function ($value) {
            return trim((string) $value, " \t\r\n\xEF\xBB\xBF");
        }, array_shift($rows));
        $type = in_array('id_nonreguler', $headers, true) ? 'non_reguler' : (in_array('Program_id', $headers, true) ? 'reguler' : null);
        if (! $type || ! in_array('bulan', $headers, true)) {
            return back()->withErrors(['file' => 'Header Realisasi tidak dikenali.']);
        }

        $imported = 0;
        $skipped = 0;
        DB::transaction(function () use ($rows, $headers, $type, &$imported, &$skipped) {
            foreach ($rows as $row) {
                if (! is_array($row) || count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }
                $values = array_pad($row, count($headers), null);
                $item = array_combine($headers, array_slice($values, 0, count($headers)));
                $idValue = $item[$type === 'non_reguler' ? 'id_nonreguler' : 'Program_id'] ?? null;
                $id = is_numeric($idValue) ? (int) $idValue : 0;
                $month = $this->monthNumber($item['bulan'] ?? null);
                if (! $id || ! $month) {
                    $skipped++;

                    continue;
                }
                $yearValue = $item['tahun'] ?? date('Y');
                $year = is_numeric($yearValue) ? (int) $yearValue : (int) date('Y');
                RealisasiVsBatch::updateOrCreate(
                    ['program_type' => $type, 'program_id' => $id, 'tahun' => $year, 'bulan' => $month],
                    ['legacy_realized_batch' => $this->number($item['REALISASI_BATCH'] ?? null), 'legacy_realized_participants' => $this->number($item['REALISASI_PESERTA'] ?? null)]
                );
                $imported++;
            }
        });

        ActivityLog::log(
            'Import',
            'Realisasi Vs Target',
            "Mengimport {$imported} data realisasi vs target dari file ".$request->file('file')->getClientOriginalName(),
            ['filename' => $request->file('file')->getClientOriginalName(), 'count' => $imported, 'skipped' => $skipped]
        );
        $this->clearRvbCache();

        return back()->with('success', "{$imported} data realisasi berhasil disimpan".($skipped ? ". {$skipped} baris dilewati karena ID atau bulan tidak valid" : '').'. Nilai akan digantikan otomatis saat Training Record tersedia.');
    }

    public function export(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $rawMonth = $request->input('month');
        $isAllMonths = ($rawMonth === 'all' || strcasecmp((string) $rawMonth, 'semua') === 0 || strcasecmp((string) $rawMonth, 'semua bulan') === 0);
        $month = $isAllMonths ? null : ($this->monthNumber($rawMonth) ?? (int) date('n'));
        $type = $request->input('type', 'all');
        $region = trim((string) $request->input('region', ''));
        $subKategori = trim((string) $request->input('sub_kategori', ''));
        $rows = $this->buildRows($year, $month, $type, $region, $subKategori);

        $headers = ['Realisasi ID', 'Batch ID', 'Tipe', 'Sub Kategori', 'Region', 'Bulan', 'Planned Batch', 'Realisasi Batch', '% Batch', 'Planned Peserta', 'Realisasi Peserta', '% Peserta'];
        $dataRows = [];
        $monthLabel = $isAllMonths ? 'Jan - Des' : ($month ? self::MONTH_3_LETTERS[$month] : 'Jan - Des');
        foreach ($rows as $row) {
            $dataRows[] = [
                $row->realisasi_id,
                $row->batch_id,
                $row->program_type,
                $row->sub_kategori ?? '-',
                $row->region,
                $monthLabel,
                $row->planned_batch,
                $row->realized_batch,
                $row->batch_percent,
                $row->planned_participants,
                $row->realized_participants,
                $row->participant_percent,
            ];
        }

        $filename = 'realisasi_vs_batch_'.$year.'_'.($isAllMonths ? 'Semua_Bulan' : self::MONTH_3_LETTERS[$month]).'.xlsx';

        return ExcelExporter::download($filename, $headers, $dataRows, 'Realisasi Vs Batch');
    }

    private function buildRows(int $year, ?int $month, string $type, string $region = '', string $subKategori = '')
    {
        $v = (int) Cache::get('rvb_cache_version', 1);
        $cacheKey = "rvb_rows_v{$v}_{$year}_{$month}_{$type}_{$region}_{$subKategori}";

        $cachedData = Cache::remember($cacheKey, 180, function () use ($year, $month, $type, $region, $subKategori) {
            $programs = collect();
            if ($type !== 'reguler') {
                $programs = $programs->concat(
                    ProgramNonReguler::all()->map(fn ($program) => $this->programData($program, 'non_reguler', $year, $month))
                );
            }
            if ($type !== 'non_reguler') {
                $programs = $programs->concat(
                    ProgramReguler::all()
                        ->filter(fn ($p) => ! $p->isMandor())
                        ->map(fn ($program) => $this->programData($program, 'reguler', $year, $month))
                );
            }
            $programs = $programs->filter(fn ($program) => $program !== null);
            if ($region !== '') {
                $programs = $programs->filter(fn ($program) => strtolower(trim((string) $program['region'])) === strtolower($region));
            }
            if ($type === 'non_reguler' && $subKategori !== '') {
                $programs = $programs->filter(fn ($program) => strtolower(trim((string) ($program['sub_kategori'] ?? ''))) === strtolower($subKategori));
            }

            // Training records filtered by year and cumulative month
            $allTrainingRecords = TrainingRecord::where('status', 'valid')->get();
            $filteredRecords = $allTrainingRecords->filter(function ($r) use ($year, $month) {
                $rYear = null;
                if (! empty($r->tahun) && is_numeric($r->tahun)) {
                    $rYear = (int) $r->tahun;
                } elseif (! empty($r->training_date)) {
                    $rYear = (int) date('Y', strtotime($r->training_date));
                } elseif (! empty($r->created_at)) {
                    $rYear = (int) date('Y', strtotime($r->created_at));
                } else {
                    $rYear = 2026;
                }

                if ($rYear !== $year) {
                    return false;
                }

                if ($month !== null) {
                    $mNum = null;
                    if ($r->bulan) {
                        $mNum = $this->monthNumber($r->bulan);
                    }
                    if (! $mNum && $r->training_date) {
                        $mNum = (int) date('n', strtotime($r->training_date));
                    }
                    if ($mNum !== null && $mNum > $month) {
                        return false;
                    }
                }

                return true;
            });

            $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);
            $normName = function ($str) {
                $str = strtolower(trim((string) $str));
                $str = preg_replace('/\s*\(.*?\)\s*/', '', $str);

                return preg_replace('/[^a-z0-9]/', '', $str);
            };

            $matchesProg = function ($rNorm, $bNorm, $rRaw, $bRaw) {
                if ($rNorm === $bNorm) return true;
                if (str_starts_with($rNorm, 'frmengajar') && (str_starts_with($bNorm, 'frmengajar') || str_contains($bRaw, 'fr mengajar'))) return true;
                $isRTot = (preg_match('/\b(tot)\b/i', $rRaw) || str_contains(strtolower($rRaw), 'training of trainers')) && ! str_contains(strtolower($rRaw), 'total productive maintenance');
                $isBTot = (preg_match('/\b(tot)\b/i', $bRaw) || str_contains(strtolower($bRaw), 'training of trainers')) && ! str_contains(strtolower($bRaw), 'total productive maintenance');
                if ($isRTot && $isBTot) return true;
                if (str_contains($rNorm, 'bearing') && str_contains($bNorm, 'bearing')) return true;
                if (str_contains($rNorm, 'mentoringasistantadmin') && str_contains($bNorm, 'mentoringasistantadmin')) return true;
                if ((str_contains($rNorm, 'klinikkerjamandor') || str_contains($rNorm, 'klinikkinerjamandor')) && str_contains($bNorm, 'klinikkerjamandor')) return true;
                if (str_contains($rNorm, 'pamongtpa') && str_contains($bNorm, 'pamongtpa')) return true;
                if (str_contains($rNorm, 'dumptruckmitsubi') && str_contains($bNorm, 'dumptruckmitsubi')) return true;
                if (str_contains($rNorm, 'analisapekerjaan') && str_contains($bNorm, 'analisapekerjaan')) return true;
                if (str_contains($rNorm, 'yieldmaking') && str_contains($bNorm, 'yieldmaking')) return true;
                return false;
            };

            // Reguler records
            $regRecords = $filteredRecords->where('program_type', 'reguler');

            // Non-reguler matching
            $nonRegRecords = $filteredRecords->where('program_type', 'non_reguler');
            $nonRegMasterIds = array_flip($programs->where('program_type', 'non_reguler')->pluck('program_id')->all());

            $nonRegRecordsByProgId = [];
            $unassignedNonReg = [];
            foreach ($nonRegRecords as $r) {
                $pId = $r->program_id ? (int) $r->program_id : null;
                if ($pId && isset($nonRegMasterIds[$pId])) {
                    $nonRegRecordsByProgId[$pId][] = $r;
                } else {
                    $unassignedNonReg[] = $r;
                }
            }

            $matchedUnassignedNonReg = [];
            $nonRegRecordsMap = [];
            foreach ($programs->where('program_type', 'non_reguler') as $p) {
                $bId = (int) $p['program_id'];
                $bName = trim((string) $p['program_name']);
                $bNorm = $normName($bName);
                $bRaw = strtolower($bName);
                $bRegion = trim((string) $p['region']);

                $list = $nonRegRecordsByProgId[$bId] ?? [];
                foreach ($unassignedNonReg as $r) {
                    if (isset($matchedUnassignedNonReg[$r->id])) continue;
                    if ($bRegion !== '' && ! RegionHelper::matches($r->region, $bRegion)) continue;
                    $rNorm = $normName($r->nama_pelatihan);
                    $rRaw = (string) $r->nama_pelatihan;

                    if ($matchesProg($rNorm, $bNorm, $rRaw, $bRaw)) {
                        $list[] = $r;
                        $matchedUnassignedNonReg[$r->id] = true;
                    }
                }
                $nonRegRecordsMap[$bId] = $list;
            }

            return $programs->map(function ($program) use ($regRecords, $nonRegRecordsMap, $cleanStr) {
                $progType = $program['program_type'];
                $progId = (int) $program['program_id'];

                if ($progType === 'reguler') {
                    $pNameNorm = strtolower($cleanStr(preg_replace('/\s*\(.*?\)\s*/', '', $program['program_name'])));
                    $pRecs = $regRecords->filter(function ($r) use ($progId, $pNameNorm, $cleanStr) {
                        if ((int) $r->program_id === $progId) return true;
                        if (! $r->program_id) {
                            $notes = strtolower($cleanStr($r->notes ?? ''));
                            $nama = strtolower($cleanStr($r->nama_pelatihan ?? ''));
                            return str_contains($notes, $pNameNorm) || str_contains($nama, $pNameNorm);
                        }
                        return false;
                    });

                    $seen = [];
                    $uniqueCount = 0;
                    foreach ($pRecs as $r) {
                        $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                        if (! isset($seen[$ident])) {
                            $seen[$ident] = true;
                            $uniqueCount++;
                        }
                    }
                    $realizedParticipants = $uniqueCount;
                    $explicitBatch = (int) ($program['realisasi_batch'] ?? 0);
                    $realizedBatch = $explicitBatch > 0 ? $explicitBatch : ($realizedParticipants > 0 ? 1 : 0);
                } else {
                    $records = $nonRegRecordsMap[$progId] ?? [];
                    $realizedParticipants = count($records);
                    $explicitBatch = (int) ($program['realisasi_batch'] ?? 0);
                    $realizedBatch = $explicitBatch > 0 ? $explicitBatch : ($realizedParticipants > 0 ? 1 : 0);
                }

                $row = array_merge($program, [
                    'realisasi_id' => null,
                    'realized_batch' => $realizedBatch,
                    'realized_participants' => $realizedParticipants,
                ]);
                $row['batch_percent'] = $this->percent($row['realized_batch'], $row['planned_batch']);
                $row['participant_percent'] = $this->percent($row['realized_participants'], $row['planned_participants']);

                return $row;
            })->values()->all();
        });

        return collect($cachedData)->map(fn ($item) => (object) $item);
    }

    private function clearRvbCache(): void
    {
        TrainingMetricService::clearAllDashboardCaches();
    }

    public function updateBatch(Request $request)
    {
        $request->validate([
            'program_type' => 'required|in:reguler,non_reguler',
            'program_id' => 'required|integer',
            'realisasi_batch' => 'required|integer|min:0',
        ]);

        $type = $request->input('program_type');
        $id = $request->input('program_id');
        $val = (int) $request->input('realisasi_batch');

        if ($type === 'non_reguler') {
            $program = ProgramNonReguler::findOrFail($id);
            $program->update(['realisasi_batch' => $val]);
            ActivityLog::log('Edit', 'Realisasi Vs Target', "Mengubah realisasi batch program non-reguler '{$program->jenis_pelatihan}' menjadi {$val}");
        } else {
            $program = ProgramReguler::findOrFail($id);
            $program->update(['Batch' => $val]);
            ActivityLog::log('Edit', 'Realisasi Vs Target', "Mengubah realisasi batch program reguler '{$program->Training_Program}' menjadi {$val}");
        }

        $this->clearRvbCache();

        return response()->json(['message' => 'Realisasi batch berhasil diperbarui.', 'realisasi_batch' => $val]);
    }

    private function programData($program, string $type, int $year, ?int $month): ?array
    {
        $programYear = null;
        if ($type === 'reguler') {
            $programYear = (! empty($program->tahun) && is_numeric($program->tahun)) ? (int) $program->tahun : TrainingMetricService::resolveProgramRegulerYear($program);
        } else {
            $programYear = (! empty($program->tahun) && is_numeric($program->tahun)) ? (int) $program->tahun : TrainingMetricService::resolveProgramNonRegulerYear($program);
        }

        if ($programYear !== null && $programYear !== $year) {
            return null;
        }

        $dates = $program->tanggal_pelaksanaan ?: [];
        if (! $dates) {
            $dates = $type === 'reguler' ? [$program->Mulai_program, $program->Selesai_program] : [$program->start_date, $program->end_date];
        }

        $hasMonthDates = collect($dates)->filter()->isNotEmpty();
        if ($hasMonthDates && $month !== null) {
            $inMonth = collect($dates)->contains(fn ($date) => $date && (int) date('n', strtotime($date)) <= $month);
            if (! $inMonth && $type === 'reguler') {
                return null;
            }
        }

        return [
            'program_type' => $type,
            'program_id' => $program->getKey(),
            'batch_id' => $program->getKey(),
            'program_name' => $type === 'reguler' ? $program->Training_Program : $program->jenis_pelatihan,
            'region' => $type === 'reguler' ? $program->Region : $program->region,
            'sub_kategori' => $type === 'non_reguler' ? ($program->sub_kategori ?? '') : null,
            'planned_batch' => $type === 'reguler' ? 1 : (int) ($program->TARGET_BATCH ?? $program->total_batch ?? 0),
            'realisasi_batch' => $type === 'reguler' ? (int) ($program->Batch ?? 0) : (int) ($program->realisasi_batch ?? 0),
            'planned_participants' => $type === 'reguler' ? (int) $program->target_peserta : (int) ($program->TARGET_PESERTA ?? $program->target_total ?? 0),
        ];
    }

    public function availableYears(): array
    {
        return Cache::remember('rvb_available_years', 300, function () {
            return ProgramFilterHelper::getAvailableYears();
        });
    }

    private function recordMatchesActivity(TrainingRecord $record, array $program): bool
    {
        if ($record->program_type === $program['program_type'] && (int) $record->program_id === (int) $program['program_id']) {
            return true;
        }

        $recordName = strtolower(trim((string) $record->activityName()));
        $programName = strtolower(trim((string) ($program['program_name'] ?? '')));
        if ($recordName === '' || $programName === '') {
            return false;
        }

        if ($recordName === $programName
            || str_starts_with($recordName, $programName)
            || str_starts_with($programName, $recordName)) {
            return true;
        }

        $cleanRecord = preg_replace('/[^\w\s]/u', ' ', $recordName);
        $cleanProgram = preg_replace('/[^\w\s]/u', ' ', $programName);

        $recordWords = array_values(array_filter(explode(' ', $cleanRecord)));
        $programWords = array_values(array_filter(explode(' ', $cleanProgram)));

        if (empty($recordWords) || empty($programWords)) {
            return false;
        }

        // If program is 1 word (e.g. "Supervisory", "Damkarhutlabun")
        if (count($programWords) === 1) {
            return in_array($programWords[0], $recordWords, true) || str_contains($cleanRecord, $programWords[0]);
        }

        // If program is 2 or more words
        if (count($recordWords) >= 2) {
            if ($recordWords[0] === $programWords[0] && $recordWords[1] === $programWords[1]) {
                return true;
            }
        }

        $firstTwoProgramWords = implode(' ', array_slice($programWords, 0, 2));
        if (str_contains($cleanRecord, $firstTwoProgramWords)) {
            return true;
        }

        return false;
    }

    private function recordMatchesRegion(TrainingRecord $record, ?string $programRegion): bool
    {
        $programRegion = strtolower(trim((string) $programRegion));
        if ($programRegion === '') {
            return true;
        }

        return $this->normalizeRegion($record->region) === $this->normalizeRegion($programRegion);
    }

    private function normalizeRegion($value): string
    {
        return strtolower(trim(str_replace(' fr', '', (string) $value)));
    }

    private function percent(int $value, int $total): float
    {
        return $total > 0 ? round($value / $total * 100, 2) : 0;
    }

    private function number($value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    public function monthNumber($value): ?int
    {
        return TrainingMetricService::parseMonthNumber($value);
    }
}
