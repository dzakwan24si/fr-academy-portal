<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Barang;
use App\Models\Administrasi\Pembelian;
use App\Models\Administrasi\StockOpname;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventarisSummaryController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = $request->query('year', (int) date('Y'));
        $years = [2024, 2025, 2026];

        $locations = ['Gudang', 'Kantor', 'Mess'];
        $summaryData = [];

        foreach ($locations as $lokasi) {
            $barangList = Barang::where('lokasi', $lokasi)
                ->orderBy('nama_asset', 'asc')
                ->get();

            $items = [];
            foreach ($barangList as $barang) {
                // Get latest Stock Opname up to or within selected year
                $latestSO = StockOpname::where('barang_id', $barang->id)
                    ->whereYear('tanggal_stock_opname', '<=', $selectedYear)
                    ->orderByDesc('tanggal_stock_opname')
                    ->first();

                // Total purchased up to this year
                $totalPembelian = Pembelian::where('barang_id', $barang->id)
                    ->whereYear('tanggal_pembelian', '<=', $selectedYear)
                    ->sum('jumlah');

                $stok = $latestSO ? $latestSO->grand_total : $totalPembelian;
                $baik = $latestSO ? $latestSO->jumlah_baik : $stok;
                $rusakRingan = $latestSO ? $latestSO->jumlah_rusak_ringan : 0;
                $rusakBerat = $latestSO ? $latestSO->jumlah_rusak_berat : 0;
                $keterangan = $latestSO ? $latestSO->keterangan : ($barang->keterangan ?: '-');

                $items[] = [
                    'id' => $barang->id,
                    'nama_asset' => $barang->nama_asset,
                    'satuan' => $barang->satuan,
                    'stok' => $stok,
                    'baik' => $baik,
                    'rusak_ringan' => $rusakRingan,
                    'rusak_berat' => $rusakBerat,
                    'keterangan' => $keterangan ?: '-',
                ];
            }

            $summaryData[$lokasi] = $items;
        }

        return view('Administrasi.Pages.Inventaris.summary', [
            'summaryData' => $summaryData,
            'years' => $years,
            'selectedYear' => (int) $selectedYear,
            'activeTab' => 'summary',
        ]);
    }
}
