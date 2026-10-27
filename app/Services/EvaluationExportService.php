<?php

namespace App\Services;

use App\Models\DailyClass;
use App\Models\TraineeEvaluation;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvaluationExportService
{
    public const QUESTIONS = [
        1 => [
            'category' => 'A. Isi Materi Pelatihan',
            'text' => 'Manfaat materi training terhadap pekerjaan',
            'options' => [
                'A' => 'Sangat bermanfaat sekali',
                'B' => 'Bermanfaat bagi pekerjaan',
                'C' => 'Cukup bermanfaat',
                'D' => 'Kurang bermanfaat',
                'E' => 'Tidak bermanfaat',
            ],
        ],
        2 => [
            'category' => 'A. Isi Materi Pelatihan',
            'text' => 'Kelengkapan materi',
            'options' => [
                'A' => 'Sangat lengkap',
                'B' => 'Lengkap',
                'C' => 'Cukup',
                'D' => 'Kurang',
                'E' => 'Tidak dipersiapkan',
            ],
        ],
        3 => [
            'category' => 'A. Isi Materi Pelatihan',
            'text' => 'Lewat training ini keahlian dan ilmu saya bertambah',
            'options' => [
                'A' => 'Sangat setuju sekali',
                'B' => 'Setuju',
                'C' => 'Cukup setuju',
                'D' => 'Kurang setuju',
                'E' => 'Tidak setuju',
            ],
        ],
        4 => [
            'category' => 'A. Isi Materi Pelatihan',
            'text' => 'Materi training ini dapat saya terapkan di pekerjaan saya',
            'options' => [
                'A' => 'Sangat setuju sekali',
                'B' => 'Setuju',
                'C' => 'Cukup setuju',
                'D' => 'Kurang setuju',
                'E' => 'Tidak setuju',
            ],
        ],
        5 => [
            'category' => 'B. Instruktur / Trainer',
            'text' => 'Penguasaan materi oleh instruktur / trainer',
            'options' => [
                'A' => 'Sangat menguasai',
                'B' => 'Bagus',
                'C' => 'Sedang',
                'D' => 'Kurang',
                'E' => 'Tidak menguasai',
            ],
        ],
        6 => [
            'category' => 'B. Instruktur / Trainer',
            'text' => 'Metode penyampaian materi oleh instruktur / trainer',
            'options' => [
                'A' => 'Sangat mudah dipahami dan menarik',
                'B' => 'Mudah dipahami',
                'C' => 'Cukup bisa dipahami',
                'D' => 'Kurang dipahami',
                'E' => 'Sulit dipahami dan tidak menarik',
            ],
        ],
        7 => [
            'category' => 'B. Instruktur / Trainer',
            'text' => 'Kecepatan dalam pemberian materi training',
            'options' => [
                'A' => 'Sangat teratur ada intonasi dan mudah diikuti',
                'B' => 'Teratur ada intonasi dan mudah diikuti',
                'C' => 'Sedang bisa diikuti',
                'D' => 'Cepat kurang bisa diikuti',
                'E' => 'Sangat cepat tidak bisa diikuti',
            ],
        ],
        8 => [
            'category' => 'B. Instruktur / Trainer',
            'text' => 'Contoh-contoh yang diberikan oleh trainer',
            'options' => [
                'A' => 'Sesuai dan sangat lengkap',
                'B' => 'Sesuai dan cukup lengkap',
                'C' => 'Sesuai namun kurang lengkap',
                'D' => 'Kurang sesuai dengan kondisi yg ada',
                'E' => 'Tidak sesuai dengan kondisi yang ada',
            ],
        ],
        9 => [
            'category' => 'B. Instruktur / Trainer',
            'text' => 'Trainer dalam menjawab pertanyaan dan bimbingan praktek',
            'options' => [
                'A' => 'Jelas dan detail',
                'B' => 'Jelas dan mengenai sasaran',
                'C' => 'Cukup mengenai sasaran',
                'D' => 'Kurang mengenai sasaran',
                'E' => 'Tidak mengenai sasaran',
            ],
        ],
        10 => [
            'category' => 'C. Fasilitas dan Pelayanan',
            'text' => 'Fasilitas kelas (White board, flip chart, LCD, kenyamanan ruangan, dsb)',
            'options' => [
                'A' => 'Baik dan sangat lengkap',
                'B' => 'Baik dan lengkap',
                'C' => 'Cukup lengkap',
                'D' => 'Kurang lengkap',
                'E' => 'Tidak lengkap',
            ],
        ],
        11 => [
            'category' => 'C. Fasilitas dan Pelayanan',
            'text' => 'Pelayanan Mess & Akomodasi (Makan, penginapan, dsb)',
            'options' => [
                'A' => 'Sangat baik',
                'B' => 'Baik',
                'C' => 'Cukup',
                'D' => 'Kurang',
                'E' => 'Tidak memadai',
            ],
        ],
        12 => [
            'category' => 'C. Fasilitas dan Pelayanan',
            'text' => 'Pelayanan Learning Centre selama kegiatan berlangsung',
            'options' => [
                'A' => 'Sangat baik',
                'B' => 'Baik',
                'C' => 'Cukup',
                'D' => 'Kurang',
                'E' => 'Tidak baik',
            ],
        ],
    ];

    public static function export(DailyClass $dailyClass): StreamedResponse
    {
        $evaluations = TraineeEvaluation::where('daily_class_id', $dailyClass->id)
            ->with('trainee')
            ->orderBy('created_at', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();

        // Common Styles
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Calibri'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28844B']], // Dark Emerald Green
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E6B37']]],
        ];

        $dataStyle = [
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ];

        // SHEET 1: Respons Evaluasi (Google Form Style)
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Respons Evaluasi');

        $headers = [
            'Timestamp',
            'NIK',
            'Nama Peserta',
            'Jabatan',
            'Unit',
            'Region',
        ];
        for ($i = 1; $i <= 12; $i++) {
            $headers[] = $i . '. ' . self::QUESTIONS[$i]['text'];
        }
        $headers[] = 'Penjelasan (Jika memilih D/E)';
        $headers[] = 'Kesan & Pesan';

        $colIndex = 1;
        foreach ($headers as $header) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet1->getCell("{$colLetter}1")->setValue($header);
            $colIndex++;
        }

        $highestCol1 = $sheet1->getHighestColumn();
        $sheet1->getStyle("A1:{$highestCol1}1")->applyFromArray($headerStyle);
        $sheet1->getRowDimension(1)->setRowHeight(36);

        $rowIndex = 2;
        foreach ($evaluations as $eval) {
            $trainee = $eval->trainee;
            $answers = $eval->answers ?? [];

            $row = [
                $eval->created_at ? $eval->created_at->format('d/m/Y H:i:s') : '-',
                $trainee->nik ?? '-',
                $trainee->name ?? 'Anonim',
                $trainee->talent_to ?? '-',
                $trainee->unit ?? '-',
                $trainee->origin ?? '-',
            ];

            for ($i = 1; $i <= 12; $i++) {
                $ansCode = strtoupper(trim((string)($answers['q' . $i] ?? '')));
                if ($ansCode !== '' && isset(self::QUESTIONS[$i]['options'][$ansCode])) {
                    $row[] = $ansCode . ' - ' . self::QUESTIONS[$i]['options'][$ansCode];
                } elseif ($ansCode !== '') {
                    $row[] = $ansCode;
                } else {
                    $row[] = '-';
                }
            }

            $row[] = $eval->explanation_de ?: '-';
            $row[] = $eval->kesan_pesan ?: '-';

            $cIdx = 1;
            foreach ($row as $val) {
                $cL = Coordinate::stringFromColumnIndex($cIdx);
                $sheet1->getCell("{$cL}{$rowIndex}")->setValue($val);
                $cIdx++;
            }
            $sheet1->getRowDimension($rowIndex)->setRowHeight(24);
            $rowIndex++;
        }

        $lastRow1 = max(1, $rowIndex - 1);
        if ($lastRow1 > 1) {
            $sheet1->getStyle("A2:{$highestCol1}{$lastRow1}")->applyFromArray($dataStyle);
            $sheet1->getStyle("A2:F{$lastRow1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        for ($col = 1; $col <= count($headers); $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            if ($col <= 6) {
                $sheet1->getColumnDimension($colLetter)->setAutoSize(true);
            } else {
                $sheet1->getColumnDimension($colLetter)->setWidth(30);
            }
        }

        // SHEET 2: Ringkasan Summary
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Ringkasan Summary');

        $sheet2->setCellValue('A1', 'LAPORAN REKAPITULASI EVALUASI TRAINING');
        $sheet2->setCellValue('A2', 'Materi Pelatihan: ' . $dailyClass->title);
        $sheet2->setCellValue('A3', 'Tanggal Pelaksanaan: ' . ($dailyClass->date ? Carbon::parse($dailyClass->date)->translatedFormat('d F Y') : '-'));
        $sheet2->setCellValue('A4', 'Trainer / Instruktur: ' . ($dailyClass->trainer_name ?: '-'));
        $sheet2->setCellValue('A5', 'Total Partisipasi: ' . $evaluations->count() . ' Peserta');

        $sheet2->getStyle('A1:A5')->getFont()->setName('Calibri')->setBold(true);
        $sheet2->getStyle('A1')->getFont()->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('28844B'));
        $sheet2->getStyle('A2:A5')->getFont()->setSize(10);

        $summaryHeaders = ['No', 'Kategori', 'Pertanyaan', 'A (Sangat Baik)', 'B (Baik)', 'C (Cukup)', 'D (Kurang)', 'E (Sangat Kurang)', 'Total Respons', 'Skor Rata-Rata (1-5)', '% Positif (A & B)'];
        $colIdx = 1;
        foreach ($summaryHeaders as $sh) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $sheet2->setCellValue("{$colLetter}7", $sh);
            $colIdx++;
        }

        $highestCol2 = $sheet2->getHighestColumn();
        $sheet2->getStyle("A7:{$highestCol2}7")->applyFromArray($headerStyle);
        $sheet2->getRowDimension(7)->setRowHeight(28);

        $sRow = 8;
        $weights = ['A' => 5, 'B' => 4, 'C' => 3, 'D' => 2, 'E' => 1];

        for ($i = 1; $i <= 12; $i++) {
            $counts = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
            foreach ($evaluations as $e) {
                $code = strtoupper(trim((string)($e->answers['q' . $i] ?? '')));
                if (isset($counts[$code])) {
                    $counts[$code]++;
                }
            }
            $totalAns = array_sum($counts);
            $avgScore = 0;
            if ($totalAns > 0) {
                $sumScore = 0;
                foreach ($counts as $k => $cnt) {
                    $sumScore += $cnt * $weights[$k];
                }
                $avgScore = round($sumScore / $totalAns, 2);
            }
            $pctPositive = $totalAns > 0 ? round((($counts['A'] + $counts['B']) / $totalAns) * 100, 1) . '%' : '0%';

            $sheet2->setCellValue("A{$sRow}", $i);
            $sheet2->setCellValue("B{$sRow}", self::QUESTIONS[$i]['category']);
            $sheet2->setCellValue("C{$sRow}", self::QUESTIONS[$i]['text']);
            $sheet2->setCellValue("D{$sRow}", $counts['A']);
            $sheet2->setCellValue("E{$sRow}", $counts['B']);
            $sheet2->setCellValue("F{$sRow}", $counts['C']);
            $sheet2->setCellValue("G{$sRow}", $counts['D']);
            $sheet2->setCellValue("H{$sRow}", $counts['E']);
            $sheet2->setCellValue("I{$sRow}", $totalAns);
            $sheet2->setCellValue("J{$sRow}", $avgScore);
            $sheet2->setCellValue("K{$sRow}", $pctPositive);

            $sheet2->getRowDimension($sRow)->setRowHeight(22);
            $sRow++;
        }

        $lastRow2 = $sRow - 1;
        $sheet2->getStyle("A8:{$highestCol2}{$lastRow2}")->applyFromArray($dataStyle);
        $sheet2->getStyle("A8:A{$lastRow2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet2->getStyle("D8:K{$lastRow2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($col = 1; $col <= count($summaryHeaders); $col++) {
            $colLetter = Coordinate::stringFromColumnIndex($col);
            if ($colLetter === 'C') {
                $sheet2->getColumnDimension($colLetter)->setWidth(45);
            } else {
                $sheet2->getColumnDimension($colLetter)->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Evaluasi_' . Str::slug($dailyClass->title) . '_' . date('Ymd_His') . '.xlsx';

        $responseHeaders = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
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
}
