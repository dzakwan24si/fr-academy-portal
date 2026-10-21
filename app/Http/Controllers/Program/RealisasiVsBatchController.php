<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\RealisasiVsBatch;
use App\Models\Program\TrainingRecord;
use App\Services\ExcelExporter;
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
        Cache::increment('rvb_cache_version');
        Cache::forget('rvb_available_years');

        return back()->with('success', 'Realisasi berhasil dibuat tanpa mengubah nilai hasil perhitungan.');
    }

    public function destroy($id)
    {
        RealisasiVsBatch::findOrFail($id)->delete();
        Cache::increment('rvb_cache_version');
        Cache::forget('rvb_available_years');

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
        Cache::increment('rvb_cache_version');
        Cache::forget('rvb_available_years');

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
        Cache::increment('rvb_cache_version');
        Cache::forget('rvb_available_years');

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
                $programs = $programs->concat(ProgramNonReguler::all()->map(fn ($program) => $this->programData($program, 'non_reguler', $year, $month)));
            }
        if ($type !== 'non_reguler') {
            $programs = $programs->concat(ProgramReguler::all()->map(fn ($program) => $this->programData($program, 'reguler', $year, $month)));
        }
        $programs = $programs->filter(fn ($program) => $program !== null);
        if ($region !== '') {
            $programs = $programs->filter(fn ($program) => strtolower(trim((string) $program['region'])) === strtolower($region));
        }
        if ($type === 'non_reguler' && $subKategori !== '') {
            $programs = $programs->filter(fn ($program) => strtolower(trim((string) ($program['sub_kategori'] ?? ''))) === strtolower($subKategori));
        }

        // Pre-filter TrainingRecords: fetch only records from the target window ($year-1 to $year, or null/0)
        $rawRecords = TrainingRecord::where('status', 'valid')
            ->where(function ($q) use ($year) {
                $q->whereBetween('tahun', [$year - 1, $year])
                    ->orWhereNull('tahun')
                    ->orWhere('tahun', 0);
            })
            ->get();

        // Pre-process relevant records: cache recordName, cleanWords, monthNum, region once
        $processedRecords = [];
        foreach ($rawRecords as $r) {
            $rMonth = $r->bulan ? $this->monthNumber($r->bulan) : null;
            if ($month !== null && $rMonth !== null && $rMonth > $month) {
                continue;
            }

            $name = strtolower(trim((string) $r->activityName()));
            $clean = preg_replace('/[^\w\s]/u', ' ', $name);
            $words = array_values(array_filter(explode(' ', $clean)));

            $processedRecords[] = (object) [
                'id' => $r->id,
                'program_type' => $r->program_type,
                'program_id' => (int) $r->program_id,
                'region' => strtolower(trim((string) ($r->region ?? ''))),
                'tahun' => (int) $r->tahun,
                'bulan' => $r->bulan,
                'monthNum' => $rMonth,
                'name' => $name,
                'clean' => $clean,
                'words' => $words,
                'firstTwoWords' => count($words) >= 2 ? $words[0].' '.$words[1] : ($words[0] ?? ''),
                'activityDates' => $r->activityDates(),
                'activity' => $r->activity(),
            ];
        }

        return $programs->map(function ($program) use ($year, $month, $processedRecords) {
            $progType = $program['program_type'];
            $progId = (int) $program['program_id'];
            $progName = strtolower(trim((string) ($program['program_name'] ?? '')));
            $cleanProg = preg_replace('/[^\w\s]/u', ' ', $progName);
            $progWords = array_values(array_filter(explode(' ', $cleanProg)));
            $firstTwoProg = implode(' ', array_slice($progWords, 0, 2));
            $isCarryOver = str_contains($progName, 'carry over');
            $progRegion = strtolower(trim((string) ($program['region'] ?? '')));
            $normProgRegion = $this->normalizeRegion($progRegion);

            $matchingCount = 0;

            foreach ($processedRecords as $r) {
                // 1. Check Region for non_reguler
                if ($progType === 'non_reguler' && $progRegion !== '') {
                    $recordRegion = $r->region;
                    if ($recordRegion === '') {
                        $act = $r->activity;
                        $recordRegion = strtolower(trim((string) ($act?->Region ?? $act?->region ?? '')));
                    }
                    if ($recordRegion !== '') {
                        if ($recordRegion !== $progRegion
                            && ! str_contains($recordRegion, $progRegion)
                            && ! str_contains($progRegion, $recordRegion)
                            && $this->normalizeRegion($recordRegion) !== $normProgRegion) {
                            continue;
                        }
                    }
                }

                // 2. Check Year & Month
                if ($r->tahun > 0 || $r->bulan) {
                    if ($r->tahun > 0 && $r->tahun !== $year) {
                        if (! ($isCarryOver && ($r->tahun === $year - 1 || $r->tahun === $year))) {
                            continue;
                        }
                    }
                    if ($r->bulan) {
                        if ($month !== null && ($r->monthNum === null || $r->monthNum > $month)) {
                            continue;
                        }
                    }
                } else {
                    $hasDate = false;
                    foreach ($r->activityDates as $d) {
                        if ($d && (int) date('Y', strtotime($d)) === $year && ($month === null || (int) date('n', strtotime($d)) <= $month)) {
                            $hasDate = true;
                            break;
                        }
                    }
                    if (! $hasDate) {
                        continue;
                    }
                }

                // 3. Match Activity
                $isMatch = false;
                if ($r->program_type === $progType && $r->program_id === $progId) {
                    $isMatch = true;
                } elseif ($r->name !== '' && $progName !== '') {
                    if ($r->name === $progName || str_starts_with($r->name, $progName) || str_starts_with($progName, $r->name)) {
                        $isMatch = true;
                    } elseif (! empty($r->words) && ! empty($progWords)) {
                        if (count($progWords) === 1) {
                            $isMatch = in_array($progWords[0], $r->words, true) || str_contains($r->clean, $progWords[0]);
                        } else {
                            if (count($r->words) >= 2 && $r->firstTwoWords === $firstTwoProg) {
                                $isMatch = true;
                            } elseif ($firstTwoProg !== '' && str_contains($r->clean, $firstTwoProg)) {
                                $isMatch = true;
                            }
                        }
                    }
                }

                if ($isMatch) {
                    $matchingCount++;
                }
            }

            $realizedParticipants = $matchingCount;
            if ($program['program_type'] === 'reguler') {
                $realizedBatch = $realizedParticipants > 0 ? 1 : 0;
            } else {
                $explicitBatch = (int) ($program['realisasi_batch'] ?? $program['total_batch'] ?? 0);
                $realizedBatch = $explicitBatch > 0 ? $explicitBatch : ($realizedParticipants > 0 ? 1 : 0);
            }

                $row = array_merge($program, ['realisasi_id' => null, 'realized_batch' => $realizedBatch, 'realized_participants' => $realizedParticipants]);
                $row['batch_percent'] = $this->percent($row['realized_batch'], $row['planned_batch']);
                $row['participant_percent'] = $this->percent($row['realized_participants'], $row['planned_participants']);

                return $row;
            })->values()->all();
        });

        return collect($cachedData)->map(fn ($item) => (object) $item);
    }

    public function updateBatch(Request $request)
    {
        $request->validate([
            'program_type' => 'required|in:non_reguler',
            'program_id' => 'required|integer',
            'realisasi_batch' => 'required|integer|min:0',
        ]);

        $id = $request->input('program_id');
        $val = (int) $request->input('realisasi_batch');

        $program = ProgramNonReguler::findOrFail($id);
        $program->update(['realisasi_batch' => $val]);
        ActivityLog::log('Edit', 'Realisasi Vs Target', "Mengubah realisasi batch program non-reguler '{$program->jenis_pelatihan}' menjadi {$val}");
        Cache::increment('rvb_cache_version');

        return response()->json(['message' => 'Realisasi batch berhasil diperbarui.', 'realisasi_batch' => $val]);
    }

    private function programData($program, string $type, int $year, ?int $month): ?array
    {
        $programYear = null;
        if ($type === 'reguler') {
            if ($program->Mulai_program) {
                $programYear = (int) date('Y', strtotime($program->Mulai_program));
            } elseif ($program->tahun) {
                $programYear = (int) $program->tahun;
            } elseif ($program->Selesai_program) {
                $programYear = (int) date('Y', strtotime($program->Selesai_program));
            }
        } else {
            if ($program->tahun) {
                $programYear = (int) $program->tahun;
            } elseif ($program->start_date) {
                $programYear = (int) date('Y', strtotime($program->start_date));
            } elseif ($program->end_date) {
                $programYear = (int) date('Y', strtotime($program->end_date));
            }
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
            'planned_batch' => $type === 'reguler' ? 1 : (int) ($program->TARGET_BATCH ?? $program->realisasi_batch ?? $program->total_batch ?? 0),
            'realisasi_batch' => $type === 'reguler' ? (int) ($program->Batch ?? 1) : (int) ($program->realisasi_batch ?? $program->total_batch ?? 0),
            'planned_participants' => $type === 'reguler' ? (int) $program->target_peserta : (int) ($program->TARGET_PESERTA ?? $program->target_total ?? 0),
        ];
    }

    public function availableYears(): array
    {
        return Cache::remember('rvb_available_years', 300, function () {
            $regYears = ProgramReguler::all()->map(function ($item) {
                if ($item->Mulai_program) {
                    return (int) $item->Mulai_program->format('Y');
                }
                if ($item->tahun) {
                    return (int) $item->tahun;
                }
                if ($item->Selesai_program) {
                    return (int) $item->Selesai_program->format('Y');
                }

                return null;
            });

            $nonRegYears = ProgramNonReguler::all()->map(function ($item) {
                if ($item->tahun) {
                    return (int) $item->tahun;
                }
                if ($item->start_date) {
                    return (int) $item->start_date->format('Y');
                }
                if ($item->end_date) {
                    return (int) $item->end_date->format('Y');
                }

                return null;
            });

            $rvbYears = RealisasiVsBatch::whereNotNull('tahun')->where('tahun', '>', 0)->distinct()->pluck('tahun')->map(fn ($v) => (int) $v);
            $trYears = TrainingRecord::whereNotNull('tahun')->where('tahun', '>', 0)->distinct()->pluck('tahun')->map(fn ($v) => (int) $v);

            return collect($regYears->all())
                ->merge($nonRegYears->all())
                ->merge($rvbYears->all())
                ->merge($trYears->all())
                ->merge([(int) date('Y')])
                ->filter()->map(fn ($v) => (int) $v)->unique()->sortDesc()->values()->all();
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
