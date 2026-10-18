<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\Pembelian;
use App\Models\Inventaris\StockOpname;
use Illuminate\Http\Request;

class InventarisDashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->query('year', date('Y'));

        // Fetch all available years from Pembelian & StockOpname
        $pembelianYears = Pembelian::selectRaw('YEAR(tanggal_pembelian) as yr')->whereNotNull('tanggal_pembelian')->pluck('yr')->map(fn ($y) => (int) $y)->toArray();
        $soYears = StockOpname::selectRaw('YEAR(tanggal_stock_opname) as yr')->whereNotNull('tanggal_stock_opname')->pluck('yr')->map(fn ($y) => (int) $y)->toArray();
        $defaultYears = [2020, 2021, 2022, 2023, 2024, 2025, 2026, (int) date('Y')];
        $allYears = array_values(array_unique(array_merge($defaultYears, $pembelianYears, $soYears)));
        sort($allYears);

        // 1. KPI CARD METRICS
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
            $latestSO = StockOpname::where('barang_id', $barang->id)
                ->whereYear('tanggal_stock_opname', '<=', $selectedYear)
                ->orderByDesc('tanggal_stock_opname')
                ->first();

            $totalPembelian = Pembelian::where('barang_id', $barang->id)
                ->whereYear('tanggal_pembelian', '<=', $selectedYear)
                ->sum('jumlah');

            $stok = $latestSO ? $latestSO->grand_total : $totalPembelian;
            $baik = $latestSO ? $latestSO->jumlah_baik : $stok;
            $rusak = $latestSO ? $latestSO->jumlah_rusak : 0;

            $totalAsetUnit += $stok;
            $totalKondisiBaik += $baik;
            $totalKondisiRusak += $rusak;

            $isGudang = in_array(ucfirst(strtolower($barang->jenis_lokasi ?: $barang->lokasi)), ['Gudang']);
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
                    'jenis_lokasi' => $barang->jenis_lokasi ?: 'Kantor',
                    'lokasi_spesifik' => $latestSO ? $latestSO->lokasi_spesifik : '-',
                    'jumlah_rusak' => $rusak,
                    'jumlah_baik' => $baik,
                    'grand_total' => $stok,
                    'keterangan' => $latestSO ? $latestSO->keterangan : '-',
                ];
            }
        }

        // Sort damaged items by jumlah_rusak DESC (Seluruh barang yang perlu maintenance / rusak)
        usort($damagedItems, fn ($a, $b) => $b['jumlah_rusak'] <=> $a['jumlah_rusak']);
        $topDamagedItems = $damagedItems;

        $healthRatio = $totalAsetUnit > 0 ? round(($totalKondisiBaik / $totalAsetUnit) * 100, 1) : 0;
        $damageRatio = $totalAsetUnit > 0 ? round(($totalKondisiRusak / $totalAsetUnit) * 100, 1) : 0;

        $totalPembelianYtd = Pembelian::whereYear('tanggal_pembelian', $selectedYear)->sum('jumlah');

        // 2. CHART 1 DATA: Condition by Location (Gudang vs Kantor)
        $chartLocationCondition = [
            'categories' => ['Gudang', 'Kantor'],
            'baik' => [$gudangBaik, $kantorBaik],
            'rusak' => [$gudangRusak, $kantorRusak],
        ];

        // 3. CHART 2 DATA: Room Breakdown (Kantor Rooms)
        $kantorBarang = Barang::where(function ($q) {
            $q->where('jenis_lokasi', 'Kantor')->orWhere('lokasi', 'Kantor');
        })->get();

        $roomMap = [];
        foreach ($kantorBarang as $b) {
            $latestSO = StockOpname::where('barang_id', $b->id)
                ->whereYear('tanggal_stock_opname', '<=', $selectedYear)
                ->orderByDesc('tanggal_stock_opname')
                ->first();

            $totalPembelian = Pembelian::where('barang_id', $b->id)
                ->whereYear('tanggal_pembelian', '<=', $selectedYear)
                ->sum('jumlah');

            $stok = $latestSO ? $latestSO->grand_total : $totalPembelian;
            $baik = $latestSO ? $latestSO->jumlah_baik : $stok;
            $rusak = $latestSO ? $latestSO->jumlah_rusak : 0;

            $roomName = ($latestSO && ! empty(trim($latestSO->lokasi_spesifik)))
                ? trim($latestSO->lokasi_spesifik)
                : (($b->lokasi && ! in_array($b->lokasi, ['Kantor', 'Gudang'])) ? $b->lokasi : 'Kantor (Umum)');

            if (! isset($roomMap[$roomName])) {
                $roomMap[$roomName] = (object) [
                    'lokasi_spesifik' => $roomName,
                    'total_baik' => 0,
                    'total_rusak' => 0,
                    'total_aset' => 0,
                ];
            }

            $roomMap[$roomName]->total_baik += $baik;
            $roomMap[$roomName]->total_rusak += $rusak;
            $roomMap[$roomName]->total_aset += $stok;
        }

        $roomSummaryList = array_values($roomMap);
        usort($roomSummaryList, fn ($a, $b) => $b->total_aset <=> $a->total_aset);
        $roomSummary = collect($roomSummaryList);

        $chartRoom = [
            'rooms' => $roomSummary->pluck('lokasi_spesifik')->toArray(),
            'baik' => $roomSummary->pluck('total_baik')->map(fn ($v) => (int) $v)->toArray(),
            'rusak' => $roomSummary->pluck('total_rusak')->map(fn ($v) => (int) $v)->toArray(),
        ];

        // 4. CHART 3 DATA: Purchase Trend per Year
        $purchaseTrendData = [];
        foreach ($allYears as $yr) {
            $qty = Pembelian::whereYear('tanggal_pembelian', $yr)->sum('jumlah');
            $purchaseTrendData[] = (int) $qty;
        }

        $chartPurchaseTrend = [
            'years' => $allYears,
            'quantities' => $purchaseTrendData,
        ];

        return view('Administrasi.Pages.Inventaris.dashboard', [
            'selectedYear' => $selectedYear,
            'years' => $allYears,
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
