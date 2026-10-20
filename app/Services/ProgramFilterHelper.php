<?php

namespace App\Services;

use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;

class ProgramFilterHelper
{
    /**
     * Get all unique available years dynamically from program_reguler,
     * program_non_reguler, and training_records tables.
     */
    public static function getAvailableYears(): array
    {
        $years = collect();

        // 1. Extract from ProgramReguler
        $regulers = ProgramReguler::all();
        foreach ($regulers as $p) {
            foreach (self::extractYearsFromModel($p, true) as $y) {
                $years->push($y);
            }
        }

        // 2. Extract from ProgramNonReguler
        $nonRegulers = ProgramNonReguler::all();
        foreach ($nonRegulers as $p) {
            foreach (self::extractYearsFromModel($p, false) as $y) {
                $years->push($y);
            }
        }

        // 3. Extract from TrainingRecord
        $trYears = TrainingRecord::whereNotNull('tahun')
            ->where('tahun', '>', 0)
            ->distinct()
            ->pluck('tahun')
            ->map(fn ($y) => (int) $y)
            ->filter();
        $years = $years->concat($trYears);

        $trDateYears = TrainingRecord::whereNotNull('training_date')
            ->get()
            ->map(fn ($r) => (int) date('Y', strtotime($r->training_date)))
            ->filter();
        $years = $years->concat($trDateYears);

        // 4. Default baseline years (ensure standard system years are present)
        $currentYear = (int) date('Y');
        $years->push($currentYear);
        $years = $years->concat([2024, 2025, 2026, 2027]);

        return $years->filter(fn ($y) => $y >= 2000 && $y <= 2100)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Extract all valid years associated with a program (Reguler or Non-Reguler).
     */
    public static function extractYearsFromModel($p, bool $isReguler = true): array
    {
        $years = [];

        // 1. Explicit 'tahun' column
        if (! empty($p->tahun) && is_numeric($p->tahun)) {
            $y = (int) $p->tahun;
            if ($y >= 2000 && $y <= 2100) {
                $years[] = $y;
            }
        }

        // 2. Mulai / Start Date
        $startDate = $isReguler ? $p->Mulai_program : $p->start_date;
        if ($startDate) {
            $y = (int) date('Y', strtotime($startDate));
            if ($y >= 2000 && $y <= 2100) {
                $years[] = $y;
            }
        }

        // 3. Selesai / End Date
        $endDate = $isReguler ? $p->Selesai_program : $p->end_date;
        if ($endDate) {
            $y = (int) date('Y', strtotime($endDate));
            if ($y >= 2000 && $y <= 2100) {
                $years[] = $y;
            }
        }

        // 4. Regex extraction from Program Name / Title
        $name = $isReguler ? ($p->Training_Program ?? '') : ($p->jenis_pelatihan ?? '');
        if (preg_match_all('/\b(20\d{2})\b/', $name, $matches)) {
            foreach ($matches[1] as $m) {
                $y = (int) $m;
                if ($y >= 2000 && $y <= 2100) {
                    $years[] = $y;
                }
            }
        }

        // 5. Tanggal Pelaksanaan (array / json / string)
        if (! empty($p->tanggal_pelaksanaan)) {
            $dates = is_array($p->tanggal_pelaksanaan) ? $p->tanggal_pelaksanaan : json_decode($p->tanggal_pelaksanaan, true);
            if (is_array($dates)) {
                foreach ($dates as $d) {
                    if ($d && $time = strtotime($d)) {
                        $y = (int) date('Y', $time);
                        if ($y >= 2000 && $y <= 2100) {
                            $years[] = $y;
                        }
                    }
                }
            } elseif (is_string($p->tanggal_pelaksanaan)) {
                if (preg_match_all('/\b(20\d{2})\b/', $p->tanggal_pelaksanaan, $m)) {
                    foreach ($m[1] as $val) {
                        $y = (int) $val;
                        if ($y >= 2000 && $y <= 2100) {
                            $years[] = $y;
                        }
                    }
                }
            }
        }

        // 6. Created_at as last fallback
        if (empty($years) && ! empty($p->created_at)) {
            $y = (int) date('Y', strtotime($p->created_at));
            if ($y >= 2000 && $y <= 2100) {
                $years[] = $y;
            }
        }

        return array_values(array_unique($years));
    }

    /**
     * Check whether a program belongs to a specific target year.
     */
    public static function programBelongsToYear($p, int $year, bool $isReguler = true): bool
    {
        $years = self::extractYearsFromModel($p, $isReguler);
        if (in_array($year, $years, true)) {
            return true;
        }

        // Special carry-over rule for year 2026: include 2025 carry-over or planned 2027 if specified
        if ($year === 2026 && $isReguler) {
            $name = strtolower((string) ($p->Training_Program ?? ''));
            if (str_contains($name, 'carry over') || str_contains($name, 'tat i 2027') || str_contains($name, 'mat i 2027')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Resolve single primary year for a program.
     */
    public static function resolvePrimaryYear($p, bool $isReguler = true, int $default = 2026): int
    {
        $years = self::extractYearsFromModel($p, $isReguler);

        return ! empty($years) ? $years[0] : $default;
    }

    /**
     * Get all unique canonical regions from program_reguler, program_non_reguler, and training_record.
     */
    public static function getAvailableRegions(): array
    {
        $regRegions = ProgramReguler::whereNotNull('Region')->where('Region', '!=', '')->distinct()->pluck('Region');
        $nonRegRegions = ProgramNonReguler::whereNotNull('region')->where('region', '!=', '')->distinct()->pluck('region');
        $trRegions = TrainingRecord::whereNotNull('region')->where('region', '!=', '')->distinct()->pluck('region');

        $rawRegions = $regRegions->concat($nonRegRegions)->concat($trRegions);

        return RegionHelper::getCanonicalRegions($rawRegions);
    }
}
