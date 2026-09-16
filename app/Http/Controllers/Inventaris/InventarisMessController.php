<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\Pembelian;
use App\Models\Inventaris\StockOpname;
use App\Services\ExcelExporter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarisMessController extends Controller
{
    protected string $lokasi = 'Mess';

    /* =========================================================================
     * 1. BARANG TAB (READ-ONLY)
     * ========================================================================= */
    public function barangIndex(Request $request)
    {
        $search = $request->query('search');

        $query = Barang::where('lokasi', $this->lokasi);

        if ($search) {
            $query->where('nama_asset', 'like', "%{$search}%");
        }

        $barangList = $query->orderBy('nama_asset', 'asc')->paginate(10)->withQueryString();

        return view('Administrasi.Pages.Inventaris.mess_barang', [
            'barangList' => $barangList,
            'search' => $search,
            'activeTab' => 'barang',
        ]);
    }

    /* =========================================================================
     * 2. PEMBELIAN TAB (READ-ONLY)
     * ========================================================================= */
    public function pembelianIndex(Request $request)
    {
        $search = $request->query('search');
        $selectedYear = $request->query('year');

        // Extract available years from database
        $availableYears = Pembelian::whereHas('barang', function ($q) {
            $q->where('lokasi', $this->lokasi);
        })
        ->selectRaw('YEAR(tanggal_pembelian) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year')
        ->toArray();

        $currentYear = (int) date('Y');
        $defaultYears = [$currentYear - 1, $currentYear, $currentYear + 1];
        $allYears = array_values(array_unique(array_merge($defaultYears, $availableYears)));
        sort($allYears);

        if (!$selectedYear) {
            $selectedYear = in_array($currentYear, $allYears) ? $currentYear : (end($allYears) ?: $currentYear);
        }

        $query = Pembelian::with('barang')
            ->whereHas('barang', function ($q) {
                $q->where('lokasi', $this->lokasi);
            });

        if ($selectedYear !== 'all' && $selectedYear !== 'Semua Tahun' && $selectedYear !== 'all_years') {
            $query->whereYear('tanggal_pembelian', $selectedYear);
        }

        if ($search) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_asset', 'like', "%{$search}%");
            });
        }

        $pembelianList = $query->orderByDesc('tanggal_pembelian')->paginate(10)->withQueryString();

        // Calculate KPIs
        $kpiQuery = Pembelian::whereHas('barang', function ($q) {
            $q->where('lokasi', $this->lokasi);
        });

        if ($selectedYear !== 'all' && $selectedYear !== 'Semua Tahun' && $selectedYear !== 'all_years') {
            $kpiQuery->whereYear('tanggal_pembelian', $selectedYear);
        }

        $totalJenisAset = (clone $kpiQuery)->distinct('barang_id')->count('barang_id');
        $totalBarangDibeli = (clone $kpiQuery)->sum('jumlah');

        return view('Administrasi.Pages.Inventaris.mess_pembelian', [
            'pembelianList' => $pembelianList,
            'years' => $allYears,
            'selectedYear' => $selectedYear,
            'search' => $search,
            'totalJenisAset' => $totalJenisAset,
            'totalBarangDibeli' => $totalBarangDibeli,
            'activeTab' => 'pembelian',
        ]);
    }

    /* =========================================================================
     * 3. STOCK OPNAME SESSIONS LIST (READ-ONLY)
     * ========================================================================= */
    public function stockOpnameIndex()
    {
        $sessions = StockOpname::whereHas('barang', function ($q) {
            $q->where('lokasi', $this->lokasi);
        })
        ->select(
            'tanggal_stock_opname',
            DB::raw('MAX(status) as status'),
            DB::raw('MAX(catatan_sesi) as catatan_sesi'),
            DB::raw('COUNT(DISTINCT barang_id) as total_aset_diperiksa'),
            DB::raw('SUM(jumlah_rusak_ringan + jumlah_rusak_berat + jumlah_hilang) as total_masalah')
        )
        ->groupBy('tanggal_stock_opname')
        ->orderByDesc('tanggal_stock_opname')
        ->get();

        return view('Administrasi.Pages.Inventaris.mess_stock_opname', [
            'sessions' => $sessions,
            'activeTab' => 'stock_opname',
        ]);
    }

    /* =========================================================================
     * 4. STOCK OPNAME DETAIL VIEW (READ-ONLY)
     * ========================================================================= */
    public function stockOpnameDetail(Request $request, $date)
    {
        $search = $request->query('search');

        $query = StockOpname::with('barang')
            ->whereHas('barang', function ($q) {
                $q->where('lokasi', $this->lokasi);
            })
            ->where('tanggal_stock_opname', $date);

        if ($search) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_asset', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        $sessionMeta = StockOpname::whereHas('barang', function ($q) {
            $q->where('lokasi', $this->lokasi);
        })
        ->where('tanggal_stock_opname', $date)
        ->first();

        return view('Administrasi.Pages.Inventaris.mess_stock_opname_detail', [
            'date' => $date,
            'formattedDate' => Carbon::parse($date)->translatedFormat('d F Y'),
            'sessionMeta' => $sessionMeta,
            'items' => $items,
            'search' => $search,
            'activeTab' => 'stock_opname',
        ]);
    }

    public function stockOpnameExport($date)
    {
        $items = StockOpname::with('barang')
            ->whereHas('barang', function ($q) {
                $q->where('lokasi', $this->lokasi);
            })
            ->where('tanggal_stock_opname', $date)
            ->get();

        $headers = ['No', 'Nama Asset', 'Satuan', 'Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang', 'Grand Total', 'Keterangan'];
        $dataRows = [];

        $no = 1;
        foreach ($items as $item) {
            $dataRows[] = [
                $no++,
                $item->barang->nama_asset ?? '-',
                $item->barang->satuan ?? 'Unit',
                $item->jumlah_baik,
                $item->jumlah_rusak_ringan,
                $item->jumlah_rusak_berat,
                $item->jumlah_hilang,
                $item->grand_total,
                $item->keterangan ?? '-',
            ];
        }

        $filename = "Stock_Opname_Mess_{$date}.xlsx";
        return ExcelExporter::download($filename, $headers, $dataRows, 'Stock Opname Mess');
    }
}
