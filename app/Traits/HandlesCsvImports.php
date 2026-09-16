<?php

namespace App\Traits;

trait HandlesCsvImports
{
    /**
     * Detect CSV delimiter (comma, semicolon, or tab).
     */
    protected function detectCsvDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ',';
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine) {
            return ',';
        }

        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            return ';';
        }

        if (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
            return "\t";
        }

        return ',';
    }

    /**
     * Clean and normalize raw CSV header strings.
     */
    protected function cleanCsvHeaders(array $rawHeaders): array
    {
        $cleanHeaders = [];
        foreach ($rawHeaders as $idx => $h) {
            $clean = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h)));
            $clean = str_replace([' ', '_', '-', '.', '/', '(', ')'], '', $clean);
            $cleanHeaders[$idx] = $clean;
        }
        return $cleanHeaders;
    }

    /**
     * Safely parse numeric values from various localized formats (Indonesian, US, currency strings, floats).
     */
    protected function cleanNumeric($val, $default = 0): float
    {
        if ($val === null || $val === '') {
            return (float) $default;
        }

        if (is_numeric($val)) {
            return (float) $val;
        }

        $str = trim((string) $val);
        // Remove currency prefixes/letters and whitespace, keep digits, separators, signs, scientific notation
        $str = preg_replace('/[^\d.,\-+eE]/', '', $str);

        if ($str === '' || $str === '-' || $str === '+') {
            return (float) $default;
        }

        if (is_numeric($str)) {
            return (float) $str;
        }

        $hasDot = strpos($str, '.') !== false;
        $hasComma = strpos($str, ',') !== false;

        if ($hasDot && $hasComma) {
            $lastDot = strrpos($str, '.');
            $lastComma = strrpos($str, ',');
            if ($lastComma > $lastDot) {
                // Indonesian/EU style: 543.966.400,56 -> remove dots, change comma to dot
                $str = str_replace('.', '', $str);
                $str = str_replace(',', '.', $str);
            } else {
                // US style: 543,966,400.56 -> remove commas
                $str = str_replace(',', '', $str);
            }
        } elseif ($hasComma) {
            if (substr_count($str, ',') > 1) {
                $str = str_replace(',', '', $str);
            } else {
                $str = str_replace(',', '.', $str);
            }
        } elseif ($hasDot) {
            if (substr_count($str, '.') > 1) {
                $str = str_replace('.', '', $str);
            }
        }

        return is_numeric($str) ? (float) $str : (float) $default;
    }
}
