<?php

namespace App\Http\Controllers\Cost;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Cost\BiayaRekruit;
use App\Models\Cost\CapexRab;
use App\Models\Cost\CapexRealisasi;
use App\Models\Cost\Cost;
use App\Models\Cost\Vra;
use App\Traits\HandlesCsvImports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostController extends Controller
{
    use HandlesCsvImports;

    public const MONTH_NAMES = [
        1  => 'Januari',   2  => 'Februari', 3  => 'Maret',
        4  => 'April',     5  => 'Mei',       6  => 'Juni',
        7  => 'Juli',      8  => 'Agustus',   9  => 'September',
        10 => 'Oktober',   11 => 'November',  12 => 'Desember',
    ];

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

        // Optionally perform auto-aggregation sync on page load
        $this->syncAggregatedCosts($selectedYear, is_numeric($selectedMonth) ? (int)$selectedMonth : null);

        // 2. Query Costs
        $query = Cost::where('tahun', $selectedYear);

        if (is_numeric($selectedMonth) && (int)$selectedMonth >= 1 && (int)$selectedMonth <= 12) {
            $query->where(function ($q) use ($selectedMonth) {
                $q->where('bulan', (int)$selectedMonth)
                  ->orWhereNull('bulan');
            });
        }

        if ($selectedJenis !== 'all') {
            $query->where('jenis_cost', $selectedJenis);
        }

        if (!empty($search)) {
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
        $totalBudget        = (float) $allCosts->sum('budget');
        $totalRealisasi     = (float) $allCosts->sum('realisasi');
        $totalBudgetTahunan = (float) $allCosts->sum('budget_tahunan');
        $sisaBudget         = $totalBudgetTahunan > 0 ? ($totalBudgetTahunan - $totalRealisasi) : ($totalBudget - $totalRealisasi);
        $pctRealisasi       = $totalBudgetTahunan > 0 ? round(($totalRealisasi / $totalBudgetTahunan) * 100, 1) : ($totalBudget > 0 ? round(($totalRealisasi / $totalBudget) * 100, 1) : 0);

        $summary = compact(
            'totalBudget',
            'totalRealisasi',
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
        $monthNum = (is_numeric($month) && $month >= 1 && $month <= 12) ? (int)$month : null;

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

        DB::transaction(function () use ($year, $month, &$stats) {
            // =========================================================================
            // 1. VRA -> COST
            // =========================================================================
            $vraQuery = Vra::where('tahun', $year);
            if ($month !== null) {
                $vraQuery->where('bulan', $month);
            }

            $vraVehicles = $vraQuery->select(
                'kendaraan',
                DB::raw('SUM(total_biaya_bulan) as total_biaya'),
                DB::raw('SUM(upah) as total_upah'),
                DB::raw('SUM(BBM) as total_bbm'),
                DB::raw('SUM(preventif_maintenance) as total_maint'),
                DB::raw('SUM(biaya_lainnya) as total_lain')
            )
            ->groupBy('kendaraan')
            ->get();

            // Standard accounts map
            $standardMap = [
                'Bus'      => '1. Alokasi Bus',
                'Hilux'    => '2. Alokasi Hilux',
                'Fortuner' => '3. Alokasi Fortuner',
            ];

            // Initialize standard vehicle totals to 0
            $vehicleTotals = [
                'Bus'      => 0.0,
                'Hilux'    => 0.0,
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

            $stats['vra'] = count(array_filter($vehicleTotals)) + count($customVehicles);
            $syncedVraAccountNames = [];

            // Update standard VRA accounts (Bus, Hilux, Fortuner)
            foreach ($standardMap as $catKey => $accountName) {
                $totalVal = $vehicleTotals[$catKey];
                $existingRow = Cost::where('tahun', $year)
                    ->where(function ($q) use ($accountName, $catKey) {
                        $q->where('account_name', $accountName)
                          ->orWhere('account_name', 'like', "%Alokasi%{$catKey}%");
                    })
                    ->first();

                if ($existingRow) {
                    $existingRow->update([
                        'account_name' => $accountName,
                        'realisasi'    => $totalVal,
                        'jenis_cost'   => 'BIAYA PERJALANAN DINAS',
                        'bulan'        => null,
                    ]);
                    $syncedVraAccountNames[] = $existingRow->account_name;
                } else {
                    $newCost = Cost::create([
                        'tahun'          => $year,
                        'bulan'          => null,
                        'GL_ACCOUNT'     => '135.80.00005/850.91.05000/850.91.02002/850.91.04001/850.91.05002/850.91.05003/850.91.06001/850.91.06002',
                        'jenis_cost'     => 'BIAYA PERJALANAN DINAS',
                        'account_name'   => $accountName,
                        'budget'         => 0,
                        'realisasi'      => $totalVal,
                        'budget_tahunan' => 0,
                    ]);
                    $syncedVraAccountNames[] = $newCost->account_name;
                }
            }

            // Update custom VRA accounts if any
            foreach ($customVehicles as $cName => $totalVal) {
                $existingRow = Cost::where('tahun', $year)
                    ->where('account_name', "Alokasi {$cName}")
                    ->first();

                if ($existingRow) {
                    $existingRow->update([
                        'realisasi'  => $totalVal,
                        'jenis_cost' => 'BIAYA PERJALANAN DINAS',
                        'bulan'      => null,
                    ]);
                    $syncedVraAccountNames[] = $existingRow->account_name;
                } else {
                    $newCost = Cost::create([
                        'tahun'          => $year,
                        'bulan'          => null,
                        'GL_ACCOUNT'     => '135.80.00005/850.91.05000/850.91.02002/850.91.04001/850.91.05002/850.91.05003/850.91.06001/850.91.06002',
                        'jenis_cost'     => 'BIAYA PERJALANAN DINAS',
                        'account_name'   => "Alokasi {$cName}",
                        'budget'         => 0,
                        'realisasi'      => $totalVal,
                        'budget_tahunan' => 0,
                    ]);
                    $syncedVraAccountNames[] = $newCost->account_name;
                }
            }

            // Remove non-standard orphan Alokasi rows (e.g., Alokasi Ford Ranger..., Alokasi Mobil Test)
            $standardVraAccounts = array_values($standardMap);
            Cost::where('tahun', $year)
                ->where('account_name', 'like', 'Alokasi %')
                ->whereNotIn('account_name', array_merge($standardVraAccounts, $syncedVraAccountNames))
                ->delete();

            // =========================================================================
            // 2. capex_rab -> COST (budget_tahunan) & 3. capex_realisasi -> COST (realisasi)
            // =========================================================================
            $capexMap = [
                'kantor' => 'Capex Kantor',
                'mess'   => 'Capex Mess',
            ];

            foreach ($capexMap as $lokasi => $accName) {
                $rabTotal  = (float) CapexRab::where('tahun', $year)->where('lokasi', $lokasi)->sum('total_rp');

                $realQuery = CapexRealisasi::where('tahun', $year)->where('lokasi', $lokasi);
                if ($month !== null) {
                    $realQuery->whereMonth('tanggal', $month);
                }
                $realTotal = (float) $realQuery->sum('total_rp');

                $capexRow = Cost::where('tahun', $year)
                    ->where(function ($q) use ($accName, $lokasi) {
                        $q->where('account_name', $accName)
                          ->orWhere('account_name', 'like', "%Capex%{$lokasi}%");
                    })
                    ->first();

                if ($capexRow) {
                    $capexRow->update([
                        'budget_tahunan' => $rabTotal,
                        'realisasi'      => $realTotal,
                        'jenis_cost'     => 'CAPEX',
                        'bulan'          => null,
                    ]);
                } else {
                    Cost::create([
                        'tahun'          => $year,
                        'bulan'          => null,
                        'GL_ACCOUNT'     => '135.80.00010',
                        'jenis_cost'     => 'CAPEX',
                        'account_name'   => $accName,
                        'budget'         => 0,
                        'realisasi'      => $realTotal,
                        'budget_tahunan' => $rabTotal,
                    ]);
                }
            }

            // =========================================================================
            // 4. Biaya_Rekruit -> COST (realisasi)
            // =========================================================================
            $rekruitQuery = BiayaRekruit::query()->where(function ($q) use ($year) {
                $q->where('tahun', $year)
                  ->orWhereYear('tanggal', $year);
            });
            if ($month !== null) {
                $rekruitQuery->whereMonth('tanggal', $month);
            }
            $totalRekruit = (float) $rekruitQuery->sum('total_biaya');
            $stats['rekruit'] = $totalRekruit;

            $rekruitRow = Cost::where('tahun', $year)
                ->where(function ($q) {
                    $q->where('account_name', 'REKRUT')
                      ->orWhere('account_name', 'like', '%REKRUT%');
                })
                ->first();

            if ($rekruitRow) {
                $rekruitRow->update([
                    'realisasi'  => $totalRekruit,
                    'jenis_cost' => 'REKRUT',
                    'bulan'      => null,
                ]);
            } else {
                Cost::create([
                    'tahun'          => $year,
                    'bulan'          => null,
                    'GL_ACCOUNT'     => '135.80.00008',
                    'jenis_cost'     => 'REKRUT',
                    'account_name'   => 'REKRUT',
                    'budget'         => 0,
                    'realisasi'      => $totalRekruit,
                    'budget_tahunan' => 0,
                ]);
            }
        });

        return $stats;
    }

    /**
     * Import CSV File into Cost Database.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file'    => 'required|file|mimes:csv,txt,text|max:10240',
            'import_mode' => 'nullable|string|in:append,replace',
            'tahun'       => 'nullable|integer|min:2000|max:2099',
            'bulan'       => 'nullable',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $importMode = $request->get('import_mode', 'append');
        $defaultYear = (int) $request->get('tahun', date('Y'));
        
        $rawTargetMonth = $request->get('bulan');
        $targetMonth = (is_numeric($rawTargetMonth) && (int)$rawTargetMonth >= 1 && (int)$rawTargetMonth <= 12) ? (int)$rawTargetMonth : null;

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return back()->with('error', 'Gagal membuka file CSV yang diunggah.');
        }

        $delimiter = $this->detectCsvDelimiter($filePath);

        // Read header
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (!$rawHeaders) {
            fclose($handle);
            return back()->with('error', 'Format file CSV tidak valid atau kosong.');
        }

        // Clean & normalize headers
        $cleanHeaders = $this->cleanCsvHeaders($rawHeaders);

        // Map column indices
        $headerMap = [];
        foreach ($cleanHeaders as $idx => $col) {
            if (in_array($col, ['costid', 'id'])) $headerMap['cost_id'] = $idx;
            if (in_array($col, ['glaccount', 'gl', 'noaccount', 'kodeaccount'])) $headerMap['GL_ACCOUNT'] = $idx;
            if (in_array($col, ['jeniscost', 'kategori', 'jenispengeluaran', 'kelompok'])) $headerMap['jenis_cost'] = $idx;
            if (in_array($col, ['accountname', 'namaaccount', 'namaitem', 'uraian', 'keterangan'])) $headerMap['account_name'] = $idx;
            if (in_array($col, ['bulan', 'month', 'bln'])) $headerMap['bulan'] = $idx;
            if (in_array($col, ['tahun', 'year', 'thn'])) $headerMap['tahun'] = $idx;
            if (in_array($col, ['budget', 'anggaran', 'budgetbulan'])) $headerMap['budget'] = $idx;
            if (in_array($col, ['realisasi', 'aktual', 'actual'])) $headerMap['realisasi'] = $idx;
            if (in_array($col, ['budgettahunan', 'budgettahun', 'annualbudget', 'totalbudget'])) $headerMap['budget_tahunan'] = $idx;
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
        $updatedCount  = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                    continue;
                }

                $getVal = function ($key, $default = null) use ($row, $headerMap) {
                    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                        return trim($row[$headerMap[$key]]);
                    }
                    return $default;
                };

                $costId        = $getVal('cost_id');
                $glAccount     = $getVal('GL_ACCOUNT', null);
                $jenisCost     = $getVal('jenis_cost', 'BIAYA UMUM LAINNYA');
                $accountName   = $getVal('account_name', 'Item Biaya');
                $rawBulan      = $getVal('bulan');
                if ($rawBulan !== null && $rawBulan !== '' && is_numeric($rawBulan) && (int)$rawBulan >= 1 && (int)$rawBulan <= 12) {
                    $bulan = (int)$rawBulan;
                } else {
                    $bulan = $targetMonth;
                }

                $tahun         = (int) $getVal('tahun', $defaultYear);
                if ($tahun <= 0) $tahun = $defaultYear;

                $budget        = $this->cleanNumeric($getVal('budget', 0));
                $realisasi     = $this->cleanNumeric($getVal('realisasi', 0));
                $budgetTahunan = $this->cleanNumeric($getVal('budget_tahunan', $budget * 12));

                $data = [
                    'GL_ACCOUNT'     => $glAccount,
                    'jenis_cost'     => $jenisCost,
                    'account_name'   => $accountName,
                    'bulan'          => $bulan,
                    'tahun'          => $tahun,
                    'budget'         => $budget,
                    'realisasi'      => $realisasi,
                    'budget_tahunan' => $budgetTahunan,
                ];

                // Upsert strategy: by cost_id if present, else by (tahun, account_name, bulan)
                if ($costId && is_numeric($costId)) {
                    $existing = Cost::find((int) $costId);
                    if ($existing) {
                        $existing->update($data);
                        $updatedCount++;
                    } else {
                        $data['cost_id'] = (int) $costId;
                        Cost::create($data);
                        $importedCount++;
                    }
                } else {
                    $existing = Cost::where('tahun', $tahun)
                        ->where('account_name', $accountName)
                        ->where(function ($q) use ($bulan) {
                            if ($bulan === null) $q->whereNull('bulan');
                            else $q->where('bulan', $bulan);
                        })
                        ->first();

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
            return back()->with('error', 'Terjadi kesalahan saat import data: ' . $e->getMessage());
        }

        $monthLabel = $targetMonth ? ' (' . (self::MONTH_NAMES[$targetMonth] ?? 'Bulan ' . $targetMonth) . ')' : '';
        $msg = "Import Data Cost{$monthLabel} berhasil! ({$importedCount} data baru ditambahkan, {$updatedCount} data diperbarui).";

        ActivityLog::log(
            'Import',
            'Cost',
            "Mengimport data Cost dari file " . $file->getClientOriginalName() . " ({$importedCount} baru, {$updatedCount} diperbarui)",
            ['filename' => $file->getClientOriginalName(), 'imported' => $importedCount, 'updated' => $updatedCount]
        );

        $redirectMonth = $targetMonth ?? $request->get('current_month', 'all');

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

        return \App\Services\ExcelExporter::download('Template_Import_Cost.xlsx', $headers, $sampleRows, 'Template Cost');
    }

    /**
     * Store a new Cost entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun'          => 'required|integer|min:2000|max:2099',
            'bulan'          => 'nullable|integer|min:1|max:12',
            'GL_ACCOUNT'     => 'nullable|string|max:255',
            'jenis_cost'     => 'required|string|max:100',
            'account_name'   => 'required|string|max:150',
            'budget'         => 'nullable|numeric|min:0',
            'realisasi'      => 'nullable|numeric|min:0',
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
            'tahun'          => 'required|integer|min:2000|max:2099',
            'bulan'          => 'nullable|integer|min:1|max:12',
            'GL_ACCOUNT'     => 'nullable|string|max:255',
            'jenis_cost'     => 'required|string|max:100',
            'account_name'   => 'required|string|max:150',
            'budget'         => 'nullable|numeric|min:0',
            'realisasi'      => 'nullable|numeric|min:0',
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

        if (is_numeric($month) && (int)$month >= 1 && (int)$month <= 12) {
            $query->where('bulan', (int)$month);
            $deleted = $query->delete();
            $monthName = self::MONTH_NAMES[(int)$month] ?? "Bulan {$month}";
            $msg = "Seluruh data Cost pada {$monthName} {$year} ({$deleted} data) berhasil dihapus.";
        } else {
            $deleted = $query->delete();
            $msg = "Seluruh data Cost tahun {$year} ({$deleted} data) berhasil dihapus.";
        }

        ActivityLog::log('Hapus', 'Cost', $msg, [
            'count' => $deleted,
            'year'  => $year,
            'month' => $month,
        ]);

        return redirect()
            ->route('administrasi.cost.index', ['year' => $year, 'month' => $month])
            ->with('success', $msg);
    }

    /**
     * Export Cost data as CSV.
     */
    public function export(Request $request)
    {
        $year  = (int) $request->get('year', 2026);
        $month = $request->get('month', 'all');

        $query = Cost::where('tahun', $year);
        if (is_numeric($month) && $month >= 1 && $month <= 12) {
            $query->where('bulan', (int)$month);
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
        return \App\Services\ExcelExporter::download($filename, $headers, $dataRows, 'Cost Report');
    }
}
