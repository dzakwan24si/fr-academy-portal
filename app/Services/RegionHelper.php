<?php

namespace App\Services;

class RegionHelper
{
    /**
     * Normalize messy region string into a clean canonical keyword/name.
     * Examples:
     * - "Kaltim - FR", "Kubar", "KALTIM" -> "Kaltim"
     * - "Kalbar", "Kalbar FR", "Kalbar - FR FR" -> "Kalbar"
     * - "Riau - FRLC", "Riau", "RIAU" -> "Riau"
     */
    public static function normalize(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $trimmed = trim((string) $raw);
        if ($trimmed === '' || $trimmed === '-' || strcasecmp($trimmed, 'all') === 0 || strcasecmp($trimmed, 'null') === 0) {
            return null;
        }

        $lower = strtolower($trimmed);

        if (str_contains($lower, 'kaltim') || str_contains($lower, 'kubar')) {
            return 'Kaltim';
        }
        if (str_contains($lower, 'kalbar')) {
            return 'Kalbar';
        }
        if (str_contains($lower, 'riau') || str_contains($lower, 'frlc')) {
            return 'Riau';
        }
        if (str_contains($lower, 'kalsel')) {
            return 'Kalsel';
        }
        if (str_contains($lower, 'kalteng')) {
            return 'Kalteng';
        }
        if (str_contains($lower, 'jkt') || str_contains($lower, 'jakarta')) {
            return 'Jakarta';
        }
        if (str_contains($lower, 'sumut')) {
            return 'Sumut';
        }
        if (str_contains($lower, 'head office') || $lower === 'ho') {
            return 'Head Office';
        }

        // Strip noisy suffixes like " - FR", " FR", " - FRLC"
        $cleaned = preg_replace('/\s*-\s*fr.*$/i', '', $trimmed);
        $cleaned = preg_replace('/\s+fr(\s+fr)*$/i', '', $cleaned);
        $cleaned = trim($cleaned);

        return $cleaned !== '' ? ucwords(strtolower($cleaned)) : null;
    }

    /**
     * Get a list of clean, distinct canonical region names from a collection/array of raw regions.
     */
    public static function getCanonicalRegions($rawRegions = null): array
    {
        $defaultOrder = ['Riau', 'Kalbar', 'Kaltim'];

        if ($rawRegions === null) {
            return $defaultOrder;
        }

        $normalized = collect($rawRegions)
            ->map(fn ($r) => self::normalize((string) $r))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return collect($defaultOrder)
            ->merge($normalized)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Check if a record's region matches the selected filter keyword.
     */
    public static function matches(?string $recordRegion, ?string $filterRegion): bool
    {
        if (empty($filterRegion) || $filterRegion === 'all' || $filterRegion === '-') {
            return true;
        }
        if (empty($recordRegion) || $recordRegion === '-') {
            return false;
        }

        $rNorm = self::normalize($recordRegion);
        $fNorm = self::normalize($filterRegion);

        if ($rNorm && $fNorm && strcasecmp($rNorm, $fNorm) === 0) {
            return true;
        }

        $rLower = strtolower(trim($recordRegion));
        $fLower = strtolower(trim($filterRegion));

        if (str_contains($fLower, 'kaltim') || str_contains($fLower, 'kubar')) {
            return str_contains($rLower, 'kaltim') || str_contains($rLower, 'kubar');
        }
        if (str_contains($fLower, 'kalbar')) {
            return str_contains($rLower, 'kalbar');
        }
        if (str_contains($fLower, 'riau')) {
            return str_contains($rLower, 'riau') || str_contains($rLower, 'frlc');
        }
        if (str_contains($fLower, 'kalsel')) {
            return str_contains($rLower, 'kalsel');
        }
        if (str_contains($fLower, 'kalteng')) {
            return str_contains($rLower, 'kalteng');
        }

        return str_contains($rLower, $fLower) || str_contains($fLower, $rLower);
    }

    /**
     * Apply Eloquent query filter for region columns based on keywords.
     */
    public static function applyQueryFilter($query, string $column, ?string $filterRegion)
    {
        if (empty($filterRegion) || $filterRegion === 'all') {
            return $query;
        }

        $fLower = strtolower(trim($filterRegion));

        return $query->where(function ($q) use ($column, $fLower, $filterRegion) {
            if (str_contains($fLower, 'kaltim') || str_contains($fLower, 'kubar')) {
                $q->where($column, 'like', '%kaltim%')
                    ->orWhere($column, 'like', '%kubar%');
            } elseif (str_contains($fLower, 'kalbar')) {
                $q->where($column, 'like', '%kalbar%');
            } elseif (str_contains($fLower, 'riau')) {
                $q->where($column, 'like', '%riau%')
                    ->orWhere($column, 'like', '%frlc%');
            } elseif (str_contains($fLower, 'kalsel')) {
                $q->where($column, 'like', '%kalsel%');
            } elseif (str_contains($fLower, 'kalteng')) {
                $q->where($column, 'like', '%kalteng%');
            } elseif (str_contains($fLower, 'jkt') || str_contains($fLower, 'jakarta')) {
                $q->where($column, 'like', '%jkt%')
                    ->orWhere($column, 'like', '%jakarta%');
            } else {
                $q->where($column, 'like', "%{$filterRegion}%");
            }
        });
    }
}
