<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\Pembelian;
use App\Models\Inventaris\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarisSummaryController extends Controller
{
    public function index(Request $request)
    {
        $selectedYear = (int) $request->query('year', date('Y'));
        $years = [2020, 2021, 2022, 2023, 2024, 2025, 2026];

        $jenisLokasiFilter = $request->query('jenis_lokasi', '');
        if ($jenisLokasiFilter === 'Gudang') {
            $locations = ['Gudang'];
        } elseif ($jenisLokasiFilter === 'Kantor') {
            $locations = ['Kantor'];
        } else {
            $locations = ['Gudang', 'Kantor'];
        }

        $summaryData = [];

        // Pre-fetch all Pembelian sums and StockOpname records up to selected year in 2 fast queries
        $pembelianSums = Pembelian::whereYear('tanggal_pembelian', '<=', $selectedYear)
            ->select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('barang_id')
            ->pluck('total', 'barang_id')
            ->all();

        $allStockOpnames = StockOpname::whereYear('tanggal_stock_opname', '<=', $selectedYear)
            ->orderByDesc('tanggal_stock_opname')
            ->get()
            ->groupBy('barang_id');

        foreach ($locations as $lokasi) {
            $barangList = Barang::where('jenis_lokasi', $lokasi)
                ->orWhere('lokasi', $lokasi)
                ->orderBy('nama_asset', 'asc')
                ->get();

            $items = [];
            foreach ($barangList as $barang) {
                $soList = $allStockOpnames->get($barang->id);
                $latestSO = $soList ? $soList->first() : null;
                $totalPembelian = (int) ($pembelianSums[$barang->id] ?? 0);

                $stok = $latestSO ? $latestSO->grand_total : $totalPembelian;
                $baik = $latestSO ? $latestSO->jumlah_baik : $stok;
                $rusak = $latestSO ? $latestSO->jumlah_rusak : 0;
                $keterangan = $latestSO ? $latestSO->keterangan : ($barang->keterangan ?: '-');

                // Room breakdown for Kantor items
                $roomBreakdown = [];
                if ($lokasi === 'Kantor' && $soList) {
                    $latestDate = $latestSO->tanggal_stock_opname;
                    if ($latestDate) {
                        $roomRecords = $soList->filter(function ($rec) use ($latestDate) {
                            return $rec->tanggal_stock_opname === $latestDate && ! empty($rec->lokasi_spesifik);
                        });

                        foreach ($roomRecords as $rec) {
                            $roomBreakdown[] = [
                                'lokasi_spesifik' => $rec->lokasi_spesifik,
                                'jumlah_baik' => $rec->jumlah_baik,
                                'jumlah_rusak' => $rec->jumlah_rusak,
                                'grand_total' => $rec->grand_total,
                            ];
                        }
                    }
                }

                $items[] = [
                    'id' => $barang->id,
                    'nama_asset' => $barang->nama_asset,
                    'satuan' => $barang->satuan,
                    'jenis_lokasi' => $barang->jenis_lokasi ?: $lokasi,
                    'stok' => $stok,
                    'baik' => $baik,
                    'rusak' => $rusak,
                    'keterangan' => $keterangan ?: '-',
                    'room_breakdown' => $roomBreakdown,
                ];
            }

            $summaryData[$lokasi] = $items;
        }

        // Room summary aggregation for Kantor
        $kantorRoomSummary = collect([]);
        if (in_array('Kantor', $locations)) {
            $kantorBarangSummary = Barang::where(function ($q) {
                $q->where('jenis_lokasi', 'Kantor')->orWhere('lokasi', 'Kantor');
            })->get();

            $summaryRoomMap = [];
            foreach ($kantorBarangSummary as $b) {
                $soList = $allStockOpnames->get($b->id);
                $latestSO = $soList ? $soList->first() : null;
                $totalPembelian = (int) ($pembelianSums[$b->id] ?? 0);

                $stok = $latestSO ? $latestSO->grand_total : $totalPembelian;
                $baik = $latestSO ? $latestSO->jumlah_baik : $stok;
                $rusak = $latestSO ? $latestSO->jumlah_rusak : 0;

                $roomName = ($latestSO && ! empty(trim((string) $latestSO->lokasi_spesifik)))
                    ? trim($latestSO->lokasi_spesifik)
                    : (($b->lokasi && ! in_array($b->lokasi, ['Kantor', 'Gudang'])) ? $b->lokasi : 'Kantor (Umum)');

                if (! isset($summaryRoomMap[$roomName])) {
                    $summaryRoomMap[$roomName] = (object) [
                        'lokasi_spesifik' => $roomName,
                        'total_jenis_barang' => 0,
                        'total_baik' => 0,
                        'total_rusak' => 0,
                        'total_aset' => 0,
                    ];
                }

                $summaryRoomMap[$roomName]->total_jenis_barang += 1;
                $summaryRoomMap[$roomName]->total_baik += $baik;
                $summaryRoomMap[$roomName]->total_rusak += $rusak;
                $summaryRoomMap[$roomName]->total_aset += $stok;
            }

            $summaryRoomList = array_values($summaryRoomMap);
            usort($summaryRoomList, fn ($a, $b) => strcmp($a->lokasi_spesifik, $b->lokasi_spesifik));
            $kantorRoomSummary = collect($summaryRoomList);
        }

        return view('Administrasi.Pages.Inventaris.summary', [
            'summaryData' => $summaryData,
            'kantorRoomSummary' => $kantorRoomSummary,
            'years' => $years,
            'selectedYear' => (int) $selectedYear,
            'jenisLokasiFilter' => $jenisLokasiFilter,
            'activeTab' => 'summary',
        ]);
    }
}
