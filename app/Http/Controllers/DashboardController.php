<?php

namespace App\Http\Controllers;

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public const MONTH_3_LETTERS = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
        5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
        9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
    ];

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
        if (! empty($p->tahun) && ($y = $this->extractYearFromField($p->tahun))) {
            return $y;
        }
        if ($p->Mulai_program && ($y = $this->extractYearFromField($p->Mulai_program))) {
            return $y;
        }
        $title = $p->Training_Program ?? $p->Nama_Program_reguler ?? '';
        if (preg_match('/\b(20\d{2})\b/', $title, $m)) {
            return (int) $m[1];
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

    public function getProgramRegulerStartDate($p): ?string
    {
        if (! empty($p->tanggal_pelaksanaan)) {
            $dates = is_array($p->tanggal_pelaksanaan) ? $p->tanggal_pelaksanaan : json_decode($p->tanggal_pelaksanaan, true);
            if (is_array($dates)) {
                $validDates = array_values(array_filter($dates));
                if (! empty($validDates)) {
                    sort($validDates);

                    return $validDates[0];
                }
            }
        }

        if ($p->Mulai_program) {
            return $p->Mulai_program instanceof \DateTimeInterface
                ? $p->Mulai_program->format('Y-m-d')
                : date('Y-m-d', strtotime($p->Mulai_program));
        }

        return null;
    }

    public function resolveProgramNonRegulerYear($p): int
    {
        if (! empty($p->tahun) && ($y = $this->extractYearFromField($p->tahun))) {
            return $y;
        }
        if ($p->start_date && ($y = $this->extractYearFromField($p->start_date))) {
            return $y;
        }
        $title = $p->jenis_pelatihan ?? '';
        if (preg_match('/\b(20\d{2})\b/', $title, $m)) {
            return (int) $m[1];
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

    protected function filterRegulerByYear($programs, int $year)
    {
        return $programs->filter(function ($p) use ($year) {
            return $this->resolveProgramRegulerYear($p) === $year;
        });
    }

    protected function filterNonRegulerByYear($programs, int $year)
    {
        return $programs->filter(function ($p) use ($year) {
            return $this->resolveProgramNonRegulerYear($p) === $year;
        });
    }

    protected function filterRecordsByYearAndCumulativeMonth($records, int $year, ?int $selectedMonthNum, array $validRegulerProgIds = [])
    {
        return $records->filter(function ($r) use ($year, $selectedMonthNum, $validRegulerProgIds) {
            // 1. Year filter
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
                $isCarryOver = ($r->program_type === 'reguler' && (
                    (! empty($validRegulerProgIds) && $r->program_id && in_array((int) $r->program_id, $validRegulerProgIds, true))
                    || str_contains(strtoupper((string) $r->notes), (string) $year)
                ));
                if (! $isCarryOver) {
                    return false;
                }
            }

            // 2. Month cumulative filter
            if ($selectedMonthNum !== null) {
                $mNum = null;
                if ($r->bulan) {
                    $mNum = $this->parseMonthNumber($r->bulan);
                }
                if (! $mNum && $r->training_date) {
                    $mNum = (int) date('n', strtotime($r->training_date));
                }
                if (! $mNum && method_exists($r, 'activityDates')) {
                    $dates = $r->activityDates();
                    foreach ($dates as $d) {
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
    }

    public function index(Request $request)
    {
        $trYears = TrainingRecord::whereNotNull('tahun')->distinct()->pluck('tahun')->map(fn ($y) => (int) $y)->filter()->all();
        $regYears = ProgramReguler::all()->map(fn ($p) => $this->resolveProgramRegulerYear($p))->filter()->all();
        $nonRegYears = ProgramNonReguler::all()->map(fn ($p) => $this->resolveProgramNonRegulerYear($p))->filter()->all();

        $years = collect([2024, 2025, 2026, 2027])
            ->merge($trYears)
            ->merge($regYears)
            ->merge($nonRegYears)
            ->filter(fn ($y) => $y >= 2000 && $y <= 2100)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $yearDefault = in_array(2026, $years, true) ? 2026 : ($years[count($years) - 1] ?? (int) date('Y'));
        $year = (int) $request->input('year', $yearDefault);
        $rawMonth = $request->input('month', 'all');
        $selectedMonthNum = $this->parseMonthNumber($rawMonth);
        $selectedMonthCode = $selectedMonthNum ? self::MONTH_3_LETTERS[$selectedMonthNum] : 'all';
        $monthList = self::MONTH_3_LETTERS;

        // Pre-fetch programs and records for this year to avoid repeated full table scans
        $regulerAll = ProgramReguler::all();
        $nonRegulerAll = ProgramNonReguler::all();
        $regulerYear = $this->filterRegulerByYear($regulerAll, $year)->filter(fn ($p) => ! $p->isMandor());
        $nonRegulerYear = $this->filterNonRegulerByYear($nonRegulerAll, $year);

        // Pre-fetch TrainingRecords for the year (with carry-over for reguler if applicable)
        $regulerYearIds = $regulerYear->pluck('Program_reguler_id')->map(fn ($id) => (int) $id)->all();
        $yearRecords = TrainingRecord::where(function ($q) use ($year, $regulerYearIds) {
            $q->where('tahun', $year)
                ->orWhereYear('training_date', $year)
                ->orWhereYear('created_at', $year)
                ->orWhere(function ($sub) use ($year, $regulerYearIds) {
                    $sub->where('program_type', 'reguler')
                        ->where(function ($sub2) use ($year, $regulerYearIds) {
                            if (! empty($regulerYearIds)) {
                                $sub2->whereIn('program_id', $regulerYearIds);
                            }
                            $sub2->orWhere('notes', 'like', "%{$year}%");
                        });
                });
        })->get();
        // Fallback: if there are unpopulated tahun/date records, ensure all filtered properly
        $records = $this->filterRecordsByYearAndCumulativeMonth($yearRecords, $year, $selectedMonthNum, $regulerYearIds);

        // 1. REKAP TRAINING REPORT (Reguler, Softskill, Technical)
        $rekapTraining = $this->computeRekapTrainingReport($year, $selectedMonthNum, $regulerYear, $nonRegulerYear, $records);

        // 2. MONITORING TRAINEE UNDER 2 YEARS
        $traineeUnder2Years = $this->computeTraineeUnder2Years($year);

        // 3. MONITORING KARIR LULUSAN FRLC
        $karirLulusan = $this->computeKarirLulusan($year);

        // 4. STAFF FRESH GRADUATE (Asisten Trainee)
        $staffFreshGraduate = $this->computeStaffFreshGraduate($year, $selectedMonthNum, $regulerYear, $records);

        // 5. PROGRAM SOFTSKILL BY REGION
        $softskillRegional = $this->computeSoftskillRegional($year, $selectedMonthNum, $nonRegulerYear, $records);

        // 6. PROGRAM NON-REGULER TEKNIKAL BY CATEGORY & REGION
        $teknikalBreakdown = $this->computeTeknikalBreakdown($year, $selectedMonthNum, $nonRegulerYear, $records);

        return view('Administrasi.Pages.Dashboard.dashboard', compact(
            'year',
            'years',
            'rawMonth',
            'selectedMonthNum',
            'selectedMonthCode',
            'monthList',
            'rekapTraining',
            'traineeUnder2Years',
            'karirLulusan',
            'staffFreshGraduate',
            'softskillRegional',
            'teknikalBreakdown'
        ));
    }

    protected function computeRekapTrainingReport(int $year, ?int $selectedMonthNum = null, $regulerPrograms = null, $nonRegulerPrograms = null, $records = null): array
    {
        $regulerPrograms = $regulerPrograms ?? $this->filterRegulerByYear(ProgramReguler::all(), $year);
        // Exclude Mandor programs from Dashboard MR (only count Staf)
        $regulerPrograms = $regulerPrograms->filter(fn ($p) => ! $p->isMandor());
        $nonRegulerPrograms = $nonRegulerPrograms ?? $this->filterNonRegulerByYear(ProgramNonReguler::all(), $year);
        $regulerYearIds = $regulerPrograms->pluck('Program_reguler_id')->map(fn ($id) => (int) $id)->all();
        $records = $records ?? $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::all(), $year, $selectedMonthNum, $regulerYearIds);

        $isRecordMandor = function ($r) {
            $notes = strtoupper(trim((string) $r->notes));
            $nama = strtoupper(trim((string) $r->nama_pelatihan));

            return str_starts_with($notes, 'PMT') || str_starts_with($notes, 'PKT')
                || str_starts_with($nama, 'PMT') || str_starts_with($nama, 'PKT')
                || str_contains($notes, 'MANDOR') || str_contains($nama, 'MANDOR');
        };

        $regPlanBatch = (int) $regulerPrograms->count();
        $regPlanPart = (int) $regulerPrograms->sum('target_peserta');
        $regRecords = $records->where('program_type', 'reguler')->reject($isRecordMandor);
        $regRecordIds = array_flip($regRecords->pluck('id')->all());

        // Group reguler records by program_id to deduplicate participants per batch
        $recordsByProgId = [];
        $unassignedRegRecs = [];
        foreach ($regRecords as $r) {
            if ($r->program_id) {
                $recordsByProgId[(int) $r->program_id][] = $r;
            } else {
                $unassignedRegRecs[] = $r;
            }
        }

        $matchedUnassignedIds = [];
        $regRealBatch = 0;
        $regRealPart = 0;
        $today = date('Y-m-d');

        foreach ($regulerPrograms as $p) {
            // Pastikan tanggal_pelaksanaan awal <= dari tanggal saat ini
            $startDate = $this->getProgramRegulerStartDate($p);
            if (! $startDate || $startDate > $today) {
                continue;
            }

            $pId = (int) ($p->Program_reguler_id ?? $p->getKey());
            $pList = $recordsByProgId[$pId] ?? [];

            // Match unassigned records by notes or program name
            $pNameNorm = strtolower(trim(preg_replace('/\s*\(.*?\)\s*/', '', (string) $p->Training_Program)));
            if (! empty($unassignedRegRecs)) {
                foreach ($unassignedRegRecs as $r) {
                    if (isset($matchedUnassignedIds[$r->id])) {
                        continue;
                    }
                    $rNotes = strtolower(trim((string) $r->notes));
                    $rNama = strtolower(trim((string) $r->nama_pelatihan));
                    if ($pNameNorm !== '' && (str_contains($rNotes, $pNameNorm) || str_contains($rNama, $pNameNorm))) {
                        $pList[] = $r;
                        $matchedUnassignedIds[$r->id] = true;
                    }
                }
            }

            // Deduplicate participants per program batch
            $seen = [];
            $batchPartCount = 0;
            foreach ($pList as $r) {
                $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                if (! isset($seen[$ident])) {
                    $seen[$ident] = true;
                    $batchPartCount++;
                }
            }

            if ($batchPartCount > 0) {
                $regRealBatch += 1;
                $regRealPart += $batchPartCount;
            }
        }

        // Handle any remaining unassigned reguler records (unbudgeted)
        $remainingUnassigned = array_filter($unassignedRegRecs, fn ($r) => ! isset($matchedUnassignedIds[$r->id]));
        if (! empty($remainingUnassigned)) {
            $groupedUnbudget = collect($remainingUnassigned)->groupBy(fn ($r) => trim($r->notes ?: $r->nama_pelatihan ?: 'Reguler Unbudget'));
            foreach ($groupedUnbudget as $uRecs) {
                $seen = [];
                $uPartCount = 0;
                foreach ($uRecs as $r) {
                    $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                    if (! isset($seen[$ident])) {
                        $seen[$ident] = true;
                        $uPartCount++;
                    }
                }
                if ($uPartCount > 0) {
                    $regRealBatch += 1;
                    $regRealPart += $uPartCount;
                }
            }
        }

        $regPctBatch = $regPlanBatch > 0 ? round(($regRealBatch / $regPlanBatch) * 100) : 0;
        $regPctPart = $regPlanPart > 0 ? round(($regRealPart / $regPlanPart) * 100) : 0;

        $softPrograms = $nonRegulerPrograms->filter(fn ($p) => $p->isSoftSkill());
        $softPlanBatch = (int) $softPrograms->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
        $softPlanPart = (int) $softPrograms->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));
        $softProgramIds = $softPrograms->pluck('id_nonreguler')->all();
        $regulerPrefixes = ['FAT', 'MAT', 'TAT', 'AAT', 'DMT', 'ECT', 'PMT', 'PKT'];
        $technicalNotes = ['ESTATE', 'MILL', 'TRACTION', 'TRAKSI', 'DOWNSTREAM', 'ADMINISTRASI', 'ADMIN'];
        $softRecords = $records->filter(function ($r) use ($softPrograms, $softProgramIds, $regulerPrefixes, $regRecordIds, $technicalNotes, $isRecordMandor) {
            if ($isRecordMandor($r)) {
                return false;
            }
            if (isset($regRecordIds[$r->id])) {
                return false;
            }
            $notes = strtoupper(trim((string) $r->notes));
            foreach ($regulerPrefixes as $prefix) {
                if (str_starts_with($notes, $prefix)) {
                    return false;
                }
            }
            if (in_array($notes, $technicalNotes, true)) {
                return false;
            }
            $nama = strtolower(trim((string) $r->nama_pelatihan));
            $isSoft = ($notes === 'SOFTSKILL')
                || str_contains($notes, 'SOFT')
                || str_contains($nama, 'soft')
                || str_contains($nama, 'leadership')
                || str_contains($nama, 'supervisory')
                || str_contains($nama, 'problem solving')
                || str_contains($nama, 'communication')
                || str_contains($nama, 'talent development')
                || str_contains($nama, 'more precious than gold')
                || str_contains($nama, 'training of trainers')
                || (bool) preg_match('/\b(tot)\b/i', $nama)
                || str_contains($nama, 'fr mengajar')
                || ($r->program_id && in_array((int) $r->program_id, $softProgramIds, true));

            return $isSoft;
        });
        $softRealPart = (int) $softRecords->count();
        $softRealBatch = (int) $softPrograms->sum(fn ($p) => (int) ($p->realisasi_batch ?? 0)) ?: (int) $softRecords->pluck('nama_pelatihan')->filter()->unique()->count();
        $softPctBatch = $softPlanBatch > 0 ? round(($softRealBatch / $softPlanBatch) * 100) : 0;
        $softPctPart = $softPlanPart > 0 ? round(($softRealPart / $softPlanPart) * 100) : 0;

        $tekPrograms = $nonRegulerPrograms->filter(fn ($p) => ! $p->isSoftSkill());
        $tekPlanBatch = (int) $tekPrograms->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
        $tekPlanPart = (int) $tekPrograms->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));
        $softRecordIds = array_flip($softRecords->pluck('id')->all());
        $tekRecords = $records->filter(fn ($r) => $r->program_type !== 'reguler' && ! isset($regRecordIds[$r->id]) && ! isset($softRecordIds[$r->id]))->reject($isRecordMandor);
        $tekRealPart = (int) $tekRecords->count();
        $tekRealBatch = (int) $tekPrograms->sum(fn ($p) => (int) ($p->realisasi_batch ?? 0)) ?: (int) $tekRecords->pluck('nama_pelatihan')->filter()->unique()->count();
        $tekPctBatch = $tekPlanBatch > 0 ? round(($tekRealBatch / $tekPlanBatch) * 100) : 0;
        $tekPctPart = $tekPlanPart > 0 ? round(($tekRealPart / $tekPlanPart) * 100) : 0;

        return [
            'rows' => [
                'reguler' => [
                    'name' => 'REGULER',
                    'plan_batch' => $regPlanBatch, 'plan_participant' => $regPlanPart,
                    'realisasi_batch' => $regRealBatch, 'realisasi_participant' => $regRealPart,
                    'pct_batch' => $regPctBatch, 'pct_participant' => $regPctPart,
                ],
                'softskill' => [
                    'name' => 'SOFTSKILL',
                    'plan_batch' => $softPlanBatch, 'plan_participant' => $softPlanPart,
                    'realisasi_batch' => $softRealBatch, 'realisasi_participant' => $softRealPart,
                    'pct_batch' => $softPctBatch, 'pct_participant' => $softPctPart,
                ],
                'technical' => [
                    'name' => 'TECHNICAL/REFRESHMENT',
                    'plan_batch' => $tekPlanBatch, 'plan_participant' => $tekPlanPart,
                    'realisasi_batch' => $tekRealBatch, 'realisasi_participant' => $tekRealPart,
                    'pct_batch' => $tekPctBatch, 'pct_participant' => $tekPctPart,
                ],
            ],
            'chart_batch' => [
                'categories' => ['REGULER', 'SOFTSKILL', 'TECHNICAL/REFRESHMENT'],
                'plan' => [$regPlanBatch, $softPlanBatch, $tekPlanBatch],
                'realisasi' => [$regRealBatch, $softRealBatch, $tekRealBatch],
                'pct' => [$regPctBatch, $softPctBatch, $tekPctBatch],
            ],
            'chart_participant' => [
                'categories' => ['REGULER', 'SOFTSKILL', 'TECHNICAL/REFRESHMENT'],
                'plan' => [$regPlanPart, $softPlanPart, $tekPlanPart],
                'realisasi' => [$regRealPart, $softRealPart, $tekRealPart],
                'pct' => [$regPctPart, $softPctPart, $tekPctPart],
            ],
        ];
    }

    protected function computeTraineeUnder2Years(int $currentYear): array
    {
        $minYear = $currentYear - 2;
        $batchPrograms = AsistenProgramMonitoring::where(function ($q) use ($minYear) {
            $q->where('tahun_training', '>=', $minYear)
                ->orWhere('tahun_lulus', '>=', $minYear)
                ->orWhere('tahun_rekrut', '>=', $minYear);
        })->get();

        $programKeywords = [
            'FAT' => ['FIELD ASSISTANT', 'FAT'],
            'MAT' => ['MILL ASSISTANT', 'MAT'],
            'AAT' => ['ADMIN ASSISTANT', 'ADMINISTRATION ASSISTANT', 'AAT'],
            'TAT' => ['TRACTION ASSISTANT', 'TAT'],
        ];

        $allAsisten = Asisten::all();
        $rows = [];
        $totTrainee = 0;
        $totCulled = 0;
        $totPassed = 0;
        $totResign = 0;
        $totExisting = 0;

        foreach ($programKeywords as $prog => $keywords) {
            $filtered = $batchPrograms->filter(function ($b) use ($keywords) {
                $name = strtoupper((string) $b->nama_program);
                $cat = strtoupper((string) $b->kategori_program);
                foreach ($keywords as $kw) {
                    if (str_contains($name, $kw) || str_contains($cat, $kw)) {
                        return true;
                    }
                }

                return false;
            });

            $trainee = (int) $filtered->sum('jumlah_trainee');
            $culled = (int) $filtered->sum('jumlah_culled');
            $passed = max(0, $trainee - $culled);

            $asistenRecent = $allAsisten->filter(function ($a) use ($keywords, $minYear) {
                $pName = strtoupper((string) $a->program);
                $matchesProg = false;
                foreach ($keywords as $kw) {
                    if (str_contains($pName, $kw)) {
                        $matchesProg = true;
                        break;
                    }
                }
                if (! $matchesProg) {
                    return false;
                }

                preg_match('/\b(20\d{2})\b/', $pName, $matches);
                if (! empty($matches[1])) {
                    return (int) $matches[1] >= $minYear;
                }

                return true;
            });

            $resign = $asistenRecent->filter(fn ($a) => in_array(strtoupper(trim((string) ($a->status_karyawan ?: $a->keterangan ?: ''))), ['RESIGN', 'PHK', 'TERMINATE']))->count();
            $existing = max(0, $passed - $resign);

            $pctCulled = $trainee > 0 ? round(($culled / $trainee) * 100) : 0;
            $pctPassed = $trainee > 0 ? round(($passed / $trainee) * 100) : 0;
            $pctResign = $passed > 0 ? round(($resign / $passed) * 100) : 0;
            $pctExisting = $passed > 0 ? round(($existing / $passed) * 100) : 0;

            $rows[$prog] = [
                'program' => $prog,
                'trainee' => $trainee,
                'culled' => $culled,
                'passed' => $passed,
                'resign' => $resign,
                'existing' => $existing,
                'pct_culled' => $pctCulled,
                'pct_passed' => $pctPassed,
                'pct_resign' => $pctResign,
                'pct_existing' => $pctExisting,
            ];

            $totTrainee += $trainee;
            $totCulled += $culled;
            $totPassed += $passed;
            $totResign += $resign;
            $totExisting += $existing;
        }

        $totPctCulled = $totTrainee > 0 ? round(($totCulled / $totTrainee) * 100) : 0;
        $totPctPassed = $totTrainee > 0 ? round(($totPassed / $totTrainee) * 100) : 0;
        $totPctResign = $totPassed > 0 ? round(($totResign / $totPassed) * 100) : 0;
        $totPctExisting = $totPassed > 0 ? round(($totExisting / $totPassed) * 100) : 0;

        return [
            'rows' => $rows,
            'total' => [
                'program' => 'Total',
                'trainee' => $totTrainee,
                'culled' => $totCulled,
                'passed' => $totPassed,
                'resign' => $totResign,
                'existing' => $totExisting,
                'pct_culled' => $totPctCulled,
                'pct_passed' => $totPctPassed,
                'pct_resign' => $totPctResign,
                'pct_existing' => $totPctExisting,
            ],
            'chart' => [
                'categories' => array_keys($programKeywords),
                'culled' => array_column($rows, 'pct_culled'),
                'passed' => array_column($rows, 'pct_passed'),
                'resign' => array_column($rows, 'pct_resign'),
                'existing' => array_column($rows, 'pct_existing'),
            ],
        ];
    }

    protected function computeKarirLulusan(int $year): array
    {
        $csvPath = storage_path('app/alumni_monitoring.csv');
        $programs = ['FAT', 'MAT', 'AAT', 'TAT', 'DMT'];
        $rows = [];
        $totPc = 0;
        $totGm = 0;
        $totMk = 0;
        $totAskep = 0;
        $totAsst = 0;
        $totAll = 0;

        foreach ($programs as $prog) {
            $rows[$prog] = [
                'program' => $prog,
                'pc' => 0, 'gm' => 0, 'mk_mm' => 0, 'askep_ktu' => 0, 'asst' => 0,
                'total' => 0,
                'pct_pc' => 0, 'pct_gm' => 0, 'pct_mk_mm' => 0, 'pct_askep_ktu' => 0, 'pct_asst' => 0,
            ];
        }

        if (file_exists($csvPath)) {
            $f = fopen($csvPath, 'r');
            $header = fgetcsv($f, 0, ';', '"', '\\');
            $lvlIdx = array_search('LVL', $header);
            $progIdx = array_search('PROG', $header);
            $programIdx = array_search('Program', $header);
            $ketIdx = array_search('Keterangan', $header);
            $ketResignIdx = array_search('Keterangan Resign', $header);
            $tglResignIdx = array_search('Tanggal Resign', $header);

            while (($row = fgetcsv($f, 0, ';', '"', '\\')) !== false) {
                if (empty(array_filter($row))) {
                    continue;
                }
                $lvl = strtoupper(trim($row[$lvlIdx] ?? ''));
                if ($lvl === '' || $lvl === '-') {
                    continue;
                }

                $ket = trim($row[$ketIdx] ?? '');
                $ketResign = trim($row[$ketResignIdx] ?? '');
                $tglResign = trim($row[$tglResignIdx] ?? '');

                // Trainee must be active (Keterangan: Eksis / Aktif)
                $ketLower = strtolower($ket);
                $isEksis = ($ketLower === 'eksis' || $ketLower === 'aktif' || str_contains($ketLower, 'eksis') || str_contains($ketLower, 'aktif'));
                if (! $isEksis) {
                    continue;
                }
                if (str_contains($ketLower, 'resign') || str_contains($ketLower, 'phk') || str_contains($ketLower, 'culled') || str_contains($ketLower, 'gagal')) {
                    continue;
                }
                if (str_contains(strtolower($ketResign), 'resign') || str_contains(strtolower($ketResign), 'phk')) {
                    continue;
                }
                if (! empty($tglResign)) {
                    continue;
                }

                $prog = strtoupper(trim($row[$progIdx] ?? ''));
                $program = trim($row[$programIdx] ?? '');

                // Filter only starting from 2011 (pre-2011 like 2002-2010 are excluded)
                $rowYear = 0;
                if (preg_match('/\b(20\d{2})\b/', $program, $m)) {
                    $rowYear = (int) $m[1];
                } elseif (preg_match('/\b(20\d{2})\b/', (string) ($row[$tahunIdx] ?? ''), $m)) {
                    $rowYear = (int) $m[1];
                } elseif (preg_match('/\b(20\d{2})\b/', (string) ($row[15] ?? ''), $m)) {
                    $rowYear = (int) $m[1];
                } elseif (preg_match('/\b(20\d{2})\b/', (string) ($row[22] ?? ''), $m)) {
                    $rowYear = (int) $m[1];
                }
                if ($rowYear > 0 && $rowYear < 2011) {
                    continue;
                }

                if ($prog === 'ECT' || str_contains(strtoupper($program), 'ESTATE CADET')) {
                    $prog = 'FAT';
                } elseif ($prog === '') {
                    $progUpper = strtoupper($program);
                    if (str_contains($progUpper, 'FIELD ASSISTANT') || str_contains($progUpper, 'STAFF OPERATION') || str_contains($progUpper, 'FAT')) {
                        $prog = 'FAT';
                    } elseif (str_contains($progUpper, 'MILL ASSISTANT') || str_contains($progUpper, 'MAT')) {
                        $prog = 'MAT';
                    } elseif (str_contains($progUpper, 'ADMIN ASSISTANT') || str_contains($progUpper, 'AAT')) {
                        $prog = 'AAT';
                    } elseif (str_contains($progUpper, 'TRACTION ASSISTANT') || str_contains($progUpper, 'TAT')) {
                        $prog = 'TAT';
                    } elseif (str_contains($progUpper, 'DOWNSTREAM') || str_contains($progUpper, 'DMT')) {
                        $prog = 'DMT';
                    }
                }

                if (! isset($rows[$prog])) {
                    continue;
                }

                if (in_array($lvl, ['PC', 'P', 'DIR'], true)) {
                    $rows[$prog]['pc']++;
                } elseif ($lvl === 'GM') {
                    $rows[$prog]['gm']++;
                } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true)) {
                    $rows[$prog]['mk_mm']++;
                } elseif (in_array($lvl, ['ASKEP', 'KTU'], true)) {
                    $rows[$prog]['askep_ktu']++;
                } elseif (in_array($lvl, ['ASISTEN', 'ASST', 'STAFF'], true)) {
                    $rows[$prog]['asst']++;
                }
            }
            fclose($f);
        } else {
            // Fallback to Asisten table in database (exclude resigned / non-active, and only starting from 2011)
            $allAsisten = Asisten::whereNotNull('level_jabatan')
                ->where('level_jabatan', '!=', '')
                ->where('level_jabatan', '!=', '-')
                ->where(function ($q) {
                    $q->whereNull('status_resign')
                        ->orWhere(function ($sq) {
                            $sq->where('status_resign', 'not like', '%Resign%')
                                ->where('status_resign', 'not like', '%PHK%');
                        });
                })
                ->where(function ($q) {
                    $q->where('keterangan', 'like', '%Eksis%')
                        ->orWhere('keterangan', 'like', '%Aktif%');
                })
                ->where(function ($q) {
                    $q->whereNull('tanggal_resign')
                        ->orWhere('tanggal_resign', '');
                })
                ->where(function ($q) {
                    $q->whereNull('tahun')
                        ->orWhere('tahun', '>=', 2011);
                })
                ->get();

            $keywords = [
                'FAT' => ['FIELD ASSISTANT', 'STAFF OPERATION', 'FAT'],
                'MAT' => ['MILL ASSISTANT', 'MAT'],
                'AAT' => ['ADMIN ASSISTANT', 'ADMINISTRATION ASSISTANT', 'AAT'],
                'TAT' => ['TRACTION ASSISTANT'],
                'DMT' => ['DOWNSTREAM', 'DMT'],
            ];

            foreach ($allAsisten as $a) {
                $lvl = strtoupper(trim((string) $a->level_jabatan));
                $pName = strtoupper((string) $a->program);

                $matchedProg = null;
                foreach ($keywords as $p => $kws) {
                    foreach ($kws as $kw) {
                        if (str_contains($pName, $kw)) {
                            $matchedProg = $p;
                            break 2;
                        }
                    }
                }

                if (! $matchedProg || ! isset($rows[$matchedProg])) {
                    continue;
                }

                if (in_array($lvl, ['PC', 'P', 'DIR'], true)) {
                    $rows[$matchedProg]['pc']++;
                } elseif ($lvl === 'GM') {
                    $rows[$matchedProg]['gm']++;
                } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true)) {
                    $rows[$matchedProg]['mk_mm']++;
                } elseif (in_array($lvl, ['ASKEP', 'KTU'], true)) {
                    $rows[$matchedProg]['askep_ktu']++;
                } elseif (in_array($lvl, ['ASISTEN', 'ASST', 'STAFF'], true)) {
                    $rows[$matchedProg]['asst']++;
                }
            }
        }

        // Calculate totals and percentages
        foreach ($rows as $prog => &$r) {
            $total = $r['pc'] + $r['gm'] + $r['mk_mm'] + $r['askep_ktu'] + $r['asst'];
            $r['total'] = $total;
            $r['pct_pc'] = $total > 0 ? round(($r['pc'] / $total) * 100, 1) : 0;
            $r['pct_gm'] = $total > 0 ? round(($r['gm'] / $total) * 100, 1) : 0;
            $r['pct_mk_mm'] = $total > 0 ? round(($r['mk_mm'] / $total) * 100, 1) : 0;
            $r['pct_askep_ktu'] = $total > 0 ? round(($r['askep_ktu'] / $total) * 100, 1) : 0;
            $r['pct_asst'] = $total > 0 ? round(($r['asst'] / $total) * 100, 1) : 0;

            $totPc += $r['pc'];
            $totGm += $r['gm'];
            $totMk += $r['mk_mm'];
            $totAskep += $r['askep_ktu'];
            $totAsst += $r['asst'];
            $totAll += $total;
        }

        $totalRow = [
            'program' => 'TOTAL',
            'pc' => $totPc, 'gm' => $totGm, 'mk_mm' => $totMk, 'askep_ktu' => $totAskep, 'asst' => $totAsst,
            'total' => $totAll,
            'pct_pc' => $totAll > 0 ? round(($totPc / $totAll) * 100, 2) : 0,
            'pct_gm' => $totAll > 0 ? round(($totGm / $totAll) * 100, 2) : 0,
            'pct_mk_mm' => $totAll > 0 ? round(($totMk / $totAll) * 100, 2) : 0,
            'pct_askep_ktu' => $totAll > 0 ? round(($totAskep / $totAll) * 100, 2) : 0,
            'pct_asst' => $totAll > 0 ? round(($totAsst / $totAll) * 100, 2) : 0,
        ];

        return ['rows' => $rows, 'total' => $totalRow];
    }

    protected function computeStaffFreshGraduate(int $year, ?int $selectedMonthNum = null, $reguler = null, $records = null): array
    {
        $reguler = $reguler ?? $this->filterRegulerByYear(ProgramReguler::all(), $year);
        $regulerYearIds = $reguler->pluck('Program_reguler_id')->map(fn ($id) => (int) $id)->all();
        $records = $records ?? $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::where('program_type', 'reguler')->get(), $year, $selectedMonthNum, $regulerYearIds);
        $records = $records->where('program_type', 'reguler');

        $programs = ['FAT', 'MAT', 'DMT', 'TAT', 'AAT'];
        $rows = [];
        $totPlanB = 0;
        $totPlanP = 0;
        $totRealB = 0;
        $totRealP = 0;

        $recordsByProgId = [];
        $unassignedRecs = [];
        foreach ($records as $r) {
            if ($r->program_id) {
                $recordsByProgId[(int) $r->program_id][] = $r;
            } else {
                $unassignedRecs[] = $r;
            }
        }

        $today = date('Y-m-d');
        foreach ($programs as $prog) {
            $matchingMaster = $reguler->filter(fn ($p) => str_starts_with(strtoupper($p->Training_Program), $prog));
            $planBatch = (int) $matchingMaster->count();
            $planPart = (int) $matchingMaster->sum('target_peserta');

            $realBatch = 0;
            $realPart = 0;

            foreach ($matchingMaster as $p) {
                // Pastikan tanggal_pelaksanaan awal <= dari tanggal saat ini
                $startDate = $this->getProgramRegulerStartDate($p);
                if (! $startDate || $startDate > $today) {
                    continue;
                }

                $pId = (int) ($p->Program_reguler_id ?? $p->getKey());
                $pRecs = $recordsByProgId[$pId] ?? [];

                // Deduplicate participants within each program batch
                $seen = [];
                $batchPartCount = 0;
                foreach ($pRecs as $r) {
                    $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                    if (! isset($seen[$ident])) {
                        $seen[$ident] = true;
                        $batchPartCount++;
                    }
                }

                if ($batchPartCount > 0) {
                    $realBatch += 1;
                    $realPart += $batchPartCount;
                }
            }

            // Also check unassigned records for this prefix if any
            $matchingUnassigned = array_filter($unassignedRecs, function ($r) use ($prog) {
                return str_contains(strtoupper((string) $r->notes), $prog)
                    || str_contains(strtoupper((string) $r->nama_pelatihan), $prog);
            });
            if (! empty($matchingUnassigned)) {
                $seen = [];
                $uCount = 0;
                foreach ($matchingUnassigned as $r) {
                    $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                    if (! isset($seen[$ident])) {
                        $seen[$ident] = true;
                        $uCount++;
                    }
                }
                if ($uCount > 0) {
                    $realBatch += 1;
                    $realPart += $uCount;
                }
            }

            $pctBatch = $planBatch > 0 ? round(($realBatch / $planBatch) * 100) : 0;
            $pctPart = $planPart > 0 ? round(($realPart / $planPart) * 100) : 0;

            $rows[$prog] = [
                'name' => $prog,
                'plan_batch' => $planBatch,
                'plan_participant' => $planPart,
                'realisasi_batch' => $realBatch,
                'realisasi_participant' => $realPart,
                'pct_batch' => $pctBatch,
                'pct_participant' => $pctPart,
            ];

            $totPlanB += $planBatch;
            $totPlanP += $planPart;
            $totRealB += $realBatch;
            $totRealP += $realPart;
        }

        $totPctB = $totPlanB > 0 ? round(($totRealB / $totPlanB) * 100) : 0;
        $totPctP = $totPlanP > 0 ? round(($totRealP / $totPlanP) * 100) : 0;

        return [
            'total' => [
                'name' => 'Asisten Trainee',
                'plan_batch' => $totPlanB, 'plan_participant' => $totPlanP,
                'realisasi_batch' => $totRealB, 'realisasi_participant' => $totRealP,
                'pct_batch' => $totPctB, 'pct_participant' => $totPctP,
            ],
            'rows' => $rows,
            'chart' => [
                'categories' => $programs,
                'plan' => array_column($rows, 'plan_participant'),
                'realisasi' => array_column($rows, 'realisasi_participant'),
                'pct' => array_column($rows, 'pct_participant'),
            ],
        ];
    }

    protected function computeSoftskillRegional(int $year, ?int $selectedMonthNum = null, $nonRegPrograms = null, $records = null): array
    {
        $nonRegAll = $nonRegPrograms ?? $this->filterNonRegulerByYear(ProgramNonReguler::all(), $year);
        $nonReg = $nonRegAll->filter(fn ($p) => $p->isSoftSkill());
        $records = $records ?? $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::where('program_type', 'non_reguler')->get(), $year, $selectedMonthNum);
        $records = $records->where('program_type', 'non_reguler');

        $regions = ['Riau', 'Kalbar', 'Kubar'];
        $rows = [];
        $totPlanB = 0;
        $totPlanP = 0;
        $totRealB = 0;
        $totRealP = 0;

        foreach ($regions as $reg) {
            $regKey = ($reg === 'Kubar') ? 'kaltim' : strtolower($reg);
            $matchingProgs = $nonReg->filter(fn ($p) => str_contains(strtolower((string) $p->region), $regKey));

            $planBatch = (int) $matchingProgs->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
            $planPart = (int) $matchingProgs->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));

            $regulerPrefixes = ['FAT', 'MAT', 'TAT', 'AAT', 'DMT', 'ECT', 'PMT', 'PKT'];
            $technicalNotes = ['ESTATE', 'MILL', 'TRACTION', 'TRAKSI', 'DOWNSTREAM', 'ADMINISTRASI', 'ADMIN'];
            $matchingProgIds = $matchingProgs->pluck('id_nonreguler')->all();
            $matchingProgIdsFlip = array_flip($matchingProgIds);

            $matchingRecs = $records->filter(function ($r) use ($matchingProgIdsFlip, $regKey, $regulerPrefixes, $technicalNotes) {
                $notes = strtoupper(trim((string) $r->notes));
                foreach ($regulerPrefixes as $prefix) {
                    if (str_starts_with($notes, $prefix)) {
                        return false;
                    }
                }
                if (in_array($notes, $technicalNotes, true)) {
                    return false;
                }
                $nama = strtolower(trim((string) $r->nama_pelatihan));
                $isSoft = ($notes === 'SOFTSKILL')
                    || str_contains($notes, 'SOFT')
                    || str_contains($nama, 'soft')
                    || str_contains($nama, 'leadership')
                    || str_contains($nama, 'supervisory')
                    || str_contains($nama, 'problem solving')
                    || str_contains($nama, 'communication')
                    || str_contains($nama, 'talent development')
                    || str_contains($nama, 'more precious than gold')
                    || str_contains($nama, 'training of trainers')
                    || (bool) preg_match('/\b(tot)\b/i', $nama)
                    || str_contains($nama, 'fr mengajar')
                    || ($r->program_id && isset($matchingProgIdsFlip[$r->program_id]));

                if (! $isSoft) {
                    return false;
                }

                $rReg = strtolower((string) $r->region);
                if ($regKey === 'kaltim' || $regKey === 'kubar') {
                    return str_contains($rReg, 'kaltim') || str_contains($rReg, 'kubar');
                }

                return str_contains($rReg, $regKey);
            });
            $realPart = (int) $matchingRecs->count();
            $matchingRecProgIds = array_flip($matchingRecs->pluck('program_id')->filter()->all());
            $realBatch = (int) $matchingProgs->sum(function ($p) use ($matchingRecProgIds) {
                $explicitBatch = (int) ($p->realisasi_batch ?? $p->total_batch ?? 0);
                if ($explicitBatch > 0) {
                    return $explicitBatch;
                }

                return isset($matchingRecProgIds[$p->id_nonreguler]) ? 1 : 0;
            });

            $pctB = $planBatch > 0 ? round(($realBatch / $planBatch) * 100) : 0;
            $pctP = $planPart > 0 ? round(($realPart / $planPart) * 100) : 0;

            $rows[$reg] = [
                'region' => $reg,
                'plan_batch' => $planBatch, 'plan_participant' => $planPart,
                'realisasi_batch' => $realBatch, 'realisasi_participant' => $realPart,
                'pct_batch' => $pctB, 'pct_participant' => $pctP,
            ];

            $totPlanB += $planBatch;
            $totPlanP += $planPart;
            $totRealB += $realBatch;
            $totRealP += $realPart;
        }

        $totPctB = $totPlanB > 0 ? round(($totRealB / $totPlanB) * 100) : 0;
        $totPctP = $totPlanP > 0 ? round(($totRealP / $totPlanP) * 100) : 0;

        return [
            'total' => [
                'region' => 'Softskill',
                'plan_batch' => $totPlanB, 'plan_participant' => $totPlanP,
                'realisasi_batch' => $totRealB, 'realisasi_participant' => $totRealP,
                'pct_batch' => $totPctB, 'pct_participant' => $totPctP,
            ],
            'rows' => $rows,
            'chart' => [
                'categories' => $regions,
                'plan_batch' => array_column($rows, 'plan_batch'),
                'realisasi_batch' => array_column($rows, 'realisasi_batch'),
                'pct_batch' => array_column($rows, 'pct_batch'),
                'plan_participant' => array_column($rows, 'plan_participant'),
                'realisasi_participant' => array_column($rows, 'realisasi_participant'),
                'pct_participant' => array_column($rows, 'pct_participant'),
            ],
        ];
    }

    protected function computeTeknikalBreakdown(int $year, ?int $selectedMonthNum = null, $nonRegPrograms = null, $records = null): array
    {
        $nonRegAll = $nonRegPrograms ?? $this->filterNonRegulerByYear(ProgramNonReguler::all(), $year);
        $nonReg = $nonRegAll->filter(fn ($p) => ! $p->isSoftSkill());
        $records = $records ?? $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::where('program_type', 'non_reguler')->get(), $year, $selectedMonthNum);
        $records = $records->where('program_type', 'non_reguler');

        // Pre-group records by program_id for instant O(1) matching
        $recordsByProgramId = $records->whereNotNull('program_id')->groupBy('program_id');
        $recordsWithoutProgId = $records->whereNull('program_id');

        $categories = ['ESTATE', 'MILL', 'TRACTION', 'DOWNSTREAM', 'ADMINISTRASI'];
        $groups = [];
        $chartPartPlan = [];
        $chartPartReal = [];
        $chartPartPct = [];
        $chartBatchPlan = [];
        $chartBatchReal = [];
        $chartBatchPct = [];

        foreach ($categories as $cat) {
            $catKey = strtolower($cat);
            $catProgs = $nonReg->filter(function ($p) use ($catKey) {
                $haystack = strtolower(($p->kategori_pelatihan ?? '').' '.($p->sub_kategori ?? '').' '.($p->jenis_pelatihan ?? ''));
                if ($catKey === 'estate') {
                    return str_contains($haystack, 'estate') || str_contains($haystack, 'tanaman') || str_contains($haystack, 'agronomi') || str_contains($haystack, 'kebun')
                        || (! str_contains($haystack, 'mill') && ! str_contains($haystack, 'pabrik') && ! str_contains($haystack, 'traction') && ! str_contains($haystack, 'traksi') && ! str_contains($haystack, 'downstream') && ! str_contains($haystack, 'administrasi') && ! str_contains($haystack, 'admin'));
                }
                if ($catKey === 'mill') {
                    return str_contains($haystack, 'mill') || str_contains($haystack, 'pabrik') || str_contains($haystack, 'processing');
                }
                if ($catKey === 'traction') {
                    return str_contains($haystack, 'traction') || str_contains($haystack, 'traksi') || str_contains($haystack, 'bengkel') || str_contains($haystack, 'workshop');
                }
                if ($catKey === 'downstream') {
                    return str_contains($haystack, 'downstream') || str_contains($haystack, 'refinery') || str_contains($haystack, 'biodiesel');
                }
                if ($catKey === 'administrasi') {
                    return str_contains($haystack, 'administrasi') || str_contains($haystack, 'admin') || str_contains($haystack, 'finance') || str_contains($haystack, 'accounting') || str_contains($haystack, 'hrd') || str_contains($haystack, 'ktu');
                }

                return false;
            });

            $catProgIds = $catProgs->pluck('id_nonreguler')->all();
            $catRecsProg = collect();
            foreach ($catProgIds as $pid) {
                if (isset($recordsByProgramId[$pid])) {
                    $catRecsProg = $catRecsProg->concat($recordsByProgramId[$pid]);
                }
            }

            $unlinkedRecs = $recordsWithoutProgId->filter(function ($r) use ($catKey) {
                $notes = strtolower((string) $r->notes);
                if ($catKey === 'estate') {
                    return str_contains($notes, 'estate') || str_contains($notes, 'tanaman') || str_contains($notes, 'agronomi');
                }
                if ($catKey === 'mill') {
                    return str_contains($notes, 'mill') || str_contains($notes, 'pabrik');
                }
                if ($catKey === 'traction') {
                    return str_contains($notes, 'traction') || str_contains($notes, 'traksi') || str_contains($notes, 'bengkel');
                }
                if ($catKey === 'downstream') {
                    return str_contains($notes, 'downstream');
                }
                if ($catKey === 'administrasi') {
                    return str_contains($notes, 'administrasi') || str_contains($notes, 'admin');
                }

                return false;
            });

            $catRecs = $catRecsProg->concat($unlinkedRecs);

            $planB = (int) $catProgs->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
            $planP = (int) $catProgs->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));
            $realP = (int) $catRecs->count();

            $realB = (int) $catProgs->sum(function ($p) use ($recordsByProgramId) {
                $explicitBatch = (int) ($p->realisasi_batch ?? $p->total_batch ?? 0);
                if ($explicitBatch > 0) {
                    return $explicitBatch;
                }

                return isset($recordsByProgramId[$p->id_nonreguler]) ? 1 : 0;
            });

            $pctB = $planB > 0 ? round(($realB / $planB) * 100) : 0;
            $pctP = $planP > 0 ? round(($realP / $planP) * 100) : 0;

            $items = [];
            if ($cat !== 'DOWNSTREAM') {
                foreach (['Riau', 'Kalbar', 'Kubar'] as $reg) {
                    $regKey = ($reg === 'Kubar') ? 'kaltim' : strtolower($reg);
                    $regProgs = $catProgs->filter(fn ($p) => str_contains(strtolower((string) $p->region), $regKey));
                    $rPlanB = (int) $regProgs->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
                    $rPlanP = (int) $regProgs->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));

                    $rRecs = $catRecs->filter(fn ($r) => str_contains(strtolower((string) $r->region), $regKey));
                    $rRealP = (int) $rRecs->count();
                    $rRealB = (int) $regProgs->sum(function ($p) use ($recordsByProgramId) {
                        $explicitBatch = (int) ($p->realisasi_batch ?? $p->total_batch ?? 0);
                        if ($explicitBatch > 0) {
                            return $explicitBatch;
                        }

                        return isset($recordsByProgramId[$p->id_nonreguler]) ? 1 : 0;
                    });

                    $items[] = [
                        'name' => "{$cat} - {$reg}",
                        'plan_batch' => $rPlanB, 'plan_participant' => $rPlanP,
                        'realisasi_batch' => $rRealB, 'realisasi_participant' => $rRealP,
                        'pct_batch' => $rPlanB > 0 ? round(($rRealB / $rPlanB) * 100) : 0,
                        'pct_participant' => $rPlanP > 0 ? round(($rRealP / $rPlanP) * 100) : 0,
                    ];
                }
            }

            $groups[$cat] = [
                'total' => [
                    'name' => $cat,
                    'plan_batch' => $planB, 'plan_participant' => $planP,
                    'realisasi_batch' => $realB, 'realisasi_participant' => $realP,
                    'pct_batch' => $pctB, 'pct_participant' => $pctP,
                ],
                'items' => $items,
            ];

            $chartPartPlan[] = $planP;
            $chartPartReal[] = $realP;
            $chartPartPct[] = $pctP;
            $chartBatchPlan[] = $planB;
            $chartBatchReal[] = $realB;
            $chartBatchPct[] = $pctB;
        }

        return [
            'groups' => $groups,
            'chart_participant' => [
                'categories' => $categories,
                'plan' => $chartPartPlan,
                'realisasi' => $chartPartReal,
                'pct' => $chartPartPct,
            ],
            'chart_batch' => [
                'categories' => $categories,
                'plan' => $chartBatchPlan,
                'realisasi' => $chartBatchReal,
                'pct' => $chartBatchPct,
            ],
        ];
    }
}
