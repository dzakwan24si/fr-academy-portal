<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ImportTrainingCalendar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-training-calendar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import training calendar data from Excel to local JSON cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sourcePath = 'C:\\Users\\ASUS\\Downloads\\Kalender Pelatiha (1).xlsx';

        if (! file_exists($sourcePath)) {
            // Fallback to checking Kalender Pelatiha.xlsx
            $sourcePath = 'C:\\Users\\ASUS\\Downloads\\Kalender Pelatiha.xlsx';
        }

        if (! file_exists($sourcePath)) {
            $this->error('Excel file not found at: C:\\Users\\ASUS\\Downloads\\Kalender Pelatiha (1).xlsx');

            return 1;
        }

        $this->info('Copying Excel file to storage/app/Kalender_Pelatihan.xlsx...');

        // Ensure storage directory exists
        $storageDir = storage_path('app');
        if (! is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $destinationPath = storage_path('app/Kalender_Pelatihan.xlsx');
        copy($sourcePath, $destinationPath);

        $this->info('Parsing Excel file...');

        $zip = new ZipArchive;
        if ($zip->open($destinationPath) !== true) {
            $this->error('Failed to open excel file.');

            return 1;
        }

        // 1. Parse Shared Strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml !== false) {
            $xml = simplexml_load_string($sharedStringsXml);
            foreach ($xml->si as $val) {
                if (isset($val->t)) {
                    $sharedStrings[] = (string) $val->t;
                } else {
                    $text = '';
                    foreach ($val->r as $r) {
                        $text .= (string) $r->t;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        // 2. Helper to get cell values
        $getCellVal = function ($cell, $sharedStrings) {
            $type = (string) $cell['t'];
            $val = (string) $cell->v;
            if ($type === 's') {
                return trim($sharedStrings[(int) $val] ?? '');
            }

            return trim($val);
        };

        // 3. Helper to map columns
        $getRowData = function ($row, $sharedStrings, $getCellVal) {
            $data = [];
            foreach ($row->c as $cell) {
                $ref = (string) $cell['r'];
                preg_match('/^[A-Z]+/', $ref, $matches);
                $col = $matches[0] ?? '';
                $data[$col] = $getCellVal($cell, $sharedStrings);
            }

            return $data;
        };

        $unifiedPrograms = [];

        // --- SHEET 1: Non Regular & Soft Skill ---
        $sheet1Xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheet1Xml !== false) {
            $xml = simplexml_load_string($sheet1Xml);

            $currentSection = 'LEADERSHIP / SOFT SKILLS';
            $currentSubsection = '';
            $currentProgramNo = '';
            $currentProgramName = '';

            foreach ($xml->sheetData->row as $row) {
                $rowIndex = (int) $row['r'];
                if ($rowIndex < 8) {
                    continue;
                } // Skip headers & summary row

                $rowCells = $getRowData($row, $sharedStrings, $getCellVal);
                if (empty($rowCells)) {
                    continue;
                }

                $no = $rowCells['B'] ?? '';
                $program = $rowCells['C'] ?? '';

                // Detect Section Headers
                if ($no === 'I' && empty($program)) {
                    $currentSection = 'LEADERSHIP / SOFT SKILLS';
                    $currentSubsection = '';

                    continue;
                } elseif ($no === 'II' && $program === 'TECHNICAL') {
                    $currentSection = 'TECHNICAL';
                    $currentSubsection = '';

                    continue;
                }

                // Detect Subsection Headers
                if ($currentSection === 'TECHNICAL' && empty($program) && in_array($no, ['A', 'B', 'C', 'D', 'E'])) {
                    continue;
                }

                if ($no === 'A' && $program === 'ESTATE') {
                    $currentSubsection = 'ESTATE';

                    continue;
                } elseif ($no === 'B' && $program === 'MILL') {
                    $currentSubsection = 'MILL';

                    continue;
                } elseif ($no === 'C' && $program === 'TRAKSI') {
                    $currentSubsection = 'TRAKSI';

                    continue;
                } elseif ($no === 'D' && $program === 'DOWNSTREAM') {
                    $currentSubsection = 'DOWNSTREAM';

                    continue;
                } elseif ($no === 'E' && $program === 'ADMINISTRASI') {
                    $currentSubsection = 'ADMINISTRASI';

                    continue;
                }

                if (strpos($program, 'Total') !== false || strpos($no, 'Total') !== false || strpos($program, 'Jumlah') !== false) {
                    continue;
                }

                if ($no !== '' && $program !== '') {
                    $currentProgramNo = $no;
                    $currentProgramName = $program;
                }

                $region = $rowCells['D'] ?? '';
                if ($region !== '' && $currentProgramName !== '') {
                    $category = 'Training Corporate';
                    if (strcasecmp($region, 'Riau') === 0) {
                        $category = 'Training Riau';
                    } elseif (strcasecmp($region, 'Kalbar') === 0) {
                        $category = 'Training Kalbar';
                    } elseif (strcasecmp($region, 'Kaltim FR') === 0 || strcasecmp($region, 'Kaltim') === 0) {
                        $category = 'Training Kaltim';
                    } elseif (strcasecmp($region, 'Corporate') === 0) {
                        $category = 'Training Corporate';
                    } else {
                        $category = 'Training '.$region;
                    }

                    $calendar = [];
                    $monthMap = [
                        'AC' => 'Jan', 'AD' => 'Jan', 'AE' => 'Jan', 'AF' => 'Jan',
                        'AG' => 'Feb', 'AH' => 'Feb', 'AI' => 'Feb', 'AJ' => 'Feb',
                        'AK' => 'Mar', 'AL' => 'Mar', 'AM' => 'Mar', 'AN' => 'Mar',
                        'AO' => 'Apr', 'AP' => 'Apr', 'AQ' => 'Apr', 'AR' => 'Apr',
                        'AS' => 'Mei', 'AT' => 'Mei', 'AU' => 'Mei', 'AV' => 'Mei',
                        'AW' => 'Juni', 'AX' => 'Juni', 'AY' => 'Juni', 'AZ' => 'Juni',
                        'BA' => 'Juli', 'BB' => 'Juli', 'BC' => 'Juli', 'BD' => 'Juli',
                        'BE' => 'Ags', 'BF' => 'Ags', 'BG' => 'Ags', 'BH' => 'Ags',
                        'BI' => 'Sept', 'BJ' => 'Sept', 'BK' => 'Sept', 'BL' => 'Sept',
                        'BM' => 'Okt', 'BN' => 'Okt', 'BO' => 'Okt', 'BP' => 'Okt',
                        'BQ' => 'Nov', 'BR' => 'Nov', 'BS' => 'Nov', 'BT' => 'Nov',
                        'BU' => 'Des', 'BV' => 'Des', 'BW' => 'Des', 'BX' => 'Des',
                    ];

                    foreach ($monthMap as $col => $monthName) {
                        if (! empty($rowCells[$col])) {
                            $calendar[$monthName] = $rowCells[$col];
                        }
                    }

                    $unifiedPrograms[] = [
                        'sheet' => 'Non-Reguler & Soft Skill',
                        'category' => $category,
                        'section' => $currentSection,
                        'subsection' => $currentSubsection,
                        'no' => $currentProgramNo,
                        'name' => $currentProgramName,
                        'region' => $region,
                        'trainer' => $rowCells['E'] ?? '-',
                        'type_training' => $rowCells['F'] ?? '-',
                        'total_hours' => (int) ($rowCells['G'] ?? 0),
                        'days' => (int) ($rowCells['H'] ?? 0),
                        'participants' => [
                            'non_staff' => (int) ($rowCells['I'] ?? 0),
                            'ast' => (int) ($rowCells['J'] ?? 0),
                            'askep' => (int) ($rowCells['K'] ?? 0),
                            'mgr' => (int) ($rowCells['L'] ?? 0),
                            'gm' => (int) ($rowCells['M'] ?? 0),
                            'head' => (int) ($rowCells['N'] ?? 0),
                        ],
                        'target_participants' => [
                            'staf' => (int) ($rowCells['O'] ?? 0),
                            'non_staf' => (int) ($rowCells['P'] ?? 0),
                            'total' => (int) ($rowCells['Q'] ?? 0),
                        ],
                        'total_batches' => (int) ($rowCells['U'] ?? 0),
                        'location' => $rowCells['V'] ?? '-',
                        'calendar' => $calendar,
                    ];
                }
            }
        }

        // --- SHEET 2: KaPel Regular ---
        $sheet2Xml = $zip->getFromName('xl/worksheets/sheet2.xml');
        if ($sheet2Xml !== false) {
            $xml = simplexml_load_string($sheet2Xml);

            $currentSection = 'Field Assistant Training ( FAT)';

            foreach ($xml->sheetData->row as $row) {
                $rowIndex = (int) $row['r'];
                if ($rowIndex < 6) {
                    continue;
                }

                $rowCells = $getRowData($row, $sharedStrings, $getCellVal);
                if (empty($rowCells)) {
                    continue;
                }

                $prog = $rowCells['D'] ?? '';
                $batch = $rowCells['F'] ?? '';
                $period = $rowCells['H'] ?? '';

                if (preg_match('/^[A-Z]\.\s+(.*)/', $prog, $matches)) {
                    $currentSection = trim($matches[1]);

                    continue;
                }

                if (strpos($prog, 'Jumlah') !== false || (empty($prog) && empty($batch) && empty($period))) {
                    continue;
                }

                if ($prog !== '' && $batch !== '') {
                    $category = 'Training Reguler - Staf';
                    if (strpos($currentSection, 'Mandor') !== false || strpos($prog, 'PMT') !== false || strpos($prog, 'Mandor') !== false) {
                        $category = 'Training Reguler - Mandor';
                    }

                    $calendar = [];
                    $months = [
                        'O' => 'Jan', 'P' => 'Feb', 'Q' => 'Mar', 'R' => 'Apr',
                        'S' => 'Mei', 'T' => 'Jun', 'U' => 'Jul', 'V' => 'Ags',
                        'W' => 'Sept', 'X' => 'Okt', 'Y' => 'Nov', 'Z' => 'Des',
                    ];
                    foreach ($months as $col => $mName) {
                        if (! empty($rowCells[$col])) {
                            $calendar[$mName] = $rowCells[$col];
                        }
                    }

                    $unifiedPrograms[] = [
                        'sheet' => 'Reguler',
                        'category' => $category,
                        'section' => $currentSection,
                        'subsection' => '',
                        'no' => $batch,
                        'name' => $currentSection.' - '.$prog,
                        'region' => '-',
                        'trainer' => 'Internal',
                        'type_training' => 'Batch '.$batch,
                        'total_hours' => 0,
                        'days' => 0,
                        'participants' => [
                            'non_staff' => 0,
                            'ast' => 0,
                            'askep' => 0,
                            'mgr' => 0,
                            'gm' => 0,
                            'head' => 0,
                        ],
                        'target_participants' => [
                            'staf' => (int) ($rowCells['L'] ?? 0),
                            'non_staf' => 0,
                            'total' => (int) ($rowCells['L'] ?? 0),
                        ],
                        'total_batches' => 1,
                        'location' => $rowCells['N'] ?? 'FRLC Kubang',
                        'calendar' => $calendar,
                        'period' => $period,
                    ];
                }
            }
        }

        $zip->close();

        // Inject placeholders for Mandor Training since the user will provide this later
        $hasMandor = false;
        foreach ($unifiedPrograms as $p) {
            if ($p['category'] === 'Training Reguler - Mandor') {
                $hasMandor = true;
                break;
            }
        }
        if (! $hasMandor) {
            $unifiedPrograms[] = [
                'sheet' => 'Reguler',
                'category' => 'Training Reguler - Mandor',
                'section' => 'Program Mandor Tanaman (PMT)',
                'subsection' => '',
                'no' => 'I-2026',
                'name' => 'Program Mandor Tanaman (PMT) - PMT Tanaman',
                'region' => '-',
                'trainer' => 'Internal',
                'type_training' => 'Batch I-2026',
                'total_hours' => 0,
                'days' => 0,
                'participants' => [
                    'non_staff' => 0,
                    'ast' => 0,
                    'askep' => 0,
                    'mgr' => 0,
                    'gm' => 0,
                    'head' => 0,
                ],
                'target_participants' => [
                    'staf' => 30,
                    'non_staf' => 0,
                    'total' => 30,
                ],
                'total_batches' => 1,
                'location' => 'FRLC Kubang',
                'calendar' => [
                    'Mei' => '30',
                    'Jun' => '30',
                ],
                'period' => '1 Mei 2026 - 31 Jun 2026',
            ];
            $unifiedPrograms[] = [
                'sheet' => 'Reguler',
                'category' => 'Training Reguler - Mandor',
                'section' => 'Program Mandor Tanaman (PMT)',
                'subsection' => '',
                'no' => 'II-2026',
                'name' => 'Program Mandor Tanaman (PMT) - PMT Tanaman',
                'region' => '-',
                'trainer' => 'Internal',
                'type_training' => 'Batch II-2026',
                'total_hours' => 0,
                'days' => 0,
                'participants' => [
                    'non_staff' => 0,
                    'ast' => 0,
                    'askep' => 0,
                    'mgr' => 0,
                    'gm' => 0,
                    'head' => 0,
                ],
                'target_participants' => [
                    'staf' => 25,
                    'non_staf' => 0,
                    'total' => 25,
                ],
                'total_batches' => 1,
                'location' => 'FRLC Kubang',
                'calendar' => [
                    'Sept' => '25',
                    'Okt' => '25',
                ],
                'period' => '1 Sept 2026 - 31 Okt 2026',
            ];
        }

        // Write to JSON
        $jsonContent = json_encode($unifiedPrograms, JSON_PRETTY_PRINT);
        file_put_contents(storage_path('app/kalender_training.json'), $jsonContent);

        $this->info('Successfully imported '.count($unifiedPrograms).' training records!');

        return 0;
    }
}
