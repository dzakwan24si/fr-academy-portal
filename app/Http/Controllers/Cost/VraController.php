<?php

namespace App\Http\Controllers\Cost;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Cost\Vra;
use App\Services\ExcelExporter;
use App\Traits\HandlesCsvImports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VraController extends Controller
{
    use HandlesCsvImports;

    private const MONTHS = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Dynamic Years & Selected Year
        $selectedYear = (int) $request->get('year', 2026);
        $dbYears = Vra::select('tahun')->distinct()->pluck('tahun')->toArray();
        $baseYears = range(date('Y') - 3, date('Y') + 2);
        $years = array_values(array_unique(array_filter(array_merge($baseYears, $dbYears, [$selectedYear]))));
        sort($years);

        $selectedPeriod = $request->get('period', 'overall'); // 'overall' or '1'..'12'
        $search = trim($request->get('search', ''));
        $months = self::MONTHS;

        // 3. Query Data based on Period Context
        if ($selectedPeriod === 'overall') {
            // Overall View: Group by vehicle for annual summary
            $query = Vra::where('tahun', $selectedYear);

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_polisi', 'like', "%{$search}%")
                        ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            // Get grouped vehicle annual summary
            $records = $query->select(
                'nomor_polisi',
                'kendaraan',
                DB::raw('MAX(vra_id) as vra_id'),
                DB::raw('COUNT(bulan) as total_bulan_tercatat'),
                DB::raw('SUM(upah) as upah'),
                DB::raw('SUM(BBM) as BBM'),
                DB::raw('MAX(BBM_tahun) as BBM_tahun'),
                DB::raw('SUM(preventif_maintenance) as preventif_maintenance'),
                DB::raw('SUM(biaya_lainnya) as biaya_lainnya'),
                DB::raw('SUM(total_biaya_bulan) as total_biaya_bulan'),
                DB::raw('MIN(KM_awal) as KM_awal'),
                DB::raw('MAX(KM_akhir) as KM_akhir'),
                DB::raw('SUM(KM) as KM'),
                DB::raw('SUM(BBM_liter) as BBM_liter'),
                DB::raw('MAX(budget_tahun) as budget_tahun')
            )
                ->groupBy('nomor_polisi', 'kendaraan')
                ->orderBy('nomor_polisi', 'asc')
                ->paginate(15)
                ->withQueryString();

            // Calculate computed metrics per aggregated row
            $records->getCollection()->transform(function ($item) {
                $item->BBM_KM_liter = $item->BBM_liter > 0 ? ($item->KM / $item->BBM_liter) : 0;
                $item->VRA = $item->KM > 0 ? ($item->total_biaya_bulan / $item->KM) : 0;

                return $item;
            });

            // Calculate overall totals
            $allAnnualRecords = Vra::where('tahun', $selectedYear);
            if (! empty($search)) {
                $allAnnualRecords->where(function ($q) use ($search) {
                    $q->where('nomor_polisi', 'like', "%{$search}%")
                        ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            $allGrouped = $allAnnualRecords->select(
                'nomor_polisi',
                'kendaraan',
                DB::raw('SUM(upah) as upah'),
                DB::raw('SUM(BBM) as BBM'),
                DB::raw('MAX(BBM_tahun) as BBM_tahun'),
                DB::raw('SUM(preventif_maintenance) as preventif_maintenance'),
                DB::raw('SUM(biaya_lainnya) as biaya_lainnya'),
                DB::raw('SUM(total_biaya_bulan) as total_biaya_bulan'),
                DB::raw('SUM(KM) as KM'),
                DB::raw('SUM(BBM_liter) as BBM_liter'),
                DB::raw('MAX(budget_tahun) as budget_tahun')
            )
                ->groupBy('nomor_polisi', 'kendaraan')
                ->get();

            $totals = $this->calculateTotals($allGrouped);

        } else {
            // Monthly View: Filter by specific month & year
            $monthNum = (int) $selectedPeriod;
            $query = Vra::where('tahun', $selectedYear)->where('bulan', $monthNum);

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nomor_polisi', 'like', "%{$search}%")
                        ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            $records = $query->orderBy('nomor_polisi', 'asc')
                ->orderBy('vra_id', 'asc')
                ->paginate(15)
                ->withQueryString();

            // Calculate monthly totals
            $allMonthlyRecords = Vra::where('tahun', $selectedYear)
                ->where('bulan', $monthNum);

            if (! empty($search)) {
                $allMonthlyRecords->where(function ($q) use ($search) {
                    $q->where('nomor_polisi', 'like', "%{$search}%")
                        ->orWhere('kendaraan', 'like', "%{$search}%");
                });
            }

            $totals = $this->calculateTotals($allMonthlyRecords->get());
        }

        return view('Administrasi.Pages.Cost.vra', compact(
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
            'upah' => 'nullable|numeric|min:0',
            'BBM' => 'nullable|numeric|min:0',
            'BBM_tahun' => 'nullable|numeric|min:0',
            'preventif_maintenance' => 'nullable|numeric|min:0',
            'biaya_lainnya' => 'nullable|numeric|min:0',
            'KM_awal' => 'nullable|numeric|min:0',
            'KM_akhir' => 'nullable|numeric|min:0',
            'KM' => 'nullable|numeric|min:0',
            'BBM_liter' => 'nullable|numeric|min:0',
            'budget_tahun' => 'nullable|numeric|min:0',
        ]);

        // Default numeric zero
        foreach ([
            'upah', 'BBM', 'BBM_tahun', 'preventif_maintenance',
            'biaya_lainnya', 'KM_awal', 'KM_akhir', 'KM', 'BBM_liter', 'budget_tahun',
        ] as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field] !== '' ? (float) $validated[$field] : 0;
        }

        // Auto calculate Total Biaya Bulan = upah + BBM + preventif_maintenance + biaya_lainnya
        $validated['total_biaya_bulan'] = $validated['upah']
            + $validated['BBM']
            + $validated['preventif_maintenance']
            + $validated['biaya_lainnya'];

        // Auto calculate KM
        if ($validated['KM_akhir'] > $validated['KM_awal'] && $validated['KM'] == 0) {
            $validated['KM'] = $validated['KM_akhir'] - $validated['KM_awal'];
        } elseif ($validated['KM'] > 0 && $validated['KM_akhir'] == 0) {
            $validated['KM_akhir'] = $validated['KM_awal'] + $validated['KM'];
        }

        // Auto calculate BBM_KM_liter = KM / BBM_liter
        $validated['BBM_KM_liter'] = $validated['BBM_liter'] > 0 ? ($validated['KM'] / $validated['BBM_liter']) : 0;

        // Auto calculate VRA = total_biaya_bulan / KM
        $validated['VRA'] = $validated['KM'] > 0 ? ($validated['total_biaya_bulan'] / $validated['KM']) : 0;

        $vra = Vra::create($validated);

        (new CostController)->syncAggregatedCosts((int) $validated['tahun']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data VRA berhasil ditambahkan.',
                'data' => $vra,
            ]);
        }

        return redirect()->route('administrasi.vra.index', [
            'year' => $validated['tahun'],
            'period' => $validated['bulan'],
        ])->with('success', 'Data VRA untuk kendaraan '.$validated['nomor_polisi'].' berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $vra = Vra::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $vra,
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
            'upah' => 'nullable|numeric|min:0',
            'BBM' => 'nullable|numeric|min:0',
            'BBM_tahun' => 'nullable|numeric|min:0',
            'preventif_maintenance' => 'nullable|numeric|min:0',
            'biaya_lainnya' => 'nullable|numeric|min:0',
            'KM_awal' => 'nullable|numeric|min:0',
            'KM_akhir' => 'nullable|numeric|min:0',
            'KM' => 'nullable|numeric|min:0',
            'BBM_liter' => 'nullable|numeric|min:0',
            'budget_tahun' => 'nullable|numeric|min:0',
        ]);

        // Default numeric zero
        foreach ([
            'upah', 'BBM', 'BBM_tahun', 'preventif_maintenance',
            'biaya_lainnya', 'KM_awal', 'KM_akhir', 'KM', 'BBM_liter', 'budget_tahun',
        ] as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field] !== '' ? (float) $validated[$field] : 0;
        }

        // Auto calculate total biaya bulan
        $validated['total_biaya_bulan'] = $validated['upah']
            + $validated['BBM']
            + $validated['preventif_maintenance']
            + $validated['biaya_lainnya'];

        // Auto calculate KM
        if ($validated['KM_akhir'] > $validated['KM_awal'] && $validated['KM'] == 0) {
            $validated['KM'] = $validated['KM_akhir'] - $validated['KM_awal'];
        } elseif ($validated['KM'] > 0 && $validated['KM_akhir'] == 0) {
            $validated['KM_akhir'] = $validated['KM_awal'] + $validated['KM'];
        }

        // Auto calculate BBM_KM_liter = KM / BBM_liter
        $validated['BBM_KM_liter'] = $validated['BBM_liter'] > 0 ? ($validated['KM'] / $validated['BBM_liter']) : 0;

        // Auto calculate VRA = total_biaya_bulan / KM
        $validated['VRA'] = $validated['KM'] > 0 ? ($validated['total_biaya_bulan'] / $validated['KM']) : 0;

        $vra->update($validated);

        (new CostController)->syncAggregatedCosts((int) $vra->tahun);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data VRA berhasil diperbarui.',
                'data' => $vra,
            ]);
        }

        $period = $request->get('period', $vra->bulan);

        return redirect()->route('administrasi.vra.index', [
            'year' => $vra->tahun,
            'period' => $period,
        ])->with('success', 'Data VRA untuk kendaraan '.$vra->nomor_polisi.' berhasil diperbarui.');
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
                'message' => "Data VRA {$noPol} berhasil dihapus.",
            ]);
        }

        return redirect()->route('administrasi.vra.index', [
            'year' => $year,
            'period' => $period,
        ])->with('success', "Data VRA kendaraan {$noPol} berhasil dihapus.");
    }

    /**
     * Add a new year
     */
    public function storeYear(Request $request)
    {
        $request->validate([
            'new_year' => 'required|integer|min:2000|max:2099',
        ]);

        $newYear = (int) $request->new_year;

        return redirect()->route('administrasi.vra.index', [
            'year' => $newYear,
            'period' => 'overall',
        ])->with('success', "Tahun {$newYear} berhasil dipilih sebagai periode aktif.");
    }

    /**
     * Export Report
     */
    public function export(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $period = $request->get('period', 'overall');

        $headers = [
            'vra_id',
            'nomor_polisi',
            'kendaraan',
            'bulan',
            'upah',
            'BBM',
            'BBM_tahun',
            'preventif_maintenance',
            'biaya_lainnya',
            'total_biaya_bulan',
            'KM_awal',
            'KM_akhir',
            'KM',
            'BBM_liter',
            'BBM_KM_liter',
            'VRA',
            'budget_tahun',
        ];

        $query = Vra::where('tahun', $year);
        if ($period !== 'overall') {
            $query->where('bulan', (int) $period);
        }

        $records = $query->orderBy('bulan', 'asc')->orderBy('nomor_polisi', 'asc')->get();
        $dataRows = [];

        foreach ($records as $row) {
            $dataRows[] = [
                $row->vra_id,
                $row->nomor_polisi,
                $row->kendaraan,
                $row->bulan,
                $row->upah,
                $row->BBM,
                $row->BBM_tahun,
                $row->preventif_maintenance,
                $row->biaya_lainnya,
                $row->total_biaya_bulan,
                $row->KM_awal,
                $row->KM_akhir,
                $row->KM,
                $row->BBM_liter,
                $row->BBM_KM_liter,
                $row->VRA,
                $row->budget_tahun,
            ];
        }

        $filename = "VRA_Report_{$year}_".($period === 'overall' ? 'Overall' : "Bulan_{$period}").'.xlsx';

        return ExcelExporter::download($filename, $headers, $dataRows, 'Report VRA');
    }

    /**
     * Download CSV Template for Import
     */
    public function downloadTemplate()
    {
        $headers = [
            'vra_id', 'nomor_polisi', 'kendaraan', 'bulan', 'upah', 'BBM', 'BBM_tahun',
            'preventif_maintenance', 'biaya_lainnya', 'total_biaya_bulan', 'KM_awal',
            'KM_akhir', 'KM', 'BBM_liter', 'BBM_KM_liter', 'VRA', 'budget_tahun',
        ];
        $sampleRows = [
            [1, 'Hilux BM 8177 QG', 'Mobil Pool/Hilux', 5, 4723210, 1122000, 3222000, 2504820, 6419500, 14769530, 58135, 59661, 1526, 165, 9.25, 9679, 81026370],
            [2, 'Mobil BM 7835 AZ', 'Bus Training', 5, 2577604, 849900, 3128600, 0, 2443981.88, 5871485.88, 138907, 139658, 751, 124.97, 6.01, 7818, 43433211],
        ];

        return ExcelExporter::download('Template_Import_VRA.xlsx', $headers, $sampleRows, 'Template VRA');
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

        // Open file
        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return back()->with('error', 'Gagal membuka file CSV yang diunggah.');
        }

        $delimiter = $this->detectCsvDelimiter($filePath);
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeaders) {
            fclose($handle);

            return back()->with('error', 'Format file CSV tidak valid atau file kosong.');
        }

        $cleanHeaders = $this->cleanCsvHeaders($rawHeaders);

        // Normalize header mapping
        $headerMap = [];
        foreach ($cleanHeaders as $index => $clean) {
            if (in_array($clean, ['vraid', 'id', 'idvra'])) {
                $headerMap['vra_id'] = $index;
            } elseif (in_array($clean, ['nomorpolisi', 'nopolisi', 'nopol', 'plat', 'platnomor'])) {
                $headerMap['nomor_polisi'] = $index;
            } elseif (in_array($clean, ['kendaraan', 'namakendaraan', 'jeniskendaraan', 'unit', 'mobil', 'tipekendaraan'])) {
                $headerMap['kendaraan'] = $index;
            } elseif (in_array($clean, ['bulan', 'month', 'bln', 'periode'])) {
                $headerMap['bulan'] = $index;
            } elseif (in_array($clean, ['tahun', 'year', 'thn'])) {
                $headerMap['tahun'] = $index;
            } elseif (in_array($clean, ['upah', 'upahbulan', 'biayaupah', 'gaji'])) {
                $headerMap['upah'] = $index;
            } elseif (in_array($clean, ['bbmtahun', 'bbmpertahun', 'bahanbakartahun'])) {
                $headerMap['BBM_tahun'] = $index;
            } elseif (in_array($clean, ['bbm', 'bbmbulan', 'bahanbakar', 'biayabbm'])) {
                $headerMap['BBM'] = $index;
            } elseif (in_array($clean, ['preventifmaintenance', 'prevmaint', 'preventivemaintenance', 'maintenance', 'perawatan'])) {
                $headerMap['preventif_maintenance'] = $index;
            } elseif (in_array($clean, ['biayalainnya', 'biayalain', 'lainnya'])) {
                $headerMap['biaya_lainnya'] = $index;
            } elseif (in_array($clean, ['totalbiayabulan', 'totalbiaya', 'total'])) {
                $headerMap['total_biaya_bulan'] = $index;
            } elseif (in_array($clean, ['kmawal', 'odometerawal', 'kilometerawal'])) {
                $headerMap['KM_awal'] = $index;
            } elseif (in_array($clean, ['kmakhir', 'odometerakhir', 'kilometerakhir'])) {
                $headerMap['KM_akhir'] = $index;
            } elseif (in_array($clean, ['km', 'kmbulan', 'totalkm', 'jarak'])) {
                $headerMap['KM'] = $index;
            } elseif (in_array($clean, ['bbmliter', 'literbbm', 'liter'])) {
                $headerMap['BBM_liter'] = $index;
            } elseif (in_array($clean, ['bbmkmliter', 'kmliter', 'kml', 'rasiobbm'])) {
                $headerMap['BBM_KM_liter'] = $index;
            } elseif (in_array($clean, ['vra', 'biayaperkm', 'costperkm'])) {
                $headerMap['VRA'] = $index;
            } elseif (in_array($clean, ['budgettahun', 'budgetkmtahun', 'budgetkm', 'budget'])) {
                $headerMap['budget_tahun'] = $index;
            }
        }

        $fallbackYear = (int) $request->get('year', 2026);
        $fallbackMonth = $request->get('period') === 'overall' ? 5 : (int) $request->get('period', 5);

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

            $tahun = isset($headerMap['tahun']) && ! empty($row[$headerMap['tahun']]) ? (int) $row[$headerMap['tahun']] : $fallbackYear;
            $bulan = isset($headerMap['bulan']) && ! empty($row[$headerMap['bulan']]) ? (int) $row[$headerMap['bulan']] : $fallbackMonth;

            // Month text fallback (Jan -> 1, Mei -> 5, etc.)
            if ($bulan === 0 && isset($headerMap['bulan']) && ! empty($row[$headerMap['bulan']])) {
                $mText = strtolower(trim($row[$headerMap['bulan']]));
                $monthTextMap = [
                    'jan' => 1, 'januari' => 1, 'feb' => 2, 'februari' => 2, 'mar' => 3, 'maret' => 3,
                    'apr' => 4, 'april' => 4, 'mei' => 5, 'may' => 5, 'jun' => 6, 'juni' => 6,
                    'jul' => 7, 'juli' => 7, 'aug' => 8, 'agustus' => 8, 'agu' => 8, 'sep' => 9, 'september' => 9,
                    'okt' => 10, 'oktober' => 10, 'oct' => 10, 'nov' => 11, 'november' => 11, 'des' => 12, 'desember' => 12, 'dec' => 12,
                ];
                $bulan = $monthTextMap[$mText] ?? 1;
            }

            $upah = isset($headerMap['upah']) ? $this->cleanNumeric($row[$headerMap['upah']], 0) : 0;
            $bbm = isset($headerMap['BBM']) ? $this->cleanNumeric($row[$headerMap['BBM']], 0) : 0;
            $bbmTahun = isset($headerMap['BBM_tahun']) ? $this->cleanNumeric($row[$headerMap['BBM_tahun']], $bbm * 12) : ($bbm * 12);
            $maint = isset($headerMap['preventif_maintenance']) ? $this->cleanNumeric($row[$headerMap['preventif_maintenance']], 0) : 0;
            $lainnya = isset($headerMap['biaya_lainnya']) ? $this->cleanNumeric($row[$headerMap['biaya_lainnya']], 0) : 0;

            $totalBiayaBulan = isset($headerMap['total_biaya_bulan']) && $this->cleanNumeric($row[$headerMap['total_biaya_bulan']], 0) > 0
                ? $this->cleanNumeric($row[$headerMap['total_biaya_bulan']], 0)
                : ($upah + $bbm + $maint + $lainnya);

            $kmAwal = isset($headerMap['KM_awal']) ? $this->cleanNumeric($row[$headerMap['KM_awal']], 0) : 0;
            $kmAkhir = isset($headerMap['KM_akhir']) ? $this->cleanNumeric($row[$headerMap['KM_akhir']], 0) : 0;
            $km = isset($headerMap['KM']) && $this->cleanNumeric($row[$headerMap['KM']], 0) > 0
                ? $this->cleanNumeric($row[$headerMap['KM']], 0)
                : ($kmAkhir > $kmAwal ? ($kmAkhir - $kmAwal) : 0);

            if ($kmAkhir == 0 && $kmAwal > 0 && $km > 0) {
                $kmAkhir = $kmAwal + $km;
            }

            $bbmLiter = isset($headerMap['BBM_liter']) ? $this->cleanNumeric($row[$headerMap['BBM_liter']], 0) : 0;

            $bbmKmLiter = isset($headerMap['BBM_KM_liter']) && $this->cleanNumeric($row[$headerMap['BBM_KM_liter']], 0) > 0
                ? $this->cleanNumeric($row[$headerMap['BBM_KM_liter']], 0)
                : ($bbmLiter > 0 ? ($km / $bbmLiter) : 0);

            $vraVal = isset($headerMap['VRA']) && $this->cleanNumeric($row[$headerMap['VRA']], 0) > 0
                ? $this->cleanNumeric($row[$headerMap['VRA']], 0)
                : ($km > 0 ? ($totalBiayaBulan / $km) : 0);

            $budgetTahun = isset($headerMap['budget_tahun']) ? $this->cleanNumeric($row[$headerMap['budget_tahun']], 0) : 0;

            // Check existing by vra_id if present, or by (nomor_polisi, tahun, bulan)
            $existing = null;
            if (isset($headerMap['vra_id']) && ! empty($row[$headerMap['vra_id']])) {
                $id = (int) $row[$headerMap['vra_id']];
                $existing = Vra::find($id);
            }

            if (! $existing) {
                $existing = Vra::where('nomor_polisi', $noPol)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->first();
            }

            $dataArray = [
                'nomor_polisi' => $noPol,
                'kendaraan' => $kendaraan ?: ($existing ? $existing->kendaraan : 'Kendaraan Operasional'),
                'tahun' => $tahun,
                'bulan' => $bulan,
                'upah' => $upah,
                'BBM' => $bbm,
                'BBM_tahun' => $bbmTahun,
                'preventif_maintenance' => $maint,
                'biaya_lainnya' => $lainnya,
                'total_biaya_bulan' => $totalBiayaBulan,
                'KM_awal' => $kmAwal,
                'KM_akhir' => $kmAkhir,
                'KM' => $km,
                'BBM_liter' => $bbmLiter,
                'BBM_KM_liter' => $bbmKmLiter,
                'VRA' => $vraVal,
                'budget_tahun' => $budgetTahun,
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
        $msg .= '.';

        ActivityLog::log(
            'Import',
            'VRA',
            'Mengimport data VRA dari file '.$file->getClientOriginalName()." ({$insertedCount} baru, {$updatedCount} diperbarui)",
            ['filename' => $file->getClientOriginalName(), 'inserted' => $insertedCount, 'updated' => $updatedCount]
        );

        return redirect()->route('administrasi.vra.index', [
            'year' => $fallbackYear,
            'period' => $request->get('period', 'overall'),
        ])->with('success', $msg);
    }

    /**
     * Delete all VRA records.
     */
    public function destroyAll(Request $request)
    {
        $year = $request->get('year');
        $period = $request->get('period', 'overall');

        $query = Vra::query();

        if ($year && is_numeric($year)) {
            $query->where('tahun', (int) $year);
        }

        if ($period !== 'overall' && is_numeric($period) && (int) $period >= 1 && (int) $period <= 12) {
            $query->where('bulan', (int) $period);
            $deleted = $query->delete();
            $monthName = self::MONTHS[(int) $period] ?? "Bulan {$period}";
            $msg = "Seluruh data VRA pada {$monthName} {$year} ({$deleted} data) berhasil dihapus.";
        } else {
            $deleted = $query->delete();
            $msg = $year ? "Seluruh data VRA tahun {$year} ({$deleted} data) berhasil dihapus." : "Seluruh data VRA ({$deleted} data) berhasil dihapus.";
        }

        ActivityLog::log('Hapus', 'VRA', $msg, [
            'count' => $deleted,
            'year' => $year,
            'period' => $period,
        ]);

        return redirect()->route('administrasi.vra.index', [
            'year' => $year ?: 2026,
            'period' => $period,
        ])->with('success', $msg);
    }

    /**
     * Helper to compute aggregate totals for VRA records.
     */
    protected function calculateTotals($rows): array
    {
        $totalKM = $rows->sum('KM');
        $totalBiaya = $rows->sum('total_biaya_bulan');
        $totalBBM_liter = $rows->sum('BBM_liter');

        return [
            'count' => $rows->count(),
            'upah' => $rows->sum('upah'),
            'bbm' => $rows->sum('BBM'),
            'bbm_tahun' => $rows->sum('BBM_tahun'),
            'maintenance' => $rows->sum('preventif_maintenance'),
            'lainnya' => $rows->sum('biaya_lainnya'),
            'total_biaya' => $totalBiaya,
            'km' => $totalKM,
            'bbm_liter' => $totalBBM_liter,
            'bbm_km_liter' => $totalBBM_liter > 0 ? ($totalKM / $totalBBM_liter) : 0,
            'vra' => $totalKM > 0 ? ($totalBiaya / $totalKM) : 0,
            'budget_tahun' => $rows->sum('budget_tahun'),
        ];
    }
}
