<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\Pembelian;
use App\Models\Inventaris\StockOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventarisDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Fetch available years strictly from StockOpname (only years with real stock opname data)
        $soYears = StockOpname::selectRaw('YEAR(tanggal_stock_opname) as yr')
            ->whereNotNull('tanggal_stock_opname')
            ->distinct()
            ->pluck('yr')
            ->map(fn ($y) => (int) $y)
            ->filter()
            ->sort()
            ->values()
            ->all();

        if (empty($soYears)) {
            $soYears = [(int) date('Y')];
        }

        $defaultYear = end($soYears); // Default ke tahun terbaru yang memiliki Stock Opname
        $selectedYear = (int) $request->query('year', $defaultYear);
        if (! in_array($selectedYear, $soYears, true)) {
            $selectedYear = $defaultYear;
        }

        // 2. Fetch available months with Stock Opname in the selected year
        $monthNamesId = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $rawMonths = StockOpname::selectRaw('MONTH(tanggal_stock_opname) as mo, max(tanggal_stock_opname) as max_date, count(*) as c')
            ->whereYear('tanggal_stock_opname', $selectedYear)
            ->whereNotNull('tanggal_stock_opname')
            ->groupBy('mo')
            ->orderBy('mo')
            ->get();

        $availableMonths = [];
        $latestMonthInYear = null;
        foreach ($rawMonths as $rm) {
            $mNum = (int) $rm->mo;
            $availableMonths[$mNum] = [
                'num' => $mNum,
                'name' => $monthNamesId[$mNum] ?? "Bulan $mNum",
                'max_date' => $rm->max_date,
                'is_latest' => false,
            ];
            $latestMonthInYear = $mNum;
        }

        if ($latestMonthInYear && isset($availableMonths[$latestMonthInYear])) {
            $availableMonths[$latestMonthInYear]['is_latest'] = true;
        }

        $selectedMonth = $request->query('month') !== null
            ? (int) $request->query('month')
            : ($latestMonthInYear ?: (int) date('n'));

        if (! empty($availableMonths) && ! isset($availableMonths[$selectedMonth])) {
            $selectedMonth = $latestMonthInYear;
        }

        // Cari tanggal sesi Stock Opname terakhir yang dilakukan pada tahun & bulan tersebut
        $latestSessionDate = StockOpname::whereYear('tanggal_stock_opname', $selectedYear)
            ->when($selectedMonth, fn ($q) => $q->whereMonth('tanggal_stock_opname', $selectedMonth))
            ->max('tanggal_stock_opname');

        $latestSessionDateFormatted = $latestSessionDate
            ? Carbon::parse($latestSessionDate)->locale('id')->translatedFormat('d F Y')
            : null;

        // Ambil seluruh record SO pada tanggal sesi terakhir tersebut
        $sessionRecords = $latestSessionDate
            ? StockOpname::whereDate('tanggal_stock_opname', $latestSessionDate)->get()
            : collect();

        // Agregasi per barang pada sesi tanggal terakhir tersebut
        $soByBarang = [];
        foreach ($sessionRecords as $so) {
            $bId = $so->barang_id;
            if (! isset($soByBarang[$bId])) {
                $soByBarang[$bId] = [
                    'baik' => 0,
                    'rusak' => 0,
                    'grand_total' => 0,
                    'lokasi_spesifik' => $so->lokasi_spesifik,
                    'keterangan' => $so->keterangan,
                ];
            }
            $soByBarang[$bId]['baik'] += (int) $so->jumlah_baik;
            $soByBarang[$bId]['rusak'] += (int) $so->jumlah_rusak;
            $soByBarang[$bId]['grand_total'] += (int) $so->grand_total;
        }

        // 3. KPI CARD METRICS
        $totalJenisBarang = Barang::count();
        $totalBarangGudang = Barang::where('jenis_lokasi', 'Gudang')->orWhere('lokasi', 'Gudang')->count();
        $totalBarangKantor = Barang::where('jenis_lokasi', 'Kantor')->orWhere('lokasi', 'Kantor')->count();

        $allBarang = Barang::all();
        $totalAsetUnit = 0;
        $totalKondisiBaik = 0;
        $totalKondisiRusak = 0;

        $gudangBaik = 0;
        $gudangRusak = 0;
        $kantorBaik = 0;
        $kantorRusak = 0;

        $damagedItems = [];

        foreach ($allBarang as $barang) {
            $isGudang = in_array(ucfirst(strtolower($barang->jenis_lokasi ?: $barang->lokasi)), ['Gudang']);

            if (isset($soByBarang[$barang->id])) {
                $stok = $soByBarang[$barang->id]['grand_total'];
                $baik = $soByBarang[$barang->id]['baik'];
                $rusak = $soByBarang[$barang->id]['rusak'];
                $lokSpesifik = $soByBarang[$barang->id]['lokasi_spesifik'] ?: ($isGudang ? 'Gudang' : 'Kantor');
                $keterangan = $soByBarang[$barang->id]['keterangan'] ?: '-';
            } else {
                $totalPembelian = Pembelian::where('barang_id', $barang->id)
                    ->whereYear('tanggal_pembelian', '<=', $selectedYear)
                    ->sum('jumlah');

                $stok = $totalPembelian;
                $baik = $stok;
                $rusak = 0;
                $lokSpesifik = $isGudang ? 'Gudang' : 'Kantor';
                $keterangan = '-';
            }

            $totalAsetUnit += $stok;
            $totalKondisiBaik += $baik;
            $totalKondisiRusak += $rusak;

            if ($isGudang) {
                $gudangBaik += $baik;
                $gudangRusak += $rusak;
            } else {
                $kantorBaik += $baik;
                $kantorRusak += $rusak;
            }

            if ($rusak > 0) {
                $damagedItems[] = [
                    'nama_asset' => $barang->nama_asset,
                    'satuan' => $barang->satuan,
                    'jenis_lokasi' => $barang->jenis_lokasi ?: ($isGudang ? 'Gudang' : 'Kantor'),
                    'lokasi_spesifik' => $lokSpesifik,
                    'jumlah_rusak' => $rusak,
                    'jumlah_baik' => $baik,
                    'grand_total' => $stok,
                    'keterangan' => $keterangan,
                ];
            }
        }

        // Sort damaged items by jumlah_rusak DESC
        usort($damagedItems, fn ($a, $b) => $b['jumlah_rusak'] <=> $a['jumlah_rusak']);
        $topDamagedItems = $damagedItems;

        $healthRatio = $totalAsetUnit > 0 ? round(($totalKondisiBaik / $totalAsetUnit) * 100, 1) : 0;
        $damageRatio = $totalAsetUnit > 0 ? round(($totalKondisiRusak / $totalAsetUnit) * 100, 1) : 0;

        $totalPembelianYtd = Pembelian::whereYear('tanggal_pembelian', $selectedYear)->sum('jumlah');

        // 4. CHART 1 DATA: Condition by Location (Gudang vs Kantor)
        $chartLocationCondition = [
            'categories' => ['Gudang', 'Kantor'],
            'baik' => [$gudangBaik, $kantorBaik],
            'rusak' => [$gudangRusak, $kantorRusak],
        ];

        // 5. CHART 2 DATA: Room Breakdown (Kantor Rooms) dari sesi SO terakhir
        $roomMap = [];
        foreach ($sessionRecords as $so) {
            $roomName = trim((string) ($so->lokasi_spesifik ?: 'Kantor (Umum)'));
            if ($roomName === '') {
                $roomName = 'Kantor (Umum)';
            }

            if (! isset($roomMap[$roomName])) {
                $roomMap[$roomName] = (object) [
                    'lokasi_spesifik' => $roomName,
                    'total_baik' => 0,
                    'total_rusak' => 0,
                    'total_aset' => 0,
                ];
            }

            $roomMap[$roomName]->total_baik += (int) $so->jumlah_baik;
            $roomMap[$roomName]->total_rusak += (int) $so->jumlah_rusak;
            $roomMap[$roomName]->total_aset += (int) $so->grand_total;
        }

        $roomSummaryList = array_values($roomMap);
        usort($roomSummaryList, fn ($a, $b) => $b->total_aset <=> $a->total_aset);
        $roomSummary = collect($roomSummaryList);

        $chartRoom = [
            'rooms' => $roomSummary->pluck('lokasi_spesifik')->toArray(),
            'baik' => $roomSummary->pluck('total_baik')->map(fn ($v) => (int) $v)->toArray(),
            'rusak' => $roomSummary->pluck('total_rusak')->map(fn ($v) => (int) $v)->toArray(),
        ];

        // 6. CHART 3 DATA: Purchase Trend per Year
        $purchaseYears = Pembelian::selectRaw('YEAR(tanggal_pembelian) as yr')
            ->whereNotNull('tanggal_pembelian')
            ->distinct()
            ->pluck('yr')
            ->map(fn ($y) => (int) $y)
            ->filter()
            ->sort()
            ->values()
            ->all();

        $purchaseTrendData = [];
        foreach ($purchaseYears as $yr) {
            $qty = Pembelian::whereYear('tanggal_pembelian', $yr)->sum('jumlah');
            $purchaseTrendData[] = (int) $qty;
        }

        $chartPurchaseTrend = [
            'years' => $purchaseYears,
            'quantities' => $purchaseTrendData,
        ];

        return view('Administrasi.Pages.Inventaris.dashboard', [
            'selectedYear' => $selectedYear,
            'years' => $soYears,
            'selectedMonth' => $selectedMonth,
            'availableMonths' => $availableMonths,
            'latestSessionDateFormatted' => $latestSessionDateFormatted,
            'totalJenisBarang' => $totalJenisBarang,
            'totalBarangGudang' => $totalBarangGudang,
            'totalBarangKantor' => $totalBarangKantor,
            'totalAsetUnit' => $totalAsetUnit,
            'totalKondisiBaik' => $totalKondisiBaik,
            'totalKondisiRusak' => $totalKondisiRusak,
            'healthRatio' => $healthRatio,
            'damageRatio' => $damageRatio,
            'totalPembelianYtd' => $totalPembelianYtd,
            'chartLocationCondition' => $chartLocationCondition,
            'chartRoom' => $chartRoom,
            'chartPurchaseTrend' => $chartPurchaseTrend,
            'topDamagedItems' => $topDamagedItems,
            'roomSummary' => $roomSummary,
            'activeTab' => 'dashboard',
        ]);
    }
}
