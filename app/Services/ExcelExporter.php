<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelExporter
{
    /**
     * Download styled Excel (.xlsx) file matching the standard green header design.
     *
     * @param string $filename Output filename (e.g. "Template_Monitoring_Alumni.xlsx")
     * @param array $headers List of header column titles
     * @param array $dataRows List of data rows (each row is an array of values)
     * @param string $sheetTitle Sheet title name
     * @return StreamedResponse
     */
    public static function download(string $filename, array $headers, array $dataRows = [], string $sheetTitle = 'Sheet1'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
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

            $cleanH = strtolower(trim((string)$header));
            if (in_array($cleanH, ['budget', 'realisasi', 'budget_tahunan', 'total_biaya', 'biaya', 'harga', 'total_rp', 'upah', 'bbm', 'preventif_maintenance', 'biaya_lainnya', 'total_biaya_bulan', 'debit', 'kredit', 'nominal', 'amount', 'total_budget', 'total_realisasi']) 
                || str_contains($cleanH, 'biaya') 
                || str_contains($cleanH, 'budget') 
                || str_contains($cleanH, 'realisasi') 
                || str_contains($cleanH, 'harga')) {
                $currencyCols[] = $colLetter;
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
            foreach ($row as $val) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->getCell("{$colLetter}{$rowIndex}")->setValue($val);
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

        // Auto-fit Column Widths
        $numCols = count($headers);
        for ($col = 1; $col <= $numCols; $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Ensure extension is .xlsx
        if (!str_ends_with(strtolower($filename), '.xlsx')) {
            $filename = preg_replace('/\.[^.]+$/', '', $filename) . '.xlsx';
        }

        $responseHeaders = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, $responseHeaders);
    }
}
