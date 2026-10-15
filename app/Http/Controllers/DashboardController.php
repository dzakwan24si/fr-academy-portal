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

    protected function filterRecordsByYearAndCumulativeMonth($records, int $year, ?int $selectedMonthNum)
    {
        return $records->filter(function ($r) use ($year, $selectedMonthNum) {
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
                return false;
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
        $rawMonth = $request->input('month', 'all');
        $selectedMonthNum = $this->parseMonthNumber($rawMonth);
        $selectedMonthCode = $selectedMonthNum ? self::MONTH_3_LETTERS[$selectedMonthNum] : 'all';
        $monthList = self::MONTH_3_LETTERS;

        // 1. REKAP TRAINING REPORT (Reguler, Softskill, Technical)
        $rekapTraining = $this->computeRekapTrainingReport($year, $selectedMonthNum);

        // 2. MONITORING TRAINEE UNDER 2 YEARS
        $traineeUnder2Years = $this->computeTraineeUnder2Years($year);

        // 3. MONITORING KARIR LULUSAN FRLC
        $karirLulusan = $this->computeKarirLulusan($year);

        // 4. STAFF FRESH GRADUATE (Asisten Trainee)
        $staffFreshGraduate = $this->computeStaffFreshGraduate($year, $selectedMonthNum);

        // 5. PROGRAM SOFTSKILL BY REGION
        $softskillRegional = $this->computeSoftskillRegional($year, $selectedMonthNum);

        // 6. PROGRAM NON-REGULER TEKNIKAL BY CATEGORY & REGION
        $teknikalBreakdown = $this->computeTeknikalBreakdown($year, $selectedMonthNum);

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

    protected function computeRekapTrainingReport(int $year, ?int $selectedMonthNum = null): array
    {
        $regulerPrograms = $this->filterRegulerByYear(ProgramReguler::all(), $year);
        $nonRegulerPrograms = $this->filterNonRegulerByYear(ProgramNonReguler::all(), $year);
        $records = $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::all(), $year, $selectedMonthNum);

        $regPlanBatch = (int) $regulerPrograms->count();
        $regPlanPart = (int) $regulerPrograms->sum('target_peserta');
        $regRecords = $records->where('program_type', 'reguler');
        $regRecordIds = array_flip($regRecords->pluck('id')->all());
        $regRealPart = (int) $regRecords->pluck('participant_identifier')->filter()->unique()->count() ?: (int) $regRecords->count();
        $regRealBatch = (int) $regRecords->pluck('notes')->filter()->unique()->count() ?: ($regRealPart > 0 ? $regPlanBatch : 0);
        $regPctBatch = $regPlanBatch > 0 ? round(($regRealBatch / $regPlanBatch) * 100) : 0;
        $regPctPart = $regPlanPart > 0 ? round(($regRealPart / $regPlanPart) * 100) : 0;

        $softPrograms = $nonRegulerPrograms->filter(fn ($p) => $p->isSoftSkill());
        $softPlanBatch = (int) $softPrograms->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
        $softPlanPart = (int) $softPrograms->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));
        $softProgramIds = $softPrograms->pluck('id_nonreguler')->all();
        $regulerPrefixes = ['FAT', 'MAT', 'TAT', 'AAT', 'DMT', 'ECT', 'PMT', 'PKT'];
        $technicalNotes = ['ESTATE', 'MILL', 'TRACTION', 'TRAKSI', 'DOWNSTREAM', 'ADMINISTRASI', 'ADMIN'];
        $softRecords = $records->filter(function ($r) use ($softPrograms, $softProgramIds, $regulerPrefixes, $regRecordIds, $technicalNotes) {
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
        $tekRecords = $records->filter(fn ($r) => ! isset($regRecordIds[$r->id]) && ! isset($softRecordIds[$r->id]));
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
        $allAsisten = Asisten::whereNotNull('program')->get();
        $minYear = $year - 2;
        $allAsisten = $allAsisten->filter(function ($a) use ($year, $minYear) {
            $pName = strtoupper((string) $a->program);
            preg_match('/\b(20\d{2})\b/', $pName, $matches);
            if (! empty($matches[1])) {
                return (int) $matches[1] >= $minYear && (int) $matches[1] <= $year;
            }
            if (! empty($a->tahun_lulus) || ! empty($a->tahun_training) || ! empty($a->tahun_rekrut)) {
                $yL = (int) $a->tahun_lulus;
                $yT = (int) $a->tahun_training;
                $yR = (int) $a->tahun_rekrut;

                return ($yL >= $minYear && $yL <= $year) || ($yT >= $minYear && $yT <= $year) || ($yR >= $minYear && $yR <= $year);
            }

            return true;
        });

        $programKeywords = [
            'FAT' => ['FIELD ASSISTANT', 'FAT'],
            'MAT' => ['MILL ASSISTANT', 'MAT'],
            'AAT' => ['ADMIN ASSISTANT', 'ADMINISTRATION ASSISTANT', 'AAT'],
            'TAT' => ['TRACTION ASSISTANT', 'TAT'],
            'DMT' => ['DOWNSTREAM', 'DMT'],
        ];

        $rows = [];
        $totPc = 0;
        $totGm = 0;
        $totMk = 0;
        $totAskep = 0;
        $totAsst = 0;
        $totAll = 0;

        foreach ($programKeywords as $prog => $keywords) {
            $progAsisten = $allAsisten->filter(function ($a) use ($keywords) {
                $pName = strtoupper((string) $a->program);
                foreach ($keywords as $kw) {
                    if (str_contains($pName, $kw)) {
                        return true;
                    }
                }

                return false;
            });

            $pc = 0;
            $gm = 0;
            $mk = 0;
            $askep = 0;
            $asst = 0;

            foreach ($progAsisten as $a) {
                $lvl = strtoupper(trim((string) ($a->level_jabatan ?: '')));
                if ($lvl === '') {
                    $lvl = strtoupper(trim((string) ($a->jabatan_terakhir ?: $a->jabatan_awal ?: '')));
                }
                if (str_contains($lvl, 'PC') || str_contains($lvl, 'CONTROLLER')) {
                    $pc++;
                } elseif (str_contains($lvl, 'GM') || str_contains($lvl, 'GENERAL MANAGER')) {
                    $gm++;
                } elseif (str_contains($lvl, 'MK') || str_contains($lvl, 'MM') || str_contains($lvl, 'MANAGER') || str_contains($lvl, 'KASIE')) {
                    $mk++;
                } elseif (str_contains($lvl, 'ASKEP') || str_contains($lvl, 'KTU')) {
                    $askep++;
                } else {
                    $asst++;
                }
            }

            $total = $pc + $gm + $mk + $askep + $asst;

            $rows[$prog] = [
                'program' => $prog,
                'pc' => $pc, 'gm' => $gm, 'mk_mm' => $mk, 'askep_ktu' => $askep, 'asst' => $asst,
                'total' => $total,
                'pct_pc' => $total > 0 ? round(($pc / $total) * 100, 1) : 0,
                'pct_gm' => $total > 0 ? round(($gm / $total) * 100, 1) : 0,
                'pct_mk_mm' => $total > 0 ? round(($mk / $total) * 100, 1) : 0,
                'pct_askep_ktu' => $total > 0 ? round(($askep / $total) * 100, 1) : 0,
                'pct_asst' => $total > 0 ? round(($asst / $total) * 100, 1) : 0,
            ];

            $totPc += $pc;
            $totGm += $gm;
            $totMk += $mk;
            $totAskep += $askep;
            $totAsst += $asst;
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

    protected function computeStaffFreshGraduate(int $year, ?int $selectedMonthNum = null): array
    {
        $reguler = $this->filterRegulerByYear(ProgramReguler::all(), $year);
        $records = $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::where('program_type', 'reguler')->get(), $year, $selectedMonthNum);

        $programs = ['FAT', 'MAT', 'DMT', 'TAT', 'AAT'];
        $rows = [];
        $totPlanB = 0;
        $totPlanP = 0;
        $totRealB = 0;
        $totRealP = 0;

        foreach ($programs as $prog) {
            $matchingMaster = $reguler->filter(fn ($p) => str_starts_with(strtoupper($p->Training_Program), $prog));
            $planBatch = (int) $matchingMaster->count();
            $planPart = (int) $matchingMaster->sum('target_peserta');

            $matchingRecs = $records->filter(fn ($r) => str_contains(strtoupper($r->notes), $prog));
            $realPart = (int) $matchingRecs->pluck('participant_identifier')->filter()->unique()->count() ?: (int) $matchingRecs->count();
            $realBatch = (int) $matchingRecs->pluck('notes')->filter()->unique()->count() ?: ($realPart > 0 ? max(1, $planBatch) : 0);

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

    protected function computeSoftskillRegional(int $year, ?int $selectedMonthNum = null): array
    {
        $nonReg = $this->filterNonRegulerByYear(ProgramNonReguler::all(), $year)->filter(fn ($p) => $p->isSoftSkill());
        $records = $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::where('program_type', 'non_reguler')->get(), $year, $selectedMonthNum);

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
            $matchingRecs = $records->filter(function ($r) use ($matchingProgs, $regKey, $regulerPrefixes, $technicalNotes) {
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
                    || ($r->program_id && $matchingProgs->contains('id_nonreguler', $r->program_id));

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
            $realBatch = (int) $matchingProgs->sum(function ($p) use ($matchingRecs) {
                $explicitBatch = (int) ($p->realisasi_batch ?? $p->total_batch ?? 0);
                if ($explicitBatch > 0) {
                    return $explicitBatch;
                }
                $hasRecords = $matchingRecs->contains('program_id', $p->id_nonreguler);

                return $hasRecords ? 1 : 0;
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

    protected function computeTeknikalBreakdown(int $year, ?int $selectedMonthNum = null): array
    {
        $nonReg = $this->filterNonRegulerByYear(ProgramNonReguler::all(), $year)->filter(fn ($p) => ! $p->isSoftSkill());
        $records = $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::where('program_type', 'non_reguler')->get(), $year, $selectedMonthNum);

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

            $planB = (int) $catProgs->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
            $planP = (int) $catProgs->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));

            $catRecs = $records->filter(function ($r) use ($catProgs, $catKey) {
                if ($r->program_id) {
                    return $catProgs->contains('id_nonreguler', $r->program_id);
                }
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
            $realP = (int) $catRecs->count();
            $realB = (int) $catProgs->sum(function ($p) use ($catRecs) {
                $explicitBatch = (int) ($p->realisasi_batch ?? $p->total_batch ?? 0);
                if ($explicitBatch > 0) {
                    return $explicitBatch;
                }
                $hasRecords = $catRecs->contains('program_id', $p->id_nonreguler);

                return $hasRecords ? 1 : 0;
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
                    $rRealB = (int) $regProgs->sum(function ($p) use ($rRecs) {
                        $explicitBatch = (int) ($p->realisasi_batch ?? $p->total_batch ?? 0);
                        if ($explicitBatch > 0) {
                            return $explicitBatch;
                        }
                        $hasRecords = $rRecs->contains('program_id', $p->id_nonreguler);

                        return $hasRecords ? 1 : 0;
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
