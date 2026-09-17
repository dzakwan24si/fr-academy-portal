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
use Illuminate\Support\Facades\Storage;

class InventarisGudangController extends Controller
{
    protected string $lokasi = 'Gudang';

    /* =========================================================================
     * 1. BARANG TAB
     * ========================================================================= */
    public function barangIndex(Request $request)
    {
        $search = $request->query('search');
        $jenisLokasiFilter = $request->query('jenis_lokasi');

        $query = Barang::where(function($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        });

        if ($jenisLokasiFilter && in_array($jenisLokasiFilter, ['Kantor', 'Gudang'])) {
            $query->where('jenis_lokasi', $jenisLokasiFilter);
        }

        if ($search) {
            $query->where('nama_asset', 'like', "%{$search}%");
        }

        $barangList = $query->orderBy('nama_asset', 'asc')->paginate(10)->withQueryString();

        return view('Administrasi.Pages.Inventaris.gudang_barang', [
            'barangList' => $barangList,
            'search' => $search,
            'jenisLokasiFilter' => $jenisLokasiFilter,
            'activeTab' => 'barang',
        ]);
    }

    public function barangStore(Request $request)
    {
        $validated = $request->validate([
            'nama_asset' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'jenis_lokasi' => 'required|in:Kantor,Gudang',
            'keterangan' => 'nullable|string',
        ]);

        $validated['lokasi'] = $validated['jenis_lokasi'];

        Barang::create($validated);

        return redirect()->route('administrasi.inventaris.gudang.barang')
            ->with('success', 'Aset barang berhasil ditambahkan!');
    }

    public function barangUpdate(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $validated = $request->validate([
            'nama_asset' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'jenis_lokasi' => 'required|in:Kantor,Gudang',
            'keterangan' => 'nullable|string',
        ]);

        $validated['lokasi'] = $validated['jenis_lokasi'];

        $barang->update($validated);

        return redirect()->route('administrasi.inventaris.gudang.barang')
            ->with('success', 'Data aset barang berhasil diperbarui!');
    }

    public function barangDestroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('administrasi.inventaris.gudang.barang')
            ->with('success', 'Data aset barang berhasil dihapus!');
    }

    public function barangTemplate()
    {
        $headers = ['nama_asset', 'satuan', 'keterangan'];
        $sampleRows = [
            ['Semen Padang 50kg', 'Sak', 'Pengadaan stok gudang'],
            ['Cat Tembok Putih 25kg', 'Pail', 'Stok renovasi gedung'],
        ];

        return ExcelExporter::download('Template_Import_Barang_Gudang.xlsx', $headers, $sampleRows, 'Template Barang Gudang');
    }

    public function barangImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return redirect()->back()->withErrors(['file' => 'Gagal membaca file import.']);
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header || count($header) < 2) {
            rewind($handle);
            $header = fgetcsv($handle, 1000, ';');
        }

        $imported = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false || ($data = fgetcsv($handle, 1000, ';')) !== false) {
            if (empty($data) || count($data) < 1 || empty(trim($data[0]))) {
                continue;
            }

            $namaAsset = trim($data[0]);
            $satuan = isset($data[1]) && !empty(trim($data[1])) ? trim($data[1]) : 'Unit';
            $keterangan = isset($data[2]) ? trim($data[2]) : null;

            Barang::create([
                'nama_asset' => $namaAsset,
                'satuan' => $satuan,
                'jenis_lokasi' => $this->lokasi,
                'lokasi' => $this->lokasi,
                'keterangan' => $keterangan,
            ]);

            $imported++;
        }
        fclose($handle);

        return redirect()->route('administrasi.inventaris.gudang.barang')
            ->with('success', "Berhasil mengimpor {$imported} data barang untuk Gudang!");
    }

    /* =========================================================================
     * 2. PEMBELIAN TAB
     * ========================================================================= */
    public function pembelianIndex(Request $request)
    {
        $search = $request->query('search');
        $selectedYear = $request->query('year');
        $lokasiSpesifikFilter = $request->query('lokasi_spesifik');

        $availableYears = Pembelian::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
        ->selectRaw('YEAR(tanggal_pembelian) as year')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year')
        ->toArray();

        $currentYear = (int) date('Y');
        $defaultYears = [2020, 2021, 2022, 2023, 2024, 2025, $currentYear];
        $allYears = array_values(array_unique(array_merge($defaultYears, $availableYears)));
        sort($allYears);

        if (!$selectedYear) {
            $selectedYear = 'all';
        }

        $query = Pembelian::with('barang')
            ->whereHas('barang', function ($q) {
                $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
            });

        if ($selectedYear !== 'all' && $selectedYear !== 'Semua Tahun' && $selectedYear !== 'all_years') {
            $query->whereYear('tanggal_pembelian', $selectedYear);
        }

        if ($lokasiSpesifikFilter) {
            $query->where('lokasi_spesifik', $lokasiSpesifikFilter);
        }

        if ($search) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_asset', 'like', "%{$search}%");
            });
        }

        $pembelianList = $query->orderByDesc('tanggal_pembelian')->paginate(10)->withQueryString();

        $kpiQuery = Pembelian::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        });

        if ($selectedYear !== 'all' && $selectedYear !== 'Semua Tahun' && $selectedYear !== 'all_years') {
            $kpiQuery->whereYear('tanggal_pembelian', $selectedYear);
        }

        $totalJenisAset = (clone $kpiQuery)->distinct('barang_id')->count('barang_id');
        $totalBarangDibeli = (clone $kpiQuery)->sum('jumlah');

        $allBarang = Barang::where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi)->orderBy('nama_asset', 'asc')->get();

        return view('Administrasi.Pages.Inventaris.gudang_pembelian', [
            'pembelianList' => $pembelianList,
            'allBarang' => $allBarang,
            'years' => $allYears,
            'selectedYear' => $selectedYear,
            'search' => $search,
            'lokasiSpesifikFilter' => $lokasiSpesifikFilter,
            'totalJenisAset' => $totalJenisAset,
            'totalBarangDibeli' => $totalBarangDibeli,
            'activeTab' => 'pembelian',
        ]);
    }

    public function pembelianStore(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'tanggal_pembelian' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'lokasi_spesifik' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Pembelian::create($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;

        return redirect()->route('administrasi.inventaris.gudang.pembelian', ['year' => $year])
            ->with('success', 'Data pembelian aset berhasil disimpan!');
    }

    public function pembelianUpdate(Request $request, $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'tanggal_pembelian' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'lokasi_spesifik' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $pembelian->update($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;

        return redirect()->route('administrasi.inventaris.gudang.pembelian', ['year' => $year])
            ->with('success', 'Data pembelian berhasil diperbarui!');
    }

    public function pembelianDestroy($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->delete();

        return redirect()->back()->with('success', 'Data pembelian berhasil dihapus!');
    }

    public function pembelianTemplate()
    {
        $headers = ['nama_asset', 'tanggal_pembelian', 'jumlah', 'lokasi_spesifik', 'keterangan'];
        $sampleRows = [
            ['Semen Padang 50kg', date('Y-m-d'), '10', '', 'Pembelian tahap 1'],
        ];

        return ExcelExporter::download('Template_Import_Pembelian_Gudang.xlsx', $headers, $sampleRows, 'Template Pembelian Gudang');
    }

    public function pembelianImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return redirect()->back()->withErrors(['file' => 'Gagal membaca file import.']);
        }

        $header = fgetcsv($handle, 1000, ',');
        if (!$header || count($header) < 2) {
            rewind($handle);
            $header = fgetcsv($handle, 1000, ';');
        }

        $imported = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false || ($data = fgetcsv($handle, 1000, ';')) !== false) {
            if (empty($data) || count($data) < 2 || empty(trim($data[0]))) {
                continue;
            }

            $namaAsset = trim($data[0]);
            $tanggal = !empty(trim($data[1])) ? trim($data[1]) : date('Y-m-d');
            $jumlah = isset($data[2]) ? (int) trim($data[2]) : 1;
            $lokasiSpesifik = isset($data[3]) ? trim($data[3]) : null;
            $keterangan = isset($data[4]) ? trim($data[4]) : null;

            $barang = Barang::where(function($q) {
                $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
            })->where('nama_asset', $namaAsset)->first();

            if (!$barang) {
                $barang = Barang::create([
                    'nama_asset' => $namaAsset,
                    'satuan' => 'Unit',
                    'jenis_lokasi' => $this->lokasi,
                    'lokasi' => $this->lokasi,
                ]);
            }

            Pembelian::create([
                'barang_id' => $barang->id,
                'tanggal_pembelian' => $tanggal,
                'jumlah' => max(1, $jumlah),
                'lokasi_spesifik' => $lokasiSpesifik,
                'keterangan' => $keterangan,
            ]);

            $imported++;
        }
        fclose($handle);

        return redirect()->route('administrasi.inventaris.gudang.pembelian')
            ->with('success', "Berhasil mengimpor {$imported} data pembelian untuk Gudang!");
    }

    /* =========================================================================
     * 3. STOCK OPNAME SESSIONS LIST
     * ========================================================================= */
    public function stockOpnameIndex()
    {
        $sessions = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
        ->select(
            'tanggal_stock_opname',
            DB::raw('MAX(status) as status'),
            DB::raw('MAX(catatan_sesi) as catatan_sesi'),
            DB::raw('COUNT(DISTINCT barang_id) as total_aset_diperiksa'),
            DB::raw('SUM(jumlah_rusak) as total_masalah')
        )
        ->groupBy('tanggal_stock_opname')
        ->orderByDesc('tanggal_stock_opname')
        ->get();

        return view('Administrasi.Pages.Inventaris.gudang_stock_opname', [
            'sessions' => $sessions,
            'activeTab' => 'stock_opname',
        ]);
    }

    public function stockOpnameSessionStore(Request $request)
    {
        $validated = $request->validate([
            'tanggal_stock_opname' => 'required|date',
            'status' => 'required|in:Selesai,Draf',
            'catatan_sesi' => 'nullable|string',
        ]);

        $existing = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })->where('tanggal_stock_opname', $validated['tanggal_stock_opname'])->first();

        if ($existing) {
            return redirect()->route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $validated['tanggal_stock_opname']])
                ->with('info', 'Sesi Stock Opname untuk tanggal tersebut sudah ada.');
        }

        $firstBarang = Barang::where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi)->first();
        if ($firstBarang) {
            StockOpname::create([
                'barang_id' => $firstBarang->id,
                'tanggal_stock_opname' => $validated['tanggal_stock_opname'],
                'status' => $validated['status'],
                'catatan_sesi' => $validated['catatan_sesi'],
                'jumlah_baik' => 0,
                'jumlah_rusak' => 0,
                'grand_total' => 0,
                'keterangan' => 'Inisialisasi Stock Opname',
            ]);
        }

        return redirect()->route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $validated['tanggal_stock_opname']])
            ->with('success', 'Sesi Stock Opname berhasil dibuat!');
    }

    public function stockOpnameSessionUpdate(Request $request, $date)
    {
        $validated = $request->validate([
            'status' => 'required|in:Selesai,Draf',
            'catatan_sesi' => 'nullable|string',
        ]);

        StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
        ->where('tanggal_stock_opname', $date)
        ->update([
            'status' => $validated['status'],
            'catatan_sesi' => $validated['catatan_sesi'],
        ]);

        return redirect()->route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $date])
            ->with('success', 'Informasi sesi Stock Opname berhasil diperbarui!');
    }

    public function stockOpnameSessionDestroy($date)
    {
        StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
        ->where('tanggal_stock_opname', $date)
        ->delete();

        return redirect()->route('administrasi.inventaris.gudang.stock-opname')
            ->with('success', 'Sesi Stock Opname tanggal ' . Carbon::parse($date)->translatedFormat('d F Y') . ' berhasil dihapus!');
    }

    /* =========================================================================
     * 4. STOCK OPNAME DETAIL VIEW
     * ========================================================================= */
    public function stockOpnameDetail(Request $request, $date)
    {
        $search = $request->query('search');
        $lokasiSpesifikFilter = $request->query('lokasi_spesifik');

        $query = StockOpname::with('barang')
            ->whereHas('barang', function ($q) {
                $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
            })
            ->where('tanggal_stock_opname', $date);

        if ($lokasiSpesifikFilter) {
            $query->where('lokasi_spesifik', $lokasiSpesifikFilter);
        }

        if ($search) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_asset', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        $sessionMeta = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
        ->where('tanggal_stock_opname', $date)
        ->first();

        $allBarang = Barang::where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi)->orderBy('nama_asset', 'asc')->get();

        return view('Administrasi.Pages.Inventaris.gudang_stock_opname_detail', [
            'date' => $date,
            'formattedDate' => Carbon::parse($date)->translatedFormat('d F Y'),
            'sessionMeta' => $sessionMeta,
            'items' => $items,
            'allBarang' => $allBarang,
            'search' => $search,
            'lokasiSpesifikFilter' => $lokasiSpesifikFilter,
            'activeTab' => 'stock_opname',
        ]);
    }

    public function stockOpnameItemStore(Request $request, $date)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'lokasi_spesifik' => 'nullable|string|max:255',
            'jumlah_baik' => 'nullable|integer|min:0',
            'jumlah_rusak' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_kerusakan')) {
            $fotoPath = $request->file('foto_kerusakan')->store('inventaris', 'public');
        }

        $sessionMeta = StockOpname::where('tanggal_stock_opname', $date)->first();
        $status = $sessionMeta ? $sessionMeta->status : 'Selesai';
        $catatan = $sessionMeta ? $sessionMeta->catatan_sesi : null;

        $baik = (int) ($validated['jumlah_baik'] ?? 0);
        $rusak = (int) ($validated['jumlah_rusak'] ?? 0);

        StockOpname::create([
            'barang_id' => $validated['barang_id'],
            'tanggal_stock_opname' => $date,
            'status' => $status,
            'catatan_sesi' => $catatan,
            'lokasi_spesifik' => $validated['lokasi_spesifik'] ?? null,
            'jumlah_baik' => $baik,
            'jumlah_rusak' => $rusak,
            'grand_total' => $baik + $rusak,
            'keterangan' => $validated['keterangan'] ?? null,
            'foto_kerusakan' => $fotoPath,
        ]);

        return redirect()->route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $date])
            ->with('success', 'Data aset berhasil ditambahkan ke Stock Opname!');
    }

    public function stockOpnameItemUpdate(Request $request, $id)
    {
        $item = StockOpname::findOrFail($id);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'lokasi_spesifik' => 'nullable|string|max:255',
            'jumlah_baik' => 'nullable|integer|min:0',
            'jumlah_rusak' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('foto_kerusakan')) {
            if ($item->foto_kerusakan && Storage::disk('public')->exists($item->foto_kerusakan)) {
                Storage::disk('public')->delete($item->foto_kerusakan);
            }
            $item->foto_kerusakan = $request->file('foto_kerusakan')->store('inventaris', 'public');
        }

        $baik = (int) ($validated['jumlah_baik'] ?? 0);
        $rusak = (int) ($validated['jumlah_rusak'] ?? 0);

        $item->barang_id = $validated['barang_id'];
        $item->lokasi_spesifik = $validated['lokasi_spesifik'] ?? null;
        $item->jumlah_baik = $baik;
        $item->jumlah_rusak = $rusak;
        $item->grand_total = $baik + $rusak;
        $item->keterangan = $validated['keterangan'] ?? null;
        $item->save();

        return redirect()->route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $item->tanggal_stock_opname->format('Y-m-d')])
            ->with('success', 'Data kondisi barang berhasil diperbarui!');
    }

    public function stockOpnameItemDestroy($id)
    {
        $item = StockOpname::findOrFail($id);
        $date = $item->tanggal_stock_opname->format('Y-m-d');

        if ($item->foto_kerusakan && Storage::disk('public')->exists($item->foto_kerusakan)) {
            Storage::disk('public')->delete($item->foto_kerusakan);
        }

        $item->delete();

        return redirect()->route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $date])
            ->with('success', 'Data barang berhasil dihapus dari Stock Opname!');
    }

    public function stockOpnameExport($date)
    {
        $items = StockOpname::with('barang')
            ->whereHas('barang', function ($q) {
                $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
            })
            ->where('tanggal_stock_opname', $date)
            ->get();

        $headers = ['ID', 'Nama Asset', 'Satuan', 'Jenis Lokasi', 'Lokasi Spesifik', 'Baik', 'Rusak', 'Grand Total', 'Keterangan'];
        $dataRows = [];

        foreach ($items as $item) {
            $dataRows[] = [
                $item->id,
                $item->barang?->nama_asset,
                $item->barang?->satuan,
                $item->barang?->jenis_lokasi,
                $item->lokasi_spesifik ?: '-',
                $item->jumlah_baik,
                $item->jumlah_rusak,
                $item->grand_total,
                $item->keterangan ?: '-',
            ];
        }

        $filename = "Stock_Opname_Gudang_{$date}.xlsx";
        return ExcelExporter::download($filename, $headers, $dataRows, 'Stock Opname Gudang');
    }

    public function barangDestroyAll()
    {
        $msg = '';
        DB::transaction(function () use (&$msg) {
            $barangIds = Barang::where('jenis_lokasi', $this->lokasi)
                ->orWhere('lokasi', $this->lokasi)
                ->pluck('id');

            StockOpname::whereIn('barang_id', $barangIds)->delete();
            Pembelian::whereIn('barang_id', $barangIds)->delete();
            $deleted = Barang::whereIn('id', $barangIds)->delete();
            $msg = "Seluruh data Barang inventaris Gudang ({$deleted} data) berhasil dihapus.";
        });

        return redirect()->route('administrasi.inventaris.gudang.barang')->with('success', $msg);
    }

    public function pembelianDestroyAll()
    {
        $barangIds = Barang::where('jenis_lokasi', $this->lokasi)
            ->orWhere('lokasi', $this->lokasi)
            ->pluck('id');
        $deleted = Pembelian::whereIn('barang_id', $barangIds)->delete();

        return redirect()->route('administrasi.inventaris.gudang.pembelian')
            ->with('success', "Seluruh data Pembelian inventaris Gudang ({$deleted} data) berhasil dihapus.");
    }

    public function stockOpnameDestroyAll()
    {
        $barangIds = Barang::where('jenis_lokasi', $this->lokasi)
            ->orWhere('lokasi', $this->lokasi)
            ->pluck('id');
        $deleted = StockOpname::whereIn('barang_id', $barangIds)->delete();

        return redirect()->route('administrasi.inventaris.gudang.stock-opname')
            ->with('success', "Seluruh data Stock Opname inventaris Gudang ({$deleted} data) berhasil dihapus.");
    }
}
