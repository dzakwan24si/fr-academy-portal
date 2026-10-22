<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\Monitoring\MonitoringAngkatan;
use App\Models\Monitoring\MonitoringCulled;
use App\Models\Monitoring\MonitoringEmployee;
use App\Models\Monitoring\MonitoringTalent;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use App\Services\ProgramFilterHelper;
use App\Services\SpreadsheetReader;
use App\Services\TrainingMetricService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardRealisasiController extends Controller
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

    protected function filterRecordsByYearAndCumulativeMonth($records, int $year, ?int $selectedMonthNum, array $regulerYearById = [], array $regulerYearByAng = [])
    {
        return TrainingMetricService::filterRecordsByYearAndCumulativeMonth($records, $year, $selectedMonthNum, [], $regulerYearById, $regulerYearByAng);
    }

    public function index(Request $request)
    {
        $filterOptions = $this->getFilterOptions();
        $years = $filterOptions['years'];
        $allRegions = $filterOptions['allRegions'];
        $regulerProgramOptions = collect($filterOptions['regulerProgramOptions']);
        $nonRegulerProgramOptions = collect($filterOptions['nonRegulerProgramOptions']);
        $nonRegulerCategoryOptions = $filterOptions['nonRegulerCategoryOptions'];

        $yearDefault = in_array(2026, $years, true) ? 2026 : ($years[count($years) - 1] ?? (int) date('Y'));
        $year = (int) $request->input('year', $yearDefault);

        $rawMonth = $request->input('month', 'all');
        $selectedMonthNum = $this->parseMonthNumber($rawMonth);
        $selectedMonthCode = ($selectedMonthNum && isset(self::MONTH_3_LETTERS[$selectedMonthNum]))
            ? self::MONTH_3_LETTERS[$selectedMonthNum]
            : 'all';
        $monthList = self::MONTH_3_LETTERS;

        $rawRegion = trim((string) $request->input('region', 'all'));
        $region = ($rawRegion === '' || $rawRegion === 'Semua Region') ? 'all' : $rawRegion;
        if (strcasecmp($region, 'Kulbar') === 0 || strcasecmp($region, 'Kaltim FR') === 0) {
            $region = 'Kaltim';
        } elseif (strcasecmp($region, 'Kalbar') === 0) {
            $region = 'Kalbar';
        }
        $programType = trim((string) $request->input('program_type', 'all'));
        $selectedProgram = trim((string) $request->input('program', 'all'));
        $category = trim((string) $request->input('category', 'all'));
        $unit = trim((string) $request->input('unit', 'all'));
        $level = trim((string) $request->input('level', 'all'));

        $availablePrograms = $programType === 'reguler' ? $regulerProgramOptions
            : ($programType === 'non_reguler' ? $nonRegulerProgramOptions
            : $regulerProgramOptions->concat($nonRegulerProgramOptions)->unique()->values());

        $v = (int) Cache::get('dreal_cache_version', 1);
        $cacheKey = "dreal_metrics_v{$v}_".md5(json_encode([$year, $selectedMonthCode, $region, $programType, $selectedProgram, $category, $unit, $level]));

        $metrics = Cache::remember($cacheKey, 300, function () use ($year, $selectedMonthNum, $region, $programType, $selectedProgram, $category, $unit, $level) {
            return $this->computeMetrics($year, $selectedMonthNum, $region, $programType, $selectedProgram, $category, $unit, $level);
        });

        return view('Administrasi.Pages.Dashboard.dashboard_realisasi', array_merge($metrics, compact(
            'years',
            'year',
            'monthList',
            'selectedMonthCode',
            'selectedMonthNum',
            'region',
            'allRegions',
            'programType',
            'selectedProgram',
            'category',
            'availablePrograms',
            'regulerProgramOptions',
            'nonRegulerProgramOptions',
            'nonRegulerCategoryOptions',
            'unit',
            'level'
        )));
    }

    protected function getFilterOptions(): array
    {
        return Cache::remember('dreal_filter_options', 300, function () {
            $trYears = TrainingRecord::whereNotNull('tahun')->distinct()->pluck('tahun')->map(fn ($y) => (int) $y)->filter()->all();
            $trDateYears = TrainingRecord::whereNotNull('training_date')->distinct()->pluck('training_date')->map(fn ($d) => (int) date('Y', strtotime($d)))->filter()->all();
            $regYears = ProgramReguler::whereNotNull('tahun')->pluck('tahun')
                ->merge(ProgramReguler::whereNotNull('Mulai_program')->pluck('Mulai_program')->map(fn ($d) => (int) date('Y', strtotime($d))))
                ->map(fn ($y) => (int) $y)->filter()->all();
            $nonRegYears = ProgramNonReguler::whereNotNull('tahun')->pluck('tahun')
                ->merge(ProgramNonReguler::whereNotNull('start_date')->pluck('start_date')->map(fn ($d) => (int) date('Y', strtotime($d))))
                ->map(fn ($y) => (int) $y)->filter()->all();

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

            $regulerRegions = ProgramReguler::distinct()->pluck('Region')->filter()->map(fn ($r) => trim((string) $r));
            $nonRegulerRegions = ProgramNonReguler::distinct()->pluck('region')->filter()->map(fn ($r) => trim((string) $r));
            $recordRegions = TrainingRecord::distinct()->pluck('region')->filter()->map(fn ($r) => trim((string) $r));

            $allRegions = $regulerRegions->merge($nonRegulerRegions)->merge($recordRegions)
                ->map(function ($r) {
                    if (strcasecmp($r, 'riau') === 0) return 'Riau';
                    if (strcasecmp($r, 'kalbar') === 0) return 'Kalbar';
                    if (strcasecmp($r, 'kulbar') === 0 || strcasecmp($r, 'kaltim') === 0 || strcasecmp($r, 'kaltim fr') === 0) return 'Kaltim';
                    if (strcasecmp($r, 'corporate') === 0) return 'Corporate';
                    return $r;
                })
                ->filter(fn ($r) => ! in_array(strtolower($r), ['kulbar']))
                ->unique()
                ->values()
                ->all();

            $regulerProgramOptions = ProgramReguler::pluck('Training_Program')
                ->toBase()
                ->map(fn ($p) => trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $p)))
                ->filter()->unique()->values()->all();

            $nonRegulerProgramOptions = ProgramNonReguler::pluck('jenis_pelatihan')
                ->toBase()
                ->map(fn ($p) => trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $p)))
                ->filter()->unique()->values()->all();

            $nonRegulerCategoryOptions = ProgramNonReguler::pluck('sub_kategori')
                ->filter()
                ->map(fn ($c) => trim((string) $c))
                ->reject(fn ($c) => in_array(strtolower($c), ['managerial and leadership', 'technical', 'managerial']))
                ->unique()
                ->values()
                ->all();

            return compact('years', 'allRegions', 'regulerProgramOptions', 'nonRegulerProgramOptions', 'nonRegulerCategoryOptions');
        });
    }

    protected function computeMetrics(int $year, ?int $selectedMonthNum, string $region, string $programType, string $selectedProgram, string $category, string $unit, string $level): array
    {
        $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);
        $normalizeName = fn (?string $name) => TrainingMetricService::normalizeName($name);
        $normalizeAngkatan = fn (string $name) => TrainingMetricService::normalizeAngkatan($name);

        $trainingRecordQuery = TrainingRecord::query();
        if ($region !== 'all') {
            if (strcasecmp($region, 'Kaltim') === 0 || strcasecmp($region, 'Kaltim FR') === 0) {
                $trainingRecordQuery->where(function ($q) {
                    $q->where('region', 'LIKE', '%kaltim%')
                        ->orWhere('region', 'LIKE', '%Kaltim FR%')
                        ->orWhere('region', 'LIKE', '%kulbar%');
                });
            } elseif (strcasecmp($region, 'Kalbar') === 0) {
                $trainingRecordQuery->where('region', 'LIKE', '%kalbar%');
            } else {
                $trainingRecordQuery->where('region', 'LIKE', '%'.$region.'%');
            }
        }
        if ($level !== 'all' && $level !== '') {
            $trainingRecordQuery->where('level', 'LIKE', '%'.$level.'%');
        }

        $allEmployees = MonitoringEmployee::all();
        $empByAngkatan = [];
        $empResignByAngkatan = [];
        foreach ($allEmployees as $e) {
            if ($e->angkatan_fr_academy) {
                $norm = $normalizeAngkatan($e->angkatan_fr_academy);
                $normN = $normalizeName($e->nama);
                if ($normN) {
                    $empByAngkatan[$norm][] = $normN;
                    $stat = strtoupper(trim($e->status ?: $e->kategori ?: ''));
                    if (in_array($stat, ['RESIGN', 'TERMINATE']) || ! empty($e->date_terminate) || ! empty($e->alasan_resign)) {
                        $empResignByAngkatan[$norm][] = $normN;
                    }
                }
            }
        }

        $allAngkatans = MonitoringAngkatan::pluck('angkatan', 'id_angkatan')->all();
        $allCulled = MonitoringCulled::all();
        $culledByAngkatan = [];
        foreach ($allCulled as $c) {
            $rawAng = $allAngkatans[$c->id_angkatan] ?? null;
            if ($rawAng) {
                $norm = $normalizeAngkatan($rawAng);
                $normN = $normalizeName($c->nama);
                if ($normN) {
                    $culledByAngkatan[$norm][] = $normN;
                }
            }
        }

        $chartRegulerMap = ProgramReguler::all()->keyBy('Program_reguler_id');
        $regulerYearByAng = [];
        $regulerYearById = [];
        foreach ($chartRegulerMap as $p) {
            $pName = trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $p->Training_Program));
            $norm = $normalizeAngkatan($pName);
            $pYear = (! empty($p->tahun) && is_numeric($p->tahun)) ? (int) $p->tahun : $this->resolveProgramRegulerYear($p);
            $regulerYearByAng[$norm] = $pYear;
            if ($p->Program_reguler_id) {
                $regulerYearById[(int) $p->Program_reguler_id] = $pYear;
            }
        }

        $allTrainingRecords = (clone $trainingRecordQuery)->where(function ($q) {
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
        $allTrainingRecords = $this->filterRecordsByYearAndCumulativeMonth($allTrainingRecords, $year, $selectedMonthNum, $regulerYearById, $regulerYearByAng);

        $trByAngkatan = [];
        foreach ($allTrainingRecords as $r) {
            if ($r->notes) {
                $norm = $normalizeAngkatan($r->notes);
                $normN = $normalizeName($r->participant_name);
                if ($normN) {
                    $trByAngkatan[$norm][] = $normN;
                }
            }
        }

        $regulerProgramsQuery = ProgramReguler::query();
        if ($region !== 'all') {
            if (strcasecmp($region, 'Kaltim') === 0 || strcasecmp($region, 'Kaltim FR') === 0) {
                $regulerProgramsQuery->where(function ($q) {
                    $q->where('Region', 'LIKE', '%kaltim%')
                        ->orWhere('Region', 'LIKE', '%Kaltim FR%')
                        ->orWhere('Region', 'LIKE', '%kulbar%');
                });
            } elseif (strcasecmp($region, 'Kalbar') === 0) {
                $regulerProgramsQuery->where('Region', 'LIKE', '%kalbar%');
            } else {
                $regulerProgramsQuery->where('Region', 'LIKE', '%'.$region.'%');
            }
        }
        $regulerPrograms = $regulerProgramsQuery->get()->filter(function ($p) use ($year) {
            $pYear = (! empty($p->tahun) && is_numeric($p->tahun)) ? (int) $p->tahun : $this->resolveProgramRegulerYear($p);
            return $pYear === (int) $year;
        });

        $tableRegulerPrograms = $regulerPrograms;

        if ($programType === 'non_reguler') {
            $regulerPrograms = collect();
            $tableRegulerPrograms = collect();
        } else {
            if ($category === 'staf') {
                $stafFilter = fn ($p) => ! str_contains($cleanStr($p->jenis_program_reguler), 'mandor');
                $regulerPrograms = $regulerPrograms->filter($stafFilter);
                $tableRegulerPrograms = $tableRegulerPrograms->filter($stafFilter);
            } elseif ($category === 'mandor') {
                $mandorFilter = fn ($p) => str_contains($cleanStr($p->jenis_program_reguler), 'mandor');
                $regulerPrograms = $regulerPrograms->filter($mandorFilter);
                $tableRegulerPrograms = $tableRegulerPrograms->filter($mandorFilter);
            }

            if ($selectedProgram !== 'all' && $selectedProgram !== '') {
                $progFilter = function ($p) use ($selectedProgram, $cleanStr) {
                    $pName = trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $p->Training_Program));
                    return strcasecmp($pName, $selectedProgram) === 0 || str_contains($cleanStr($pName), $cleanStr($selectedProgram));
                };
                $regulerPrograms = $regulerPrograms->filter($progFilter);
                $tableRegulerPrograms = $tableRegulerPrograms->filter($progFilter);
            }
        }

        // 1. Compute MT Program Tables
        $mtData = $this->computeMtTables(
            $tableRegulerPrograms, $trByAngkatan, $empByAngkatan, $culledByAngkatan, $empResignByAngkatan,
            $year, $selectedMonthNum, $allTrainingRecords
        );

        // 2. Compute KPI Cards
        $kpiCards = $this->computeKpiCards(
            $year, $selectedMonthNum, $region, $programType, $category, $selectedProgram,
            $regulerPrograms, $allTrainingRecords, $mtData['stafRencana'], $trainingRecordQuery
        );

        // 3. Compute Monthly Chart Data
        $monthlyData = $this->computeMonthlyChartData($year, $region, $programType, $category, $selectedProgram, $trainingRecordQuery);

        // 4. Compute Evaluasi
        $evaluasi = $this->computeEvaluasi();

        return array_merge($mtData, $kpiCards, compact('monthlyData', 'evaluasi'));
    }

    protected function computeMtTables(
        $tableRegulerPrograms,
        array $trByAngkatan,
        array $empByAngkatan,
        array $culledByAngkatan,
        array $empResignByAngkatan,
        int $year,
        ?int $selectedMonthNum,
        $allTrainingRecords
    ): array {
        $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);
        $normalizeAngkatan = fn (string $name) => TrainingMetricService::normalizeAngkatan($name);

        $todayStr = date('Y-m-d');
        if ($selectedMonthNum !== null) {
            $periodEnd = Carbon::create($year, $selectedMonthNum)->endOfMonth()->format('Y-m-d');
            $cutoffDate = ($year === (int) date('Y') && $periodEnd > $todayStr) ? $todayStr : $periodEnd;
        } else {
            $cutoffDate = ($year === (int) date('Y')) ? $todayStr : Carbon::create($year, 12, 31)->format('Y-m-d');
        }

        $isRecordMandor = fn ($r) => str_contains(strtoupper((string) $r->notes), 'MANDOR') || str_contains(strtoupper((string) $r->nama_pelatihan), 'MANDOR');
        $regStaffRecords = $allTrainingRecords->where('program_type', 'reguler')->reject($isRecordMandor);
        $regMandorRecords = $allTrainingRecords->where('program_type', 'reguler')->filter($isRecordMandor);

        $progIdsWithTrainingRecords = TrainingRecord::where('program_type', 'reguler')
            ->whereNotNull('program_id')
            ->distinct()
            ->pluck('program_id')
            ->map(fn ($id) => (int) $id)
            ->flip()
            ->all();

        $staffRecordsByProgId = [];
        $unassignedStaffRecs = [];
        foreach ($regStaffRecords as $r) {
            if ($r->program_id) {
                $staffRecordsByProgId[(int) $r->program_id][] = $r;
            } else {
                $unassignedStaffRecs[] = $r;
            }
        }

        $mandorRecordsByProgId = [];
        $unassignedMandorRecs = [];
        foreach ($regMandorRecords as $r) {
            if ($r->program_id) {
                $mandorRecordsByProgId[(int) $r->program_id][] = $r;
            } else {
                $unassignedMandorRecs[] = $r;
            }
        }

        $matchedUnassignedIds = [];

        $mtStafTable = [];
        $stafRencana = 0;
        $stafKelas = 0;
        $stafResign = 0;
        $stafLulus = 0;

        $mtMandorTable = [];
        $mandorRencana = 0;
        $mandorKelas = 0;
        $mandorResign = 0;
        $mandorLulus = 0;

        foreach ($tableRegulerPrograms as $p) {
            $pName = trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $p->Training_Program));
            $normPName = $normalizeAngkatan($pName);

            $resignedEmpNames = [];
            foreach ($empResignByAngkatan as $ang => $nList) {
                if ($ang === $normPName || (str_starts_with($normPName, 'AAT-II') && str_starts_with($ang, 'AAT-II'))) {
                    foreach ($nList as $n) {
                        $resignedEmpNames[$n] = true;
                    }
                }
            }
            $culledNames = [];
            foreach ($culledByAngkatan as $ang => $nList) {
                if ($ang === $normPName || (str_starts_with($normPName, 'AAT-II') && str_starts_with($ang, 'AAT-II'))) {
                    foreach ($nList as $n) {
                        $culledNames[$n] = true;
                    }
                }
            }

            $allResigned = array_unique(array_merge(array_keys($resignedEmpNames), array_keys($culledNames)));
            $autoResign = count($allResigned);

            $startDate = $p->Mulai_program?->format('Y-m-d') ?: TrainingMetricService::getProgramRegulerStartDate($p);
            $endDate = $p->Selesai_program?->format('Y-m-d') ?: TrainingMetricService::getProgramRegulerEndDate($p);
            $hasStarted = ($startDate && $startDate <= $cutoffDate);

            $isMandor = str_contains($cleanStr($p->jenis_program_reguler), 'mandor');
            $rencana = (int) ($p->TARGET_PESERTA ?? $p->target_peserta ?? 0);

            if (! $hasStarted) {
                $kelas = 0;
            } else {
                $pId = (int) ($p->Program_reguler_id ?? $p->getKey());
                $pList = $isMandor ? ($mandorRecordsByProgId[$pId] ?? []) : ($staffRecordsByProgId[$pId] ?? []);
                $unassignedRecs = $isMandor ? $unassignedMandorRecs : $unassignedStaffRecs;

                $pNameNorm = strtolower(trim(preg_replace('/\s*\(.*?\)\s*/', '', (string) $p->Training_Program)));
                if (! empty($unassignedRecs)) {
                    foreach ($unassignedRecs as $r) {
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

                $seen = [];
                $batchPartCount = 0;
                foreach ($pList as $r) {
                    $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                    if (! isset($seen[$ident])) {
                        $seen[$ident] = true;
                        $batchPartCount++;
                    }
                }

                $hasRecordsInDb = isset($progIdsWithTrainingRecords[$pId]);

                if ($batchPartCount > 0) {
                    $kelas = $batchPartCount;
                } elseif ($hasRecordsInDb) {
                    $kelas = 0;
                } elseif ($p->kelas !== null) {
                    $kelas = (int) $p->kelas;
                } else {
                    $kelas = TrainingMetricService::resolveRegulerKelasCount($p, $trByAngkatan, $empByAngkatan, $culledByAngkatan);
                }
            }

            $resign = ($p->resign !== null && $hasStarted) ? (int) $p->resign : ($hasStarted ? $autoResign : 0);
            $staff = ($p->staff !== null && $hasStarted) ? (int) $p->staff : (($hasStarted && $p->lulus !== null) ? (int) $p->lulus : max(0, $kelas - $resign));
            $lulus = $staff;

            $rowItem = [
                'id' => $p->Program_reguler_id,
                'name' => $pName,
                'region' => $p->Region ?: 'Nasional',
                'rencana' => $rencana,
                'kelas' => $kelas,
                'resign' => $resign,
                'staff' => $staff,
                'lulus' => $lulus,
            ];

            if ($isMandor) {
                $mandorRencana += $rencana;
                $mandorKelas += $kelas;
                $mandorResign += $resign;
                $mandorLulus += $lulus;
                $mtMandorTable[] = $rowItem;
            } else {
                $stafRencana += $rencana;
                $stafKelas += $kelas;
                $stafResign += $resign;
                $stafLulus += $lulus;
                $mtStafTable[] = $rowItem;
            }
        }

        $mtStafSummary = [
            'peserta' => $stafKelas,
            'rencana' => $stafRencana,
            'resign' => $stafResign,
            'lulus' => $stafLulus,
        ];

        $mtMandorSummary = [
            'peserta' => $mandorKelas,
            'rencana' => $mandorRencana,
            'resign' => $mandorResign,
            'lulus' => $mandorLulus,
        ];

        $mtProgramTable = array_merge($mtStafTable, $mtMandorTable);
        $mtSummary = [
            'peserta' => $stafKelas + $mandorKelas,
            'rencana' => $stafRencana + $mandorRencana,
            'resign' => $stafResign + $mandorResign,
            'lulus' => $stafLulus + $mandorLulus,
        ];

        return compact(
            'mtStafTable', 'mtStafSummary', 'mtMandorTable', 'mtMandorSummary',
            'mtProgramTable', 'mtSummary', 'stafRencana', 'stafKelas'
        );
    }

    protected function computeKpiCards(int $year, ?int $selectedMonthNum, string $region, string $programType, string $category, string $selectedProgram, $regulerPrograms, $allTrainingRecords, int $stafRencana, $trainingRecordQuery): array
    {
        $cleanStr = fn ($str) => SpreadsheetReader::cleanString($str);

        $todayStr = date('Y-m-d');
        if ($selectedMonthNum !== null) {
            $periodEnd = Carbon::create($year, $selectedMonthNum)->endOfMonth()->format('Y-m-d');
            $cutoffDate = ($year === (int) date('Y') && $periodEnd > $todayStr) ? $todayStr : $periodEnd;
        } else {
            $cutoffDate = ($year === (int) date('Y')) ? $todayStr : Carbon::create($year, 12, 31)->format('Y-m-d');
        }

        // 1. Reguler MT Staff Card
        $regulerStaffPrograms = $regulerPrograms->filter(fn ($p) => ! str_contains($cleanStr($p->jenis_program_reguler), 'mandor'));
        $isRecordMandor = fn ($r) => str_contains(strtoupper((string) $r->notes), 'MANDOR') || str_contains(strtoupper((string) $r->nama_pelatihan), 'MANDOR');
        $regStaffRecords = $allTrainingRecords->where('program_type', 'reguler')->reject($isRecordMandor);

        $progIdsWithTrainingRecords = TrainingRecord::where('program_type', 'reguler')
            ->whereNotNull('program_id')
            ->distinct()
            ->pluck('program_id')
            ->map(fn ($id) => (int) $id)
            ->flip()
            ->all();

        $recordsByProgId = [];
        $unassignedRegRecs = [];
        foreach ($regStaffRecords as $r) {
            if ($r->program_id) {
                $recordsByProgId[(int) $r->program_id][] = $r;
            } else {
                $unassignedRegRecs[] = $r;
            }
        }

        $matchedUnassignedIds = [];
        $regRealPart = 0;
        $regulerBerjalan = 0;
        $regulerSelesai = 0;

        foreach ($regulerStaffPrograms as $p) {
            $startDate = $p->Mulai_program?->format('Y-m-d') ?: TrainingMetricService::getProgramRegulerStartDate($p);
            $endDate = $p->Selesai_program?->format('Y-m-d') ?: TrainingMetricService::getProgramRegulerEndDate($p);
            $hasStarted = ($startDate && $startDate <= $cutoffDate);

            if (! $hasStarted) {
                if ($selectedMonthNum === null) {
                    $regulerBerjalan++;
                }
                continue;
            }

            $pId = (int) ($p->Program_reguler_id ?? $p->getKey());
            $pList = $recordsByProgId[$pId] ?? [];

            $pNameNorm = strtolower(trim(preg_replace('/\s*\(.*?\)\s*/', '', (string) $p->Training_Program)));
            if (! empty($unassignedRegRecs)) {
                foreach ($unassignedRegRecs as $r) {
                    if (isset($matchedUnassignedIds[$r->id])) continue;
                    $rNotes = strtolower(trim((string) $r->notes));
                    $rNama = strtolower(trim((string) $r->nama_pelatihan));
                    if ($pNameNorm !== '' && (str_contains($rNotes, $pNameNorm) || str_contains($rNama, $pNameNorm))) {
                        $pList[] = $r;
                        $matchedUnassignedIds[$r->id] = true;
                    }
                }
            }

            $seen = [];
            $batchPartCount = 0;
            foreach ($pList as $r) {
                $ident = strtolower(trim($r->participant_identifier ?: $r->participant_name ?: (string) $r->id));
                if (! isset($seen[$ident])) {
                    $seen[$ident] = true;
                    $batchPartCount++;
                }
            }

            $hasRecordsInDb = isset($progIdsWithTrainingRecords[$pId]);
            if ($batchPartCount > 0) {
                $regRealPart += $batchPartCount;
            } elseif (! $hasRecordsInDb && $p->kelas !== null) {
                $regRealPart += (int) $p->kelas;
            }

            if ($endDate && $endDate <= $cutoffDate) {
                $regulerSelesai++;
            } else {
                $regulerBerjalan++;
            }
        }

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
                    $regRealPart += $uPartCount;
                }
            }
        }

        $regTargetPeserta = $stafRencana > 0 ? $stafRencana : (int) $regulerStaffPrograms->sum(fn ($p) => (int) ($p->TARGET_PESERTA ?? $p->target_peserta ?? 0));
        $regulerPercentage = $regTargetPeserta > 0 ? round(($regRealPart / $regTargetPeserta) * 100) : 0;

        $regulerMt = [
            'percentage' => $regulerPercentage,
            'target' => 100,
            'jumlah_peserta' => $regRealPart,
            'target_peserta' => $regTargetPeserta,
            'program_berjalan' => $regulerBerjalan,
            'program_selesai' => $regulerSelesai,
        ];

        // 2. Talent Program Card
        $talentQuery = MonitoringTalent::query();
        if ($region !== 'all') {
            if (strcasecmp($region, 'Kaltim') === 0 || strcasecmp($region, 'Kaltim FR') === 0) {
                $talentQuery->where(function ($q) {
                    $q->where('region', 'LIKE', '%kaltim%')->orWhere('region', 'LIKE', '%kulbar%');
                });
            } elseif (strcasecmp($region, 'Kalbar') === 0) {
                $talentQuery->where('region', 'LIKE', '%kalbar%');
            } else {
                $searchReg = str_replace([' FR', ' fr'], '', $region);
                $talentQuery->where('region', 'LIKE', "%{$searchReg}%");
            }
        }
        $totalTalent = $talentQuery->count();
        $sudahPenugasan = (clone $talentQuery)->where(function ($sub) {
            $sub->where('status', 'LIKE', '%sudah penugasan%')->orWhere('status', 'LIKE', '%confirm%');
        })->count();
        $akanPenugasan = (clone $talentQuery)->where(function ($sub) {
            $sub->where('status', 'LIKE', '%project khusus%')->orWhere('status', 'LIKE', '%akan penugasan%');
        })->count();
        $belumPenugasan = (clone $talentQuery)->where('status', 'LIKE', '%belum penugasan%')->count();
        $talentPercentage = (float) AppSetting::get('talent_program_progress_materi', 83);
        $talentToBreakdown = (clone $talentQuery)->select('talent_to', DB::raw('count(*) as total'))
            ->whereNotNull('talent_to')
            ->where('talent_to', '!=', '')
            ->groupBy('talent_to')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($item) => ['role' => $item->talent_to, 'count' => (int) $item->total])
            ->all();

        $talentProgram = [
            'percentage' => $talentPercentage,
            'target' => 100,
            'jumlah_talent' => $totalTalent,
            'sudah_penugasan' => $sudahPenugasan,
            'akan_penugasan' => $akanPenugasan,
            'belum_penugasan' => $belumPenugasan,
            'breakdown' => $talentToBreakdown,
        ];

        // 3 & 4. Soft Skill & Teknikal
        $nonRegulerProgramsQuery = ProgramNonReguler::query();
        if ($region !== 'all') {
            if (strcasecmp($region, 'Kaltim') === 0 || strcasecmp($region, 'Kaltim FR') === 0) {
                $nonRegulerProgramsQuery->where(function ($q) {
                    $q->where('region', 'LIKE', '%kaltim%')
                        ->orWhere('region', 'LIKE', '%Kaltim FR%')
                        ->orWhere('region', 'LIKE', '%kulbar%');
                });
            } elseif (strcasecmp($region, 'Kalbar') === 0) {
                $nonRegulerProgramsQuery->where('region', 'LIKE', '%kalbar%');
            } else {
                $nonRegulerProgramsQuery->where('region', 'LIKE', '%'.$region.'%');
            }
        }
        $regulerPrefixes = ['FAT', 'MAT', 'TAT', 'AAT', 'DMT', 'ECT', 'PMT', 'PKT'];
        $nonRegulerPrograms = $nonRegulerProgramsQuery->get();
        $nonRegulerPrograms = $this->filterNonRegulerByYear($nonRegulerPrograms, $year)->filter(function ($p) use ($regulerPrefixes) {
            $name = strtoupper(trim($p->jenis_pelatihan));
            foreach ($regulerPrefixes as $prefix) {
                if (str_starts_with($name, $prefix)) return false;
            }
            return true;
        });

        $trNonReg = (clone $trainingRecordQuery)->where(function ($q) {
            $q->where('program_type', 'non_reguler')
                ->orWhere('reg_non_reg', 'LIKE', '%non%')
                ->orWhereIn('notes', ['Softskill', 'Traksi', 'Administrasi', 'Mill', 'Estate', 'Downstream']);
        })->get();
        $trNonReg = $this->filterRecordsByYearAndCumulativeMonth($trNonReg, $year, $selectedMonthNum)->filter(function ($r) use ($regulerPrefixes) {
            $notes = strtoupper(trim((string) $r->notes));
            foreach ($regulerPrefixes as $prefix) {
                if (str_starts_with($notes, $prefix)) return false;
            }
            return true;
        });

        if ($programType === 'reguler') {
            $nonRegulerPrograms = collect();
        } else {
            if ($category === 'managerial' || strcasecmp($category, 'Managerial and Leadership') === 0 || strcasecmp($category, 'Softskill') === 0) {
                $nonRegulerPrograms = $nonRegulerPrograms->filter(fn ($p) => $p->isSoftSkill() || str_contains($cleanStr($p->kategori_pelatihan), 'managerial') || str_contains($cleanStr($p->sub_kategori), 'soft'));
            } elseif ($category === 'technical' || strcasecmp($category, 'Technical') === 0) {
                $nonRegulerPrograms = $nonRegulerPrograms->filter(fn ($p) => ! $p->isSoftSkill() || str_contains($cleanStr($p->kategori_pelatihan), 'technical'));
            } elseif ($category !== 'all' && $category !== '') {
                $searchCat = $cleanStr($category);
                $nonRegulerPrograms = $nonRegulerPrograms->filter(fn ($p) => str_contains($cleanStr($p->sub_kategori), $searchCat) || str_contains($cleanStr($p->kategori_pelatihan), $searchCat) || str_contains($cleanStr($p->jenis_pelatihan), $searchCat));
            }

            if ($selectedProgram !== 'all' && $selectedProgram !== '') {
                $nonRegulerPrograms = $nonRegulerPrograms->filter(function ($p) use ($selectedProgram, $cleanStr) {
                    $pName = trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $p->jenis_pelatihan));
                    return strcasecmp($pName, $selectedProgram) === 0 || str_contains($cleanStr($pName), $cleanStr($selectedProgram));
                });
            }
        }

        $softPrograms = [];
        $tekPrograms = [];
        foreach ($nonRegulerPrograms as $p) {
            if ($p->isSoftSkill()) {
                $softPrograms[] = $p;
            } else {
                $tekPrograms[] = $p;
            }
        }

        $allSoftProgramIdsMap = array_flip(ProgramNonReguler::all()->filter(fn ($p) => $p->isSoftSkill())->pluck('id_nonreguler')->all());
        $technicalNotesMap = ['ESTATE' => true, 'MILL' => true, 'TRACTION' => true, 'TRAKSI' => true, 'DOWNSTREAM' => true, 'ADMINISTRASI' => true, 'ADMIN' => true];

        $softNonRegRecords = [];
        $tekNonRegRecords = [];

        foreach ($trNonReg as $r) {
            $notes = strtoupper(trim((string) $r->notes));
            $nama = strtolower(trim((string) $r->nama_pelatihan));

            if (isset($technicalNotesMap[$notes])) {
                $isSoft = false;
            } else {
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
                    || ($r->program_id && isset($allSoftProgramIdsMap[(int) $r->program_id]));
            }

            if ($selectedProgram !== 'all' && $selectedProgram !== '') {
                $cleanFilter = $cleanStr($selectedProgram);
                $matchesSelected = str_contains($cleanStr($r->nama_pelatihan), $cleanFilter) || str_contains($cleanStr($r->notes), $cleanFilter);
                if (! $matchesSelected) continue;
            }

            if ($isSoft) {
                $softNonRegRecords[] = $r;
            } else {
                $tekNonRegRecords[] = $r;
            }
        }

        $calcMetrics = function ($progList, array $records) {
            $targPeserta = 0;
            $targBatch = 0;
            $realBatch = 0;

            foreach ($progList as $p) {
                $targPeserta += (int) ($p->TARGET_PESERTA ?? $p->target_total ?? 0);
                $targBatch += (int) ($p->TARGET_BATCH ?? $p->total_batch ?? 0);
                $explicitBatch = (int) ($p->realisasi_batch ?? $p->total_batch ?? 0);
                if ($explicitBatch > 0) $realBatch += $explicitBatch;
            }

            if (empty($progList)) {
                return [
                    'percentage' => 0, 'target' => 100,
                    'peserta_realisasi' => 0, 'peserta_target' => 0,
                    'batch_realisasi' => 0, 'batch_target' => 0,
                    'total_program' => 0,
                ];
            }

            $realPeserta = count($records);
            if ($realBatch === 0 && $realPeserta > 0) {
                $distinct = [];
                foreach ($records as $r) {
                    $k = ($r->nama_pelatihan ?: $r->notes).'|'.$r->region.'|'.$r->bulan;
                    $distinct[$k] = true;
                }
                $realBatch = count($distinct);
            }

            $pct = $targPeserta > 0 ? round(($realPeserta / $targPeserta) * 100) : 0;

            return [
                'percentage' => $pct, 'target' => 100,
                'peserta_realisasi' => $realPeserta, 'peserta_target' => $targPeserta,
                'batch_realisasi' => $realBatch, 'batch_target' => $targBatch,
                'total_program' => count($progList),
            ];
        };

        $softSkill = $calcMetrics($softPrograms, $softNonRegRecords);
        $teknikal = $calcMetrics($tekPrograms, $tekNonRegRecords);

        return compact('regulerMt', 'talentProgram', 'softSkill', 'teknikal');
    }

    protected function computeMonthlyChartData(int $year, string $region, string $programType, string $category, string $selectedProgram, $trainingRecordQuery): array
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthMap = ['JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MEI' => 5, 'MAY' => 5, 'JUN' => 6, 'JUL' => 7, 'AGU' => 8, 'AGS' => 8, 'AUG' => 8, 'SEP' => 9, 'OKT' => 10, 'OCT' => 10, 'NOV' => 11, 'DES' => 12, 'DEC' => 12];

        $chartRegulerMap = ProgramReguler::all()->keyBy('Program_reguler_id');
        $chartNonRegulerMap = ProgramNonReguler::all()->keyBy('id_nonreguler');

        $cleanedRegulerList = [];
        foreach ($chartRegulerMap as $p) {
            $pName = strtoupper(trim(preg_replace('/\s*\(.*?\)\s*/', '', (string) $p->Training_Program)));
            if ($pName !== '') $cleanedRegulerList[] = ['name' => $pName, 'tahun' => $p->tahun];
        }
        $cleanedNonRegulerList = [];
        foreach ($chartNonRegulerMap as $p) {
            $pName = strtoupper(trim(preg_replace('/\s*\(.*?\)\s*/', '', (string) $p->jenis_pelatihan)));
            if ($pName !== '') $cleanedNonRegulerList[] = ['name' => $pName, 'tahun' => $p->tahun];
        }

        $chartRawRecords = (clone $trainingRecordQuery)->get();
        $chartYearRecords = $chartRawRecords->filter(function ($r) use ($year, $chartRegulerMap, $chartNonRegulerMap, $cleanedRegulerList, $cleanedNonRegulerList) {
            if (! empty($r->tahun) && is_numeric($r->tahun)) {
                return (int) $r->tahun === (int) $year;
            }

            $pTahun = null;
            if ($r->program_type === 'reguler') {
                if ($r->program_id && isset($chartRegulerMap[$r->program_id])) {
                    $pTahun = $chartRegulerMap[$r->program_id]->tahun;
                } else {
                    $note = strtoupper(trim((string) ($r->notes ?: $r->nama_pelatihan)));
                    foreach ($cleanedRegulerList as $item) {
                        if (str_contains($note, $item['name']) || str_contains($item['name'], $note)) {
                            $pTahun = $item['tahun'];
                            break;
                        }
                    }
                }
            } elseif ($r->program_type === 'non_reguler') {
                if ($r->program_id && isset($chartNonRegulerMap[$r->program_id])) {
                    $pTahun = $chartNonRegulerMap[$r->program_id]->tahun;
                } else {
                    $note = strtoupper(trim((string) ($r->notes ?: $r->nama_pelatihan)));
                    foreach ($cleanedNonRegulerList as $item) {
                        if (str_contains($note, $item['name']) || str_contains($item['name'], $note)) {
                            $pTahun = $item['tahun'];
                            break;
                        }
                    }
                }
            }

            if ($pTahun !== null) return (int) $pTahun === (int) $year;
            if (! empty($r->training_date)) return (int) date('Y', strtotime($r->training_date)) === (int) $year;
            return false;
        });

        $monthlyPesertaMap = array_fill(1, 12, 0);
        $monthlyProgramSets = array_fill(1, 12, []);
        $monthlyParticipantSeen = array_fill(1, 12, []);

        foreach ($chartYearRecords as $r) {
            $mCode = strtoupper(substr(trim((string) $r->bulan), 0, 3));
            $mNum = $monthMap[$mCode] ?? null;
            if (! $mNum && $r->training_date) {
                $mNum = (int) date('n', strtotime($r->training_date));
            }

            $parentMonths = [];
            if ($r->program_type === 'reguler' && $r->program_id && isset($chartRegulerMap[$r->program_id])) {
                $p = $chartRegulerMap[$r->program_id];
                $dates = is_array($p->tanggal_pelaksanaan) ? $p->tanggal_pelaksanaan : json_decode($p->tanggal_pelaksanaan ?? '[]', true);
                if (is_array($dates)) {
                    foreach ($dates as $d) {
                        if ($d && (int) date('Y', strtotime($d)) === (int) $year) $parentMonths[] = (int) date('n', strtotime($d));
                    }
                }
                if (empty($parentMonths) && $p->Mulai_program && (int) date('Y', strtotime($p->Mulai_program)) === (int) $year) {
                    $parentMonths[] = (int) date('n', strtotime($p->Mulai_program));
                }
            } elseif ($r->program_type === 'non_reguler' && $r->program_id && isset($chartNonRegulerMap[$r->program_id])) {
                $p = $chartNonRegulerMap[$r->program_id];
                $dates = is_array($p->tanggal_pelaksanaan) ? $p->tanggal_pelaksanaan : json_decode($p->tanggal_pelaksanaan ?? '[]', true);
                if (is_array($dates)) {
                    foreach ($dates as $d) {
                        if ($d && (int) date('Y', strtotime($d)) === (int) $year) $parentMonths[] = (int) date('n', strtotime($d));
                    }
                }
                if (empty($parentMonths) && $p->start_date && (int) date('Y', strtotime($p->start_date)) === (int) $year) {
                    $parentMonths[] = (int) date('n', strtotime($p->start_date));
                }
            }
            $parentMonths = array_values(array_unique(array_filter($parentMonths)));
            if (! empty($parentMonths) && (! $mNum || ! in_array($mNum, $parentMonths, true))) {
                $mNum = $parentMonths[0];
            }

            if ($mNum && isset($monthlyPesertaMap[$mNum])) {
                $progKey = $r->program_id ? ($r->program_type.':'.$r->program_id) : ($r->nama_pelatihan ?: $r->notes);
                if ($r->program_type === 'reguler') {
                    $pKey = $r->participant_name ? trim(strtolower($r->participant_name)) : ($r->participant_identifier ?: $r->id);
                    if (! isset($monthlyParticipantSeen[$mNum][$progKey][$pKey])) {
                        $monthlyParticipantSeen[$mNum][$progKey][$pKey] = true;
                        $monthlyPesertaMap[$mNum]++;
                    }
                } else {
                    $monthlyPesertaMap[$mNum]++;
                }
                $monthlyProgramSets[$mNum][$progKey] = true;
            }
        }

        $isDefaultView = ($year === 2026 && $region === 'all' && $programType === 'all' && $category === 'all' && ($selectedProgram === 'all' || $selectedProgram === ''));
        $mentorBatchCounts2026 = [
            1 => 11, 2 => 15, 3 => 16, 4 => 18, 5 => 31, 6 => 21,
            7 => 17, 8 => 27, 9 => 36, 10 => 0, 11 => 0, 12 => 0,
        ];

        $monthlyPeserta = [];
        $monthlyProgram = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyPeserta[] = $monthlyPesertaMap[$m];
            $monthlyProgram[] = ($isDefaultView && isset($mentorBatchCounts2026[$m])) ? $mentorBatchCounts2026[$m] : count($monthlyProgramSets[$m]);
        }

        return [
            'labels' => $months,
            'peserta' => $monthlyPeserta,
            'program' => $monthlyProgram,
            'total_peserta_ytd' => array_sum($monthlyPeserta),
            'total_program_ytd' => array_sum($monthlyProgram),
        ];
    }

    protected function computeEvaluasi(): array
    {
        return [
            'average_score' => 4.43,
            'max_score' => 5.00,
            'ratings' => [
                ['stars' => '5. Sangat Baik', 'percent' => 58, 'color' => 'bg-[#1b4332]'],
                ['stars' => '4. Baik', 'percent' => 32, 'color' => 'bg-[#2d5a27]'],
                ['stars' => '3. Cukup', 'percent' => 7, 'color' => 'bg-amber-500'],
                ['stars' => '2. Kurang', 'percent' => 2, 'color' => 'bg-orange-500'],
                ['stars' => '1. Sangat Kurang', 'percent' => 1, 'color' => 'bg-rose-500'],
            ],
            'footer' => [
                'rata_rata' => '4.43 / 5.00',
                'responden' => '3.248 org',
                'pelatihan' => '129 prg',
                'respon_rate' => '92%',
            ],
        ];
    }

    public function updateRegulerRow(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:program_reguler,Program_reguler_id',
            'kelas' => 'nullable|integer|min:0',
            'resign' => 'nullable|integer|min:0',
            'staff' => 'nullable|integer|min:0',
            'lulus' => 'nullable|integer|min:0',
        ]);

        $program = ProgramReguler::findOrFail($validated['id']);

        $kelas = array_key_exists('kelas', $validated) ? ($validated['kelas'] !== null ? (int) $validated['kelas'] : null) : $program->kelas;
        $resign = array_key_exists('resign', $validated) ? ($validated['resign'] !== null ? (int) $validated['resign'] : null) : $program->resign;

        $staff = null;
        if (array_key_exists('staff', $validated) && $validated['staff'] !== null) {
            $staff = (int) $validated['staff'];
        } elseif (array_key_exists('lulus', $validated) && $validated['lulus'] !== null) {
            $staff = (int) $validated['lulus'];
        } elseif ($program->staff !== null) {
            $staff = $program->staff;
        } elseif ($program->lulus !== null) {
            $staff = $program->lulus;
        }

        $program->update([
            'kelas' => $kelas,
            'resign' => $resign,
            'staff' => $staff,
            'lulus' => $staff,
        ]);

        ActivityLog::log(
            'Edit',
            'Dashboard Realisasi',
            "Mengubah data {$program->Training_Program}: Kelas={$kelas}, Resign={$resign}, Staff={$staff}",
            ['id' => $program->Program_reguler_id, 'kelas' => $kelas, 'resign' => $resign, 'staff' => $staff]
        );

        Cache::put('dreal_cache_version', time());
        Cache::put('dashboard_cache_version', time());
        Cache::put('rvb_cache_version', time());
        Cache::forget('dreal_filter_options');
        Cache::forget('portal_dashboard_years');

        return response()->json([
            'success' => true,
            'message' => 'Data program '.$program->Training_Program.' berhasil diperbarui.',
            'data' => [
                'id' => $program->Program_reguler_id,
                'name' => $program->Training_Program,
                'rencana' => (int) ($program->TARGET_PESERTA ?? $program->target_peserta ?? 0),
                'kelas' => $program->kelas ?? 0,
                'resign' => $program->resign ?? 0,
                'staff' => $program->staff ?? 0,
                'lulus' => $program->lulus ?? 0,
            ],
        ]);
    }
}

