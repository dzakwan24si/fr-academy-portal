<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\Pembelian;
use App\Models\Inventaris\StockOpname;
use App\Services\ExcelExporter;
use App\Services\SpreadsheetReader;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

abstract class BaseSubsystemInventarisController extends Controller
{
    protected string $lokasi = '';
    protected string $viewPrefix = '';
    protected string $routePrefix = '';

    /* =========================================================================
     * 1. BARANG TAB
     * ========================================================================= */
    public function barangIndex(Request $request)
    {
        $search = $request->query('search');
        $jenisLokasiFilter = $request->query('jenis_lokasi');

        $query = Barang::where(function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        });

        if ($jenisLokasiFilter && in_array($jenisLokasiFilter, ['Kantor', 'Gudang'])) {
            $query->where('jenis_lokasi', $jenisLokasiFilter);
        }

        if ($search) {
            $query->where('nama_asset', 'like', "%{$search}%");
        }

        $barangList = $query->orderBy('nama_asset', 'asc')->paginate(10)->withQueryString();

        return view("Administrasi.Pages.Inventaris.{$this->viewPrefix}_barang", [
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

        return redirect()->route("{$this->routePrefix}.barang")
            ->with('success', "Aset barang {$this->lokasi} berhasil ditambahkan!");
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

        return redirect()->route("{$this->routePrefix}.barang")
            ->with('success', "Data aset barang {$this->lokasi} berhasil diperbarui!");
    }

    public function barangDestroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route("{$this->routePrefix}.barang")
            ->with('success', "Data aset barang {$this->lokasi} berhasil dihapus!");
    }

    protected function parseCsvFile(Request $request): array
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $rows = SpreadsheetReader::readCsv($file->getRealPath());

        if (empty($rows)) {
            return ['headerMap' => [], 'rows' => []];
        }

        $rawHeader = array_shift($rows);
        $headerMap = [];
        foreach ($rawHeader as $idx => $colName) {
            $cleanName = strtolower(trim(str_replace(["\xEF\xBB\xBF", ' ', '_', '-'], '', $colName)));
            $headerMap[$cleanName] = $idx;
        }

        return [
            'headerMap' => $headerMap,
            'rows' => $rows,
        ];
    }

    protected function parseDateString(?string $tanggalRaw): string
    {
        if (empty($tanggalRaw)) {
            return date('Y-m-d');
        }

        $parsed = SpreadsheetReader::parseDate($tanggalRaw);
        if ($parsed) {
            return $parsed;
        }

        if (preg_match('/^\d{4}$/', trim((string) $tanggalRaw))) {
            return trim((string) $tanggalRaw).'-01-01';
        }

        try {
            return Carbon::parse($tanggalRaw)->format('Y-m-d');
        } catch (\Exception $e) {
            return date('Y-m-d');
        }
    }

    protected function findOrCreateBarang(?string $idBarang, ?string $namaAsset): Barang
    {
        $barang = null;

        if ($idBarang !== null && $idBarang !== '') {
            $barang = Barang::where('id', $idBarang)
                ->where(function ($q) {
                    $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
                })
                ->first();
        }

        if (! $barang && $namaAsset !== null && $namaAsset !== '') {
            $barang = Barang::where('nama_asset', $namaAsset)
                ->where(function ($q) {
                    $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
                })
                ->first();
        }

        if (! $barang) {
            $finalNamaAsset = ($namaAsset !== null && $namaAsset !== '') ? $namaAsset : 'Barang Tanpa Nama (Import)';
            $barang = Barang::create([
                'nama_asset' => $finalNamaAsset,
                'satuan' => 'Unit',
                'jenis_lokasi' => $this->lokasi,
                'lokasi' => $this->lokasi,
                'keterangan' => 'Dibuat otomatis dari import CSV/Excel '.$this->lokasi,
            ]);
        }

        return $barang;
    }

    public function barangTemplate()
    {
        $headers = ['id_barang', 'nama_barang', 'satuan', 'keterangan'];
        $sampleRows = $this->lokasi === 'Kantor'
            ? [
                ['25', 'Komputer - CPU', 'Unit', 'Fasilitas staf kantor'],
                ['32', 'Projector/LCD', 'Unit', 'Ruang Meeting Utama'],
            ]
            : [
                ['13', 'Topi Jepang', 'Unit', 'Stok gudang'],
                ['18', 'Egrek + Tangkai', 'Unit', 'ada 7 set di SAM 2'],
            ];

        return ExcelExporter::download("Template_Import_Barang_{$this->lokasi}.xlsx", $headers, $sampleRows, "Template Barang {$this->lokasi}");
    }

    public function barangImport(Request $request)
    {
        $parsed = $this->parseCsvFile($request);
        $headerMap = $parsed['headerMap'];
        $rows = $parsed['rows'];

        if (empty($rows)) {
            return redirect()->route("{$this->routePrefix}.barang")
                ->with('error', 'File CSV kosong atau format header tidak sesuai.');
        }

        $imported = 0;
        foreach ($rows as $row) {
            $id = isset($headerMap['idbarang']) ? ($row[$headerMap['idbarang']] ?? null) : null;
            if ($id === null && isset($headerMap['id'])) {
                $id = $row[$headerMap['id']] ?? null;
            }

            $nama = isset($headerMap['namabarang']) ? ($row[$headerMap['namabarang']] ?? null) : null;
            if ($nama === null && isset($headerMap['namaasset'])) {
                $nama = $row[$headerMap['namaasset']] ?? null;
            }
            if ($nama === null && isset($headerMap['nama'])) {
                $nama = $row[$headerMap['nama']] ?? null;
            }

            $satuan = isset($headerMap['satuan']) ? ($row[$headerMap['satuan']] ?? null) : null;
            $ket = isset($headerMap['keterangan']) ? ($row[$headerMap['keterangan']] ?? null) : null;

            if (empty($nama) && empty($id)) {
                continue;
            }

            $barang = null;
            if ($id && is_numeric($id)) {
                $barang = Barang::where('id', $id)
                    ->where(function ($q) {
                        $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
                    })->first();
            }

            if (! $barang && $nama) {
                $barang = Barang::where('nama_asset', $nama)
                    ->where(function ($q) {
                        $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
                    })->first();
            }

            $data = [
                'nama_asset' => $nama ?: ($barang ? $barang->nama_asset : 'Aset Tanpa Nama'),
                'satuan' => $satuan ?: ($barang ? $barang->satuan : 'Unit'),
                'jenis_lokasi' => $this->lokasi,
                'lokasi' => $this->lokasi,
                'keterangan' => $ket ?: ($barang ? $barang->keterangan : null),
            ];

            if ($barang) {
                $barang->update($data);
            } else {
                Barang::create($data);
            }
            $imported++;
        }

        return redirect()->route("{$this->routePrefix}.barang")
            ->with('success', "Berhasil mengimpor {$imported} data barang untuk {$this->lokasi}!");
    }

    /* =========================================================================
     * 2. PEMBELIAN TAB
     * ========================================================================= */
    public function pembelianIndex(Request $request)
    {
        $search = $request->query('search');
        $selectedYear = $request->query('year');
        $month = $request->query('month');
        $barangId = $request->query('barang_id');

        $allYears = Pembelian::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
            ->selectRaw('YEAR(tanggal_pembelian) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        $currentYear = (int) date('Y');
        if (! in_array($currentYear, $allYears)) {
            array_unshift($allYears, $currentYear);
        }

        $query = Pembelian::with('barang')
            ->whereHas('barang', function ($q) {
                $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
            });

        if ($selectedYear && $selectedYear !== 'all') {
            $query->whereYear('tanggal_pembelian', $selectedYear);
        }

        if ($month && $month !== 'all') {
            $query->whereMonth('tanggal_pembelian', $month);
        }

        if ($barangId && $barangId !== 'all') {
            $query->where('barang_id', $barangId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_pesanan', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('barang', function ($b) use ($search) {
                        $b->where('nama_asset', 'like', "%{$search}%");
                    });
            });
        }

        $pembelianList = $query->orderBy('tanggal_pembelian', 'desc')->paginate(10)->withQueryString();

        $allBarang = Barang::where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi)->orderBy('nama_asset', 'asc')->get();

        $availableRooms = StockOpname::whereNotNull('lokasi_spesifik')
            ->where('lokasi_spesifik', '!=', '')
            ->distinct()
            ->pluck('lokasi_spesifik')
            ->sort()
            ->values()
            ->toArray();

        return view("Administrasi.Pages.Inventaris.{$this->viewPrefix}_pembelian", [
            'pembelianList' => $pembelianList,
            'allBarang' => $allBarang,
            'availableRooms' => $availableRooms,
            'years' => $allYears,
            'selectedYear' => $selectedYear,
            'month' => $month,
            'search' => $search,
            'barangId' => $barangId,
            'activeTab' => 'pembelian',
        ]);
    }

    public function pembelianStore(Request $request)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'tanggal_pembelian' => 'required|date',
            'no_surat_pesanan' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        Pembelian::create($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;

        return redirect()->route("{$this->routePrefix}.pembelian", ['year' => $year])
            ->with('success', "Data pembelian {$this->lokasi} berhasil ditambahkan!");
    }

    public function pembelianUpdate(Request $request, $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'tanggal_pembelian' => 'required|date',
            'no_surat_pesanan' => 'nullable|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $pembelian->update($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;

        return redirect()->route("{$this->routePrefix}.pembelian", ['year' => $year])
            ->with('success', "Data pembelian {$this->lokasi} berhasil diperbarui!");
    }

    public function pembelianDestroy($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $year = Carbon::parse($pembelian->tanggal_pembelian)->year;
        $pembelian->delete();

        return redirect()->route("{$this->routePrefix}.pembelian", ['year' => $year])
            ->with('success', "Data pembelian {$this->lokasi} berhasil dihapus!");
    }

    public function pembelianTemplate()
    {
        $headers = ['id_pembelian', 'id_barang', 'nama_barang', 'tanggal_pembelian', 'no_surat_pesanan', 'jumlah', 'keterangan'];
        $sampleRows = $this->lokasi === 'Kantor'
            ? [
                ['1', '25', 'Komputer - CPU', date('Y-m-d'), 'PO/KTR/2026/001', '5', 'Pengadaan PC baru'],
                ['2', '27', 'Kursi Merah', date('Y-m-d'), 'PO/KTR/2026/002', '10', 'Kursi meeting baru'],
            ]
            : [
                ['1', '13', 'Topi Jepang', date('Y-m-d'), 'PO/GDG/2026/001', '50', 'Pengadaan APD rutin'],
                ['2', '18', 'Egrek + Tangkai', date('Y-m-d'), 'PO/GDG/2026/002', '20', 'Perlengkapan panen'],
            ];

        return ExcelExporter::download("Template_Import_Pembelian_{$this->lokasi}.xlsx", $headers, $sampleRows, "Template Pembelian {$this->lokasi}");
    }

    public function pembelianImport(Request $request)
    {
        $parsed = $this->parseCsvFile($request);
        $headerMap = $parsed['headerMap'];
        $rows = $parsed['rows'];

        if (empty($rows)) {
            return redirect()->route("{$this->routePrefix}.pembelian")
                ->with('error', 'File CSV kosong atau format header tidak sesuai.');
        }

        $imported = 0;
        foreach ($rows as $row) {
            $idPembelian = isset($headerMap['idpembelian']) ? ($row[$headerMap['idpembelian']] ?? null) : null;
            if ($idPembelian === null && isset($headerMap['id'])) {
                $idPembelian = $row[$headerMap['id']] ?? null;
            }

            $idBarang = isset($headerMap['idbarang']) ? ($row[$headerMap['idbarang']] ?? null) : null;
            $namaBarang = isset($headerMap['namabarang']) ? ($row[$headerMap['namabarang']] ?? null) : null;
            if ($namaBarang === null && isset($headerMap['namaasset'])) {
                $namaBarang = $row[$headerMap['namaasset']] ?? null;
            }

            $tglRaw = isset($headerMap['tanggalpembelian']) ? ($row[$headerMap['tanggalpembelian']] ?? null) : null;
            if ($tglRaw === null && isset($headerMap['tanggal'])) {
                $tglRaw = $row[$headerMap['tanggal']] ?? null;
            }

            $noSP = isset($headerMap['nosuratpesanan']) ? ($row[$headerMap['nosuratpesanan']] ?? null) : null;
            if ($noSP === null && isset($headerMap['suratpesanan'])) {
                $noSP = $row[$headerMap['suratpesanan']] ?? null;
            }
            if ($noSP === null && isset($headerMap['nopo'])) {
                $noSP = $row[$headerMap['nopo']] ?? null;
            }

            $jumlah = isset($headerMap['jumlah']) ? (int) ($row[$headerMap['jumlah']] ?? 0) : 0;
            if ($jumlah <= 0 && isset($headerMap['qty'])) {
                $jumlah = (int) ($row[$headerMap['qty']] ?? 0);
            }

            $ket = isset($headerMap['keterangan']) ? ($row[$headerMap['keterangan']] ?? null) : null;

            if (empty($namaBarang) && empty($idBarang)) {
                continue;
            }

            $barang = $this->findOrCreateBarang($idBarang, $namaBarang);
            $tgl = $this->parseDateString($tglRaw);

            $pembelian = null;
            if ($idPembelian && is_numeric($idPembelian)) {
                $pembelian = Pembelian::find($idPembelian);
            }

            $data = [
                'barang_id' => $barang->id,
                'tanggal_pembelian' => $tgl,
                'no_surat_pesanan' => $noSP,
                'jumlah' => $jumlah > 0 ? $jumlah : 1,
                'keterangan' => $ket,
            ];

            if ($pembelian) {
                $pembelian->update($data);
            } else {
                Pembelian::create($data);
            }
            $imported++;
        }

        return redirect()->route("{$this->routePrefix}.pembelian")
            ->with('success', "Berhasil mengimpor {$imported} data pembelian untuk {$this->lokasi}!");
    }

    /* =========================================================================
     * 3. STOCK OPNAME TAB (SESSIONS)
     * ========================================================================= */
    public function stockOpnameIndex(Request $request)
    {
        $search = $request->query('search');

        $query = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
            ->select(
                'tanggal_stock_opname',
                DB::raw('COUNT(id) as total_items'),
                DB::raw('SUM(grand_total) as total_qty'),
                DB::raw('SUM(jumlah_baik) as total_baik'),
                DB::raw('SUM(jumlah_rusak) as total_rusak')
            )
            ->groupBy('tanggal_stock_opname')
            ->orderBy('tanggal_stock_opname', 'desc');

        if ($search) {
            $query->where('tanggal_stock_opname', 'like', "%{$search}%");
        }

        $sessions = $query->paginate(10)->withQueryString();

        $allBarang = Barang::where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi)->orderBy('nama_asset', 'asc')->get();

        return view("Administrasi.Pages.Inventaris.{$this->viewPrefix}_stock_opname", [
            'sessions' => $sessions,
            'allBarang' => $allBarang,
            'search' => $search,
            'activeTab' => 'stock_opname',
        ]);
    }

    public function stockOpnameSessionStore(Request $request)
    {
        $validated = $request->validate([
            'tanggal_stock_opname' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $exists = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })->where('tanggal_stock_opname', $validated['tanggal_stock_opname'])->exists();

        if ($exists) {
            return redirect()->route("{$this->routePrefix}.stock-opname")
                ->with('error', 'Sesi Stock Opname pada tanggal ini sudah ada!');
        }

        $allBarang = Barang::where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi)->get();

        if ($allBarang->isEmpty()) {
            return redirect()->route("{$this->routePrefix}.stock-opname")
                ->with('error', "Belum ada master barang {$this->lokasi}. Tambahkan barang terlebih dahulu!");
        }

        $inserted = 0;
        foreach ($allBarang as $b) {
            $totalBeli = (int) Pembelian::where('barang_id', $b->id)
                ->where('tanggal_pembelian', '<=', $validated['tanggal_stock_opname'])
                ->sum('jumlah');

            StockOpname::create([
                'barang_id' => $b->id,
                'tanggal_stock_opname' => $validated['tanggal_stock_opname'],
                'lokasi_spesifik' => $this->lokasi.' Utama',
                'jumlah_baik' => $totalBeli,
                'jumlah_rusak' => 0,
                'grand_total' => $totalBeli,
                'keterangan' => $validated['keterangan'] ?? 'Inisialisasi sesi otomatis',
            ]);
            $inserted++;
        }

        return redirect()->route("{$this->routePrefix}.stock-opname.detail", ['date' => $validated['tanggal_stock_opname']])
            ->with('success', "Sesi Stock Opname {$this->lokasi} berhasil dibuat dengan {$inserted} item barang!");
    }

    public function stockOpnameSessionUpdate(Request $request, $date)
    {
        $validated = $request->validate([
            'new_tanggal_stock_opname' => 'required|date',
        ]);

        $newDate = $validated['new_tanggal_stock_opname'];

        if ($newDate !== $date) {
            $exists = StockOpname::whereHas('barang', function ($q) {
                $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
            })->where('tanggal_stock_opname', $newDate)->exists();

            if ($exists) {
                return redirect()->route("{$this->routePrefix}.stock-opname")
                    ->with('error', 'Sesi Stock Opname pada tanggal tujuan sudah ada!');
            }

            StockOpname::whereHas('barang', function ($q) {
                $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
            })
                ->where('tanggal_stock_opname', $date)
                ->update(['tanggal_stock_opname' => $newDate]);
        }

        return redirect()->route("{$this->routePrefix}.stock-opname")
            ->with('success', 'Tanggal sesi Stock Opname berhasil diperbarui!');
    }

    public function stockOpnameSessionDestroy($date)
    {
        $items = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })->where('tanggal_stock_opname', $date)->get();

        foreach ($items as $item) {
            if ($item->foto_kerusakan && Storage::disk('public')->exists($item->foto_kerusakan)) {
                Storage::disk('public')->delete($item->foto_kerusakan);
            }
            $item->delete();
        }

        return redirect()->route("{$this->routePrefix}.stock-opname")
            ->with('success', 'Sesi Stock Opname dan seluruh datanya berhasil dihapus!');
    }

    /* =========================================================================
     * 4. STOCK OPNAME DETAIL TAB
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

        if ($search) {
            $query->whereHas('barang', function ($b) use ($search) {
                $b->where('nama_asset', 'like', "%{$search}%");
            });
        }

        if ($lokasiSpesifikFilter && $lokasiSpesifikFilter !== 'all') {
            $query->where('lokasi_spesifik', $lokasiSpesifikFilter);
        }

        $items = $query->paginate(15)->withQueryString();

        $allRooms = StockOpname::whereHas('barang', function ($q) {
            $q->where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi);
        })
            ->where('tanggal_stock_opname', $date)
            ->whereNotNull('lokasi_spesifik')
            ->distinct()
            ->pluck('lokasi_spesifik')
            ->toArray();

        $allBarang = Barang::where('jenis_lokasi', $this->lokasi)->orWhere('lokasi', $this->lokasi)->orderBy('nama_asset', 'asc')->get();

        return view("Administrasi.Pages.Inventaris.{$this->viewPrefix}_stock_opname_detail", [
            'date' => $date,
            'items' => $items,
            'allBarang' => $allBarang,
            'allRooms' => $allRooms,
            'search' => $search,
            'lokasiSpesifikFilter' => $lokasiSpesifikFilter,
            'activeTab' => 'stock_opname',
        ]);
    }

    public function stockOpnameItemStore(Request $request, $date)
    {
        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'lokasi_spesifik' => 'nullable|string|max:255',
            'jumlah_baik' => 'required|integer|min:0',
            'jumlah_rusak' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto_kerusakan')) {
            $fotoPath = $request->file('foto_kerusakan')->store('inventaris', 'public');
        }

        $baik = (int) $validated['jumlah_baik'];
        $rusak = (int) $validated['jumlah_rusak'];

        StockOpname::create([
            'barang_id' => $validated['barang_id'],
            'tanggal_stock_opname' => $date,
            'lokasi_spesifik' => $validated['lokasi_spesifik'] ?? null,
            'jumlah_baik' => $baik,
            'jumlah_rusak' => $rusak,
            'grand_total' => $baik + $rusak,
            'keterangan' => $validated['keterangan'] ?? null,
            'foto_kerusakan' => $fotoPath,
        ]);

        return redirect()->route("{$this->routePrefix}.stock-opname.detail", ['date' => $date])
            ->with('success', "Data aset berhasil ditambahkan ke Stock Opname {$this->lokasi}!");
    }

    public function stockOpnameItemUpdate(Request $request, $id)
    {
        $item = StockOpname::findOrFail($id);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'lokasi_spesifik' => 'nullable|string|max:255',
            'jumlah_baik' => 'required|integer|min:0',
            'jumlah_rusak' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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

        return redirect()->route("{$this->routePrefix}.stock-opname.detail", ['date' => $item->tanggal_stock_opname->format('Y-m-d')])
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

        return redirect()->route("{$this->routePrefix}.stock-opname.detail", ['date' => $date])
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

        $filename = "Stock_Opname_{$this->lokasi}_{$date}.xlsx";

        return ExcelExporter::download($filename, $headers, $dataRows, "Stock Opname {$this->lokasi}");
    }

    public function stockOpnameTemplate()
    {
        $headers = ['id_stock_opname', 'id_barang', 'nama_barang', 'tanggal_stock_opname', 'lokasi_spesifik', 'jumlah_baik', 'jumlah_rusak', 'keterangan'];
        $sampleRows = $this->lokasi === 'Kantor'
            ? [
                ['1', '25', 'Komputer - CPU', date('Y-m-d'), 'Ruang Staf Admin', '5', '1', 'Kondisi operasional'],
                ['2', '27', 'Kursi Merah', date('Y-m-d'), 'Ruang Meeting', '20', '2', '2 unit butuh perbaikan'],
            ]
            : [
                ['1', '13', 'Topi Jepang', date('Y-m-d'), 'Gudang Utama', '100', '0', 'Stok utuh'],
                ['2', '18', 'Egrek + Tangkai', date('Y-m-d'), 'SAM 2', '7', '0', '7 set di SAM 2'],
            ];

        return ExcelExporter::download("Template_Import_Stock_Opname_{$this->lokasi}.xlsx", $headers, $sampleRows, "Template Stock Opname {$this->lokasi}");
    }

    public function stockOpnameImport(Request $request)
    {
        $parsed = $this->parseCsvFile($request);
        $headerMap = $parsed['headerMap'];
        $rows = $parsed['rows'];

        if (empty($rows)) {
            return redirect()->back()->with('error', 'File CSV kosong atau format header tidak sesuai.');
        }

        $imported = 0;
        foreach ($rows as $row) {
            $idSO = isset($headerMap['idstockopname']) ? ($row[$headerMap['idstockopname']] ?? null) : null;
            if ($idSO === null && isset($headerMap['id'])) {
                $idSO = $row[$headerMap['id']] ?? null;
            }

            $idBarang = isset($headerMap['idbarang']) ? ($row[$headerMap['idbarang']] ?? null) : null;
            $namaBarang = isset($headerMap['namabarang']) ? ($row[$headerMap['namabarang']] ?? null) : null;
            if ($namaBarang === null && isset($headerMap['namaasset'])) {
                $namaBarang = $row[$headerMap['namaasset']] ?? null;
            }

            $tglRaw = isset($headerMap['tanggalstockopname']) ? ($row[$headerMap['tanggalstockopname']] ?? null) : null;
            if ($tglRaw === null && isset($headerMap['tanggal'])) {
                $tglRaw = $row[$headerMap['tanggal']] ?? null;
            }

            $lokasiSpesifik = isset($headerMap['lokasispesifik']) ? ($row[$headerMap['lokasispesifik']] ?? null) : null;
            if ($lokasiSpesifik === null && isset($headerMap['ruangan'])) {
                $lokasiSpesifik = $row[$headerMap['ruangan']] ?? null;
            }

            $baik = isset($headerMap['jumlahbaik']) ? (int) ($row[$headerMap['jumlahbaik']] ?? 0) : 0;
            if ($baik === 0 && isset($headerMap['baik'])) {
                $baik = (int) ($row[$headerMap['baik']] ?? 0);
            }

            $rusak = isset($headerMap['jumlahrusak']) ? (int) ($row[$headerMap['jumlahrusak']] ?? 0) : 0;
            if ($rusak === 0 && isset($headerMap['rusak'])) {
                $rusak = (int) ($row[$headerMap['rusak']] ?? 0);
            }

            $ket = isset($headerMap['keterangan']) ? ($row[$headerMap['keterangan']] ?? null) : null;

            if (empty($namaBarang) && empty($idBarang)) {
                continue;
            }

            $barang = $this->findOrCreateBarang($idBarang, $namaBarang);
            $tgl = $this->parseDateString($tglRaw);

            $so = null;
            if ($idSO && is_numeric($idSO)) {
                $so = StockOpname::find($idSO);
            }

            $data = [
                'barang_id' => $barang->id,
                'tanggal_stock_opname' => $tgl,
                'lokasi_spesifik' => $lokasiSpesifik ?: $this->lokasi.' Utama',
                'jumlah_baik' => $baik,
                'jumlah_rusak' => $rusak,
                'grand_total' => $baik + $rusak,
                'keterangan' => $ket,
            ];

            if ($so) {
                $so->update($data);
            } else {
                StockOpname::create($data);
            }
            $imported++;
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data Stock Opname {$this->lokasi}!");
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
            $msg = "Seluruh data Barang inventaris {$this->lokasi} ({$deleted} data) berhasil dihapus.";
        });

        return redirect()->route("{$this->routePrefix}.barang")->with('success', $msg);
    }

    public function pembelianDestroyAll()
    {
        $barangIds = Barang::where('jenis_lokasi', $this->lokasi)
            ->orWhere('lokasi', $this->lokasi)
            ->pluck('id');
        $deleted = Pembelian::whereIn('barang_id', $barangIds)->delete();

        return redirect()->route("{$this->routePrefix}.pembelian")
            ->with('success', "Seluruh data Pembelian inventaris {$this->lokasi} ({$deleted} data) berhasil dihapus.");
    }

    public function stockOpnameDestroyAll()
    {
        $barangIds = Barang::where('jenis_lokasi', $this->lokasi)
            ->orWhere('lokasi', $this->lokasi)
            ->pluck('id');
        $deleted = StockOpname::whereIn('barang_id', $barangIds)->delete();

        return redirect()->route("{$this->routePrefix}.stock-opname")
            ->with('success', "Seluruh data Stock Opname inventaris {$this->lokasi} ({$deleted} data) berhasil dihapus.");
    }
}
