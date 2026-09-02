<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Mpp;
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
        // 1. Available Years
        $dbYears = Mpp::select('tahun')->distinct()->pluck('tahun')->toArray();
        $defaultYears = [2024, 2025, 2026, 2027, 2028];
        $years = array_unique(array_merge($defaultYears, $dbYears));
        sort($years);

        // 2. Selected Context: Year & Month
        $selectedYear = (int) $request->get('year', 2026);
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

        return view('Administrasi.Pages.mpp', compact(
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
            'tahun' => 'required|integer',
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
            ]
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
            'new_year' => 'required|integer|min:2000|max:2099'
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

        $filename = "Laporan_MPP_Jabatan_{$monthName}_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($year, $month, $monthName) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            // Title info
            fputcsv($handle, ["Laporan MPP Jabatan - Periode {$monthName} {$year}"]);
            fputcsv($handle, []);

            // Header row
            fputcsv($handle, ['Nama Jabatan', 'Staff', 'PBT', 'KHT', 'KHL', 'Total']);

            $data = Mpp::getMatrixData($year, $month);
            $matrix = $data['matrix'];
            $totals = $data['totals'];

            foreach ($matrix as $row) {
                $rowTotal = $row['Staff'] + $row['PBT'] + $row['KHT'] + $row['KHL'];
                fputcsv($handle, [
                    $row['nama_jabatan'],
                    $row['Staff'],
                    $row['PBT'],
                    $row['KHT'],
                    $row['KHL'],
                    $rowTotal
                ]);
            }

            // Total Row
            fputcsv($handle, [
                'TOTAL',
                $totals['Staff'],
                $totals['PBT'],
                $totals['KHT'],
                $totals['KHL'],
                $totals['total_overall']
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
