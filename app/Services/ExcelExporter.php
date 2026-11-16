<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExporter
{
    /**
     * Download styled Excel (.xlsx) file matching the standard green header design.
     * If ZipArchive is not installed on the server, automatically falls back to clean UTF-8 CSV.
     *
     * @param  string  $filename  Output filename (e.g. "Template_Monitoring_Alumni.xlsx")
     * @param  array  $headers  List of header column titles
     * @param  array  $dataRows  List of data rows (each row is an array of values)
     * @param  string  $sheetTitle  Sheet title name
     */
    public static function download(string $filename, array $headers, array $dataRows = [], string $sheetTitle = 'Sheet1'): StreamedResponse
    {
        // 1. Fallback to CSV if PHP zip extension (ZipArchive) is missing on hosting server
        if (! class_exists('\ZipArchive')) {
            $csvFilename = preg_replace('/\.[^.]+$/', '', $filename).'.csv';
            $responseHeaders = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$csvFilename.'"',
                'Cache-Control' => 'max-age=0, must-revalidate',
                'Pragma' => 'public',
            ];

            return new StreamedResponse(function () use ($headers, $dataRows) {
                if (ob_get_length()) {
                    @ob_end_clean();
                }
                $handle = fopen('php://output', 'w');
                // UTF-8 BOM so Excel opens with proper characters
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, $headers);
                foreach ($dataRows as $row) {
                    fputcsv($handle, (array) $row);
                }
                fclose($handle);
            }, 200, $responseHeaders);
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($sheetTitle, 0, 31)); // Max sheet name length

        // Header Style (Image #1 style): Green Fill (#008000), White Bold Text, Center + Middle Align, Thin Border
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '008000'], // Vibrant Dark Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];

        // Data Cells Style: Center + Middle Align, Thin Border
        $dataStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D3D3D3'],
                ],
            ],
        ];

        // Write Headers
        $colIndex = 1;
        $currencyCols = [];
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getCell("{$colLetter}1")->setValue($header);

            $cleanH = strtolower(trim((string) $header));

            // Kolom kuantitas / jumlah fisik tidak boleh diformat sebagai mata uang (Rp)
            $isQuantity = str_contains($cleanH, 'batch')
                || str_contains($cleanH, 'peserta')
                || str_contains($cleanH, 'orang')
                || str_contains($cleanH, 'jam')
                || str_contains($cleanH, 'hari')
                || str_contains($cleanH, 'bulan')
                || str_contains($cleanH, 'mandor')
                || str_contains($cleanH, 'staf')
                || str_contains($cleanH, 'head')
                || str_contains($cleanH, 'mgr')
                || str_contains($cleanH, 'gm')
                || str_contains($cleanH, 'ast')
                || str_contains($cleanH, 'askep')
                || str_contains($cleanH, 'kelas')
                || str_contains($cleanH, 'program')
                || str_contains($cleanH, 'lulus')
                || str_contains($cleanH, 'resign')
                || str_contains($cleanH, 'tahun')
                || str_contains($cleanH, 'persen')
                || str_contains($cleanH, '%');

            if (! $isQuantity) {
                if (in_array($cleanH, ['budget', 'realisasi', 'budget_tahunan', 'total_biaya', 'biaya', 'harga', 'total_rp', 'upah', 'bbm', 'preventif_maintenance', 'biaya_lainnya', 'total_biaya_bulan', 'debit', 'kredit', 'nominal', 'amount', 'total_budget', 'total_realisasi', 'uang_saku'])
                    || str_contains($cleanH, 'biaya')
                    || str_contains($cleanH, 'budget')
                    || str_contains($cleanH, 'harga')
                    || str_contains($cleanH, 'uang')
                    || str_contains($cleanH, 'nominal')
                    || str_contains($cleanH, 'amount')
                    || ($cleanH === 'realisasi' || str_contains($cleanH, 'realisasi_biaya') || str_contains($cleanH, 'realisasi_rp') || str_contains($cleanH, 'realisasi rp') || str_contains($cleanH, 'total_realisasi'))) {
                    $currencyCols[] = $colLetter;
                }
            }
            $colIndex++;
        }

        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28); // Standard comfortable row height for headers

        // Write Data Rows
        $rowIndex = 2;
        foreach ($dataRows as $row) {
            $colIndex = 1;
            foreach ((array) $row as $val) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                if (is_string($val)) {
                    $sheet->getCell("{$colLetter}{$rowIndex}")->setValueExplicit($val, DataType::TYPE_STRING);
                } else {
                    $sheet->getCell("{$colLetter}{$rowIndex}")->setValue($val);
                }
                $colIndex++;
            }
            $sheet->getRowDimension($rowIndex)->setRowHeight(22);
            $rowIndex++;
        }

        $lastRow = max(1, $rowIndex - 1);
        if ($lastRow > 1) {
            $sheet->getStyle("A2:{$highestColumn}{$lastRow}")->applyFromArray($dataStyle);
        }

        // Apply Currency Format ("Rp "#,##0) and Right Alignment to currency columns up to row 100 for templates
        $formatMaxRow = max(100, $lastRow);
        foreach ($currencyCols as $colLetter) {
            $sheet->getStyle("{$colLetter}2:{$colLetter}{$formatMaxRow}")
                ->getNumberFormat()
                ->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("{$colLetter}2:{$colLetter}{$formatMaxRow}")
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // Auto-fit Column Widths safely
        $hasGd = extension_loaded('gd');
        $numCols = count($headers);
        for ($col = 1; $col <= $numCols; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            if ($hasGd) {
                try {
                    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                } catch (\Throwable $e) {
                    $sheet->getColumnDimension($colLetter)->setWidth(20);
                }
            } else {
                $sheet->getColumnDimension($colLetter)->setWidth(20);
            }
        }

        // Ensure extension is .xlsx
        if (! str_ends_with(strtolower($filename), '.xlsx')) {
            $filename = preg_replace('/\.[^.]+$/', '', $filename).'.xlsx';
        }

        $responseHeaders = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'max-age=0, must-revalidate',
            'Pragma' => 'public',
        ];

        return new StreamedResponse(function () use ($spreadsheet) {
            if (ob_get_length()) {
                @ob_end_clean();
            }
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $responseHeaders);
    }

    /**
     * Download styled Excel (.xlsx) file with top metadata header block (Title, Subtitle, Details) above the table.
     *
     * @param  string  $filename  Output filename (e.g. "Absensi_Pelatihan.xlsx")
     * @param  string  $title  Main title (e.g. "DAFTAR HADIR PESERTA PELATIHAN")
     * @param  string  $subtitle  Subtitle (e.g. "FR Academy - Program Non Reguler")
     * @param  array  $details  List of details rows: [['Label Kiri', ': Nilai', 'Label Kanan', ': Nilai'], ...]
     * @param  array  $headers  List of header column titles
     * @param  array  $dataRows  List of data rows
     * @param  string  $sheetTitle  Sheet title name
     */
    public static function downloadWithHeader(
        string $filename,
        string $title,
        string $subtitle,
        array $details,
        array $headers,
        array $dataRows = [],
        string $sheetTitle = 'Daftar Hadir'
    ): StreamedResponse {
        // Fallback generator ke CSV jika server tidak mendukung ZipArchive/Excel
        $downloadCsv = function () use ($filename, $title, $subtitle, $details, $headers, $dataRows) {
            $csvFilename = preg_replace('/\.[^.]+$/', '', $filename).'.csv';
            $responseHeaders = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$csvFilename.'"',
                'Cache-Control' => 'max-age=0, must-revalidate',
                'Pragma' => 'public',
            ];

            return new StreamedResponse(function () use ($title, $subtitle, $details, $headers, $dataRows) {
                if (ob_get_length()) {
                    @ob_end_clean();
                }
                $handle = fopen('php://output', 'w');
                // UTF-8 BOM
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($handle, [$title]);
                fputcsv($handle, [$subtitle]);
                fputcsv($handle, []);
                foreach ($details as $d) {
                    fputcsv($handle, $d);
                }
                fputcsv($handle, []);
                fputcsv($handle, $headers);
                foreach ($dataRows as $row) {
                    fputcsv($handle, (array) $row);
                }
                fclose($handle);
            }, 200, $responseHeaders);
        };

        if (! class_exists('\ZipArchive') || str_ends_with(strtolower($filename), '.csv')) {
            return $downloadCsv();
        }

        try {
            $spreadsheet = new Spreadsheet;
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(substr($sheetTitle, 0, 31));

            $numCols = count($headers);
            $highestColLetter = Coordinate::stringFromColumnIndex($numCols);

            // 1. Title Row
            $sheet->mergeCells("A1:{$highestColLetter}1");
            $sheet->setCellValue('A1', $title);
            $sheet->getStyle('A1')->getFont()->setName('Calibri')->setSize(14)->setBold(true)->getColor()->setRGB('111827');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(1)->setRowHeight(26);

            // 2. Subtitle Row
            $sheet->mergeCells("A2:{$highestColLetter}2");
            $sheet->setCellValue('A2', $subtitle);
            $sheet->getStyle('A2')->getFont()->setName('Calibri')->setSize(10)->setItalic(true)->getColor()->setRGB('6B7280');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension(2)->setRowHeight(18);

            // 3. Spacer Row
            $sheet->getRowDimension(3)->setRowHeight(8);

            // 4. Details Block (Rows 4 .. 4 + count($details) - 1)
            $detailRow = 4;
            foreach ($details as $d) {
                $sheet->setCellValue("A{$detailRow}", $d[0] ?? '');
                $sheet->getStyle("A{$detailRow}")->getFont()->setName('Calibri')->setSize(10)->setBold(true)->getColor()->setRGB('374151');
                $sheet->getStyle("A{$detailRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

                if ($numCols >= 6) {
                    $sheet->mergeCells("B{$detailRow}:D{$detailRow}");
                    $sheet->setCellValue("B{$detailRow}", $d[1] ?? '');
                    $sheet->getStyle("B{$detailRow}")->getFont()->setName('Calibri')->setSize(10)->getColor()->setRGB('111827');
                    $sheet->getStyle("B{$detailRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

                    $sheet->setCellValue("E{$detailRow}", $d[2] ?? '');
                    $sheet->getStyle("E{$detailRow}")->getFont()->setName('Calibri')->setSize(10)->setBold(true)->getColor()->setRGB('374151');
                    $sheet->getStyle("E{$detailRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);

                    $sheet->mergeCells("F{$detailRow}:{$highestColLetter}{$detailRow}");
                    $sheet->setCellValue("F{$detailRow}", $d[3] ?? '');
                    $sheet->getStyle("F{$detailRow}")->getFont()->setName('Calibri')->setSize(10)->getColor()->setRGB('111827');
                    $sheet->getStyle("F{$detailRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                } else {
                    $sheet->setCellValue("B{$detailRow}", $d[1] ?? '');
                }

                $sheet->getRowDimension($detailRow)->setRowHeight(20);
                $detailRow++;
            }

            // Spacer Row before table
            $sheet->getRowDimension($detailRow)->setRowHeight(10);
            $headerRow = $detailRow + 1;

            // Header Style: Vibrant Green Fill (#008000), White Bold Text, Center + Middle Align, Thin Border
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                    'name' => 'Calibri',
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '008000'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ];

            // Data Cells Style: Center + Middle Align, Thin Border
            $dataStyle = [
                'font' => [
                    'name' => 'Calibri',
                    'size' => 10.5,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D3D3D3'],
                    ],
                ],
            ];

            // Write Table Headers
            $colIndex = 1;
            foreach ($headers as $header) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->getCell("{$colLetter}{$headerRow}")->setValue($header);
                $colIndex++;
            }

            $sheet->getStyle("A{$headerRow}:{$highestColLetter}{$headerRow}")->applyFromArray($headerStyle);
            $sheet->getRowDimension($headerRow)->setRowHeight(28);

            // Write Data Rows
            $dataStartRow = $headerRow + 1;
            $currentRow = $dataStartRow;
            foreach ($dataRows as $row) {
                $colIndex = 1;
                foreach ((array) $row as $val) {
                    $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                    if (is_string($val)) {
                        $sheet->getCell("{$colLetter}{$currentRow}")->setValueExplicit($val, DataType::TYPE_STRING);
                    } else {
                        $sheet->getCell("{$colLetter}{$currentRow}")->setValue($val);
                    }
                    $colIndex++;
                }
                $sheet->getRowDimension($currentRow)->setRowHeight(22);
                $currentRow++;
            }

            $lastRow = max($headerRow, $currentRow - 1);
            if ($lastRow >= $dataStartRow) {
                $sheet->getStyle("A{$dataStartRow}:{$highestColLetter}{$lastRow}")->applyFromArray($dataStyle);
            }

            // Auto-fit Column Widths
            $hasGd = extension_loaded('gd');
            for ($col = 1; $col <= $numCols; $col++) {
                $colLetter = Coordinate::stringFromColumnIndex($col);
                if ($hasGd) {
                    try {
                        $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                    } catch (\Throwable $e) {
                        $sheet->getColumnDimension($colLetter)->setWidth(20);
                    }
                } else {
                    $sheet->getColumnDimension($colLetter)->setWidth(20);
                }
            }

            // Ensure extension is .xlsx
            if (! str_ends_with(strtolower($filename), '.xlsx')) {
                $filename = preg_replace('/\.[^.]+$/', '', $filename).'.xlsx';
            }

            $responseHeaders = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'max-age=0, must-revalidate',
                'Pragma' => 'public',
            ];

            return new StreamedResponse(function () use ($spreadsheet) {
                if (ob_get_length()) {
                    @ob_end_clean();
                }
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, 200, $responseHeaders);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Excel generation failed, falling back to CSV: '.$e->getMessage());

            return $downloadCsv();
        }
    }
}
