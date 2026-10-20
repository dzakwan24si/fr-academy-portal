<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Program\TrainingRecord;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Http\Controllers\Program\RealisasiVsBatchController;

$ctrl = new RealisasiVsBatchController();

$year = 2026;
$month = 1;

$t0 = microtime(true);

$records = TrainingRecord::where('status', 'valid')
    ->where(function ($q) use ($year) {
        $q->whereBetween('tahun', [$year - 1, $year])
            ->orWhereNull('tahun')
            ->orWhere('tahun', 0);
    })
    ->get();

$processedRecords = [];
foreach ($records as $r) {
    $rMonth = $r->bulan ? $ctrl->monthNumber($r->bulan) : null;
    if ($rMonth !== null && $rMonth > $month) {
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

$programs = collect();
$programs = $programs->concat(ProgramNonReguler::all()->map(fn ($p) => (new ReflectionMethod($ctrl, 'programData'))->invoke($ctrl, $p, 'non_reguler', $year, $month)));
$programs = $programs->concat(ProgramReguler::all()->map(fn ($p) => (new ReflectionMethod($ctrl, 'programData'))->invoke($ctrl, $p, 'reguler', $year, $month)));
$programs = $programs->filter()->values();

$refReg = new ReflectionMethod($ctrl, 'recordMatchesRegion');
$refReg->setAccessible(true);
$refNorm = new ReflectionMethod($ctrl, 'normalizeRegion');
$refNorm->setAccessible(true);

$results = [];
foreach ($programs as $program) {
    $progType = $program['program_type'];
    $progId = (int) $program['program_id'];
    $progName = strtolower(trim((string) ($program['program_name'] ?? '')));
    $cleanProg = preg_replace('/[^\w\s]/u', ' ', $progName);
    $progWords = array_values(array_filter(explode(' ', $cleanProg)));
    $firstTwoProg = implode(' ', array_slice($progWords, 0, 2));
    $isCarryOver = str_contains($progName, 'carry over');
    $progRegion = strtolower(trim((string) ($program['region'] ?? '')));
    $normProgRegion = $refNorm->invoke($ctrl, $progRegion);

    $matchingCount = 0;

    foreach ($processedRecords as $r) {
        // Region
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
                    && $refNorm->invoke($ctrl, $recordRegion) !== $normProgRegion) {
                    continue;
                }
            }
        }

        // Year & month
        if ($r->tahun > 0 || $r->bulan) {
            if ($r->tahun > 0 && $r->tahun !== $year) {
                if (! ($isCarryOver && ($r->tahun === $year - 1 || $r->tahun === $year))) {
                    continue;
                }
            }
            if ($r->bulan) {
                if ($r->monthNum === null || $r->monthNum > $month) {
                    continue;
                }
            }
        } else {
            $hasDate = false;
            foreach ($r->activityDates as $d) {
                if ($d && (int) date('Y', strtotime($d)) === $year && (int) date('n', strtotime($d)) <= $month) {
                    $hasDate = true;
                    break;
                }
            }
            if (! $hasDate) {
                continue;
            }
        }

        // Match Activity
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

    if ($matchingCount > 0) {
        $results[$program['program_type'] . '_' . $program['program_id']] = [
            'name' => $program['program_name'],
            'realized_participants' => $matchingCount
        ];
    }
}

$elapsed = microtime(true) - $t0;
echo "Optimized count > 0: " . count($results) . " in " . round($elapsed, 3) . "s" . PHP_EOL;
echo json_encode($results, JSON_PRETTY_PRINT) . PHP_EOL;
