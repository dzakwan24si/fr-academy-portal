<?php

namespace App\Services;

use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use Carbon\Carbon;
use DateTimeInterface;

class TrainingMetricService
{
    /**
     * Map month representation to integer (1 - 12).
     */
    public static function parseMonthNumber($value): ?int
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

    /**
     * Extract 4-digit year from diverse inputs (DateTime, string, JSON, array, timestamp).
     */
    public static function extractYearFromField($val): ?int
    {
        if (empty($val)) {
            return null;
        }
        if ($val instanceof DateTimeInterface) {
            return (int) $val->format('Y');
        }
        if (is_numeric($val) && (int) $val >= 2000 && (int) $val <= 2100) {
            return (int) $val;
        }
        if (is_array($val)) {
            foreach ($val as $item) {
                $y = self::extractYearFromField($item);
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
                    return self::extractYearFromField($decoded);
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

    /**
     * Resolve the primary year of a Program Reguler.
     */
    public static function resolveProgramRegulerYear($p): int
    {
        if (! empty($p->tahun) && ($y = self::extractYearFromField($p->tahun))) {
            return $y;
        }
        if ($p->Mulai_program && ($y = self::extractYearFromField($p->Mulai_program))) {
            return $y;
        }
        $title = $p->Training_Program ?? $p->Nama_Program_reguler ?? '';
        if (preg_match('/\b(20\d{2})\b/', $title, $m)) {
            return (int) $m[1];
        }
        if (! empty($p->tanggal_pelaksanaan) && ($y = self::extractYearFromField($p->tanggal_pelaksanaan))) {
            return $y;
        }
        if ($p->Selesai_program && ($y = self::extractYearFromField($p->Selesai_program))) {
            return $y;
        }
        if ($p->created_at && ($y = self::extractYearFromField($p->created_at))) {
            return $y;
        }

        return 2026;
    }

    /**
     * Resolve the primary year of a Program Non-Reguler.
     */
    public static function resolveProgramNonRegulerYear($p): int
    {
        if (! empty($p->tahun) && ($y = self::extractYearFromField($p->tahun))) {
            return $y;
        }
        if ($p->start_date && ($y = self::extractYearFromField($p->start_date))) {
            return $y;
        }
        $title = $p->jenis_pelatihan ?? '';
        if (preg_match('/\b(20\d{2})\b/', $title, $m)) {
            return (int) $m[1];
        }
        if (! empty($p->tanggal_pelaksanaan) && ($y = self::extractYearFromField($p->tanggal_pelaksanaan))) {
            return $y;
        }
        if ($p->end_date && ($y = self::extractYearFromField($p->end_date))) {
            return $y;
        }
        if ($p->created_at && ($y = self::extractYearFromField($p->created_at))) {
            return $y;
        }

        return 2026;
    }

    /**
     * Get start date string (Y-m-d) for Program Reguler.
     */
    public static function getProgramRegulerStartDate($p): ?string
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
            return $p->Mulai_program instanceof DateTimeInterface
                ? $p->Mulai_program->format('Y-m-d')
                : date('Y-m-d', strtotime($p->Mulai_program));
        }

        return null;
    }

    /**
     * Get end date string (Y-m-d) for Program Reguler.
     */
    public static function getProgramRegulerEndDate($p): ?string
    {
        if ($p->Selesai_program) {
            return $p->Selesai_program instanceof DateTimeInterface
                ? $p->Selesai_program->format('Y-m-d')
                : date('Y-m-d', strtotime($p->Selesai_program));
        }

        if (! empty($p->tanggal_pelaksanaan)) {
            $dates = is_array($p->tanggal_pelaksanaan) ? $p->tanggal_pelaksanaan : json_decode($p->tanggal_pelaksanaan, true);
            if (is_array($dates)) {
                $validDates = array_values(array_filter($dates));
                if (! empty($validDates)) {
                    sort($validDates);

                    return end($validDates) ?: null;
                }
            }
        }

        return null;
    }

    /**
     * Filter Collection of ProgramReguler by target year.
     */
    public static function filterRegulerByYear($programs, int $year)
    {
        return $programs->filter(function ($p) use ($year) {
            $pYear = (! empty($p->tahun) && is_numeric($p->tahun)) ? (int) $p->tahun : self::resolveProgramRegulerYear($p);

            return $pYear === $year;
        });
    }

    /**
     * Filter Collection of ProgramNonReguler by target year.
     */
    public static function filterNonRegulerByYear($programs, int $year)
    {
        return $programs->filter(function ($p) use ($year) {
            $pYear = (! empty($p->tahun) && is_numeric($p->tahun)) ? (int) $p->tahun : self::resolveProgramNonRegulerYear($p);

            return $pYear === $year;
        });
    }

    /**
     * Filter TrainingRecord collection by year and cumulative month.
     * Unified logic for all dashboards.
     */
    public static function filterRecordsByYearAndCumulativeMonth($records, int $year, ?int $selectedMonthNum, array $validRegulerProgIds = [], array $regulerYearById = [], array $regulerYearByAng = [])
    {
        return $records->filter(function ($r) use ($year, $selectedMonthNum, $validRegulerProgIds, $regulerYearById, $regulerYearByAng) {
            // 1. Year filter
            $rYear = null;
            if ($r->program_type === 'reguler') {
                if ($r->program_id && isset($regulerYearById[(int) $r->program_id])) {
                    $rYear = $regulerYearById[(int) $r->program_id];
                } elseif (! empty($r->notes)) {
                    $norm = self::normalizeAngkatan((string) $r->notes);
                    if (isset($regulerYearByAng[$norm])) {
                        $rYear = $regulerYearByAng[$norm];
                    }
                }
            }

            if ($rYear === null) {
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
            }

            if ($rYear !== $year) {
                // Support carry-over training records for reguler programs
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
                    $mNum = self::parseMonthNumber($r->bulan);
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

    /**
     * Standardized name normalization for employee / trainee comparisons.
     */
    public static function normalizeName(?string $name): string
    {
        if (! $name) {
            return '';
        }

        $clean = strtolower(trim($name));
        $clean = preg_replace('/[.\-_,]/', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        $clean = str_replace('deofinka', 'df', $clean);

        return trim($clean);
    }

    /**
     * Standardized angkatan batch normalization (e.g. "FAT 1 2024", "TAT-II", etc.).
     */
    public static function normalizeAngkatan(string $name): string
    {
        $norm = strtoupper(trim(preg_replace('/\s*\(.*?\)\s*/', '', $name)));
        $norm = str_replace(
            ['FIELD ASSISTANT', 'ESTATE CADET TRAINEE', 'ESTATE CADET', 'MILL ASSISTANT', 'ADMIN ASSISTANT', 'PROCESSING', 'PABRIK'],
            ['FAT', 'ECT', 'ECT', 'MAT', 'AAT', 'PMT', 'PMT'],
            $norm
        );
        $norm = preg_replace('/[^A-Z0-9]/', ' ', $norm);
        $parts = array_values(array_filter(explode(' ', $norm)));

        return implode('-', $parts);
    }

    /**
     * Compute actual participant count for a ProgramReguler batch using matched TrainingRecords,
     * Employees, and Culled data.
     */
    public static function resolveRegulerKelasCount($program, array $trByAngkatan, array $empByAngkatan, array $culledByAngkatan): int
    {
        if ($program->kelas !== null) {
            return (int) $program->kelas;
        }

        $pName = trim((string) preg_replace('/\s*\(.*?\)\s*/', '', $program->Training_Program));
        $normPName = self::normalizeAngkatan($pName);

        $names = [];
        foreach ([$trByAngkatan, $empByAngkatan, $culledByAngkatan] as $source) {
            foreach ($source as $ang => $nList) {
                if ($ang === $normPName || (str_starts_with($normPName, 'AAT-II') && str_starts_with($ang, 'AAT-II'))) {
                    foreach ($nList as $n) {
                        if ($n) {
                            $names[$n] = true;
                        }
                    }
                }
            }
        }

        return count($names);
    }
}
