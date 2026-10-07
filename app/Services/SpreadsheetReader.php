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
        if (is_numeric($val) && (float) $val > 10000 && (float) $val < 70000) {
            return gmdate('Y-m-d', ((int) $val - 25569) * 86400);
        }
        $time = strtotime($val);

        return $time ? date('Y-m-d', $time) : null;
    }

    /**
     * Clean header/text string from invisible unicode characters and normalize whitespace.
     */
    public static function cleanString($str): string
    {
        return strtolower(trim(preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{2060}\x{00A0}]/u', '', (string) $str)));
    }
}
