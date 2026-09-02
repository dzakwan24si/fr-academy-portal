<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Vra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Available Years
        $dbYears = Vra::select('tahun')->distinct()->pluck('tahun')->toArray();
        $defaultYears = [2024, 2025, 2026, 2027, 2028];
        $years = array_unique(array_merge($defaultYears, $dbYears));
        sort($years);

        // 2. Selected Year & Period Context
        $selectedYear = (int) $request->get('year', 2026);
        $selectedPeriod = $request->get('period', 'overall'); // 'overall' or '1'..'12'
        $search = $request->get('search', '');

        // Month Mapping
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        // 3. Query Data based on Period Context
        if ($selectedPeriod === 'overall') {
            // Overall View: Group by vehicle for annual summary
            $query = Vra::where('tahun', $selectedYear);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_polisi', 'like', "%{$search}%")
                      ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            // Get unique vehicle annual summaries for the year
            $records = $query->select(
                'nomor_polisi',
                'kendaraan',
                DB::raw('MAX(vra_id) as vra_id'),
                DB::raw('MAX(upah_tahun) as upah_tahun'),
                DB::raw('MAX(BBM_tahun) as BBM_tahun'),
                DB::raw('MAX(preventif_maintenance_tahun) as preventif_maintenance_tahun'),
                DB::raw('MAX(biaya_lainnya_tahun) as biaya_lainnya_tahun'),
                DB::raw('MAX(total_biaya_tahun) as total_biaya_tahun'),
                DB::raw('MAX(KM_tahun) as KM_tahun')
            )
            ->groupBy('nomor_polisi', 'kendaraan')
            ->orderBy('nomor_polisi', 'asc')
            ->paginate(15)
            ->withQueryString();

            // Calculate overall totals
            $allAnnualRecords = Vra::where('tahun', $selectedYear)
                ->select(
                    'nomor_polisi',
                    'kendaraan',
                    DB::raw('MAX(upah_tahun) as upah_tahun'),
                    DB::raw('MAX(BBM_tahun) as BBM_tahun'),
                    DB::raw('MAX(preventif_maintenance_tahun) as preventif_maintenance_tahun'),
                    DB::raw('MAX(biaya_lainnya_tahun) as biaya_lainnya_tahun'),
                    DB::raw('MAX(total_biaya_tahun) as total_biaya_tahun'),
                    DB::raw('MAX(KM_tahun) as KM_tahun')
                )
                ->groupBy('nomor_polisi', 'kendaraan')
                ->get();

            $totals = [
                'count' => $allAnnualRecords->count(),
                'upah' => $allAnnualRecords->sum('upah_tahun'),
                'bbm' => $allAnnualRecords->sum('BBM_tahun'),
                'maintenance' => $allAnnualRecords->sum('preventif_maintenance_tahun'),
                'lainnya' => $allAnnualRecords->sum('biaya_lainnya_tahun'),
                'total_biaya' => $allAnnualRecords->sum('total_biaya_tahun'),
                'km' => $allAnnualRecords->sum('KM_tahun'),
            ];

        } else {
            // Monthly View: Filter by specific month & year
            $monthNum = (int) $selectedPeriod;
            $query = Vra::where('tahun', $selectedYear)->where('bulan', $monthNum);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_polisi', 'like', "%{$search}%")
                      ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            $records = $query->orderBy('nomor_polisi', 'asc')
                ->paginate(15)
                ->withQueryString();

            // Calculate monthly totals
            $allMonthlyRecords = Vra::where('tahun', $selectedYear)
                ->where('bulan', $monthNum)
                ->get();

            $totals = [
                'count' => $allMonthlyRecords->count(),
                'upah' => $allMonthlyRecords->sum('upah_bulan'),
                'bbm' => $allMonthlyRecords->sum('BBM_bulan'),
                'maintenance' => $allMonthlyRecords->sum('preventif_maintenance_bulan'),
                'lainnya' => $allMonthlyRecords->sum('biaya_lainnya_bulan'),
                'total_biaya' => $allMonthlyRecords->sum('total_biaya_bulan'),
                'km_bulan' => $allMonthlyRecords->sum('KM_bulan'),
            ];
        }

        return view('Administrasi.Pages.vra', compact(
            'years',
            'selectedYear',
            'selectedPeriod',
            'months',
            'records',
            'totals',
            'search'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_polisi' => 'required|string|max:20',
            'kendaraan' => 'required|string|max:100',
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
            'upah_bulan' => 'nullable|numeric|min:0',
            'upah_tahun' => 'nullable|numeric|min:0',
            'BBM_bulan' => 'nullable|numeric|min:0',
            'BBM_tahun' => 'nullable|numeric|min:0',
            'preventif_maintenance_bulan' => 'nullable|numeric|min:0',
            'preventif_maintenance_tahun' => 'nullable|numeric|min:0',
            'biaya_lainnya_bulan' => 'nullable|numeric|min:0',
            'biaya_lainnya_tahun' => 'nullable|numeric|min:0',
            'KM_awal' => 'nullable|numeric|min:0',
            'KM_akhir' => 'nullable|numeric|min:0',
            'KM_bulan' => 'nullable|numeric|min:0',
            'KM_tahun' => 'nullable|numeric|min:0',
        ]);

        // Default numeric zero
        foreach ([
            'upah_bulan', 'upah_tahun', 'BBM_bulan', 'BBM_tahun',
            'preventif_maintenance_bulan', 'preventif_maintenance_tahun',
            'biaya_lainnya_bulan', 'biaya_lainnya_tahun',
            'KM_awal', 'KM_akhir', 'KM_bulan', 'KM_tahun'
        ] as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field] !== '' ? (float) $validated[$field] : 0;
        }

        // Auto calculate total biaya bulan
        $validated['total_biaya_bulan'] = $validated['upah_bulan']
            + $validated['BBM_bulan']
            + $validated['preventif_maintenance_bulan']
            + $validated['biaya_lainnya_bulan'];

        // Auto calculate total biaya tahun
        $validated['total_biaya_tahun'] = $validated['upah_tahun']
            + $validated['BBM_tahun']
            + $validated['preventif_maintenance_tahun']
            + $validated['biaya_lainnya_tahun'];

        // Auto calculate KM bulan if KM Akhir and KM Awal are provided and KM_bulan not explicitly given
        if ($validated['KM_akhir'] > $validated['KM_awal'] && $validated['KM_bulan'] == 0) {
            $validated['KM_bulan'] = $validated['KM_akhir'] - $validated['KM_awal'];
        }

        $vra = Vra::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data VRA berhasil ditambahkan.',
                'data' => $vra
            ]);
        }

        return redirect()->route('administrasi.vra.index', [
            'year' => $validated['tahun'],
            'period' => $validated['bulan']
        ])->with('success', 'Data VRA untuk kendaraan ' . $validated['nomor_polisi'] . ' berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vra = Vra::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $vra
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $vra = Vra::findOrFail($id);

        $validated = $request->validate([
            'nomor_polisi' => 'required|string|max:20',
            'kendaraan' => 'required|string|max:100',
            'upah_bulan' => 'nullable|numeric|min:0',
            'upah_tahun' => 'nullable|numeric|min:0',
            'BBM_bulan' => 'nullable|numeric|min:0',
            'BBM_tahun' => 'nullable|numeric|min:0',
            'preventif_maintenance_bulan' => 'nullable|numeric|min:0',
            'preventif_maintenance_tahun' => 'nullable|numeric|min:0',
            'biaya_lainnya_bulan' => 'nullable|numeric|min:0',
            'biaya_lainnya_tahun' => 'nullable|numeric|min:0',
            'KM_awal' => 'nullable|numeric|min:0',
            'KM_akhir' => 'nullable|numeric|min:0',
            'KM_bulan' => 'nullable|numeric|min:0',
            'KM_tahun' => 'nullable|numeric|min:0',
        ]);

        // Default numeric zero
        foreach ([
            'upah_bulan', 'upah_tahun', 'BBM_bulan', 'BBM_tahun',
            'preventif_maintenance_bulan', 'preventif_maintenance_tahun',
            'biaya_lainnya_bulan', 'biaya_lainnya_tahun',
            'KM_awal', 'KM_akhir', 'KM_bulan', 'KM_tahun'
        ] as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field] !== '' ? (float) $validated[$field] : 0;
        }

        // Auto calculate total biaya bulan
        $validated['total_biaya_bulan'] = $validated['upah_bulan']
            + $validated['BBM_bulan']
            + $validated['preventif_maintenance_bulan']
            + $validated['biaya_lainnya_bulan'];

        // Auto calculate total biaya tahun
        $validated['total_biaya_tahun'] = $validated['upah_tahun']
            + $validated['BBM_tahun']
            + $validated['preventif_maintenance_tahun']
            + $validated['biaya_lainnya_tahun'];

        // Auto calculate KM bulan if KM Akhir and KM Awal are provided and KM_bulan not explicitly given
        if ($validated['KM_akhir'] > $validated['KM_awal'] && $validated['KM_bulan'] == 0) {
            $validated['KM_bulan'] = $validated['KM_akhir'] - $validated['KM_awal'];
        }

        // Update record preserving original tahun and bulan
        $vra->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data VRA berhasil diperbarui.',
                'data' => $vra
            ]);
        }

        $period = $request->get('period', $vra->bulan);

        return redirect()->route('administrasi.vra.index', [
            'year' => $vra->tahun,
            'period' => $period
        ])->with('success', 'Data VRA untuk kendaraan ' . $vra->nomor_polisi . ' berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $vra = Vra::findOrFail($id);
        $year = $vra->tahun;
        $period = $request->get('period', $vra->bulan);
        $noPol = $vra->nomor_polisi;

        $vra->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data VRA {$noPol} berhasil dihapus."
            ]);
        }

        return redirect()->route('administrasi.vra.index', [
            'year' => $year,
            'period' => $period
        ])->with('success', "Data VRA kendaraan {$noPol} berhasil dihapus.");
    }

    /**
     * Add a new year
     */
    public function storeYear(Request $request)
    {
        $request->validate([
            'new_year' => 'required|integer|min:2000|max:2099'
        ]);

        $newYear = (int) $request->new_year;

        return redirect()->route('administrasi.vra.index', [
            'year' => $newYear,
            'period' => 'overall'
        ])->with('success', "Tahun {$newYear} berhasil dipilih sebagai periode aktif.");
    }

    /**
     * Export Report
     */
    public function export(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $period = $request->get('period', 'overall');

        $filename = "VRA_Report_{$year}_" . ($period === 'overall' ? 'Overall' : "Bulan_{$period}") . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($year, $period) {
            $handle = fopen('php://output', 'w');

            if ($period === 'overall') {
                fputcsv($handle, [
                    'No. Polisi', 'Kendaraan', 'Upah/Tahun (IDR)', 'BBM/Tahun (IDR)',
                    'Prev. Maint./Tahun (IDR)', 'Biaya Lainnya (IDR)', 'Total Biaya (IDR)', 'KM/Tahun', 'Tahun'
                ]);

                $data = Vra::where('tahun', $year)
                    ->select(
                        'nomor_polisi', 'kendaraan',
                        DB::raw('MAX(upah_tahun) as upah_tahun'),
                        DB::raw('MAX(BBM_tahun) as BBM_tahun'),
                        DB::raw('MAX(preventif_maintenance_tahun) as preventif_maintenance_tahun'),
                        DB::raw('MAX(biaya_lainnya_tahun) as biaya_lainnya_tahun'),
                        DB::raw('MAX(total_biaya_tahun) as total_biaya_tahun'),
                        DB::raw('MAX(KM_tahun) as KM_tahun')
                    )
                    ->groupBy('nomor_polisi', 'kendaraan')
                    ->get();

                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row->nomor_polisi,
                        $row->kendaraan,
                        $row->upah_tahun,
                        $row->BBM_tahun,
                        $row->preventif_maintenance_tahun,
                        $row->biaya_lainnya_tahun,
                        $row->total_biaya_tahun,
                        $row->KM_tahun,
                        $year
                    ]);
                }
            } else {
                fputcsv($handle, [
                    'No. Polisi', 'Kendaraan', 'Upah/Bulan (IDR)', 'BBM/Bulan (IDR)',
                    'Prev. Maint./Bulan (IDR)', 'Biaya Lainnya/Bulan (IDR)', 'Total Biaya/Bulan (IDR)',
                    'KM Awal', 'KM Akhir', 'KM/Bulan', 'Bulan', 'Tahun'
                ]);

                $data = Vra::where('tahun', $year)->where('bulan', (int) $period)->get();

                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row->nomor_polisi,
                        $row->kendaraan,
                        $row->upah_bulan,
                        $row->BBM_bulan,
                        $row->preventif_maintenance_bulan,
                        $row->biaya_lainnya_bulan,
                        $row->total_biaya_bulan,
                        $row->KM_awal,
                        $row->KM_akhir,
                        $row->KM_bulan,
                        $row->bulan,
                        $row->tahun
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV Template for Import
     */
    public function downloadTemplate()
    {
        $filename = "Template_Import_VRA.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'nomor_polisi',
                'kendaraan',
                'tahun',
                'bulan',
                'upah_bulan',
                'BBM_bulan',
                'preventif_maintenance_bulan',
                'biaya_lainnya_bulan',
                'KM_awal',
                'KM_akhir',
                'KM_bulan',
                'upah_tahun',
                'BBM_tahun',
                'preventif_maintenance_tahun',
                'biaya_lainnya_tahun',
                'KM_tahun'
            ]);

            // Sample rows
            fputcsv($handle, [
                'B 1234 ABC',
                'Toyota Hilux Double Cabin',
                '2026',
                '1',
                '3750000',
                '1041666',
                '666666',
                '100000',
                '10000',
                '12041',
                '2041',
                '45000000',
                '12500000',
                '8000000',
                '1200000',
                '24500'
            ]);

            fputcsv($handle, [
                'B 5678 DEF',
                'Mitsubishi Triton',
                '2026',
                '1',
                '3750000',
                '1183333',
                '791666',
                '66666',
                '25000',
                '27341',
                '2341',
                '45000000',
                '14200000',
                '9500000',
                '800000',
                '28100'
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import CSV File into VRA Database
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,text|max:10240',
            'import_mode' => 'nullable|string|in:append,replace',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $importMode = $request->get('import_mode', 'append');

        // Optional replace existing data
        if ($importMode === 'replace') {
            Vra::truncate();
        }

        // Detect delimiter
        $handle = fopen($filePath, 'r');
        if (!$handle) {
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
        if (!$rawHeaders) {
            fclose($handle);
            return back()->with('error', 'Format file CSV tidak valid atau file kosong.');
        }

        // Normalize header mapping
        $headerMap = [];
        foreach ($rawHeaders as $index => $header) {
            // Clean BOM and sanitize
            $clean = strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header)));
            $clean = str_replace([' ', '_', '-', '.', '/'], '', $clean);

            if (in_array($clean, ['nomorpolisi', 'nopolisi', 'nopol', 'plat', 'platnomor'])) {
                $headerMap['nomor_polisi'] = $index;
            } elseif (in_array($clean, ['kendaraan', 'namakendaraan', 'jeniskendaraan', 'unit', 'mobil', 'tipekendaraan'])) {
                $headerMap['kendaraan'] = $index;
            } elseif (in_array($clean, ['tahun', 'year', 'thn'])) {
                $headerMap['tahun'] = $index;
            } elseif (in_array($clean, ['bulan', 'month', 'bln', 'periode'])) {
                $headerMap['bulan'] = $index;
            } elseif (in_array($clean, ['upahbulan', 'upahperbulan', 'gajibulan', 'upahbulanan'])) {
                $headerMap['upah_bulan'] = $index;
            } elseif (in_array($clean, ['upahtahun', 'upahpertahun', 'gajitahun', 'upahtahunan'])) {
                $headerMap['upah_tahun'] = $index;
            } elseif (in_array($clean, ['bbmbulan', 'bbmperbulan', 'bahanbakarbulan', 'bensinbulan'])) {
                $headerMap['BBM_bulan'] = $index;
            } elseif (in_array($clean, ['bbmtahun', 'bbmpertahun', 'bahanbakartahun', 'bensintahun'])) {
                $headerMap['BBM_tahun'] = $index;
            } elseif (in_array($clean, ['preventifmaintenancebulan', 'prevmaintbulan', 'maintenancebulan', 'perawatanbulan'])) {
                $headerMap['preventif_maintenance_bulan'] = $index;
            } elseif (in_array($clean, ['preventifmaintenancetahun', 'prevmainttahun', 'maintenancetahun', 'perawatantahun'])) {
                $headerMap['preventif_maintenance_tahun'] = $index;
            } elseif (in_array($clean, ['biayalainnyabulan', 'biayalainbulan', 'lainnyabulan'])) {
                $headerMap['biaya_lainnya_bulan'] = $index;
            } elseif (in_array($clean, ['biayalainnyatahun', 'biayalaintahun', 'lainnyatahun'])) {
                $headerMap['biaya_lainnya_tahun'] = $index;
            } elseif (in_array($clean, ['kmawal', 'odometerawal', 'kilometerawal'])) {
                $headerMap['KM_awal'] = $index;
            } elseif (in_array($clean, ['kmakhir', 'odometerakhir', 'kilometerakhir'])) {
                $headerMap['KM_akhir'] = $index;
            } elseif (in_array($clean, ['kmbulan', 'kmperbulan', 'jarakbulan'])) {
                $headerMap['KM_bulan'] = $index;
            } elseif (in_array($clean, ['kmtahun', 'kmpertahun', 'jaraktahun'])) {
                $headerMap['KM_tahun'] = $index;
            }
        }

        // Helper for numbers
        $parseNumber = function ($val) {
            if ($val === null || $val === '') return 0.0;
            // Clean currency, symbols
            $val = trim((string)$val);
            $val = str_ireplace(['rp', 'idr', ' '], '', $val);
            // Handle 1.000.000,00 vs 1000000.00
            if (strpos($val, ',') !== false && strpos($val, '.') !== false) {
                // If comma comes last, it's decimal
                if (strrpos($val, ',') > strrpos($val, '.')) {
                    $val = str_replace('.', '', $val);
                    $val = str_replace(',', '.', $val);
                } else {
                    $val = str_replace(',', '', $val);
                }
            } elseif (strpos($val, ',') !== false) {
                // Semicolon/comma separated
                $parts = explode(',', $val);
                if (count($parts) == 2 && strlen($parts[1]) <= 2) {
                    $val = str_replace(',', '.', $val);
                } else {
                    $val = str_replace(',', '', $val);
                }
            }
            return (float) $val;
        };

        // Fallback context year & month
        $fallbackYear = (int) $request->get('year', 2026);
        $fallbackMonth = $request->get('period') === 'overall' ? 1 : (int)$request->get('period', 1);

        $insertedCount = 0;
        $updatedCount = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $noPol = isset($headerMap['nomor_polisi']) && isset($row[$headerMap['nomor_polisi']]) ? trim($row[$headerMap['nomor_polisi']]) : '';
            $kendaraan = isset($headerMap['kendaraan']) && isset($row[$headerMap['kendaraan']]) ? trim($row[$headerMap['kendaraan']]) : '';

            if (empty($noPol) && empty($kendaraan)) {
                continue;
            }

            $tahun = isset($headerMap['tahun']) && !empty($row[$headerMap['tahun']]) ? (int)$row[$headerMap['tahun']] : $fallbackYear;
            $bulan = isset($headerMap['bulan']) && !empty($row[$headerMap['bulan']]) ? (int)$row[$headerMap['bulan']] : $fallbackMonth;

            // Month text fallback (Jan -> 1, etc.)
            if ($bulan === 0 && isset($headerMap['bulan']) && !empty($row[$headerMap['bulan']])) {
                $mText = strtolower(trim($row[$headerMap['bulan']]));
                $monthTextMap = [
                    'jan' => 1, 'januari' => 1, 'feb' => 2, 'februari' => 2, 'mar' => 3, 'maret' => 3,
                    'apr' => 4, 'april' => 4, 'mei' => 5, 'may' => 5, 'jun' => 6, 'juni' => 6,
                    'jul' => 7, 'juli' => 7, 'aug' => 8, 'agustus' => 8, 'agu' => 8, 'sep' => 9, 'september' => 9,
                    'okt' => 10, 'oktober' => 10, 'oct' => 10, 'nov' => 11, 'november' => 11, 'des' => 12, 'desember' => 12, 'dec' => 12
                ];
                $bulan = $monthTextMap[$mText] ?? 1;
            }

            $upahBulan = isset($headerMap['upah_bulan']) ? $parseNumber($row[$headerMap['upah_bulan']]) : 0;
            $upahTahun = isset($headerMap['upah_tahun']) ? $parseNumber($row[$headerMap['upah_tahun']]) : ($upahBulan * 12);

            $bbmBulan = isset($headerMap['BBM_bulan']) ? $parseNumber($row[$headerMap['BBM_bulan']]) : 0;
            $bbmTahun = isset($headerMap['BBM_tahun']) ? $parseNumber($row[$headerMap['BBM_tahun']]) : ($bbmBulan * 12);

            $maintBulan = isset($headerMap['preventif_maintenance_bulan']) ? $parseNumber($row[$headerMap['preventif_maintenance_bulan']]) : 0;
            $maintTahun = isset($headerMap['preventif_maintenance_tahun']) ? $parseNumber($row[$headerMap['preventif_maintenance_tahun']]) : ($maintBulan * 12);

            $lainnyaBulan = isset($headerMap['biaya_lainnya_bulan']) ? $parseNumber($row[$headerMap['biaya_lainnya_bulan']]) : 0;
            $lainnyaTahun = isset($headerMap['biaya_lainnya_tahun']) ? $parseNumber($row[$headerMap['biaya_lainnya_tahun']]) : ($lainnyaBulan * 12);

            $totalBiayaBulan = $upahBulan + $bbmBulan + $maintBulan + $lainnyaBulan;
            $totalBiayaTahun = $upahTahun + $bbmTahun + $maintTahun + $lainnyaTahun;

            $kmAwal = isset($headerMap['KM_awal']) ? $parseNumber($row[$headerMap['KM_awal']]) : 0;
            $kmAkhir = isset($headerMap['KM_akhir']) ? $parseNumber($row[$headerMap['KM_akhir']]) : 0;
            $kmBulan = isset($headerMap['KM_bulan']) ? $parseNumber($row[$headerMap['KM_bulan']]) : ($kmAkhir > $kmAwal ? ($kmAkhir - $kmAwal) : 0);
            $kmTahun = isset($headerMap['KM_tahun']) ? $parseNumber($row[$headerMap['KM_tahun']]) : ($kmBulan * 12);

            // Upsert / update record for same vehicle, year and month
            $existing = Vra::where('nomor_polisi', $noPol)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
                ->first();

            $dataArray = [
                'nomor_polisi' => $noPol,
                'kendaraan' => $kendaraan ?: ($existing ? $existing->kendaraan : 'Kendaraan Operasional'),
                'tahun' => $tahun,
                'bulan' => $bulan,
                'upah_bulan' => $upahBulan,
                'upah_tahun' => $upahTahun,
                'BBM_bulan' => $bbmBulan,
                'BBM_tahun' => $bbmTahun,
                'preventif_maintenance_bulan' => $maintBulan,
                'preventif_maintenance_tahun' => $maintTahun,
                'biaya_lainnya_bulan' => $lainnyaBulan,
                'biaya_lainnya_tahun' => $lainnyaTahun,
                'total_biaya_bulan' => $totalBiayaBulan,
                'total_biaya_tahun' => $totalBiayaTahun,
                'KM_awal' => $kmAwal,
                'KM_akhir' => $kmAkhir,
                'KM_bulan' => $kmBulan,
                'KM_tahun' => $kmTahun,
            ];

            if ($existing) {
                $existing->update($dataArray);
                $updatedCount++;
            } else {
                Vra::create($dataArray);
                $insertedCount++;
            }
        }

        fclose($handle);

        $msg = "Import CSV berhasil: {$insertedCount} data baru ditambahkan";
        if ($updatedCount > 0) {
            $msg .= ", {$updatedCount} data diperbarui";
        }
        $msg .= ".";

        return redirect()->route('administrasi.vra.index', [
            'year' => $fallbackYear,
            'period' => $request->get('period', 'overall')
        ])->with('success', $msg);
    }
}
