<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Barang;
use App\Models\Administrasi\Pembelian;
use App\Models\Administrasi\StockOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InventarisGudangController extends Controller
{
    protected string $lokasi = 'Gudang';

    /* =========================================================================
     * 1. BARANG TAB (Image 1)
     * ========================================================================= */
    public function barangIndex(Request $request)
    {
        $search = $request->query('search');

        $query = Barang::where('lokasi', $this->lokasi);

        if ($search) {
            $query->where('nama_asset', 'like', "%{$search}%");
        }

        $barangList = $query->orderBy('nama_asset', 'asc')->paginate(10)->withQueryString();

        return view('Administrasi.Pages.Inventaris.gudang_barang', [
            'barangList' => $barangList,
            'search' => $search,
            'activeTab' => 'barang',
        ]);
    }

    public function barangStore(Request $request)
    {
        $validated = $request->validate([
            'nama_asset' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        $validated['lokasi'] = $this->lokasi;

        Barang::create($validated);

        return redirect()->route('administrasi.inventaris.gudang.barang')
            ->with('success', 'Aset barang gudang berhasil ditambahkan!');
    }

    public function barangUpdate(Request $request, $id)
    {
        $barang = Barang::where('lokasi', $this->lokasi)->findOrFail($id);

        $validated = $request->validate([
            'nama_asset' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        $barang->update($validated);

        return redirect()->route('administrasi.inventaris.gudang.barang')
            ->with('success', 'Data aset barang berhasil diperbarui!');
    }

    public function barangDestroy($id)
    {
        $barang = Barang::where('lokasi', $this->lokasi)->findOrFail($id);
        $barang->delete();

        return redirect()->route('administrasi.inventaris.gudang.barang')
            ->with('success', 'Data aset barang berhasil dihapus!');
    }

    /* =========================================================================
     * 2. PEMBELIAN TAB (Image 2)
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

        // Total barang available in Gudang to populate dropdown in modal
        $allBarang = Barang::where('lokasi', $this->lokasi)->orderBy('nama_asset', 'asc')->get();

        return view('Administrasi.Pages.Inventaris.gudang_pembelian', [
            'pembelianList' => $pembelianList,
            'allBarang' => $allBarang,
            'years' => $allYears,
            'selectedYear' => $selectedYear,
            'search' => $search,
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
            'keterangan' => 'nullable|string',
        ]);

        Pembelian::create($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;

        return redirect()->route('administrasi.inventaris.gudang.pembelian', ['year' => $year])
            ->with('success', 'Data pembelian aset berhasil disimpan!');
    }

    public function pembelianUpdate(Request $request, $id)
    {
        $pembelian = Pembelian::whereHas('barang', function ($q) {
            $q->where('lokasi', $this->lokasi);
        })->findOrFail($id);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'tanggal_pembelian' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $pembelian->update($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;

        return redirect()->route('administrasi.inventaris.gudang.pembelian', ['year' => $year])
            ->with('success', 'Data pembelian berhasil diperbarui!');
    }

    public function pembelianDestroy($id)
    {
        $pembelian = Pembelian::whereHas('barang', function ($q) {
            $q->where('lokasi', $this->lokasi);
        })->findOrFail($id);

        $pembelian->delete();

        return redirect()->back()->with('success', 'Data pembelian berhasil dihapus!');
    }

    /* =========================================================================
     * 3. STOCK OPNAME SESSIONS LIST (Image 3)
     * ========================================================================= */
    public function stockOpnameIndex()
    {
        // Group by tanggal_stock_opname
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
            $q->where('lokasi', $this->lokasi);
        })->where('tanggal_stock_opname', $validated['tanggal_stock_opname'])->first();

        if ($existing) {
            return redirect()->route('administrasi.inventaris.gudang.stock-opname.detail', ['date' => $validated['tanggal_stock_opname']])
                ->with('info', 'Sesi Stock Opname untuk tanggal tersebut sudah ada.');
        }

        // Initialize session with the first available asset or empty placeholder
        $firstBarang = Barang::where('lokasi', $this->lokasi)->first();
        if ($firstBarang) {
            StockOpname::create([
                'barang_id' => $firstBarang->id,
                'tanggal_stock_opname' => $validated['tanggal_stock_opname'],
                'status' => $validated['status'],
                'catatan_sesi' => $validated['catatan_sesi'],
                'jumlah_baik' => 0,
                'jumlah_rusak_ringan' => 0,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
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
            $q->where('lokasi', $this->lokasi);
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
            $q->where('lokasi', $this->lokasi);
        })
        ->where('tanggal_stock_opname', $date)
        ->delete();

        return redirect()->route('administrasi.inventaris.gudang.stock-opname')
            ->with('success', 'Sesi Stock Opname tanggal ' . Carbon::parse($date)->translatedFormat('d F Y') . ' berhasil dihapus!');
    }

    /* =========================================================================
     * 4. STOCK OPNAME DETAIL VIEW (Image 4)
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

        // Get session meta
        $sessionMeta = StockOpname::whereHas('barang', function ($q) {
            $q->where('lokasi', $this->lokasi);
        })
        ->where('tanggal_stock_opname', $date)
        ->first();

        $allBarang = Barang::where('lokasi', $this->lokasi)->orderBy('nama_asset', 'asc')->get();

        return view('Administrasi.Pages.Inventaris.gudang_stock_opname_detail', [
            'date' => $date,
            'formattedDate' => Carbon::parse($date)->translatedFormat('d F Y'),
            'sessionMeta' => $sessionMeta,
            'items' => $items,
            'allBarang' => $allBarang,
            'search' => $search,
            'activeTab' => 'stock_opname',
        ]);
    }

    public function stockOpnameItemStore(Request $request, $date)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah_baik' => 'nullable|integer|min:0',
            'jumlah_rusak_ringan' => 'nullable|integer|min:0',
            'jumlah_rusak_berat' => 'nullable|integer|min:0',
            'jumlah_hilang' => 'nullable|integer|min:0',
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
        $rusakRingan = (int) ($validated['jumlah_rusak_ringan'] ?? 0);
        $rusakBerat = (int) ($validated['jumlah_rusak_berat'] ?? 0);
        $hilang = (int) ($validated['jumlah_hilang'] ?? 0);

        StockOpname::create([
            'barang_id' => $validated['barang_id'],
            'tanggal_stock_opname' => $date,
            'status' => $status,
            'catatan_sesi' => $catatan,
            'jumlah_baik' => $baik,
            'jumlah_rusak_ringan' => $rusakRingan,
            'jumlah_rusak_berat' => $rusakBerat,
            'jumlah_hilang' => $hilang,
            'grand_total' => $baik + $rusakRingan + $rusakBerat + $hilang,
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
            'jumlah_baik' => 'nullable|integer|min:0',
            'jumlah_rusak_ringan' => 'nullable|integer|min:0',
            'jumlah_rusak_berat' => 'nullable|integer|min:0',
            'jumlah_hilang' => 'nullable|integer|min:0',
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
        $rusakRingan = (int) ($validated['jumlah_rusak_ringan'] ?? 0);
        $rusakBerat = (int) ($validated['jumlah_rusak_berat'] ?? 0);
        $hilang = (int) ($validated['jumlah_hilang'] ?? 0);

        $item->barang_id = $validated['barang_id'];
        $item->jumlah_baik = $baik;
        $item->jumlah_rusak_ringan = $rusakRingan;
        $item->jumlah_rusak_berat = $rusakBerat;
        $item->jumlah_hilang = $hilang;
        $item->grand_total = $baik + $rusakRingan + $rusakBerat + $hilang;
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
                $q->where('lokasi', $this->lokasi);
            })
            ->where('tanggal_stock_opname', $date)
            ->get();

        $filename = "stock_opname_gudang_{$date}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items, $date) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Stock Opname Gudang - ' . $date]);
            fputcsv($handle, ['No', 'Nama Asset', 'Satuan', 'Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang', 'Grand Total', 'Keterangan']);

            $no = 1;
            foreach ($items as $item) {
                fputcsv($handle, [
                    $no++,
                    $item->barang->nama_asset ?? '-',
                    $item->barang->satuan ?? 'Unit',
                    $item->jumlah_baik,
                    $item->jumlah_rusak_ringan,
                    $item->jumlah_rusak_berat,
                    $item->jumlah_hilang,
                    $item->grand_total,
                    $item->keterangan ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
