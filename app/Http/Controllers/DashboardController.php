<?php

namespace App\Http\Controllers;

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;
use App\Models\Monitoring\MonitoringAngkatan;
use App\Models\Monitoring\MonitoringCulled;
use App\Models\Monitoring\MonitoringEmployee;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use App\Services\SpreadsheetReader;
use App\Services\TrainingMetricService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public const MONTH_3_LETTERS = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
        5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
        9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
    ];

    public function parseMonthNumber($value): ?int
    {
        return TrainingMetricService::parseMonthNumber($value);
    }

    public function extractYearFromField($val): ?int
    {
        return TrainingMetricService::extractYearFromField($val);
    }

    public function resolveProgramRegulerYear($p): int
    {
        return TrainingMetricService::resolveProgramRegulerYear($p);
    }

    public function getProgramRegulerStartDate($p): ?string
    {
        return TrainingMetricService::getProgramRegulerStartDate($p);
    }

    public function resolveProgramNonRegulerYear($p): int
    {
        return TrainingMetricService::resolveProgramNonRegulerYear($p);
    }

    protected function filterRegulerByYear($programs, int $year)
    {
        return TrainingMetricService::filterRegulerByYear($programs, $year);
    }

    protected function filterNonRegulerByYear($programs, int $year)
    {
        return TrainingMetricService::filterNonRegulerByYear($programs, $year);
    }

    protected function filterRecordsByYearAndCumulativeMonth($records, int $year, ?int $selectedMonthNum, array $validRegulerProgIds = [])
    {
        return TrainingMetricService::filterRecordsByYearAndCumulativeMonth($records, $year, $selectedMonthNum, $validRegulerProgIds);
    }

    public function index(Request $request)
    {
        $years = Cache::remember('portal_dashboard_years', 300, function () {
            $trYears = TrainingRecord::whereNotNull('tahun')->distinct()->pluck('tahun')->map(fn ($y) => (int) $y)->filter()->all();
            $regYears = ProgramReguler::whereNotNull('Mulai_program')->pluck('Mulai_program')->map(fn ($d) => (int) date('Y', strtotime($d)))->filter()->all();
            $nonRegYears = ProgramNonReguler::whereNotNull('start_date')->pluck('start_date')->map(fn ($d) => (int) date('Y', strtotime($d)))->filter()->all();

            return collect([2024, 2025, 2026, 2027])
                ->merge($trYears)
                ->merge($regYears)
                ->merge($nonRegYears)
                ->filter(fn ($y) => $y >= 2000 && $y <= 2100)
                ->unique()
                ->sort()
                ->values()
                ->all();
        });

        $yearDefault = in_array(2026, $years, true) ? 2026 : ($years[count($years) - 1] ?? (int) date('Y'));
        $year = (int) $request->input('year', $yearDefault);
        $rawMonth = $request->input('month', 'all');
        $selectedMonthNum = $this->parseMonthNumber($rawMonth);
        $selectedMonthCode = $selectedMonthNum ? self::MONTH_3_LETTERS[$selectedMonthNum] : 'all';
        $monthList = self::MONTH_3_LETTERS;

        $v = (int) Cache::get('dashboard_cache_version', 1);
        $cacheKey = "dashboard_metrics_v{$v}_{$year}_{$selectedMonthCode}";
        $metrics = Cache::remember($cacheKey, 300, function () use ($year, $selectedMonthNum) {
            $regulerAll = ProgramReguler::all();
            $nonRegulerAll = ProgramNonReguler::all();
            $regulerYear = $this->filterRegulerByYear($regulerAll, $year)->filter(fn ($p) => ! $p->isMandor());
            $nonRegulerYear = $this->filterNonRegulerByYear($nonRegulerAll, $year);

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

            $records = $this->filterRecordsByYearAndCumulativeMonth($yearRecords, $year, $selectedMonthNum, $regulerYearIds);

            return [
                'rekapTraining' => $this->computeRekapTrainingReport($year, $selectedMonthNum, $regulerYear, $nonRegulerYear, $records),
                'traineeUnder2Years' => $this->computeTraineeUnder2Years($year),
                'karirLulusan' => $this->computeKarirLulusan($year),
                'staffFreshGraduate' => $this->computeStaffFreshGraduate($year, $selectedMonthNum, $regulerYear, $records),
                'softskillRegional' => $this->computeSoftskillRegional($year, $selectedMonthNum, $nonRegulerYear, $records),
                'teknikalBreakdown' => $this->computeTeknikalBreakdown($year, $selectedMonthNum, $nonRegulerYear, $records),
            ];
        });

        $rekapTraining = $metrics['rekapTraining'];
        $traineeUnder2Years = $metrics['traineeUnder2Years'];
        $karirLulusan = $metrics['karirLulusan'];
        $staffFreshGraduate = $metrics['staffFreshGraduate'];
        $softskillRegional = $metrics['softskillRegional'];
        $teknikalBreakdown = $metrics['teknikalBreakdown'];

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

        // 1. REGULER (Management Trainee Staff)
        $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);
        $normalizeName = fn (?string $name) => TrainingMetricService::normalizeName($name);
        $normalizeAngkatan = fn (string $name) => TrainingMetricService::normalizeAngkatan($name);

        $allEmployees = MonitoringEmployee::all();
        $empByAngkatan = [];
        foreach ($allEmployees as $e) {
            if ($e->angkatan_fr_academy) {
                $norm = $normalizeAngkatan($e->angkatan_fr_academy);
                $empByAngkatan[$norm][] = $normalizeName($e->nama);
            }
        }

        $allAngkatans = MonitoringAngkatan::pluck('angkatan', 'id_angkatan')->all();
        $allCulled = MonitoringCulled::all();
        $culledByAngkatan = [];
        foreach ($allCulled as $c) {
            $rawAng = $allAngkatans[$c->id_angkatan] ?? null;
            if ($rawAng) {
                $norm = $normalizeAngkatan($rawAng);
                $culledByAngkatan[$norm][] = $normalizeName($c->nama);
            }
        }

        $regulerPrefixes = ['FAT', 'MAT', 'TAT', 'AAT', 'DMT', 'ECT', 'PMT', 'PKT'];
        if ($records !== null) {
            $allTrainingRecords = $records->filter(function ($r) use ($regulerPrefixes) {
                if ($r->program_type === 'reguler') {
                    return true;
                }
                $notes = strtoupper(trim((string) $r->notes));
                foreach ($regulerPrefixes as $prefix) {
                    if (str_starts_with($notes, $prefix)) {
                        return true;
                    }
                }

                return false;
            });
        } else {
            $allTrainingRecords = TrainingRecord::where(function ($q) {
                $q->where('program_type', 'reguler')
                    ->orWhere('notes', 'LIKE', 'FAT%')
                    ->orWhere('notes', 'LIKE', 'MAT%')
                    ->orWhere('notes', 'LIKE', 'TAT%')
                    ->orWhere('notes', 'LIKE', 'AAT%')
                    ->orWhere('notes', 'LIKE', 'DMT%')
                    ->orWhere('notes', 'LIKE', 'ECT%')
                    ->orWhere('notes', 'LIKE', 'PMT%')
                    ->orWhere('notes', 'LIKE', 'PKT%');
            })->get();
            $allTrainingRecords = $this->filterRecordsByYearAndCumulativeMonth($allTrainingRecords, $year, $selectedMonthNum, $regulerYearIds);
        }

        $trByAngkatan = [];
        foreach ($allTrainingRecords as $r) {
            if ($r->notes) {
                $norm = $normalizeAngkatan($r->notes);
                $trByAngkatan[$norm][] = $normalizeName($r->participant_name);
            }
        }

        $regPlanBatch = (int) $regulerPrograms->count();
        $regPlanPart = (int) $regulerPrograms->sum('target_peserta');
        $regRealBatch = 0;
        $regRealPart = 0;

        $today = date('Y-m-d');
        $targetDate = $selectedMonthNum ? date('Y-m-t', strtotime("{$year}-{$selectedMonthNum}-01")) : $today;

        foreach ($regulerPrograms as $p) {
            $kelas = TrainingMetricService::resolveRegulerKelasCount($p, $trByAngkatan, $empByAngkatan, $culledByAngkatan);

            // Logika terpisah untuk Realisasi Batch dan Realisasi Peserta:
            // 1. Batch: Hanya dihitung jika tanggal mulai pelaksanaan <= target tanggal (sudah mulai)
            $startDate = $this->getProgramRegulerStartDate($p);
            $isStarted = ($startDate && $startDate <= $targetDate);
            if ($isStarted && $kelas > 0) {
                $regRealBatch++;
            }

            // 2. Peserta: Dihitung dari jumlah peserta kelas yang ada
            if ($kelas > 0) {
                $regRealPart += $kelas;
            }
        }

        $regPctBatch = $regPlanBatch > 0 ? round(($regRealBatch / $regPlanBatch) * 100) : 0;
        $regPctPart = $regPlanPart > 0 ? round(($regRealPart / $regPlanPart) * 100) : 0;

        // 2 & 3. NON REGULER (SOFTSKILL & TEKNIKAL)
        if ($records !== null) {
            $trNonReg = $records->filter(function ($r) use ($regulerPrefixes) {
                if ($r->program_type === 'non_reguler') {
                    $notes = strtoupper(trim((string) $r->notes));
                    foreach ($regulerPrefixes as $prefix) {
                        if (str_starts_with($notes, $prefix)) {
                            return false;
                        }
                    }

                    return true;
                }
                if (str_contains(strtolower((string) $r->reg_non_reg), 'non')) {
                    return true;
                }
                $notes = strtoupper(trim((string) $r->notes));
                foreach ($regulerPrefixes as $prefix) {
                    if (str_starts_with($notes, $prefix)) {
                        return false;
                    }
                }

                return in_array($r->notes, ['Softskill', 'Traksi', 'Administrasi', 'Mill', 'Estate', 'Downstream']);
            });
        } else {
            $trNonReg = TrainingRecord::where(function ($q) {
                $q->where('program_type', 'non_reguler')
                    ->orWhere('reg_non_reg', 'LIKE', '%non%')
                    ->orWhereIn('notes', ['Softskill', 'Traksi', 'Administrasi', 'Mill', 'Estate', 'Downstream']);
            })->get();

            $trNonReg = $this->filterRecordsByYearAndCumulativeMonth($trNonReg, $year, $selectedMonthNum)->filter(function ($r) use ($regulerPrefixes) {
                $notes = strtoupper(trim((string) $r->notes));
                foreach ($regulerPrefixes as $prefix) {
                    if (str_starts_with($notes, $prefix)) {
                        return false;
                    }
                }

                return true;
            });
        }

        $softPrograms = $nonRegulerPrograms->filter(fn ($p) => $p->isSoftSkill());
        $softPlanBatch = (int) $softPrograms->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
        $softPlanPart = (int) $softPrograms->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));
        $softProgramIds = $softPrograms->pluck('id_nonreguler')->all();

        $tekPrograms = $nonRegulerPrograms->filter(fn ($p) => ! $p->isSoftSkill());
        $tekPlanBatch = (int) $tekPrograms->sum(fn ($p) => (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0));
        $tekPlanPart = (int) $tekPrograms->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0));

        $technicalNotes = ['ESTATE' => true, 'MILL' => true, 'TRACTION' => true, 'TRAKSI' => true, 'DOWNSTREAM' => true, 'ADMINISTRASI' => true, 'ADMIN' => true];
        $allSoftProgramIds = array_flip($softProgramIds);

        $softRecords = [];
        $tekRecords = [];
        $softPelatihanNames = [];
        $tekPelatihanNames = [];

        foreach ($trNonReg as $r) {
            $notes = strtoupper(trim((string) $r->notes));
            $nama = strtolower(trim((string) $r->nama_pelatihan));

            if (isset($technicalNotes[$notes])) {
                $tekRecords[] = $r;
                if ($r->nama_pelatihan) $tekPelatihanNames[$r->nama_pelatihan] = true;
                continue;
            }

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
                || ($r->program_id && isset($allSoftProgramIds[(int) $r->program_id]));

            if ($isSoft) {
                $softRecords[] = $r;
                if ($r->nama_pelatihan) $softPelatihanNames[$r->nama_pelatihan] = true;
            } else {
                $tekRecords[] = $r;
                if ($r->nama_pelatihan) $tekPelatihanNames[$r->nama_pelatihan] = true;
            }
        }

        $softRealPart = count($softRecords);
        $softRealBatch = (int) $softPrograms->sum(fn ($p) => (int) ($p->realisasi_batch ?? 0)) ?: count($softPelatihanNames);
        $softPctBatch = $softPlanBatch > 0 ? round(($softRealBatch / $softPlanBatch) * 100) : 0;
        $softPctPart = $softPlanPart > 0 ? round(($softRealPart / $softPlanPart) * 100) : 0;

        $tekRealPart = count($tekRecords);
        $tekRealBatch = (int) $tekPrograms->sum(fn ($p) => (int) ($p->realisasi_batch ?? 0)) ?: count($tekPelatihanNames);
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

        $allAsisten = Asisten::select(['id', 'program', 'tahun_angkatan', 'status_karyawan', 'keterangan'])
            ->where(function ($q) use ($minYear) {
                $q->where('tahun_angkatan', '>=', $minYear)
                    ->orWhereNull('tahun_angkatan')
                    ->orWhere('tahun_angkatan', 0);
            })
            ->get();
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

                if ($a->tahun_angkatan && (int) $a->tahun_angkatan > 0) {
                    return (int) $a->tahun_angkatan >= $minYear;
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
                $q->whereNull('tahun_angkatan')
                    ->orWhere('tahun_angkatan', '>=', 2011);
            })
            ->get();

        $keywords = [
            'FAT' => ['FIELD ASSISTANT', 'STAFF OPERATION', 'FAT', 'ESTATE CADET', 'ECT'],
            'MAT' => ['MILL ASSISTANT', 'MAT'],
            'AAT' => ['ADMIN ASSISTANT', 'ADMINISTRATION ASSISTANT', 'AAT'],
            'TAT' => ['TRACTION ASSISTANT', 'TAT'],
            'DMT' => ['DOWNSTREAM', 'DMT'],
        ];

        foreach ($allAsisten as $a) {
            $lvl = strtoupper(trim((string) $a->level_jabatan));
            $pName = strtoupper((string) $a->program);
            $batchName = strtoupper((string) $a->batch);

            // Filter only starting from 2011 (pre-2011 are excluded)
            $rowYear = 0;
            if (preg_match('/\b(20\d{2})\b/', $pName, $m)) {
                $rowYear = (int) $m[1];
            } elseif (preg_match('/\b(20\d{2})\b/', $batchName, $m)) {
                $rowYear = (int) $m[1];
            } elseif ($a->tahun_angkatan && preg_match('/\b(20\d{2})\b/', (string) $a->tahun_angkatan, $m)) {
                $rowYear = (int) $m[1];
            }
            if ($rowYear > 0 && $rowYear < 2011) {
                continue;
            }

            $matchedProg = null;
            foreach ($keywords as $p => $kws) {
                foreach ($kws as $kw) {
                    if (str_contains($pName, $kw) || str_contains($batchName, $kw)) {
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
        $targetDate = $selectedMonthNum ? date('Y-m-t', strtotime("{$year}-{$selectedMonthNum}-01")) : $today;
        foreach ($programs as $prog) {
            $matchingMaster = $reguler->filter(fn ($p) => str_starts_with(strtoupper($p->Training_Program), $prog));
            $planBatch = (int) $matchingMaster->count();
            $planPart = (int) $matchingMaster->sum('target_peserta');

            $realBatch = 0;
            $realPart = 0;

            foreach ($matchingMaster as $p) {
                // Logika terpisah untuk Realisasi Batch dan Realisasi Peserta:
                // 1. Batch: Hanya dihitung jika tanggal mulai pelaksanaan <= target tanggal (sudah mulai)
                $startDate = $this->getProgramRegulerStartDate($p);
                $isStarted = ($startDate && $startDate <= $targetDate);

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

                if ($isStarted && $batchPartCount > 0) {
                    $realBatch += 1;
                }

                if ($batchPartCount > 0) {
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
        $allNonRegProgIds = array_flip($nonRegAll->pluck('id_nonreguler')->map(fn ($id) => (int) $id)->all());

        $records = $records ?? $this->filterRecordsByYearAndCumulativeMonth(TrainingRecord::where('program_type', 'non_reguler')->get(), $year, $selectedMonthNum);
        $records = $records->where('program_type', 'non_reguler');

        // Pre-group records by valid program_id for instant O(1) matching.
        // Records without program_id OR with an unknown program_id are treated as unlinked records.
        $recordsByProgramId = $records->filter(fn ($r) => $r->program_id && isset($allNonRegProgIds[(int) $r->program_id]))->groupBy('program_id');
        $recordsWithoutProgId = $records->filter(fn ($r) => empty($r->program_id) || ! isset($allNonRegProgIds[(int) $r->program_id]));

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
                $notes = strtolower((string) ($r->notes ?: $r->nama_pelatihan));
                if ($catKey === 'estate') {
                    return str_contains($notes, 'estate') || str_contains($notes, 'tanaman') || str_contains($notes, 'agronomi')
                        || (! str_contains($notes, 'mill') && ! str_contains($notes, 'pabrik') && ! str_contains($notes, 'traction') && ! str_contains($notes, 'traksi') && ! str_contains($notes, 'downstream') && ! str_contains($notes, 'administrasi') && ! str_contains($notes, 'admin'));
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
