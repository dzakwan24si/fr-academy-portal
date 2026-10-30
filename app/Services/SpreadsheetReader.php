<?php

namespace App\Services;

use ZipArchive;

class SpreadsheetReader
{
    /**
     * Read CSV or XLSX file and return array of rows.
     */
    public static function read(string $path, string $extension = ''): array
    {
        if (empty($extension)) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
        }
        $ext = strtolower($extension);
        if ($ext === 'xlsx') {
            if (! class_exists('\ZipArchive')) {
                throw new \RuntimeException('Server web development belum mengaktifkan ekstensi PHP Zip (ZipArchive) untuk membaca file .xlsx. Silakan simpan / ubah file Excel Anda menjadi format .CSV lalu upload kembali.');
            }

            return self::readXlsx($path);
        }

        return self::readCsv($path);
    }

    /**
     * Read CSV with automatic delimiter detection and UTF-8 BOM removal.
     */
    public static function readCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw new \RuntimeException('File CSV tidak dapat dibuka.');
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return [];
        }

        // Strip UTF-8 / UTF-16 BOM
        $firstLine = preg_replace('/^[\xEF\xBB\xBF\xFE\xFF\xFF\xFE]+/u', '', $firstLine);

        // Detect delimiter
        $delimiters = [',', ';', "\t", '|'];
        $bestDelim = ',';
        $maxCols = 0;
        foreach ($delimiters as $d) {
            $cnt = count(str_getcsv($firstLine, $d, '"', '\\'));
            if ($cnt > $maxCols) {
                $maxCols = $cnt;
                $bestDelim = $d;
            }
        }

        $rows = [];
        $firstRow = str_getcsv($firstLine, $bestDelim, '"', '\\');
        if (! empty(array_filter($firstRow, fn ($v) => trim((string) $v) !== ''))) {
            $rows[] = $firstRow;
        }

        while (($data = fgetcsv($handle, 0, $bestDelim, '"', '\\')) !== false) {
            if ($data === [null] || empty(array_filter($data, fn ($v) => trim((string) $v) !== ''))) {
                continue;
            }
            $rows[] = $data;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Read XLSX directly via ZipArchive.
     */
    public static function readXlsx(string $path): array
    {
        $zip = new ZipArchive;
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('File Excel (.xlsx) tidak dapat dibaca.');
        }

        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $doc = simplexml_load_string($xml);
            if ($doc && isset($doc->si)) {
                foreach ($doc->si as $item) {
                    $shared[] = (string) ($item->t ?? implode('', array_map('strval', $item->r->t ?? [])));
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            for ($i = 1; $i <= 10; $i++) {
                $sheetXml = $zip->getFromName("xl/worksheets/sheet{$i}.xml");
                if ($sheetXml !== false) {
                    break;
                }
            }
        }

        if ($sheetXml === false) {
            $zip->close();
            throw new \RuntimeException('Worksheet Excel tidak ditemukan.');
        }

        $sheet = simplexml_load_string($sheetXml);
        $rows = [];
        $headerWidth = 0;

        if ($sheet && isset($sheet->sheetData->row)) {
            foreach ($sheet->sheetData->row as $xmlRow) {
                $cells = [];
                $maxIdx = -1;
                foreach ($xmlRow->c as $cell) {
                    $ref = (string) $cell['r'];
                    preg_match('/([A-Z]+)/', $ref, $match);
                    $colLetters = $match[1] ?? 'A';
                    $column = 0;
                    foreach (str_split($colLetters) as $letter) {
                        $column = $column * 26 + ord($letter) - 64;
                    }
                    $idx = $column - 1;
                    if ($idx > $maxIdx) {
                        $maxIdx = $idx;
                    }

                    $value = (string) ($cell->v ?? '');
                    if ((string) $cell['t'] === 's') {
                        $value = $shared[(int) $value] ?? $value;
                    } elseif ((string) $cell['t'] === 'inlineStr' && isset($cell->is->t)) {
                        $value = (string) $cell->is->t;
                    }
                    $cells[$idx] = $value;
                }

                if ($maxIdx >= 0) {
                    if ($headerWidth === 0) {
                        $headerWidth = $maxIdx + 1;
                    }
                    $rowWidth = max($headerWidth, $maxIdx + 1);
                    $fullRow = [];
                    for ($c = 0; $c < $rowWidth; $c++) {
                        $fullRow[$c] = $cells[$c] ?? '';
                    }
                    if (! empty(array_filter($fullRow, fn ($v) => trim((string) $v) !== ''))) {
                        $rows[] = $fullRow;
                    }
                }
            }
        }

        $zip->close();

        return $rows;
    }

    /**
     * Parse date string or Excel numeric timestamp into Y-m-d format.
     */
    public static function parseDate($value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        $val = trim((string) $value);

        // Remove invisible unicode characters
        $val = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{2060}\x{00A0}]/u', '', $val);
        $val = trim($val);

        if ($val === '' || strtolower($val) === 'null' || strtolower($val) === 'tbd' || $val === '-') {
            return null;
        }

        // Excel numeric timestamp (e.g. 45000)
        if (is_numeric($val) && (float) $val > 10000 && (float) $val < 70000) {
            return gmdate('Y-m-d', ((int) $val - 25569) * 86400);
        }

        // Discard standalone 4-digit year (e.g. 2026) so strtotime doesn't treat it as 20:26 time
        if (preg_match('/^\d{4}$/', $val)) {
            return null;
        }

        // ISO format: YYYY-MM-DD (optionally followed by time)
        if (preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})/', $val, $m)) {
            $year = (int) $m[1];
            $month = (int) $m[2];
            $day = (int) $m[3];
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Indonesian / European format: DD/MM/YYYY or DD-MM-YYYY or DD.MM.YYYY
        if (preg_match('/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})$/', $val, $m)) {
            $p1 = (int) $m[1];
            $p2 = (int) $m[2];
            $year = (int) $m[3];

            // Prefer DD/MM/YYYY (standard in Indonesia)
            if (checkdate($p2, $p1, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $p2, $p1);
            }
            // Fallback to MM/DD/YYYY if p2 > 12 and p1 <= 12
            if (checkdate($p1, $p2, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $p1, $p2);
            }
        }

        // 2-digit year format: DD/MM/YY
        if (preg_match('/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{2})$/', $val, $m)) {
            $day = (int) $m[1];
            $month = (int) $m[2];
            $year = (int) $m[3] + 2000;
            if (checkdate($month, $day, $year)) {
                return sprintf('%04d-%02d-%02d', $year, $month, $day);
            }
        }

        // Indonesian & English month text mapping (e.g. "27 Februari 2026", "27-Feb-2026", "1 Jan 2026")
        $monthMap = [
            'jan' => 1, 'januari' => 1, 'january' => 1,
            'feb' => 2, 'februari' => 2, 'february' => 2,
            'mar' => 3, 'maret' => 3, 'march' => 3,
            'apr' => 4, 'april' => 4,
            'mei' => 5, 'may' => 5,
            'jun' => 6, 'juni' => 6, 'june' => 6,
            'jul' => 7, 'juli' => 7, 'july' => 7,
            'agu' => 8, 'ags' => 8, 'agst' => 8, 'agustus' => 8, 'aug' => 8, 'august' => 8,
            'sep' => 9, 'sept' => 9, 'september' => 9,
            'okt' => 10, 'oktober' => 10, 'oct' => 10, 'october' => 10,
            'nov' => 11, 'nop' => 11, 'november' => 11,
            'des' => 12, 'desember' => 12, 'dec' => 12, 'december' => 12,
        ];

        if (preg_match('/^(\d{1,2})\s*[-\/\s]\s*([a-zA-Z]+)\s*[-\/\s]\s*(\d{4})$/', $val, $m)) {
            $day = (int) $m[1];
            $monStr = strtolower($m[2]);
            $year = (int) $m[3];
            if (isset($monthMap[$monStr])) {
                $month = $monthMap[$monStr];
                if (checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
        }

        // Text format starting with month name: "Februari 27, 2026" or "Feb 27 2026"
        if (preg_match('/^([a-zA-Z]+)\s*(\d{1,2}),?\s*(\d{4})$/', $val, $m)) {
            $monStr = strtolower($m[1]);
            $day = (int) $m[2];
            $year = (int) $m[3];
            if (isset($monthMap[$monStr])) {
                $month = $monthMap[$monStr];
                if (checkdate($month, $day, $year)) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }
        }

        $time = strtotime($val);
        if ($time && date('Y', $time) > 1970 && date('Y', $time) < 2100) {
            return date('Y-m-d', $time);
        }

        return null;
    }

    /**
     * Clean header/text string from invisible unicode characters and normalize whitespace.
     */
    public static function cleanString($str): string
    {
        return strtolower(trim(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{2060}\x{00A0}]/u', '', (string) $str)));
    }
}
