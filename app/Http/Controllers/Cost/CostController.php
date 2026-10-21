<?php

namespace App\Http\Controllers\Cost;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Cost\BiayaRekruit;
use App\Models\Cost\CapexRab;
use App\Models\Cost\CapexRealisasi;
use App\Models\Cost\Cost;
use App\Models\Cost\Vra;
use App\Services\ExcelExporter;
use App\Traits\HandlesCsvImports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CostController extends Controller
{
    use HandlesCsvImports;

    public const MONTH_NAMES = [
        1 => 'Januari',   2 => 'Februari', 3 => 'Maret',
        4 => 'April',     5 => 'Mei',       6 => 'Juni',
        7 => 'Juli',      8 => 'Agustus',   9 => 'September',
        10 => 'Oktober',   11 => 'November',  12 => 'Desember',
    ];

    private function clearCostCache(): void
    {
        Cache::increment('cost_cache_version');
    }

    /**
     * Display the Cost management and monthly report page.
     */
    public function index(Request $request)
    {
        // 1. Dynamic Year Selection
        $selectedYear = (int) $request->get('year', 2026);
        $dbYears = Cost::select('tahun')->distinct()->pluck('tahun')->toArray();
        $baseYears = range(date('Y') - 3, date('Y') + 2);
        $years = array_values(array_unique(array_filter(array_merge($baseYears, $dbYears, [$selectedYear]))));
        sort($years);

        $selectedMonth = $request->get('month', 'all'); // 'all' or 1..12
        $selectedJenis = $request->get('jenis', 'all'); // 'all' or specific jenis_cost
        $search = trim($request->get('search', ''));

        // Optionally perform auto-aggregation sync with throttle
        $v = (int) Cache::get('cost_cache_version', 1);
        $syncKey = "cost_auto_sync_v{$v}_{$selectedYear}_" . (is_numeric($selectedMonth) ? (int) $selectedMonth : 'all');
        if (! Cache::has($syncKey)) {
            $this->syncAggregatedCosts($selectedYear, is_numeric($selectedMonth) ? (int) $selectedMonth : null);
            Cache::put($syncKey, true, 300);
        }

        // 2. Query Costs
        $query = Cost::where('tahun', $selectedYear);

        if (is_numeric($selectedMonth) && (int) $selectedMonth >= 1 && (int) $selectedMonth <= 12) {
            $query->where(function ($q) use ($selectedMonth) {
                $q->where('bulan', (int) $selectedMonth)
                    ->orWhereNull('bulan');
            });
        }

        if ($selectedJenis !== 'all') {
            $query->where('jenis_cost', $selectedJenis);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%")
                    ->orWhere('jenis_cost', 'like', "%{$search}%")
                    ->orWhere('GL_ACCOUNT', 'like', "%{$search}%");
            });
        }

        if ($selectedMonth === 'all') {
            // Ketika filter 'Semua (Tahunan)', agregasikan SUM budget dan SUM realisasi per unique account
            $allCosts = $query->select(
                DB::raw('MIN(cost_id) as cost_id'),
                'GL_ACCOUNT',
                'jenis_cost',
                'account_name',
                DB::raw('SUM(budget) as budget'),
                DB::raw('SUM(realisasi) as realisasi'),
                DB::raw('MAX(budget_tahunan) as budget_tahunan'),
                DB::raw('MAX(tahun) as tahun')
            )
                ->groupBy('jenis_cost', 'GL_ACCOUNT', 'account_name')
                ->orderBy(DB::raw('MIN(cost_id)'), 'asc')
                ->get();
        } else {
            $allCosts = $query->orderBy('cost_id', 'asc')->get();
        }

        // 3. Group by jenis_cost
        $grouped = [];
        foreach ($allCosts as $cost) {
            $key = $cost->jenis_cost ?: 'BIAYA UMUM LAINNYA';
            $grouped[$key][] = $cost;
        }

        // 4. Distinct Jenis Cost for Filter
        $jenisList = Cost::where('tahun', $selectedYear)->select('jenis_cost')->distinct()->pluck('jenis_cost')->filter()->values();

        // 5. Summary Totals
        $totalBudget = (float) $allCosts->sum('budget');
        $totalRealisasi = (float) $allCosts->sum('realisasi');
        $totalBudgetTahunan = (float) $allCosts->sum('budget_tahunan');

        // Kalkulasi Realisasi Kumulatif (YTD) untuk Sisa Anggaran / Variance
        if (is_numeric($selectedMonth) && (int) $selectedMonth >= 1 && (int) $selectedMonth <= 12) {
            $monthNum = (int) $selectedMonth;
            $ytdQuery = Cost::where('tahun', $selectedYear)
                ->where(function ($q) use ($monthNum) {
                    $q->where('bulan', '<=', $monthNum)
                        ->orWhereNull('bulan');
                });

            if ($selectedJenis !== 'all') {
                $ytdQuery->where('jenis_cost', $selectedJenis);
            }

            if (! empty($search)) {
                $ytdQuery->where(function ($q) use ($search) {
                    $q->where('account_name', 'like', "%{$search}%")
                        ->orWhere('jenis_cost', 'like', "%{$search}%")
                        ->orWhere('GL_ACCOUNT', 'like', "%{$search}%");
                });
            }

            $totalRealisasiYtd = (float) $ytdQuery->sum('realisasi');
        } else {
            $totalRealisasiYtd = $totalRealisasi;
        }

        $sisaBudget = $totalBudgetTahunan > 0
            ? ($totalBudgetTahunan - $totalRealisasiYtd)
            : ($totalBudget - $totalRealisasi);

        $pctRealisasi = $totalBudgetTahunan > 0
            ? round(($totalRealisasiYtd / $totalBudgetTahunan) * 100, 1)
            : ($totalBudget > 0 ? round(($totalRealisasi / $totalBudget) * 100, 1) : 0);

        $summary = compact(
            'totalBudget',
            'totalRealisasi',
            'totalRealisasiYtd',
            'totalBudgetTahunan',
            'sisaBudget',
            'pctRealisasi'
        );

        $monthNames = self::MONTH_NAMES;

        return view('Administrasi.Pages.Cost.cost', compact(
            'years',
            'selectedYear',
            'selectedMonth',
            'selectedJenis',
            'jenisList',
            'search',
            'grouped',
            'summary',
            'monthNames'
        ));
    }

    /**
     * Trigger manual sync from VRA, Capex, and Biaya Rekruit.
     */
    public function sync(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $month = $request->get('month');
        $monthNum = (is_numeric($month) && $month >= 1 && $month <= 12) ? (int) $month : null;

        $this->clearCostCache();
        $stats = $this->syncAggregatedCosts($year, $monthNum);

        $msg = "Sinkronisasi berhasil! Data VRA ({$stats['vra']} kendaraan), Capex (Kantor & Mess), dan Rekruit ({$stats['rekruit']}) berhasil diagregasikan ke tabel COST.";

        return redirect()
            ->route('administrasi.cost.index', ['year' => $year, 'month' => $month])
            ->with('success', $msg);
    }

    /**
     * Core Aggregation & UPSERT logic for VRA, Capex, and Biaya Rekruit.
     */
    public function syncAggregatedCosts(int $year, ?int $month = null): array
    {
        $stats = ['vra' => 0, 'capex' => 2, 'rekruit' => 0];

        // Determine all active months that exist in Cost, VRA, Capex, or BiayaRekruit
        $activeCostMonths = Cost::where('tahun', $year)->whereNotNull('bulan')->distinct()->pluck('bulan')->map(fn ($m) => (int) $m)->all();
        $monthsInVra = Vra::where('tahun', $year)->whereNotNull('bulan')->distinct()->pluck('bulan')->map(fn ($m) => (int) $m)->all();
        $monthsInCapex = CapexRealisasi::where('tahun', $year)->whereNotNull('tanggal')->selectRaw('MONTH(tanggal) as m')->distinct()->pluck('m')->map(fn ($m) => (int) $m)->all();
        $monthsInRekruit = BiayaRekruit::where(fn ($q) => $q->where('tahun', $year)->orWhereYear('tanggal', $year))->whereNotNull('tanggal')->selectRaw('MONTH(tanggal) as m')->distinct()->pluck('m')->map(fn ($m) => (int) $m)->all();

        $allActiveMonths = array_values(array_unique(array_filter(array_merge($activeCostMonths, $monthsInVra, $monthsInCapex, $monthsInRekruit))));
        sort($allActiveMonths);
        if (empty($allActiveMonths)) {
            $allActiveMonths = [6, 7, 8];
        }

        // If a specific month is requested, ensure all active months are kept in sync
        $monthsToSync = $allActiveMonths;
        if ($month !== null && ! in_array($month, $monthsToSync, true)) {
            $monthsToSync[] = $month;
            sort($monthsToSync);
        }

        $standardMap = [
            'Bus' => '1. Alokasi Bus',
            'Hilux' => '2. Alokasi Hilux',
            'Fortuner' => '3. Alokasi Fortuner',
        ];
        $capexMap = [
            'kantor' => 'Capex Kantor',
            'mess' => 'Capex Mess',
        ];

        // Helper template cache to copy baseline budget and GL info across months
        $templateCache = [];
        $getTemplate = function (string $accName, ?string $fallbackPrefix = null) use ($year, &$templateCache) {
            if (isset($templateCache[$accName])) {
                return $templateCache[$accName];
            }
            $row = Cost::where('tahun', $year)->where(function ($q) use ($accName, $fallbackPrefix) {
                $q->where('account_name', $accName);
                if ($fallbackPrefix) {
                    $q->orWhere('account_name', 'like', "%{$fallbackPrefix}%");
                }
            })->whereNotNull('bulan')->first();
            if (! $row) {
                $row = Cost::where('tahun', $year)->where(function ($q) use ($accName, $fallbackPrefix) {
                    $q->where('account_name', $accName);
                    if ($fallbackPrefix) {
                        $q->orWhere('account_name', 'like', "%{$fallbackPrefix}%");
                    }
                })->first();
            }
            $templateCache[$accName] = $row;

            return $row;
        };

        DB::transaction(function () use ($year, $monthsToSync, $standardMap, $capexMap, $getTemplate, &$stats) {
            // Remove orphan null-bulan rows for these aggregated accounts to avoid duplication
            Cost::where('tahun', $year)->whereNull('bulan')->where(function ($q) {
                $q->where('account_name', 'like', '1. Alokasi%')
                    ->orWhere('account_name', 'like', '2. Alokasi%')
                    ->orWhere('account_name', 'like', '3. Alokasi%')
                    ->orWhere('account_name', 'like', 'Capex%')
                    ->orWhere('account_name', 'REKRUT');
            })->delete();

            $totalVraCount = 0;
            $totalRekruitSum = 0;

            foreach ($monthsToSync as $m) {
                // =========================================================================
                // 1. VRA -> COST (Per Bulan)
                // =========================================================================
                $vraVehicles = Vra::where('tahun', $year)
                    ->where('bulan', $m)
                    ->select(
                        'kendaraan',
                        DB::raw('SUM(total_biaya_bulan) as total_biaya')
                    )
                    ->groupBy('kendaraan')
                    ->get();

                $vehicleTotals = [
                    'Bus' => 0.0,
                    'Hilux' => 0.0,
                    'Fortuner' => 0.0,
                ];
                $customVehicles = [];

                foreach ($vraVehicles as $v) {
                    $rawName = trim((string) $v->kendaraan);
                    $cleanName = match (true) {
                        str_contains(strtolower($rawName), 'hilux') => 'Hilux',
                        str_contains(strtolower($rawName), 'bus') => 'Bus',
                        str_contains(strtolower($rawName), 'fortuner') => 'Fortuner',
                        default => $rawName,
                    };

                    if (array_key_exists($cleanName, $vehicleTotals)) {
                        $vehicleTotals[$cleanName] += (float) $v->total_biaya;
                    } else {
                        $customVehicles[$cleanName] = ($customVehicles[$cleanName] ?? 0.0) + (float) $v->total_biaya;
                    }
                }

                $totalVraCount += count(array_filter($vehicleTotals)) + count($customVehicles);

                // Update standard VRA accounts (Bus, Hilux, Fortuner) for month $m
                foreach ($standardMap as $catKey => $accountName) {
                    $val = $vehicleTotals[$catKey];
                    $tpl = $getTemplate($accountName, "Alokasi {$catKey}");
                    $matchingRows = Cost::where('tahun', $year)->where('bulan', $m)
                        ->where(fn ($q) => $q->where('account_name', $accountName)->orWhere('account_name', 'like', "%Alokasi%{$catKey}%"))
                        ->orderBy('cost_id', 'asc')
                        ->get();

                    if ($matchingRows->isNotEmpty()) {
                        $masterRow = $matchingRows->first();
                        if ($matchingRows->count() > 1) {
                            foreach ($matchingRows->slice(1) as $dup) {
                                $dup->delete();
                            }
                        }
                        $masterRow->update([
                            'account_name' => $accountName,
                            'realisasi' => $val,
                            'jenis_cost' => 'BIAYA PERJALANAN DINAS',
                            'budget' => (float) $masterRow->budget > 0 ? $masterRow->budget : ($tpl ? $tpl->budget : 0),
                            'budget_tahunan' => (float) $masterRow->budget_tahunan > 0 ? $masterRow->budget_tahunan : ($tpl ? $tpl->budget_tahunan : 0),
                        ]);
                    } else {
                        Cost::create([
                            'tahun' => $year,
                            'bulan' => $m,
                            'GL_ACCOUNT' => $tpl ? $tpl->GL_ACCOUNT : '135.80.00005/850.91.05000/850.91.02002/850.91.04001/850.91.05002/850.91.05003/850.91.06001/850.91.06002',
                            'jenis_cost' => 'BIAYA PERJALANAN DINAS',
                            'account_name' => $accountName,
                            'budget' => $tpl ? $tpl->budget : 0,
                            'budget_tahunan' => $tpl ? $tpl->budget_tahunan : 0,
                            'realisasi' => $val,
                        ]);
                    }
                }

                // Update custom VRA accounts if any for month $m
                foreach ($customVehicles as $cName => $val) {
                    $accName = "Alokasi {$cName}";
                    $tpl = $getTemplate($accName);
                    $matchingRows = Cost::where('tahun', $year)->where('bulan', $m)->where('account_name', $accName)->get();
                    if ($matchingRows->isNotEmpty()) {
                        $masterRow = $matchingRows->first();
                        if ($matchingRows->count() > 1) {
                            foreach ($matchingRows->slice(1) as $dup) {
                                $dup->delete();
                            }
                        }
                        $masterRow->update(['realisasi' => $val]);
                    } else {
                        Cost::create([
                            'tahun' => $year,
                            'bulan' => $m,
                            'GL_ACCOUNT' => $tpl ? $tpl->GL_ACCOUNT : '135.80.00005/850.91.05000/850.91.02002/850.91.04001/850.91.05002/850.91.05003/850.91.06001/850.91.06002',
                            'jenis_cost' => 'BIAYA PERJALANAN DINAS',
                            'account_name' => $accName,
                            'budget' => $tpl ? $tpl->budget : 0,
                            'budget_tahunan' => $tpl ? $tpl->budget_tahunan : 0,
                            'realisasi' => $val,
                        ]);
                    }
                }

                // =========================================================================
                // 2. capex_rab & capex_realisasi -> COST (Per Bulan)
                // =========================================================================
                foreach ($capexMap as $lokasi => $accName) {
                    $rabTotal = (float) CapexRab::where('tahun', $year)->where('lokasi', $lokasi)->sum('total_rp');
                    $realTotal = (float) CapexRealisasi::where('tahun', $year)->where('lokasi', $lokasi)->whereMonth('tanggal', $m)->sum('total_rp');
                    $tpl = $getTemplate($accName, "Capex%{$lokasi}");
                    $matchingRows = Cost::where('tahun', $year)->where('bulan', $m)
                        ->where(fn ($q) => $q->where('account_name', $accName)->orWhere('account_name', 'like', "%Capex%{$lokasi}%"))
                        ->orderBy('cost_id', 'asc')
                        ->get();

                    $budTahunan = $rabTotal > 0 ? $rabTotal : ($tpl && (float) $tpl->budget_tahunan > 0 ? $tpl->budget_tahunan : 0);
                    $monthlyBud = $tpl && (float) $tpl->budget > 0 ? $tpl->budget : ($budTahunan > 0 ? round($budTahunan / 12, 2) : 0);

                    if ($matchingRows->isNotEmpty()) {
                        $masterRow = $matchingRows->first();
                        if ($matchingRows->count() > 1) {
                            foreach ($matchingRows->slice(1) as $dup) {
                                $dup->delete();
                            }
                        }
                        $masterRow->update([
                            'account_name' => $accName,
                            'realisasi' => $realTotal,
                            'jenis_cost' => 'CAPEX',
                            'budget' => (float) $masterRow->budget > 0 ? $masterRow->budget : $monthlyBud,
                            'budget_tahunan' => $budTahunan,
                        ]);
                    } else {
                        Cost::create([
                            'tahun' => $year,
                            'bulan' => $m,
                            'GL_ACCOUNT' => $tpl ? $tpl->GL_ACCOUNT : '135.80.00010',
                            'jenis_cost' => 'CAPEX',
                            'account_name' => $accName,
                            'budget' => $monthlyBud,
                            'budget_tahunan' => $budTahunan,
                            'realisasi' => $realTotal,
                        ]);
                    }
                }

                // =========================================================================
                // 3. Biaya_Rekruit -> COST (Per Bulan)
                // =========================================================================
                $realRekruit = (float) BiayaRekruit::where(fn ($q) => $q->where('tahun', $year)->orWhereYear('tanggal', $year))
                    ->whereMonth('tanggal', $m)
                    ->sum('total_biaya');
                $totalRekruitSum += $realRekruit;

                $tpl = $getTemplate('REKRUT');
                $matchingRows = Cost::where('tahun', $year)->where('bulan', $m)
                    ->where(fn ($q) => $q->where('account_name', 'REKRUT')->orWhere('account_name', 'like', '%REKRUT%'))
                    ->orderBy('cost_id', 'asc')
                    ->get();

                if ($matchingRows->isNotEmpty()) {
                    $masterRow = $matchingRows->first();
                    if ($matchingRows->count() > 1) {
                        foreach ($matchingRows->slice(1) as $dup) {
                            $dup->delete();
                        }
                    }
                    $masterRow->update([
                        'account_name' => 'REKRUT',
                        'realisasi' => $realRekruit,
                        'jenis_cost' => 'REKRUT',
                        'budget' => (float) $masterRow->budget > 0 ? $masterRow->budget : ($tpl ? $tpl->budget : 0),
                        'budget_tahunan' => (float) $masterRow->budget_tahunan > 0 ? $masterRow->budget_tahunan : ($tpl ? $tpl->budget_tahunan : 0),
                    ]);
                } else {
                    Cost::create([
                        'tahun' => $year,
                        'bulan' => $m,
                        'GL_ACCOUNT' => $tpl ? $tpl->GL_ACCOUNT : '135.80.00008',
                        'jenis_cost' => 'REKRUT',
                        'account_name' => 'REKRUT',
                        'budget' => $tpl ? $tpl->budget : 0,
                        'budget_tahunan' => $tpl ? $tpl->budget_tahunan : 0,
                        'realisasi' => $realRekruit,
                    ]);
                }
            }

            // Remove non-standard orphan Alokasi rows
            $standardVraAccounts = array_values($standardMap);
            Cost::where('tahun', $year)
                ->where('account_name', 'like', 'Alokasi %')
                ->whereNotIn('account_name', $standardVraAccounts)
                ->delete();

            $stats['vra'] = $totalVraCount;
            $stats['rekruit'] = $totalRekruitSum;
        });

        return $stats;
    }

    /**
     * Import CSV File into Cost Database.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,text|max:10240',
            'import_mode' => 'nullable|string|in:append,replace',
            'tahun' => 'nullable|integer|min:2000|max:2099',
            'bulan' => 'nullable',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $importMode = $request->get('import_mode', 'append');
        $defaultYear = (int) $request->get('tahun', date('Y'));

        $rawTargetMonth = $request->get('bulan');
        $targetMonth = (is_numeric($rawTargetMonth) && (int) $rawTargetMonth >= 1 && (int) $rawTargetMonth <= 12) ? (int) $rawTargetMonth : null;

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return back()->with('error', 'Gagal membuka file CSV yang diunggah.');
        }

        $delimiter = $this->detectCsvDelimiter($filePath);

        // Read header
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeaders) {
            fclose($handle);

            return back()->with('error', 'Format file CSV tidak valid atau kosong.');
        }

        // Clean & normalize headers
        $cleanHeaders = $this->cleanCsvHeaders($rawHeaders);

        // Map column indices
        $headerMap = [];
        foreach ($cleanHeaders as $idx => $col) {
            if (in_array($col, ['costid', 'id'])) {
                $headerMap['cost_id'] = $idx;
            }
            if (in_array($col, ['glaccount', 'gl', 'noaccount', 'kodeaccount'])) {
                $headerMap['GL_ACCOUNT'] = $idx;
            }
            if (in_array($col, ['jeniscost', 'kategori', 'jenispengeluaran', 'kelompok'])) {
                $headerMap['jenis_cost'] = $idx;
            }
            if (in_array($col, ['accountname', 'namaaccount', 'namaitem', 'uraian', 'keterangan'])) {
                $headerMap['account_name'] = $idx;
            }
            if (in_array($col, ['bulan', 'month', 'bln'])) {
                $headerMap['bulan'] = $idx;
            }
            if (in_array($col, ['tahun', 'year', 'thn'])) {
                $headerMap['tahun'] = $idx;
            }
            if (in_array($col, ['budget', 'anggaran', 'budgetbulan'])) {
                $headerMap['budget'] = $idx;
            }
            if (in_array($col, ['realisasi', 'aktual', 'actual'])) {
                $headerMap['realisasi'] = $idx;
            }
            if (in_array($col, ['budgettahunan', 'budgettahun', 'annualbudget', 'totalbudget'])) {
                $headerMap['budget_tahunan'] = $idx;
            }
        }

        // Handle Replace mode
        if ($importMode === 'replace') {
            $delQuery = Cost::where('tahun', $defaultYear);
            if ($targetMonth !== null) {
                $delQuery->where('bulan', $targetMonth);
            }
            $delQuery->delete();
        }

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row, fn ($v) => trim($v) !== ''))) {
                    continue;
                }

                $getVal = function ($key, $default = null) use ($row, $headerMap) {
                    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                        return trim($row[$headerMap[$key]]);
                    }

                    return $default;
                };

                $costId = $getVal('cost_id');
                $glAccount = $getVal('GL_ACCOUNT', null);
                $jenisCost = $getVal('jenis_cost', 'BIAYA UMUM LAINNYA');
                $accountName = $getVal('account_name', 'Item Biaya');
                $rawBulan = $getVal('bulan');
                $parsedBulan = $this->parseMonthValue($rawBulan);
                $bulan = $parsedBulan ?? $targetMonth;

                $tahun = (int) $getVal('tahun', $defaultYear);
                if ($tahun <= 0) {
                    $tahun = $defaultYear;
                }

                $budget = $this->cleanNumeric($getVal('budget', 0));
                $realisasi = $this->cleanNumeric($getVal('realisasi', 0));
                $budgetTahunan = $this->cleanNumeric($getVal('budget_tahunan', $budget * 12));

                $data = [
                    'GL_ACCOUNT' => $glAccount,
                    'jenis_cost' => $jenisCost,
                    'account_name' => $accountName,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'budget' => $budget,
                    'realisasi' => $realisasi,
                    'budget_tahunan' => $budgetTahunan,
                ];

                $upperAcc = strtoupper(trim($accountName));
                $isAutomated = (
                    (str_contains($upperAcc, 'ALOKASI') && (str_contains($upperAcc, 'BUS') || str_contains($upperAcc, 'HILUX') || str_contains($upperAcc, 'FORTUNER'))) ||
                    (str_contains($upperAcc, 'CAPEX') && (str_contains($upperAcc, 'KANTOR') || str_contains($upperAcc, 'MESS') || str_contains($upperAcc, 'MES'))) ||
                    ($upperAcc === 'REKRUT' || str_contains($upperAcc, 'BIAYA REKRUIT') || str_contains($upperAcc, 'BIAYA REKRUT'))
                );

                if ($isAutomated) {
                    $existing = $this->findExistingAutomatedCost($tahun, $accountName);
                    if ($existing) {
                        $existing->update([
                            'GL_ACCOUNT' => $glAccount ?: $existing->GL_ACCOUNT,
                            'jenis_cost' => $jenisCost ?: $existing->jenis_cost,
                            'account_name' => $accountName ?: $existing->account_name,
                            'budget' => $budget,
                            'budget_tahunan' => $budgetTahunan > 0 ? $budgetTahunan : ($budget * 12),
                            // Keep dynamic calculated realisasi if already non-zero, otherwise update
                            'realisasi' => (float) $existing->realisasi > 0 ? $existing->realisasi : $realisasi,
                        ]);
                        $updatedCount++;
                    } else {
                        Cost::create($data);
                        $importedCount++;
                    }
                } else {
                    // Match existing record by (tahun, bulan, account_name) to avoid duplicates
                    $existing = Cost::where('tahun', $tahun)
                        ->where('account_name', $accountName)
                        ->where(function ($q) use ($bulan) {
                            if ($bulan === null) {
                                $q->whereNull('bulan');
                            } else {
                                $q->where('bulan', $bulan);
                            }
                        })
                        ->first();

                    if (! $existing && $costId && is_numeric($costId)) {
                        $candidate = Cost::find((int) $costId);
                        if ($candidate && (int) $candidate->tahun === $tahun && ((int) $candidate->bulan === (int) $bulan || ($candidate->bulan === null && $bulan === null))) {
                            $existing = $candidate;
                        }
                    }

                    if ($existing) {
                        $existing->update($data);
                        $updatedCount++;
                    } else {
                        Cost::create($data);
                        $importedCount++;
                    }
                }
            }

            DB::commit();
            fclose($handle);

            // Re-sync automatic aggregation from source tables
            $this->syncAggregatedCosts($defaultYear, $targetMonth);
        } catch (\Exception $e) {
            DB::rollBack();
            if (is_resource($handle)) {
                fclose($handle);
            }

            return back()->with('error', 'Terjadi kesalahan saat import data: '.$e->getMessage());
        }

        $monthLabel = $targetMonth ? ' ('.(self::MONTH_NAMES[$targetMonth] ?? 'Bulan '.$targetMonth).')' : '';
        $msg = "Import Data Cost{$monthLabel} berhasil! ({$importedCount} data baru ditambahkan, {$updatedCount} data diperbarui).";

        ActivityLog::log(
            'Import',
            'Cost',
            'Mengimport data Cost dari file '.$file->getClientOriginalName()." ({$importedCount} baru, {$updatedCount} diperbarui)",
            ['filename' => $file->getClientOriginalName(), 'imported' => $importedCount, 'updated' => $updatedCount]
        );

        $this->clearCostCache();

        return redirect()
            ->route('administrasi.cost.index', ['year' => $defaultYear, 'month' => $redirectMonth])
            ->with('success', $msg);
    }

    /**
     * Download CSV Template for Cost import.
     */
    public function downloadTemplate()
    {
        $headers = ['cost_id', 'GL_ACCOUNT', 'jenis_cost', 'account_name', 'bulan', 'budget', 'realisasi', 'budget_tahunan'];
        $sampleRows = [
            [1, '135.80.00001/850.91.01000', 'BIAYA KARYAWAN', 'STAFF', '', 543966400.56, 374442898, 6527596806.71],
            [2, '135.80.00001/850.91.01000', 'BIAYA KARYAWAN', 'NON STAFF', '', 59114246.53, 108583636, 709370958.31],
            [7, '135.80.00007/850.91.07000', 'BIAYA PEMELIHARAAN', 'LISTRIK', '', 18702799.26, 18503477.51, 224433591.10],
            [16, '135.80.00005/850.91.05000', 'BIAYA PERJALANAN DINAS', '1. Alokasi Bus', '', 3619434.29, 8191473.50, 43433211.46],
            [17, '135.80.00005/850.91.05000', 'BIAYA PERJALANAN DINAS', '2. Alokasi Hilux', '', 6752197.52, 5912410.00, 81026370.21],
            [18, '135.80.00005/850.91.05000', 'BIAYA PERJALANAN DINAS', '3. Alokasi Fortuner', '', 5264783.63, 3771820.25, 63177403.57],
            [32, '135.80.00008', 'REKRUT', 'REKRUT', '', 24844277.67, 300000, 298131332],
            [38, '135.80.00010', 'CAPEX', 'Capex Kantor', '', 0, 0, 0],
            [39, '135.80.00010', 'CAPEX', 'Capex Mess', '', 0, 0, 0],
        ];

        return ExcelExporter::download('Template_Import_Cost.xlsx', $headers, $sampleRows, 'Template Cost');
    }

    /**
     * Store a new Cost entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'bulan' => 'nullable|integer|min:1|max:12',
            'GL_ACCOUNT' => 'nullable|string|max:255',
            'jenis_cost' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'budget' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
            'budget_tahunan' => 'nullable|numeric|min:0',
        ]);

        Cost::create($validated);

        return redirect()
            ->route('administrasi.cost.index', ['year' => $validated['tahun']])
            ->with('success', 'Data Cost berhasil ditambahkan.');
    }

    /**
     * Show single Cost item JSON for Edit modal.
     */
    public function show($id)
    {
        $record = Cost::findOrFail($id);

        return response()->json(['success' => true, 'data' => $record]);
    }

    /**
     * Update Cost item.
     */
    public function update(Request $request, $id)
    {
        $record = Cost::findOrFail($id);

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'bulan' => 'nullable|integer|min:1|max:12',
            'GL_ACCOUNT' => 'nullable|string|max:255',
            'jenis_cost' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'budget' => 'nullable|numeric|min:0',
            'realisasi' => 'nullable|numeric|min:0',
            'budget_tahunan' => 'nullable|numeric|min:0',
        ]);

        $record->update($validated);

        return redirect()
            ->route('administrasi.cost.index', ['year' => $record->tahun])
            ->with('success', 'Data Cost berhasil diperbarui.');
    }

    /**
     * Delete Cost item.
     */
    public function destroy($id)
    {
        $record = Cost::findOrFail($id);
        $tahun = $record->tahun;
        $record->delete();

        return redirect()
            ->route('administrasi.cost.index', ['year' => $tahun])
            ->with('success', 'Data Cost berhasil dihapus.');
    }

    /**
     * Delete all Cost records (for selected month or entire year).
     */
    public function destroyAll(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $month = $request->get('month', 'all');

        $query = Cost::where('tahun', $year);

        if (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12) {
            $query->where('bulan', (int) $month);
            $deleted = $query->delete();
            $monthName = self::MONTH_NAMES[(int) $month] ?? "Bulan {$month}";
            $msg = "Seluruh data Cost pada {$monthName} {$year} ({$deleted} data) berhasil dihapus.";
        } else {
            $deleted = $query->delete();
            $msg = "Seluruh data Cost tahun {$year} ({$deleted} data) berhasil dihapus.";
        }

        ActivityLog::log('Hapus', 'Cost', $msg, [
            'count' => $deleted,
            'year' => $year,
            'month' => $month,
        ]);

        $this->clearCostCache();

        return redirect()
            ->route('administrasi.cost.index', ['year' => $year, 'month' => $month])
            ->with('success', $msg);
    }

    /**
     * Export Cost data as CSV.
     */
    public function export(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $month = $request->get('month', 'all');

        $query = Cost::where('tahun', $year);
        if (is_numeric($month) && $month >= 1 && $month <= 12) {
            $query->where('bulan', (int) $month);
        }

        $records = $query->orderBy('jenis_cost')->orderBy('cost_id')->get();

        $headers = ['cost_id', 'GL_ACCOUNT', 'jenis_cost', 'account_name', 'bulan', 'budget', 'realisasi', 'budget_tahunan'];
        $dataRows = [];

        foreach ($records as $r) {
            $dataRows[] = [
                $r->cost_id,
                $r->GL_ACCOUNT,
                $r->jenis_cost,
                $r->account_name,
                $r->bulan,
                $r->budget,
                $r->realisasi,
                $r->budget_tahunan,
            ];
        }

        $filename = "Cost_Report_{$year}.xlsx";

        return ExcelExporter::download($filename, $headers, $dataRows, 'Cost Report');
    }

    /**
     * Find existing automated cost record by account_name pattern matching.
     */
    protected function findExistingAutomatedCost(int $tahun, string $accountName): ?Cost
    {
        $upper = strtoupper(trim($accountName));

        // VRA (Bus, Hilux, Fortuner)
        if (str_contains($upper, 'ALOKASI') && (str_contains($upper, 'BUS') || str_contains($upper, 'HILUX') || str_contains($upper, 'FORTUNER'))) {
            $keyword = match (true) {
                str_contains($upper, 'BUS') => 'Bus',
                str_contains($upper, 'HILUX') => 'Hilux',
                str_contains($upper, 'FORTUNER') => 'Fortuner',
                default => '',
            };

            return Cost::where('tahun', $tahun)
                ->where(function ($q) use ($accountName, $keyword) {
                    $q->where('account_name', $accountName)
                        ->orWhere('account_name', 'like', "%Alokasi%{$keyword}%");
                })
                ->orderBy('cost_id', 'asc')
                ->first();
        }

        // Capex (Kantor, Mess)
        if (str_contains($upper, 'CAPEX') && (str_contains($upper, 'KANTOR') || str_contains($upper, 'MESS') || str_contains($upper, 'MES'))) {
            $lokasi = (str_contains($upper, 'MESS') || str_contains($upper, 'MES')) ? 'mess' : 'kantor';

            return Cost::where('tahun', $tahun)
                ->where(function ($q) use ($accountName, $lokasi) {
                    $q->where('account_name', $accountName)
                        ->orWhere('account_name', 'like', "%Capex%{$lokasi}%");
                })
                ->orderBy('cost_id', 'asc')
                ->first();
        }

        // Rekruit
        if ($upper === 'REKRUT' || str_contains($upper, 'BIAYA REKRUIT') || str_contains($upper, 'BIAYA REKRUT') || str_contains($upper, 'REKRUTMEN')) {
            return Cost::where('tahun', $tahun)
                ->where(function ($q) {
                    $q->where('account_name', 'REKRUT')
                        ->orWhere('account_name', 'like', '%REKRUT%');
                })
                ->orderBy('cost_id', 'asc')
                ->first();
        }

        return null;
    }

    /**
     * Parse month name (Indonesian / English) or numeric string into integer 1-12.
     */
    protected function parseMonthValue(?string $val): ?int
    {
        if ($val === null) {
            return null;
        }

        $clean = trim($val);
        if ($clean === '') {
            return null;
        }

        if (is_numeric($clean)) {
            $num = (int) $clean;

            return ($num >= 1 && $num <= 12) ? $num : null;
        }

        $lower = strtolower($clean);
        $monthMap = [
            'januari' => 1, 'january' => 1, 'jan' => 1,
            'februari' => 2, 'february' => 2, 'feb' => 2,
            'maret' => 3, 'march' => 3, 'mar' => 3,
            'april' => 4, 'apr' => 4,
            'mei' => 5, 'may' => 5,
            'juni' => 6, 'june' => 6, 'jun' => 6,
            'juli' => 7, 'july' => 7, 'jul' => 7,
            'agustus' => 8, 'august' => 8, 'aug' => 8, 'agt' => 8,
            'september' => 9, 'sept' => 9, 'sep' => 9,
            'oktober' => 10, 'october' => 10, 'okt' => 10, 'oct' => 10,
            'november' => 11, 'nov' => 11,
            'desember' => 12, 'december' => 12, 'des' => 12, 'dec' => 12,
        ];

        return $monthMap[$lower] ?? null;
    }
}
