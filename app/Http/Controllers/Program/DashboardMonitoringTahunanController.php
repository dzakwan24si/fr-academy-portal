<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use App\Services\RegionHelper;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardMonitoringTahunanController extends Controller
{
    public const MONTH_3_LETTERS = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
        5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
        9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
    ];

    public const MONTH_NAMES = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function extractYearFromField($val): ?int
    {
        if (empty($val)) {
            return null;
        }
        if ($val instanceof \DateTimeInterface) {
            return (int) $val->format('Y');
        }
        if (is_numeric($val) && (int) $val >= 2000 && (int) $val <= 2100) {
            return (int) $val;
        }
        if (is_array($val)) {
            foreach ($val as $item) {
                $y = $this->extractYearFromField($item);
                if ($y) {
                    return $y;
                }
            }

            return null;
        }
        if (is_string($val)) {
            $trimmed = trim($val);
            if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    return $this->extractYearFromField($decoded);
                }
            }
            if (preg_match('/\b(20\d{2})\b/', $trimmed, $m)) {
                return (int) $m[1];
            }
            $time = strtotime($trimmed);
            if ($time !== false && $time > 0) {
                return (int) date('Y', $time);
            }
        }

        return null;
    }

    public function resolveProgramRegulerYear($p): int
    {
        if ($p->Mulai_program && ($y = $this->extractYearFromField($p->Mulai_program))) {
            return $y;
        }
        if (! empty($p->tahun) && ($y = $this->extractYearFromField($p->tahun))) {
            return $y;
        }
        if (! empty($p->tanggal_pelaksanaan) && ($y = $this->extractYearFromField($p->tanggal_pelaksanaan))) {
            return $y;
        }
        if ($p->Selesai_program && ($y = $this->extractYearFromField($p->Selesai_program))) {
            return $y;
        }
        if ($p->created_at && ($y = $this->extractYearFromField($p->created_at))) {
            return $y;
        }

        return 2026;
    }

    public function resolveProgramNonRegulerYear($p): int
    {
        if (! empty($p->tahun) && ($y = $this->extractYearFromField($p->tahun))) {
            return $y;
        }
        if ($p->start_date && ($y = $this->extractYearFromField($p->start_date))) {
            return $y;
        }
        if (! empty($p->tanggal_pelaksanaan) && ($y = $this->extractYearFromField($p->tanggal_pelaksanaan))) {
            return $y;
        }
        if ($p->end_date && ($y = $this->extractYearFromField($p->end_date))) {
            return $y;
        }
        if ($p->created_at && ($y = $this->extractYearFromField($p->created_at))) {
            return $y;
        }

        return 2026;
    }

    public function index(Request $request)
    {
        $trYears = TrainingRecord::whereNotNull('tahun')->pluck('tahun')->map(fn ($y) => (int) $y)->filter()->all();
        $trDateYears = TrainingRecord::whereNotNull('training_date')->get()->map(fn ($r) => (int) date('Y', strtotime($r->training_date)))->filter()->all();
        $regYears = ProgramReguler::all()->map(fn ($p) => $this->resolveProgramRegulerYear($p))->filter()->all();
        $nonRegYears = ProgramNonReguler::all()->map(fn ($p) => $this->resolveProgramNonRegulerYear($p))->filter()->all();

        $years = collect([2024, 2025, 2026, 2027])
            ->merge($trYears)
            ->merge($trDateYears)
            ->merge($regYears)
            ->merge($nonRegYears)
            ->filter(fn ($y) => $y >= 2000 && $y <= 2100)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $yearDefault = in_array(2026, $years, true) ? 2026 : ($years[count($years) - 1] ?? (int) date('Y'));
        $year = (int) $request->input('year', $yearDefault);

        $tab = $request->input('tab', 'non_reguler'); // 'reguler' or 'non_reguler'
        $category = $request->input('category', 'all'); // 'all', 'staf', 'mandor', 'managerial', 'technical'
        $selectedProgramName = $request->input('program', 'all');
        $rawMonth = $request->input('month', 'all'); // 'all' or 1..12 or 'JAN'..'DES'
        $selectedRegion = trim((string) $request->input('region', 'all'));

        $selectedMonthNum = $this->parseMonthNumber($rawMonth); // null if 'all'
        $selectedMonthCode = $selectedMonthNum ? self::MONTH_3_LETTERS[$selectedMonthNum] : 'all';

        $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);

        // Fetch List of Available Regions (Standardized)
        $regRegions = ProgramReguler::pluck('Region');
        $nonRegRegions = ProgramNonReguler::pluck('region');
        $trRegions = TrainingRecord::pluck('region');
        $rawRegions = $regRegions->merge($nonRegRegions)->merge($trRegions);
        $regions = RegionHelper::getCanonicalRegions($rawRegions);

        // 1. Fetch Master Programs Filtered By Year
        $regulerAll = ProgramReguler::all()->filter(fn ($p) => $this->resolveProgramRegulerYear($p) === $year);
        $nonRegulerAll = ProgramNonReguler::all()->filter(fn ($p) => $this->resolveProgramNonRegulerYear($p) === $year);

        // 2. Fetch Records from TrainingRecord & Pre-filter for Current Tab, Year & Cumulative Month
        $allTrainingRecords = TrainingRecord::where('status', 'valid')->get();

        $normName = function ($str) {
            $str = strtolower(trim((string) $str));
            $str = preg_replace('/\s*\(.*?\)\s*/', '', $str);

            return preg_replace('/[^a-z0-9]/', '', $str);
        };

        $matchesProg = function ($rNorm, $bNorm, $rRaw, $bRaw) {
            if ($rNorm === $bNorm) {
                return true;
            }
            if (str_starts_with($rNorm, 'frmengajar') && (str_starts_with($bNorm, 'frmengajar') || str_contains($bRaw, 'fr mengajar'))) {
                return true;
            }

            $isRTot = (preg_match('/\b(tot)\b/i', $rRaw) || str_contains(strtolower($rRaw), 'training of trainers')) && ! str_contains(strtolower($rRaw), 'total productive maintenance');
            $isBTot = (preg_match('/\b(tot)\b/i', $bRaw) || str_contains(strtolower($bRaw), 'training of trainers')) && ! str_contains(strtolower($bRaw), 'total productive maintenance');
            if ($isRTot && $isBTot) {
                return true;
            }

            if (str_contains($rNorm, 'bearing') && str_contains($bNorm, 'bearing')) {
                return true;
            }
            if (str_contains($rNorm, 'mentoringasistantadmin') && str_contains($bNorm, 'mentoringasistantadmin')) {
                return true;
            }
            if ((str_contains($rNorm, 'klinikkerjamandor') || str_contains($rNorm, 'klinikkinerjamandor')) && str_contains($bNorm, 'klinikkerjamandor')) {
                return true;
            }
            if (str_contains($rNorm, 'pamongtpa') && str_contains($bNorm, 'pamongtpa')) {
                return true;
            }
            if (str_contains($rNorm, 'dumptruckmitsubi') && str_contains($bNorm, 'dumptruckmitsubi')) {
                return true;
            }
            if (str_contains($rNorm, 'analisapekerjaan') && str_contains($bNorm, 'analisapekerjaan')) {
                return true;
            }
            if (str_contains($rNorm, 'yieldmaking') && str_contains($bNorm, 'yieldmaking')) {
                return true;
            }

            return false;
        };

        // Filter training records by current tab, year, and cumulative month
        $filteredRecords = $allTrainingRecords->filter(function ($r) use ($tab, $year, $selectedMonthNum) {
            if ($r->program_type !== $tab) {
                return false;
            }

            // Year check
            $rYear = null;
            if (! empty($r->tahun)) {
                $rYear = (int) $r->tahun;
            } elseif (! empty($r->training_date)) {
                $rYear = (int) date('Y', strtotime($r->training_date));
            } elseif (method_exists($r, 'activityDates')) {
                $dates = $r->activityDates();
                foreach ($dates as $d) {
                    if ($d) {
                        $rYear = (int) date('Y', strtotime($d));
                        break;
                    }
                }
            } elseif (! empty($r->created_at)) {
                $rYear = (int) date('Y', strtotime($r->created_at));
            } else {
                $rYear = 2026;
            }

            if ($rYear !== $year) {
                $isCarryOver = ($tab === 'reguler' && $rYear === $year - 1 && (
                    str_contains(strtoupper((string) $r->notes), (string) $year)
                ));
                if (! $isCarryOver) {
                    return false;
                }
            }

            // Month cumulative check (Januari s/d Bulan Terpilih)
            if ($selectedMonthNum !== null) {
                $mNum = null;
                if ($r->bulan) {
                    $mNum = $this->parseMonthNumber($r->bulan);
                }
                if (! $mNum && $r->training_date) {
                    $mNum = (int) date('n', strtotime($r->training_date));
                }
                if (! $mNum && method_exists($r, 'activityDates')) {
                    foreach ($r->activityDates() as $d) {
                        if ($d) {
                            $mNum = (int) date('n', strtotime($d));
                            break;
                        }
                    }
                }

                if ($mNum !== null && $mNum > $selectedMonthNum) {
                    return false;
                }
            }

            return true;
        });

        // 3. Build Master Batches & Virtual Batches for Unbudgeted Programs
        $recordsByProgId = [];
        $unassignedRecords = [];
        foreach ($filteredRecords as $r) {
            if ($r->program_id) {
                $recordsByProgId[(int) $r->program_id][] = $r;
            } else {
                $unassignedRecords[] = $r;
            }
        }

        $masterBatches = collect();
        if ($tab === 'reguler') {
            $masterBatches = $regulerAll->map(fn ($item) => [
                'type' => 'reguler',
                'id' => $item->getKey(),
                'name' => trim((string) $item->Training_Program),
                'clean_name' => trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $item->Training_Program)),
                'batch' => 1,
                'target' => (int) $item->target_peserta,
                'region' => trim((string) $item->Region),
                'location' => $item->Lokasi_training ?: ($item->Region ?: '-'),
                'start' => $item->Mulai_program,
                'end' => $item->Selesai_program,
                'sub_category' => $item->jenis_program_reguler ?: 'Reguler',
                'kategori_pelatihan' => $item->jenis_program_reguler ?: 'Reguler',
                'model' => $item,
            ]);
        } else {
            $masterBatches = $nonRegulerAll->map(fn ($item) => [
                'type' => 'non_reguler',
                'id' => $item->getKey(),
                'name' => trim((string) $item->jenis_pelatihan),
                'clean_name' => trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $item->jenis_pelatihan)),
                'batch' => (int) ($item->TARGET_BATCH ?? $item->total_batch ?? 0),
                'target' => (int) ($item->TARGET_PESERTA ?? $item->target_total ?? 0),
                'region' => trim((string) $item->region),
                'location' => $item->lokasi_training ?: ($item->region ?: '-'),
                'start' => $item->start_date,
                'end' => $item->end_date,
                'sub_category' => $item->sub_kategori ?: 'Technical',
                'kategori_pelatihan' => $item->kategori_pelatihan ?: 'Technical',
                'model' => $item,
            ]);
        }

        // Match records to master batches
        $matchedUnassignedIds = [];
        $batchRecordsMap = [];

        foreach ($masterBatches as $batch) {
            $bId = (int) $batch['id'];
            $bNorm = $normName($batch['clean_name']);
            $bRaw = strtolower($batch['name']);
            $bRegion = $batch['region'];

            $list = $recordsByProgId[$bId] ?? [];

            foreach ($unassignedRecords as $r) {
                if (isset($matchedUnassignedIds[$r->id])) {
                    continue;
                }
                if ($bRegion !== '' && ! RegionHelper::matches($r->region, $bRegion)) {
                    continue;
                }

                $rNorm = $normName($tab === 'reguler' ? $r->notes : $r->nama_pelatihan);
                $rRaw = (string) ($tab === 'reguler' ? $r->notes : $r->nama_pelatihan);

                if ($matchesProg($rNorm, $bNorm, $rRaw, $bRaw)) {
                    $list[] = $r;
                    $matchedUnassignedIds[$r->id] = true;
                }
            }
            $batchRecordsMap[$bId] = $list;
        }

        // Collect remaining unbudgeted records and generate virtual batches for them
        $remainingUnbudget = [];
        foreach ($unassignedRecords as $r) {
            if (! isset($matchedUnassignedIds[$r->id])) {
                $remainingUnbudget[] = $r;
            }
        }

        $virtualBatches = collect();
        $vId = 90000;
        $groupedRemaining = collect($remainingUnbudget)->groupBy(fn ($r) => trim($r->nama_pelatihan ?: $r->notes ?: 'Program Unbudget').'|'.trim($r->region ?: ''));

        foreach ($groupedRemaining as $groupKey => $recs) {
            $first = $recs->first();
            $name = trim($first->nama_pelatihan ?: $first->notes ?: 'Program Unbudget');
            $reg = trim($first->region ?: '');
            $notes = trim($first->notes ?: '');

            $subCat = 'Technical';
            $katPel = 'Technical';
            $technicalNotes = ['ESTATE', 'MILL', 'TRACTION', 'TRAKSI', 'DOWNSTREAM', 'ADMINISTRASI', 'ADMIN'];
            if (strcasecmp($notes, 'softskill') === 0 || preg_match('/\b(tot)\b/i', $name) || str_contains(strtolower($name), 'leadership') || str_contains(strtolower($name), 'communication') || str_contains(strtolower($name), 'fr mengajar')) {
                $subCat = 'Softskill';
                $katPel = 'Managerial and Leadership';
            } elseif ($notes !== '') {
                $subCat = ucfirst(strtolower($notes));
            }

            $vId++;
            $firstRecDate = $recs->pluck('training_date')->filter()->first();
            $batchObj = (object) [
                'Batch' => $reg ? 'Batch '.$reg : 'Batch 1',
                'total_batch' => 1,
                'realisasi_batch' => 1,
            ];

            $virtualBatches->push([
                'type' => $tab,
                'id' => $vId,
                'name' => $name,
                'clean_name' => trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $name)),
                'batch' => 0,
                'target' => 0,
                'region' => $reg,
                'location' => $reg ?: '-',
                'start' => $firstRecDate,
                'end' => null,
                'sub_category' => $subCat,
                'kategori_pelatihan' => $katPel,
                'model' => $batchObj,
                'is_unbudget' => true,
            ]);
            $batchRecordsMap[$vId] = $recs->all();
        }

        $allBatches = $masterBatches->concat($virtualBatches);

        // Dynamic Counts for Category Cards (Gambar 2)
        $regulerTotalCount = $regulerAll->count();
        $regulerStafCount = $regulerAll->filter(fn ($p) => str_contains($cleanStr($p->jenis_program_reguler), 'staf'))->count();
        $regulerMandorCount = $regulerAll->filter(fn ($p) => str_contains($cleanStr($p->jenis_program_reguler), 'mandor'))->count();

        $nonRegulerBatches = ($tab === 'non_reguler') ? $allBatches : $nonRegulerAll->map(fn ($p) => [
            'kategori_pelatihan' => $p->kategori_pelatihan,
            'sub_category' => $p->sub_kategori,
        ]);
        $nonRegulerTotalCount = $nonRegulerBatches->count();
        $nonRegulerManagerialCount = $nonRegulerBatches->filter(fn ($p) => str_contains($cleanStr($p['kategori_pelatihan'] ?? ''), 'managerial') || str_contains($cleanStr($p['sub_category'] ?? ''), 'soft'))->count();
        $nonRegulerTechnicalCount = $nonRegulerBatches->filter(fn ($p) => str_contains($cleanStr($p['kategori_pelatihan'] ?? ''), 'technical') && ! str_contains($cleanStr($p['sub_category'] ?? ''), 'soft'))->count();

        $nonRegulerSubCategories = ['Softskill', 'Estate', 'Mill', 'Traksi', 'Administrasi', 'Downstream'];
        $subCategoryCounts = [];
        foreach ($nonRegulerSubCategories as $subCat) {
            $search = $cleanStr($subCat);
            $subCategoryCounts[$subCat] = $nonRegulerBatches->filter(function ($p) use ($search, $cleanStr) {
                return str_contains($cleanStr($p['sub_category'] ?? ''), $search)
                    || str_contains($cleanStr($p['kategori_pelatihan'] ?? ''), $search);
            })->count();
        }

        // Filter batches by Category
        $currentBatches = $allBatches;
        if ($tab === 'reguler') {
            if ($category === 'staf') {
                $currentBatches = $currentBatches->filter(fn ($p) => str_contains($cleanStr($p['sub_category']), 'staf'));
            } elseif ($category === 'mandor') {
                $currentBatches = $currentBatches->filter(fn ($p) => str_contains($cleanStr($p['sub_category']), 'mandor'));
            }
        } else {
            if ($category === 'managerial' || strcasecmp($category, 'softskill') === 0) {
                $currentBatches = $currentBatches->filter(fn ($p) => str_contains($cleanStr($p['kategori_pelatihan']), 'managerial') || str_contains($cleanStr($p['sub_category']), 'soft'));
            } elseif ($category === 'technical') {
                $currentBatches = $currentBatches->filter(fn ($p) => str_contains($cleanStr($p['kategori_pelatihan']), 'technical') && ! str_contains($cleanStr($p['sub_category']), 'soft'));
            } elseif ($category !== 'all' && $category !== '') {
                $searchCat = $cleanStr($category);
                $currentBatches = $currentBatches->filter(function ($p) use ($searchCat, $cleanStr) {
                    return str_contains($cleanStr($p['sub_category']), $searchCat)
                        || str_contains($cleanStr($p['kategori_pelatihan']), $searchCat)
                        || str_contains($cleanStr($p['name']), $searchCat);
                });
            }
        }

        // Filter currentBatches by Region keyword if selected
        if ($selectedRegion !== 'all' && $selectedRegion !== '') {
            $currentBatches = $currentBatches->filter(function ($b) use ($selectedRegion) {
                return RegionHelper::matches($b['region'] ?? '', $selectedRegion);
            });
        }

        // Group by distinct Program Name
        $programGroups = $currentBatches->groupBy('name')->map(function ($items, $name) use ($batchRecordsMap, $tab, $selectedRegion) {
            $first = $items->first();
            $related = $items->map(function ($batch) use ($batchRecordsMap, $tab, $selectedRegion) {
                $records = $batchRecordsMap[$batch['id']] ?? [];
                if ($selectedRegion !== 'all' && $selectedRegion !== '') {
                    $records = array_filter($records, fn ($r) => RegionHelper::matches($r->region, $selectedRegion));
                }

                if ($tab === 'reguler') {
                    $seen = [];
                    $uniqueCount = 0;
                    foreach ($records as $r) {
                        $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                        if (! isset($seen[$ident])) {
                            $seen[$ident] = true;
                            $uniqueCount++;
                        }
                    }
                    $actualParticipants = $uniqueCount;
                    $realizedBatch = $actualParticipants > 0 ? 1 : 0;
                } else {
                    $actualParticipants = count($records);
                    $explicitBatch = (int) ($batch['model']->realisasi_batch ?? $batch['model']->total_batch ?? 0);
                    $realizedBatch = $explicitBatch > 0 ? $explicitBatch : ($actualParticipants > 0 ? 1 : 0);
                }

                $periodStr = '- - -';
                if (! empty($batch['start'])) {
                    $periodStr = Carbon::parse($batch['start'])->format('d M Y');
                    if (! empty($batch['end'])) {
                        $periodStr .= ' – '.Carbon::parse($batch['end'])->format('d M Y');
                    }
                }

                return array_merge($batch, [
                    'actual_participants' => $actualParticipants,
                    'realized_batch' => $realizedBatch,
                    'period' => $periodStr,
                ]);
            });

            $targetBatch = $items->sum('batch');
            $targetParticipants = $items->sum('target');
            $realizedBatch = $related->sum('realized_batch');
            $realizedParticipants = $related->sum('actual_participants');

            $isUnbudget = ($targetParticipants === 0 && $realizedParticipants > 0) || ! empty($first['is_unbudget']) || str_contains(strtolower($name), 'unbudget');

            // If a virtual unbudget program has 0 realized participants in this filter, exclude it
            if (! empty($first['is_unbudget']) && $realizedParticipants === 0) {
                return null;
            }

            $participantPercent = $targetParticipants > 0
                ? round(($realizedParticipants / $targetParticipants) * 100)
                : ($isUnbudget ? 100 : 0);
            $batchPercent = $targetBatch > 0
                ? round(($realizedBatch / $targetBatch) * 100)
                : ($realizedBatch > 0 ? 100 : 0);

            return [
                'name' => $name,
                'type' => $first['type'],
                'target_batch' => $targetBatch,
                'realized_batch' => $realizedBatch,
                'batch_percent' => $batchPercent,
                'target_participants' => $targetParticipants,
                'realized_participants' => $realizedParticipants,
                'participant_percent' => $participantPercent,
                'is_unbudget' => $isUnbudget,
                'batches' => $related->values(),
            ];
        })->filter()->values();

        // Sort Programs nicely
        if ($tab === 'reguler') {
            $order = ['FAT', 'MAT', 'TAT', 'DMT', 'AAT', 'PMT', 'PKT'];
            $programGroups = $programGroups->sort(function ($a, $b) use ($order) {
                $prefixA = strtoupper(explode(' ', $a['name'])[0] ?? '');
                $prefixB = strtoupper(explode(' ', $b['name'])[0] ?? '');
                $idxA = array_search($prefixA, $order);
                $idxB = array_search($prefixB, $order);
                if ($idxA !== false && $idxB !== false) {
                    return $idxA - $idxB;
                }
                if ($idxA !== false) {
                    return -1;
                }
                if ($idxB !== false) {
                    return 1;
                }

                return strcmp($a['name'], $b['name']);
            })->values();
        } else {
            $programGroups = $programGroups->sortBy('name')->values();
        }

        // List of all program names for the Button Filter
        $allProgramButtons = $programGroups->pluck('name')->values()->all();

        // If a specific program is selected, filter to that program; otherwise show all
        $activePrograms = $programGroups;
        $isSingleProgram = false;
        $singleProgramData = null;

        if ($selectedProgramName !== 'all' && $selectedProgramName !== '') {
            $found = $programGroups->first(fn ($p) => strcasecmp($p['name'], $selectedProgramName) === 0);
            if ($found) {
                $activePrograms = collect([$found]);
                $isSingleProgram = true;
                $singleProgramData = $found;
            }
        }

        // 4. Calculate Aggregate Metrics for KPI Cards
        $totalTargetBatch = $activePrograms->sum('target_batch');
        $totalRealizedBatch = $activePrograms->sum('realized_batch');
        $batchPct = $totalTargetBatch > 0 ? round(($totalRealizedBatch / $totalTargetBatch) * 100) : ($totalRealizedBatch > 0 ? 100 : 0);

        $totalTargetPeserta = $activePrograms->sum('target_participants');
        $totalRealizedPeserta = $activePrograms->sum('realized_participants');
        $pesertaPct = $totalTargetPeserta > 0 ? round(($totalRealizedPeserta / $totalTargetPeserta) * 100) : ($totalRealizedPeserta > 0 ? 100 : 0);

        $kpi = [
            'target_batch' => $totalTargetBatch,
            'realized_batch' => $totalRealizedBatch,
            'batch_percentage' => $batchPct,
            'target_peserta' => $totalTargetPeserta,
            'realized_peserta' => $totalRealizedPeserta,
            'peserta_percentage' => $pesertaPct,
        ];

        // 5. Build Chart Data (Horizontal Bars: Peserta & Batch Modes)
        $chartLabels = [];
        $chartTargetsPeserta = [];
        $chartRealizationsPeserta = [];
        $chartPercentagesPeserta = [];
        $chartTargetsBatch = [];
        $chartRealizationsBatch = [];
        $chartPercentagesBatch = [];

        foreach ($activePrograms as $prog) {
            $chartLabels[] = $prog['name'];
            $chartTargetsPeserta[] = $prog['target_participants'];
            $chartRealizationsPeserta[] = $prog['realized_participants'];
            $chartPercentagesPeserta[] = $prog['participant_percent'];
            $chartTargetsBatch[] = $prog['target_batch'];
            $chartRealizationsBatch[] = $prog['realized_batch'];
            $chartPercentagesBatch[] = $prog['batch_percent'];
        }

        $chartData = [
            'labels' => $chartLabels,
            'peserta' => [
                'targets' => $chartTargetsPeserta,
                'realizations' => $chartRealizationsPeserta,
                'percentages' => $chartPercentagesPeserta,
            ],
            'batch' => [
                'targets' => $chartTargetsBatch,
                'realizations' => $chartRealizationsBatch,
                'percentages' => $chartPercentagesBatch,
            ],
            // Backward-compatibility keys
            'targets' => $chartTargetsPeserta,
            'realizations' => $chartRealizationsPeserta,
            'percentages' => $chartPercentagesPeserta,
        ];

        // Related Batches
        $relatedBatches = $activePrograms->flatMap(fn ($p) => $p['batches'])->values();

        // Dynamic Title for Header Banner
        $monthLabel = $selectedMonthNum ? ' (JAN - '.strtoupper(self::MONTH_3_LETTERS[$selectedMonthNum]).')' : '';
        $headerTitle = 'DASHBOARD PROGRESS PROGRAM '.strtoupper($tab === 'reguler' ? 'REGULER' : 'NON REGULER').$monthLabel.' '.$year;
        if ($isSingleProgram) {
            $headerTitle = 'DASHBOARD PROGRESS '.strtoupper($singleProgramData['name']).$monthLabel.' '.$year;
        } elseif ($category !== 'all') {
            $catLabel = match ($category) {
                'staf' => 'REGULER - STAF',
                'mandor' => 'REGULER - MANDOR',
                'managerial' => 'MANAGERIAL & LEADERSHIP',
                'technical' => 'TECHNICAL',
                default => strtoupper($category)
            };
            $headerTitle = 'DASHBOARD PROGRESS PROGRAM '.$catLabel.$monthLabel.' '.$year;
        }

        $monthList = self::MONTH_3_LETTERS;

        return view('Administrasi.Pages.Dashboard.dashboard_monitoring_tahunan', compact(
            'years',
            'year',
            'tab',
            'category',
            'selectedProgramName',
            'selectedMonthNum',
            'selectedMonthCode',
            'monthList',
            'regions',
            'selectedRegion',
            'headerTitle',
            'regulerTotalCount',
            'regulerStafCount',
            'regulerMandorCount',
            'nonRegulerTotalCount',
            'nonRegulerManagerialCount',
            'nonRegulerTechnicalCount',
            'nonRegulerSubCategories',
            'subCategoryCounts',
            'allProgramButtons',
            'programGroups',
            'activePrograms',
            'kpi',
            'chartData',
            'isSingleProgram',
            'singleProgramData',
            'relatedBatches'
        ));
    }

    public function parseMonthNumber($value): ?int
    {
        if ($value === null || $value === '' || $value === 'all') {
            return null;
        }
        if (is_numeric($value)) {
            $num = (int) $value;

            return ($num >= 1 && $num <= 12) ? $num : null;
        }

        $upper = strtoupper(substr(trim((string) $value), 0, 3));
        $map = [
            'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4,
            'MEI' => 5, 'MAY' => 5, 'JUN' => 6, 'JUL' => 7,
            'AGU' => 8, 'AGS' => 8, 'AUG' => 8, 'SEP' => 9,
            'OKT' => 10, 'OCT' => 10, 'NOV' => 11, 'DES' => 12, 'DEC' => 12,
        ];

        return $map[$upper] ?? null;
    }
}
