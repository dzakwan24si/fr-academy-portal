<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InventarisSummaryController extends Controller
{
    public function index(Request $request)
    {
        $jenisLokasiFilter = $request->query('jenis_lokasi', '');
        if ($jenisLokasiFilter === 'Gudang') {
            $locations = ['Gudang'];
        } elseif ($jenisLokasiFilter === 'Kantor') {
            $locations = ['Kantor'];
        } else {
            $locations = ['Gudang', 'Kantor'];
        }

        // Get available stock opname dates across requested locations
        $dateQuery = StockOpname::whereHas('barang', function ($q) use ($locations) {
            $q->where(function ($sub) use ($locations) {
                foreach ($locations as $loc) {
                    $sub->orWhere('jenis_lokasi', $loc)->orWhere('lokasi', $loc);
                }
            });
        });

        $availableDates = (clone $dateQuery)
            ->select('tanggal_stock_opname')
            ->distinct()
            ->orderByDesc('tanggal_stock_opname')
            ->pluck('tanggal_stock_opname')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->values()
            ->all();

        $overallLatestDate = $availableDates[0] ?? null;
        $selectedDate = $request->query('date');
        if (! $selectedDate || ! in_array($selectedDate, $availableDates, true)) {
            $selectedDate = $overallLatestDate;
        }

        // Find latest date specifically for Gudang and Kantor
        $latestDateGudang = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', 'Gudang')->orWhere('lokasi', 'Gudang');
        })->max('tanggal_stock_opname');
        if ($latestDateGudang) {
            $latestDateGudang = Carbon::parse($latestDateGudang)->format('Y-m-d');
        }

        $latestDateKantor = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', 'Kantor')->orWhere('lokasi', 'Kantor');
        })->max('tanggal_stock_opname');
        if ($latestDateKantor) {
            $latestDateKantor = Carbon::parse($latestDateKantor)->format('Y-m-d');
        }

        $summaryData = [];
        $kantorRoomSummary = collect([]);

        foreach ($locations as $lokasi) {
            // Determine active date for this location:
            // If user explicitly picked a date, use that date.
            // Otherwise, use the latest date for this location.
            $locDate = $request->query('date') ?: ($lokasi === 'Gudang' ? $latestDateGudang : $latestDateKantor);

            if (! $locDate) {
                $summaryData[$lokasi] = [];
                continue;
            }

            $soRecords = StockOpname::with('barang')
                ->whereHas('barang', function ($q) use ($lokasi) {
                    $q->where('jenis_lokasi', $lokasi)->orWhere('lokasi', $lokasi);
                })
                ->whereDate('tanggal_stock_opname', $locDate)
                ->get();

            $items = [];
            $grouped = $soRecords->groupBy('barang_id');

            foreach ($grouped as $bId => $recs) {
                $barang = $recs->first()->barang;
                $stok = (int) $recs->sum('grand_total');
                $baik = (int) $recs->sum('jumlah_baik');
                $rusak = (int) $recs->sum('jumlah_rusak');

                $keteranganList = $recs->pluck('keterangan')
                    ->map(fn ($k) => trim((string) $k))
                    ->filter(fn ($k) => ! empty($k) && $k !== '-')
                    ->unique()
                    ->values();

                $keterangan = $keteranganList->isNotEmpty()
                    ? $keteranganList->implode('; ')
                    : ($barang && $barang->keterangan ? $barang->keterangan : '-');

                $roomBreakdown = [];
                if ($lokasi === 'Kantor') {
                    foreach ($recs as $rec) {
                        if (! empty($rec->lokasi_spesifik)) {
                            $roomBreakdown[] = [
                                'lokasi_spesifik' => $rec->lokasi_spesifik,
                                'jumlah_baik' => (int) $rec->jumlah_baik,
                                'jumlah_rusak' => (int) $rec->jumlah_rusak,
                                'grand_total' => (int) $rec->grand_total,
                            ];
                        }
                    }
                }

                $items[] = [
                    'id' => $bId,
                    'nama_asset' => $barang ? $barang->nama_asset : ('Asset #' . $bId),
                    'satuan' => $barang ? ($barang->satuan ?: 'Unit') : 'Unit',
                    'jenis_lokasi' => $barang ? ($barang->jenis_lokasi ?: $lokasi) : $lokasi,
                    'stok' => $stok,
                    'baik' => $baik,
                    'rusak' => $rusak,
                    'keterangan' => $keterangan ?: '-',
                    'room_breakdown' => $roomBreakdown,
                ];
            }

            usort($items, fn ($a, $b) => strcasecmp($a['nama_asset'], $b['nama_asset']));
            $summaryData[$lokasi] = $items;

            // Room summary aggregation for Kantor on the active date
            if ($lokasi === 'Kantor' && $soRecords->isNotEmpty()) {
                $summaryRoomMap = [];
                foreach ($soRecords as $rec) {
                    $roomName = ! empty(trim((string) $rec->lokasi_spesifik))
                        ? trim($rec->lokasi_spesifik)
                        : 'Kantor (Umum)';

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
                    $summaryRoomMap[$roomName]->total_baik += (int) $rec->jumlah_baik;
                    $summaryRoomMap[$roomName]->total_rusak += (int) $rec->jumlah_rusak;
                    $summaryRoomMap[$roomName]->total_aset += (int) $rec->grand_total;
                }

                $summaryRoomList = array_values($summaryRoomMap);
                usort($summaryRoomList, fn ($a, $b) => strcmp($a->lokasi_spesifik, $b->lokasi_spesifik));
                $kantorRoomSummary = collect($summaryRoomList);
            }
        }

        return view('Administrasi.Pages.Inventaris.summary', [
            'summaryData' => $summaryData,
            'kantorRoomSummary' => $kantorRoomSummary,
            'availableDates' => $availableDates,
            'selectedDate' => $selectedDate,
            'overallLatestDate' => $overallLatestDate,
            'latestDateGudang' => $latestDateGudang,
            'latestDateKantor' => $latestDateKantor,
            'jenisLokasiFilter' => $jenisLokasiFilter,
            'activeTab' => 'summary',
        ]);
    }
}
