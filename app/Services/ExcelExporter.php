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
        // 1. Fallback to XML Spreadsheet (.xls) if PHP zip extension (ZipArchive) is missing on hosting server (e.g. PHP 7.2 live server)
        // or if requested via config/param. This requires ZERO external libraries and works natively on PHP 7.2.
        if (! class_exists('\ZipArchive') || request()->query('format') === 'xls' || config('app.export_engine') === 'xml') {
            return self::downloadStandardXmlSpreadsheet($filename, $headers, $dataRows, $sheetTitle);
        }

        try {
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Excel .xlsx generation failed, falling back to formatted XML Spreadsheet: '.$e->getMessage());

            return self::downloadStandardXmlSpreadsheet($filename, $headers, $dataRows, $sheetTitle);
        }
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
        // Jika server tidak memiliki ZipArchive (tidak bisa membuat .xlsx), gunakan XML Spreadsheet (.xls) yang sudah diformat penuh tanpa butuh zip
        if (! class_exists('\ZipArchive')) {
            return self::downloadXmlSpreadsheet($filename, $title, $subtitle, $details, $headers, $dataRows, $sheetTitle);
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
            \Illuminate\Support\Facades\Log::warning('Excel .xlsx generation failed, falling back to formatted XML Spreadsheet: '.$e->getMessage());

            return self::downloadXmlSpreadsheet($filename, $title, $subtitle, $details, $headers, $dataRows, $sheetTitle);
        }
    }

    /**
     * Download formatted Excel spreadsheet using XML Spreadsheet 2003 format (.xls).
     * This requires ZERO server extensions (no ZipArchive, no GD) and opens natively in Excel with full formatting, colors, and borders.
     */
    public static function downloadXmlSpreadsheet(
        string $filename,
        string $title,
        string $subtitle,
        array $details,
        array $headers,
        array $dataRows = [],
        string $sheetTitle = 'Daftar Hadir'
    ): StreamedResponse {
        $xlsFilename = preg_replace('/\.[^.]+$/', '', $filename).'.xls';
        $numCols = count($headers);
        $mergeAcross = max(0, $numCols - 1);

        $responseHeaders = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$xlsFilename.'"',
            'Cache-Control' => 'max-age=0, must-revalidate',
            'Pragma' => 'public',
        ];

        return new StreamedResponse(function () use ($title, $subtitle, $details, $headers, $dataRows, $sheetTitle, $numCols, $mergeAcross) {
            if (ob_get_length()) {
                @ob_end_clean();
            }

            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
            echo "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
            echo " xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n";
            echo " xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n";
            echo " xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
            echo " xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
            echo " <Styles>\n";
            echo "  <Style ss:ID=\"Default\" ss:Name=\"Normal\"><Alignment ss:Vertical=\"Center\"/><Borders/><Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Color=\"#000000\"/></Style>\n";
            echo "  <Style ss:ID=\"Title\"><Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/><Font ss:FontName=\"Calibri\" ss:Size=\"14\" ss:Bold=\"1\" ss:Color=\"#111827\"/></Style>\n";
            echo "  <Style ss:ID=\"Subtitle\"><Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Italic=\"1\" ss:Color=\"#6B7280\"/></Style>\n";
            echo "  <Style ss:ID=\"MetaLabel\"><Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Center\"/><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Bold=\"1\" ss:Color=\"#374151\"/></Style>\n";
            echo "  <Style ss:ID=\"MetaVal\"><Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Center\"/><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Color=\"#111827\"/></Style>\n";
            echo "  <Style ss:ID=\"Header\"><Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\" ss:WrapText=\"1\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Bold=\"1\" ss:Color=\"#FFFFFF\"/><Interior ss:Color=\"#008000\" ss:Pattern=\"Solid\"/></Style>\n";
            echo "  <Style ss:ID=\"Data\"><Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"10\"/></Style>\n";
            echo " </Styles>\n";

            $cleanSheetTitle = htmlspecialchars(substr($sheetTitle, 0, 31), ENT_XML1);
            echo " <Worksheet ss:Name=\"{$cleanSheetTitle}\">\n";
            echo "  <Table ss:DefaultColumnWidth=\"60\" ss:DefaultRowHeight=\"20\">\n";

            // Define column widths
            $colWidths = [40, 110, 170, 100, 90, 90, 120, 110];
            for ($i = 0; $i < $numCols; $i++) {
                $w = $colWidths[$i] ?? 100;
                echo "   <Column ss:Width=\"{$w}\"/>\n";
            }

            // Title
            $titleSafe = htmlspecialchars($title, ENT_XML1);
            echo "   <Row ss:Height=\"26\"><Cell ss:MergeAcross=\"{$mergeAcross}\" ss:StyleID=\"Title\"><Data ss:Type=\"String\">{$titleSafe}</Data></Cell></Row>\n";

            // Subtitle
            $subSafe = htmlspecialchars($subtitle, ENT_XML1);
            echo "   <Row ss:Height=\"18\"><Cell ss:MergeAcross=\"{$mergeAcross}\" ss:StyleID=\"Subtitle\"><Data ss:Type=\"String\">{$subSafe}</Data></Cell></Row>\n";
            echo "   <Row ss:Height=\"8\"/>\n";

            // Details rows
            $rightMerge = max(0, $numCols - 5);
            foreach ($details as $d) {
                $d0 = htmlspecialchars((string) ($d[0] ?? ''), ENT_XML1);
                $d1 = htmlspecialchars((string) ($d[1] ?? ''), ENT_XML1);
                $d2 = htmlspecialchars((string) ($d[2] ?? ''), ENT_XML1);
                $d3 = htmlspecialchars((string) ($d[3] ?? ''), ENT_XML1);
                echo "   <Row ss:Height=\"20\">\n";
                echo "    <Cell ss:StyleID=\"MetaLabel\"><Data ss:Type=\"String\">{$d0}</Data></Cell>\n";
                echo "    <Cell ss:MergeAcross=\"2\" ss:StyleID=\"MetaVal\"><Data ss:Type=\"String\">{$d1}</Data></Cell>\n";
                echo "    <Cell ss:StyleID=\"MetaLabel\"><Data ss:Type=\"String\">{$d2}</Data></Cell>\n";
                echo "    <Cell ss:MergeAcross=\"{$rightMerge}\" ss:StyleID=\"MetaVal\"><Data ss:Type=\"String\">{$d3}</Data></Cell>\n";
                echo "   </Row>\n";
            }

            echo "   <Row ss:Height=\"10\"/>\n";

            // Header Row
            echo "   <Row ss:Height=\"28\">\n";
            foreach ($headers as $h) {
                $hSafe = htmlspecialchars((string) $h, ENT_XML1);
                echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">{$hSafe}</Data></Cell>\n";
            }
            echo "   </Row>\n";

            // Data Rows
            foreach ($dataRows as $row) {
                $rowArr = array_values((array) $row);
                $maxLines = 1;
                foreach ($rowArr as $val) {
                    $vStr = (string) $val;
                    if (str_contains($vStr, "\n")) {
                        $norm = str_replace(["\r\n", "\r"], "\n", $vStr);
                        $norm = preg_replace("/\n{2,}/", "\n", $norm);
                        $cnt = substr_count($norm, "\n") + 1;
                        if ($cnt > $maxLines) {
                            $maxLines = $cnt;
                        }
                    }
                }
                $rHeight = $maxLines > 1 ? min(400, round(($maxLines * 16.5) + 8)) : 22;
                echo "   <Row ss:AutoFitHeight=\"1\" ss:Height=\"{$rHeight}\">\n";
                foreach ($rowArr as $val) {
                    $valStr = (string) $val;
                    $cleanVal = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $valStr);
                    $cleanVal = str_replace(["\r\n", "\r"], "\n", $cleanVal);
                    $cleanVal = preg_replace("/\n{2,}/", "\n", $cleanVal);
                    $dataType = (is_numeric($val) && strlen($valStr) < 10 && ! str_starts_with($valStr, '0')) ? 'Number' : 'String';
                    $valSafe = htmlspecialchars($cleanVal, ENT_XML1, 'UTF-8');
                    $valSafe = str_replace("\n", '&#10;', $valSafe);
                    echo "    <Cell ss:StyleID=\"Data\"><Data ss:Type=\"{$dataType}\">{$valSafe}</Data></Cell>\n";
                }
                echo "   </Row>\n";
            }

            echo "  </Table>\n";
            echo " </Worksheet>\n";
            echo "</Workbook>\n";
        }, 200, $responseHeaders);
    }

    /**
     * Download formatted standard Excel spreadsheet using Microsoft XML Spreadsheet 2003 format (.xls).
     * This requires ZERO server extensions (no ZipArchive, no GD, no PhpSpreadsheet)
     * and is 100% compatible with PHP 7.2. Opens natively in Excel and WPS with
     * full formatting, green headers, cell borders, text wrapping, and currency styles.
     */
    public static function downloadStandardXmlSpreadsheet(
        string $filename,
        array $headers,
        array $dataRows = [],
        string $sheetTitle = 'Sheet1'
    ): StreamedResponse {
        $xlsFilename = preg_replace('/\.[^.]+$/', '', $filename).'.xls';
        $numCols = count($headers);

        $responseHeaders = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$xlsFilename.'"',
            'Cache-Control' => 'max-age=0, must-revalidate',
            'Pragma' => 'public',
        ];

        // Identify currency columns
        $currencyCols = [];
        $colIndex = 0;
        foreach ($headers as $header) {
            $cleanH = strtolower(trim((string) $header));
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
                    $currencyCols[$colIndex] = true;
                }
            }
            $colIndex++;
        }

        // Calculate column widths and optimize for text wrap
        $colWidths = [];
        for ($c = 0; $c < $numCols; $c++) {
            $headerStr = (string) ($headers[$c] ?? '');
            $cleanH = strtolower(trim($headerStr));
            $maxLen = mb_strlen($headerStr, 'UTF-8');
            $sampleCount = 0;
            $hasMultiline = false;
            $maxLineLen = 0;

            foreach ($dataRows as $r) {
                if ($sampleCount++ > 40) {
                    break;
                }
                $valStr = (string) (((array) $r)[$c] ?? '');
                if (str_contains($valStr, "\n") || str_contains($valStr, "\r")) {
                    $hasMultiline = true;
                }
                $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $valStr));
                foreach ($lines as $line) {
                    $len = mb_strlen(trim($line), 'UTF-8');
                    if ($len > $maxLineLen) {
                        $maxLineLen = $len;
                    }
                }
            }

            // Identify long narrative / text columns that need larger width & wrap
            $isLongTextCol = str_contains($cleanH, 'profil')
                || str_contains($cleanH, 'histori')
                || str_contains($cleanH, 'pekerjaan')
                || str_contains($cleanH, 'achievement')
                || str_contains($cleanH, 'keterangan')
                || str_contains($cleanH, 'alasan')
                || str_contains($cleanH, 'catatan')
                || str_contains($cleanH, 'strength')
                || str_contains($cleanH, 'weakness')
                || str_contains($cleanH, 'development')
                || str_contains($cleanH, 'suggestion')
                || str_contains($cleanH, 'saran')
                || str_contains($cleanH, 'rekomendasi')
                || str_contains($cleanH, 'organisasi')
                || str_contains($cleanH, 'magang')
                || str_contains($cleanH, 'prestasi')
                || str_contains($cleanH, 'hobi')
                || str_contains($cleanH, 'evaluasi')
                || str_contains($cleanH, 'deskripsi')
                || str_contains($cleanH, 'training');

            if ($isLongTextCol || $hasMultiline || $maxLineLen > 45) {
                // Generous width for long text, wraps downward smoothly
                $colWidths[$c] = round(max(260, min(380, ($maxLineLen * 6.5) + 30)));
            } elseif (in_array($cleanH, ['no', 'umur', 'usia', 'status', 'agama', 'gender', 'jenis kelamin'])) {
                $colWidths[$c] = 55;
            } elseif (in_array($cleanH, ['employee id', 'nik', 'trainee id', 'tahun', 'batch'])) {
                $colWidths[$c] = 95;
            } else {
                $baseLen = max($maxLen, min(35, $maxLineLen));
                $colWidths[$c] = round(max(70, min(220, ($baseLen * 7.5) + 20)));
            }
        }

        return new StreamedResponse(function () use ($headers, $dataRows, $sheetTitle, $numCols, $currencyCols, $colWidths) {
            if (ob_get_length()) {
                @ob_end_clean();
            }

            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<?mso-application progid=\"Excel.Sheet\"?>\n";
            echo "<Workbook xmlns=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
            echo " xmlns:o=\"urn:schemas-microsoft-com:office:office\"\n";
            echo " xmlns:x=\"urn:schemas-microsoft-com:office:excel\"\n";
            echo " xmlns:ss=\"urn:schemas-microsoft-com:office:spreadsheet\"\n";
            echo " xmlns:html=\"http://www.w3.org/TR/REC-html40\">\n";
            echo " <Styles>\n";
            echo "  <Style ss:ID=\"Default\" ss:Name=\"Normal\"><Alignment ss:Vertical=\"Center\"/><Borders/><Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Color=\"#000000\"/></Style>\n";
            echo "  <Style ss:ID=\"Header\"><Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Center\" ss:WrapText=\"1\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#000000\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"11\" ss:Bold=\"1\" ss:Color=\"#FFFFFF\"/><Interior ss:Color=\"#008000\" ss:Pattern=\"Solid\"/></Style>\n";
            echo "  <Style ss:ID=\"DataCenter\"><Alignment ss:Horizontal=\"Center\" ss:Vertical=\"Top\" ss:WrapText=\"1\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Color=\"#111827\"/></Style>\n";
            echo "  <Style ss:ID=\"DataLeft\"><Alignment ss:Horizontal=\"Left\" ss:Vertical=\"Top\" ss:WrapText=\"1\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Color=\"#111827\"/></Style>\n";
            echo "  <Style ss:ID=\"DataRight\"><Alignment ss:Horizontal=\"Right\" ss:Vertical=\"Top\" ss:WrapText=\"1\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Color=\"#111827\"/></Style>\n";
            echo "  <Style ss:ID=\"Currency\"><Alignment ss:Horizontal=\"Right\" ss:Vertical=\"Top\" ss:WrapText=\"1\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Color=\"#111827\"/><NumberFormat ss:Format=\"&quot;Rp &quot;#,##0\"/></Style>\n";
            echo "  <Style ss:ID=\"NumberDec\"><Alignment ss:Horizontal=\"Right\" ss:Vertical=\"Top\" ss:WrapText=\"1\"/><Borders><Border ss:Position=\"Bottom\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Left\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Right\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/><Border ss:Position=\"Top\" ss:LineStyle=\"Continuous\" ss:Weight=\"1\" ss:Color=\"#D3D3D3\"/></Borders><Font ss:FontName=\"Calibri\" ss:Size=\"10\" ss:Color=\"#111827\"/><NumberFormat ss:Format=\"#,##0.00\"/></Style>\n";
            echo " </Styles>\n";

            $cleanSheetTitle = htmlspecialchars(substr($sheetTitle, 0, 31), ENT_XML1, 'UTF-8');
            echo " <Worksheet ss:Name=\"{$cleanSheetTitle}\">\n";
            echo "  <Table ss:DefaultRowHeight=\"20\">\n";

            // Output column widths
            for ($c = 0; $c < $numCols; $c++) {
                $w = $colWidths[$c] ?? 90;
                echo "   <Column ss:Width=\"{$w}\"/>\n";
            }

            // Header Row
            echo "   <Row ss:Height=\"28\">\n";
            foreach ($headers as $h) {
                $hClean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string) $h);
                $hSafe = htmlspecialchars($hClean, ENT_XML1, 'UTF-8');
                echo "    <Cell ss:StyleID=\"Header\"><Data ss:Type=\"String\">{$hSafe}</Data></Cell>\n";
            }
            echo "   </Row>\n";

            // Data Rows
            foreach ($dataRows as $row) {
                $rowArr = array_values((array) $row);

                // Calculate required row height dynamically based on wrapped and multiline content
                $maxLinesInRow = 1;
                for ($c = 0; $c < $numCols; $c++) {
                    $rawVal = (string) ($rowArr[$c] ?? '');
                    if ($rawVal === '') {
                        continue;
                    }
                    $colW = $colWidths[$c] ?? 90;
                    $charsPerLine = max(10, (int) floor(($colW - 14) / 6.2));

                    $normVal = str_replace(["\r\n", "\r"], "\n", $rawVal);
                    $normVal = preg_replace("/\n{2,}/", "\n", $normVal);
                    $lines = explode("\n", $normVal);

                    $cellLines = 0;
                    foreach ($lines as $line) {
                        $len = mb_strlen(trim($line), 'UTF-8');
                        if ($len === 0) {
                            $cellLines += 1;
                        } else {
                            $cellLines += (int) ceil($len / $charsPerLine);
                        }
                    }

                    if ($cellLines > $maxLinesInRow) {
                        $maxLinesInRow = $cellLines;
                    }
                }

                $rowHeight = $maxLinesInRow > 1
                    ? min(400, round(($maxLinesInRow * 16.5) + 8))
                    : 22;

                echo "   <Row ss:AutoFitHeight=\"1\" ss:Height=\"{$rowHeight}\">\n";
                for ($c = 0; $c < $numCols; $c++) {
                    $val = $rowArr[$c] ?? '';
                    $valStr = (string) $val;
                    $cleanVal = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $valStr);
                    $cleanVal = str_replace(["\r\n", "\r"], "\n", $cleanVal);
                    $cleanVal = preg_replace("/\n{2,}/", "\n", $cleanVal);

                    $hName = strtolower(trim((string) ($headers[$c] ?? '')));
                    $isCurr = isset($currencyCols[$c]);

                    if ($isCurr && is_numeric($val) && trim($cleanVal) !== '') {
                        $num = (float) $val;
                        echo "    <Cell ss:StyleID=\"Currency\"><Data ss:Type=\"Number\">{$num}</Data></Cell>\n";
                    } elseif (str_contains($cleanVal, "\n")) {
                        $safe = htmlspecialchars($cleanVal, ENT_XML1, 'UTF-8');
                        $safe = str_replace("\n", '&#10;', $safe);
                        echo "    <Cell ss:StyleID=\"DataLeft\"><Data ss:Type=\"String\">{$safe}</Data></Cell>\n";
                    } elseif (is_numeric($val) && ! str_starts_with(trim($valStr), '0') && strlen(trim($valStr)) < 10) {
                        if (is_float($val) || (is_string($val) && str_contains($val, '.'))) {
                            $num = (float) $val;
                            echo "    <Cell ss:StyleID=\"NumberDec\"><Data ss:Type=\"Number\">{$num}</Data></Cell>\n";
                        } else {
                            $num = (int) $val;
                            echo "    <Cell ss:StyleID=\"DataRight\"><Data ss:Type=\"Number\">{$num}</Data></Cell>\n";
                        }
                    } else {
                        $safe = htmlspecialchars($cleanVal, ENT_XML1, 'UTF-8');
                        $style = 'DataLeft';
                        if ($c === 0 || in_array($hName, ['no', 'id', 'gender', 'jenis kelamin', 'status', 'agama', 'tahun'])) {
                            $style = 'DataCenter';
                        }
                        echo "    <Cell ss:StyleID=\"{$style}\"><Data ss:Type=\"String\">{$safe}</Data></Cell>\n";
                    }
                }
                echo "   </Row>\n";
            }

            echo "  </Table>\n";
            echo " </Worksheet>\n";
            echo "</Workbook>\n";
        }, 200, $responseHeaders);
    }
}
