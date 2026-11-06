<?php

namespace App\Traits;

use App\Services\SpreadsheetReader;

trait HandlesCsvImports
{
    /**
     * Read parsed CSV dataset with clean headers and valid rows.
     *
     * @return array{headers: array, cleanHeaders: array, rows: array}
     */
    protected function parseCsvDataset(string $filePath): array
    {
        $rawRows = SpreadsheetReader::readCsv($filePath);
        if (empty($rawRows)) {
            return ['headers' => [], 'cleanHeaders' => [], 'rows' => []];
        }

        $headers = array_shift($rawRows);
        $cleanHeaders = $this->cleanCsvHeaders($headers);

        return [
            'headers' => $headers,
            'cleanHeaders' => $cleanHeaders,
            'rows' => $rawRows,
        ];
    }
    /**
     * Detect CSV delimiter (comma, semicolon, or tab).
     */
    protected function detectCsvDelimiter(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return ',';
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if (! $firstLine) {
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
            $clean = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string) $h)));
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

        if (is_int($val) || is_float($val)) {
            return (float) $val;
        }

        $raw = trim((string) $val);

        // Check if string contains currency indicators (e.g., 'Rp', 'IDR')
        $hasCurrency = (bool) preg_match('/(?:rp|idr)/i', $raw);

        // Remove currency prefixes/letters and whitespace, keep digits, separators, signs, scientific notation
        $str = preg_replace('/[^\d.,\-+eE]/', '', $raw);

        if ($str === '' || $str === '-' || $str === '+') {
            return (float) $default;
        }

        // Plain integer without dots or commas (e.g. "850000", "-500")
        if (preg_match('/^[+-]?\d+$/', $str)) {
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
                // Multiple commas: 2,460,660 -> remove commas
                $str = str_replace(',', '', $str);
            } elseif (preg_match('/^[+-]?\d{1,3},\d{3}$/', $str)) {
                // Single comma with exactly 3 digits after (US thousands format, e.g. 187,338)
                $str = str_replace(',', '', $str);
            } else {
                // Indonesian decimal format (e.g. 187,50 -> 187.50)
                $str = str_replace(',', '.', $str);
            }
        } elseif ($hasDot) {
            if (substr_count($str, '.') > 1) {
                // Multiple dots: 2.460.660 -> remove dots
                $str = str_replace('.', '', $str);
            } elseif ($hasCurrency || preg_match('/^[+-]?[1-9]\d{0,2}\.\d{3}$/', $str)) {
                // Single dot thousands separator (Indonesian format, e.g. "Rp 187.338" or "187.338" or "31.846")
                $str = str_replace('.', '', $str);
            }
            // Otherwise, keep the dot as decimal (e.g. "12.5", "12.50", "0.75")
        }

        return is_numeric($str) ? (float) $str : (float) $default;
    }
}
