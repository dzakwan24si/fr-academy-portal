<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Administrasi\Mpp;
use App\Services\ExcelExporter;
use Illuminate\Http\Request;

class MppController extends Controller
{
    /**
     * Month definitions
     */
    protected array $months = [
        1 => ['short' => 'Jan', 'full' => 'January'],
        2 => ['short' => 'Feb', 'full' => 'February'],
        3 => ['short' => 'Mar', 'full' => 'March'],
        4 => ['short' => 'Apr', 'full' => 'April'],
        5 => ['short' => 'Mei', 'full' => 'May'],
        6 => ['short' => 'Jun', 'full' => 'June'],
        7 => ['short' => 'Jul', 'full' => 'July'],
        8 => ['short' => 'Aug', 'full' => 'August'],
        9 => ['short' => 'Sep', 'full' => 'September'],
        10 => ['short' => 'Okt', 'full' => 'October'],
        11 => ['short' => 'Nov', 'full' => 'November'],
        12 => ['short' => 'Des', 'full' => 'December'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Dynamic Year Selection
        $selectedYear = (int) $request->get('year', 2026);
        $mppYears = Mpp::select('tahun')->distinct()->whereNotNull('tahun')->pluck('tahun')->toArray();
        $baseYears = range(date('Y') - 3, date('Y') + 2);
        $years = array_values(array_unique(array_filter(array_merge($baseYears, $mppYears, [$selectedYear]))));
        sort($years);

        // 2. Selected Context: Year & Month
        $selectedMonth = (int) $request->get('month', 8); // Default to August (8) per requirement/screenshot
        if ($selectedMonth < 1 || $selectedMonth > 12) {
            $selectedMonth = 8;
        }

        $search = $request->get('search', '');

        // 3. Retrieve Matrix Data
        $data = Mpp::getMatrixData($selectedYear, $selectedMonth, $search);
        $matrix = $data['matrix'];
        $totals = $data['totals'];

        // 4. Existing Jabatan List for Autocomplete Suggestions
        $existingJabatan = Mpp::distinct()->pluck('nama_jabatan')->toArray();

        // Month Names
        $monthInfo = $this->months[$selectedMonth] ?? ['short' => 'Aug', 'full' => 'August'];
        $months = $this->months;

        return view('Administrasi.Pages.Administrasi.mpp', compact(
            'years',
            'months',
            'selectedYear',
            'selectedMonth',
            'monthInfo',
            'matrix',
            'totals',
            'existingJabatan',
            'search'
        ));
    }

    /**
     * Store a newly created or batch created MPP data.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jabatan' => 'required|string|max:100',
            'tahun' => 'required|integer|min:2000|max:2099',
            'bulan' => 'required|integer|min:1|max:12',
            // Option 1: Single type entry
            'jenis_jabatan' => 'nullable|string|in:Staff,PBT,KHT,KHL',
            'jumlah' => 'nullable|integer|min:0',
            // Option 2: Batch entry for all 4 types
            'staff' => 'nullable|integer|min:0',
            'pbt' => 'nullable|integer|min:0',
            'kht' => 'nullable|integer|min:0',
            'khl' => 'nullable|integer|min:0',
        ]);

        $jabatan = trim($validated['nama_jabatan']);
        $tahun = (int) $validated['tahun'];
        $bulan = (int) $validated['bulan'];

        // If batch input is provided
        if ($request->has('staff') || $request->has('pbt') || $request->has('kht') || $request->has('khl')) {
            $types = [
                'Staff' => (int) $request->input('staff', 0),
                'PBT' => (int) $request->input('pbt', 0),
                'KHT' => (int) $request->input('kht', 0),
                'KHL' => (int) $request->input('khl', 0),
            ];

            foreach ($types as $type => $count) {
                Mpp::updateOrCreate(
                    [
                        'nama_jabatan' => $jabatan,
                        'jenis_jabatan' => $type,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                    ],
                    [
                        'jumlah' => $count,
                    ]
                );
            }

            $message = "Data MPP untuk jabatan '{$jabatan}' periode {$this->months[$bulan]['full']} {$tahun} berhasil disimpan.";
        } else {
            // Single entry fallback
            $jenis = $validated['jenis_jabatan'] ?? 'Staff';
            $jumlah = (int) ($validated['jumlah'] ?? 0);

            Mpp::updateOrCreate(
                [
                    'nama_jabatan' => $jabatan,
                    'jenis_jabatan' => $jenis,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                ],
                [
                    'jumlah' => $jumlah,
                ]
            );

            $message = "Data MPP {$jabatan} ({$jenis}: {$jumlah}) berhasil disimpan.";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('administrasi.mpp.index', [
            'year' => $tahun,
            'month' => $bulan,
        ])->with('success', $message);
    }

    /**
     * Get MPP record for a specific jabatan, year and month (for AJAX Edit Modal).
     */
    public function showByJabatan(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer',
        ]);

        $jabatan = $request->get('nama_jabatan');
        $tahun = (int) $request->get('tahun');
        $bulan = (int) $request->get('bulan');

        $records = Mpp::where('nama_jabatan', $jabatan)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get();

        $counts = [
            'Staff' => 0,
            'PBT' => 0,
            'KHT' => 0,
            'KHL' => 0,
        ];

        foreach ($records as $r) {
            if (isset($counts[$r->jenis_jabatan])) {
                $counts[$r->jenis_jabatan] = $r->jumlah;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nama_jabatan' => $jabatan,
                'tahun' => $tahun,
                'bulan' => $bulan,
                'counts' => $counts,
            ],
        ]);
    }

    /**
     * Update MPP data for a specific jabatan.
     */
    public function updateJabatan(Request $request)
    {
        $request->validate([
            'original_nama_jabatan' => 'required|string',
            'nama_jabatan' => 'required|string|max:100',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
            'staff' => 'nullable|integer|min:0',
            'pbt' => 'nullable|integer|min:0',
            'kht' => 'nullable|integer|min:0',
            'khl' => 'nullable|integer|min:0',
        ]);

        $oldJabatan = $request->input('original_nama_jabatan');
        $newJabatan = trim($request->input('nama_jabatan'));
        $tahun = (int) $request->input('tahun');
        $bulan = (int) $request->input('bulan');

        // Delete old records if name changed
        if ($oldJabatan !== $newJabatan) {
            Mpp::where('nama_jabatan', $oldJabatan)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->delete();
        }

        $types = [
            'Staff' => (int) $request->input('staff', 0),
            'PBT' => (int) $request->input('pbt', 0),
            'KHT' => (int) $request->input('kht', 0),
            'KHL' => (int) $request->input('khl', 0),
        ];

        foreach ($types as $type => $count) {
            Mpp::updateOrCreate(
                [
                    'nama_jabatan' => $newJabatan,
                    'jenis_jabatan' => $type,
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                ],
                [
                    'jumlah' => $count,
                ]
            );
        }

        $message = "Data MPP untuk jabatan '{$newJabatan}' berhasil diperbarui.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('administrasi.mpp.index', [
            'year' => $tahun,
            'month' => $bulan,
        ])->with('success', $message);
    }

    /**
     * Delete all entries for a specific jabatan in that year and month.
     */
    public function destroyJabatan(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer',
        ]);

        $jabatan = $request->input('nama_jabatan');
        $tahun = (int) $request->input('tahun');
        $bulan = (int) $request->input('bulan');

        Mpp::where('nama_jabatan', $jabatan)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->delete();

        $message = "Data MPP untuk jabatan '{$jabatan}' periode {$this->months[$bulan]['full']} {$tahun} berhasil dihapus.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('administrasi.mpp.index', [
            'year' => $tahun,
            'month' => $bulan,
        ])->with('success', $message);
    }

    /**
     * Switch/Add New Year
     */
    public function storeYear(Request $request)
    {
        $request->validate([
            'new_year' => 'required|integer|min:2000|max:2099',
        ]);

        $newYear = (int) $request->new_year;
        $currentMonth = (int) $request->get('month', 8);

        return redirect()->route('administrasi.mpp.index', [
            'year' => $newYear,
            'month' => $currentMonth,
        ])->with('success', "Tahun {$newYear} berhasil dipilih sebagai periode aktif.");
    }

    /**
     * Export Report to CSV
     */
    public function export(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $month = (int) $request->get('month', 8);
        $monthName = $this->months[$month]['full'] ?? 'August';

        $data = Mpp::getMatrixData($year, $month);
        $matrix = $data['matrix'];
        $totals = $data['totals'];

        $headers = ['Nama Jabatan', 'Staff', 'PBT', 'KHT', 'KHL', 'Total'];
        $dataRows = [];

        foreach ($matrix as $row) {
            $rowTotal = $row['Staff'] + $row['PBT'] + $row['KHT'] + $row['KHL'];
            $dataRows[] = [
                $row['nama_jabatan'],
                $row['Staff'],
                $row['PBT'],
                $row['KHT'],
                $row['KHL'],
                $rowTotal,
            ];
        }

        $dataRows[] = [
            'TOTAL',
            $totals['Staff'],
            $totals['PBT'],
            $totals['KHT'],
            $totals['KHL'],
            $totals['total_overall'],
        ];

        $filename = "Laporan_MPP_Jabatan_{$monthName}_{$year}.xlsx";

        return ExcelExporter::download($filename, $headers, $dataRows, 'Laporan MPP');
    }

    /**
     * Download Excel Template for MPP Import
     */
    public function downloadTemplate()
    {
        $headers = [
            'nama_jabatan',
            'Staff',
            'PBT',
            'KHT',
            'KHL',
            'tahun',
            'bulan',
        ];

        $sampleRows = [
            ['Manager Ops', '2', '0', '0', '0', '2026', '8'],
            ['Asisten Agronomi', '10', '2', '1', '0', '2026', '8'],
            ['Mandor Panen', '0', '2', '10', '5', '2026', '8'],
            ['Kerani', '3', '0', '0', '0', '2026', '8'],
        ];

        return ExcelExporter::download('Template_Import_MPP.xlsx', $headers, $sampleRows, 'Template MPP');
    }

    /**
     * Import CSV File into MPP Database
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,text|max:10240',
            'import_mode' => 'nullable|string|in:append,replace',
            'year' => 'nullable|integer',
            'month' => 'nullable|integer',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $importMode = $request->get('import_mode', 'append');
        $fallbackYear = (int) $request->get('year', 2026);
        $fallbackMonth = (int) $request->get('month', 8);

        // Detect delimiter
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return back()->with('error', 'Gagal membuka file CSV yang diunggah.');
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
            $delimiter = ';';
        } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
            $delimiter = "\t";
        }

        // Read header
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeaders) {
            fclose($handle);

            return back()->with('error', 'Format file CSV tidak valid atau file kosong.');
        }

        // Normalize header mapping
        $headerMap = [];
        $isMatrixFormat = false;

        foreach ($rawHeaders as $index => $header) {
            $clean = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header)));
            $clean = str_replace([' ', '_', '-', '.', '/'], '', $clean);

            if (in_array($clean, ['namajabatan', 'jabatan', 'position', 'posisi'])) {
                $headerMap['nama_jabatan'] = $index;
            } elseif (in_array($clean, ['tahun', 'year', 'thn'])) {
                $headerMap['tahun'] = $index;
            } elseif (in_array($clean, ['bulan', 'month', 'bln', 'periode'])) {
                $headerMap['bulan'] = $index;
            } elseif ($clean === 'staff') {
                $headerMap['staff'] = $index;
                $isMatrixFormat = true;
            } elseif ($clean === 'pbt') {
                $headerMap['pbt'] = $index;
                $isMatrixFormat = true;
            } elseif ($clean === 'kht') {
                $headerMap['kht'] = $index;
                $isMatrixFormat = true;
            } elseif ($clean === 'khl') {
                $headerMap['khl'] = $index;
                $isMatrixFormat = true;
            } elseif (in_array($clean, ['jenisjabatan', 'jenis', 'tipe', 'type'])) {
                $headerMap['jenis_jabatan'] = $index;
            } elseif (in_array($clean, ['jumlah', 'count', 'total', 'qty', 'kuota'])) {
                $headerMap['jumlah'] = $index;
            }
        }

        if (! isset($headerMap['nama_jabatan'])) {
            fclose($handle);

            return back()->with('error', 'Header kolom "Nama Jabatan" tidak ditemukan dalam file CSV.');
        }

        // Month text translation helper
        $monthTextMap = [
            'jan' => 1, 'januari' => 1, 'january' => 1,
            'feb' => 2, 'februari' => 2, 'february' => 2,
            'mar' => 3, 'maret' => 3, 'march' => 3,
            'apr' => 4, 'april' => 4,
            'mei' => 5, 'may' => 5,
            'jun' => 6, 'juni' => 6, 'june' => 6,
            'jul' => 7, 'juli' => 7, 'july' => 7,
            'aug' => 8, 'agustus' => 8, 'agu' => 8, 'august' => 8,
            'sep' => 9, 'september' => 9,
            'okt' => 10, 'oktober' => 10, 'oct' => 10, 'october' => 10,
            'nov' => 11, 'november' => 11,
            'des' => 12, 'desember' => 12, 'dec' => 12, 'december' => 12,
        ];

        // Replace mode: delete data for active context if replace mode selected
        if ($importMode === 'replace') {
            Mpp::where('tahun', $fallbackYear)->where('bulan', $fallbackMonth)->delete();
        }

        $insertedCount = 0;
        $updatedCount = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $jabatan = isset($headerMap['nama_jabatan']) && isset($row[$headerMap['nama_jabatan']]) ? trim($row[$headerMap['nama_jabatan']]) : '';
            if (empty($jabatan) || strtolower($jabatan) === 'total') {
                continue;
            }

            // Determine Year
            $tahun = $fallbackYear;
            if (isset($headerMap['tahun']) && isset($row[$headerMap['tahun']]) && ! empty(trim($row[$headerMap['tahun']]))) {
                $tahun = (int) trim($row[$headerMap['tahun']]);
            }

            // Determine Month
            $bulan = $fallbackMonth;
            if (isset($headerMap['bulan']) && isset($row[$headerMap['bulan']]) && ! empty(trim($row[$headerMap['bulan']]))) {
                $mRaw = strtolower(trim($row[$headerMap['bulan']]));
                if (is_numeric($mRaw)) {
                    $bulan = (int) $mRaw;
                } else {
                    $bulan = $monthTextMap[$mRaw] ?? $fallbackMonth;
                }
            }

            if ($isMatrixFormat) {
                // Matrix columns: Staff, PBT, KHT, KHL
                $types = [
                    'Staff' => isset($headerMap['staff']) && isset($row[$headerMap['staff']]) ? (int) preg_replace('/[^0-9]/', '', $row[$headerMap['staff']]) : 0,
                    'PBT' => isset($headerMap['pbt']) && isset($row[$headerMap['pbt']]) ? (int) preg_replace('/[^0-9]/', '', $row[$headerMap['pbt']]) : 0,
                    'KHT' => isset($headerMap['kht']) && isset($row[$headerMap['kht']]) ? (int) preg_replace('/[^0-9]/', '', $row[$headerMap['kht']]) : 0,
                    'KHL' => isset($headerMap['khl']) && isset($row[$headerMap['khl']]) ? (int) preg_replace('/[^0-9]/', '', $row[$headerMap['khl']]) : 0,
                ];

                foreach ($types as $type => $count) {
                    $record = Mpp::updateOrCreate(
                        [
                            'nama_jabatan' => $jabatan,
                            'jenis_jabatan' => $type,
                            'tahun' => $tahun,
                            'bulan' => $bulan,
                        ],
                        [
                            'jumlah' => $count,
                        ]
                    );

                    if ($record->wasRecentlyCreated) {
                        $insertedCount++;
                    } else {
                        $updatedCount++;
                    }
                }
            } else {
                // Row format: nama_jabatan, jenis_jabatan, jumlah
                $jenis = isset($headerMap['jenis_jabatan']) && isset($row[$headerMap['jenis_jabatan']]) ? trim($row[$headerMap['jenis_jabatan']]) : 'Staff';
                $jumlah = isset($headerMap['jumlah']) && isset($row[$headerMap['jumlah']]) ? (int) preg_replace('/[^0-9]/', '', $row[$headerMap['jumlah']]) : 0;

                // Capitalize standard types if match
                $upperJenis = strtoupper($jenis);
                if ($upperJenis === 'STAFF') {
                    $jenis = 'Staff';
                } elseif ($upperJenis === 'PBT') {
                    $jenis = 'PBT';
                } elseif ($upperJenis === 'KHT') {
                    $jenis = 'KHT';
                } elseif ($upperJenis === 'KHL') {
                    $jenis = 'KHL';
                }

                $record = Mpp::updateOrCreate(
                    [
                        'nama_jabatan' => $jabatan,
                        'jenis_jabatan' => $jenis,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                    ],
                    [
                        'jumlah' => $jumlah,
                    ]
                );

                if ($record->wasRecentlyCreated) {
                    $insertedCount++;
                } else {
                    $updatedCount++;
                }
            }
        }

        fclose($handle);

        $msg = "Import CSV MPP berhasil: {$insertedCount} data baru ditambahkan";
        if ($updatedCount > 0) {
            $msg .= ", {$updatedCount} data diperbarui";
        }
        $msg .= '.';

        ActivityLog::log(
            'Import',
            'MPP',
            'Mengimport data MPP Jabatan dari file '.$file->getClientOriginalName()." ({$insertedCount} baru, {$updatedCount} diperbarui)",
            ['filename' => $file->getClientOriginalName(), 'inserted' => $insertedCount, 'updated' => $updatedCount]
        );

        return redirect()->route('administrasi.mpp.index', [
            'year' => $fallbackYear,
            'month' => $fallbackMonth,
        ])->with('success', $msg);
    }
}
