<?php

namespace App\Http\Controllers\Inventaris;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Inventaris\Barang;
use App\Models\Inventaris\Pembelian;
use App\Models\Inventaris\StockOpname;
use App\Services\ExcelExporter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InventarisController extends Controller
{
    /* =========================================================================
     * PRIVATE HELPERS (DRY OPTIMIZATION)
     * ========================================================================= */
    private function applyJenisLokasiFilter($query, ?string $jenisLokasiFilter, ?string $relation = null)
    {
        if ($jenisLokasiFilter && in_array($jenisLokasiFilter, ['Kantor', 'Gudang'], true)) {
            if ($relation) {
                $query->whereHas($relation, function ($q) use ($jenisLokasiFilter) {
                    $q->where('jenis_lokasi', $jenisLokasiFilter)->orWhere('lokasi', $jenisLokasiFilter);
                });
            } else {
                $query->where(function ($q) use ($jenisLokasiFilter) {
                    $q->where('jenis_lokasi', $jenisLokasiFilter)->orWhere('lokasi', $jenisLokasiFilter);
                });
            }
        }

        return $query;
    }

    private function parseCsvFile(Request $request): array
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            return [];
        }

        $rawHeaderLine = fgets($handle);
        $rawHeaderLine = str_replace("\xEF\xBB\xBF", '', $rawHeaderLine);
        rewind($handle);

        $delimiter = (strpos($rawHeaderLine, ';') !== false) ? ';' : ',';

        $rawHeader = fgetcsv($handle, 1000, $delimiter);
        $headerMap = [];
        if ($rawHeader) {
            foreach ($rawHeader as $idx => $colName) {
                $cleanName = strtolower(trim(str_replace(["\xEF\xBB\xBF", ' ', '_', '-'], '', $colName)));
                $headerMap[$cleanName] = $idx;
            }
        }

        $rows = [];
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (! empty($data) && count($data) >= 1) {
                $rows[] = $data;
            }
        }
        fclose($handle);

        return [
            'headerMap' => $headerMap,
            'rows' => $rows,
        ];
    }

    private function parseDateString(?string $tanggalRaw): string
    {
        if (empty($tanggalRaw)) {
            return date('Y-m-d');
        }

        if (preg_match('/^\d{4}$/', $tanggalRaw)) {
            return $tanggalRaw.'-01-01';
        }

        try {
            return Carbon::parse($tanggalRaw)->format('Y-m-d');
        } catch (\Exception $e) {
            return date('Y-m-d');
        }
    }

    private function findOrCreateBarang(?string $idBarang, ?string $namaAsset, ?string $jenisLokasiRow = null, string $defaultLokasi = 'auto'): Barang
    {
        $barang = null;
        if (! empty($idBarang) && is_numeric($idBarang)) {
            $barang = Barang::find((int) $idBarang);
        }

        if (! $barang && ! empty($namaAsset)) {
            if (is_numeric($namaAsset)) {
                $barang = Barang::find((int) $namaAsset);
            }
            if (! $barang) {
                $barang = Barang::where('nama_asset', $namaAsset)->first();
            }
        }

        if (! $barang) {
            $finalLokasi = $jenisLokasiRow ?: ($defaultLokasi === 'auto' ? 'Kantor' : $defaultLokasi);
            $finalNama = ! empty($namaAsset) ? $namaAsset : (! empty($idBarang) ? "Asset #{$idBarang}" : 'Asset Baru');
            $attr = [
                'nama_asset' => $finalNama,
                'satuan' => 'Unit',
                'jenis_lokasi' => $finalLokasi,
                'lokasi' => $finalLokasi,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (! empty($idBarang) && is_numeric($idBarang)) {
                $attr['id'] = (int) $idBarang;
                DB::table('barang')->insert($attr);
                $barang = Barang::find((int) $idBarang);
            } else {
                $barang = Barang::create($attr);
            }
        }

        return $barang;
    }

    /* =========================================================================
     * 1. BARANG TAB (GUDANG & KANTOR UNIFIED)
     * ========================================================================= */
    public function barangIndex(Request $request)
    {
        $search = $request->query('search');
        $jenisLokasiFilter = $request->query('jenis_lokasi');

        $query = Barang::query();
        $this->applyJenisLokasiFilter($query, $jenisLokasiFilter);

        if ($search) {
            $query->where('nama_asset', 'like', "%{$search}%");
        }

        $barangList = $query->orderBy('nama_asset', 'asc')->paginate(15)->withQueryString();

        return view('Administrasi.Pages.Inventaris.inventaris_barang', [
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

        return redirect()->back()->with('success', 'Aset barang berhasil ditambahkan!');
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

        return redirect()->back()->with('success', 'Data aset barang berhasil diperbarui!');
    }

    public function barangDestroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->back()->with('success', 'Data aset barang berhasil dihapus!');
    }

    public function barangTemplate()
    {
        $headers = ['nama_asset', 'satuan', 'jenis_lokasi', 'keterangan'];
        $sampleRows = [
            ['Kursi Kerja Ergonomis', 'Unit', 'Kantor', 'Fasilitas staf'],
            ['Semen Padang 50kg', 'Sak', 'Gudang', 'Stok material'],
        ];

        return ExcelExporter::download('Template_Import_Barang.xlsx', $headers, $sampleRows, 'Template Barang');
    }

    public function barangImport(Request $request)
    {
        $defaultLokasi = $request->input('jenis_lokasi_default', 'auto');
        $csvData = $this->parseCsvFile($request);
        if (empty($csvData)) {
            return redirect()->back()->withErrors(['file' => 'Gagal membaca file import.']);
        }

        $headerMap = $csvData['headerMap'];
        $hasHeader = isset($headerMap['idbarang']) || isset($headerMap['id']) || isset($headerMap['namaasset']) || isset($headerMap['namabarang']) || isset($headerMap['nama']) || isset($headerMap['barang']);
        $rows = $csvData['rows'];

        $imported = 0;
        foreach ($rows as $data) {
            $idBarang = null;
            $namaAsset = null;
            $satuan = 'Unit';
            $jenisLokasi = null;
            $keterangan = null;

            if ($hasHeader) {
                if (isset($headerMap['idbarang']) && isset($data[$headerMap['idbarang']])) {
                    $idBarang = trim($data[$headerMap['idbarang']]);
                } elseif (isset($headerMap['id']) && isset($data[$headerMap['id']])) {
                    $idBarang = trim($data[$headerMap['id']]);
                }

                foreach (['namaasset', 'namabarang', 'nama', 'barang'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $namaAsset = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                if (isset($headerMap['satuan']) && isset($data[$headerMap['satuan']])) {
                    $satuan = trim($data[$headerMap['satuan']]);
                }

                $rawLokasi = isset($headerMap['jenislokasi']) && isset($data[$headerMap['jenislokasi']])
                    ? trim($data[$headerMap['jenislokasi']])
                    : (isset($headerMap['lokasi']) && isset($data[$headerMap['lokasi']]) ? trim($data[$headerMap['lokasi']]) : null);
                if ($rawLokasi && in_array(ucfirst(strtolower($rawLokasi)), ['Kantor', 'Gudang'], true)) {
                    $jenisLokasi = ucfirst(strtolower($rawLokasi));
                }

                if (isset($headerMap['keterangan']) && isset($data[$headerMap['keterangan']])) {
                    $keterangan = trim($data[$headerMap['keterangan']]);
                }
            } else {
                $col0 = trim($data[0] ?? '');
                if (is_numeric($col0) && isset($data[1])) {
                    $idBarang = $col0;
                    $namaAsset = trim($data[1]);
                    $satuan = isset($data[2]) && ! empty(trim($data[2])) ? trim($data[2]) : 'Unit';
                    $keterangan = isset($data[3]) ? trim($data[3]) : null;
                } else {
                    $namaAsset = $col0;
                    $satuan = isset($data[1]) && ! empty(trim($data[1])) ? trim($data[1]) : 'Unit';
                    if (isset($data[2]) && in_array(ucfirst(strtolower(trim($data[2]))), ['Kantor', 'Gudang'], true)) {
                        $jenisLokasi = ucfirst(strtolower(trim($data[2])));
                        $keterangan = isset($data[3]) ? trim($data[3]) : null;
                    } else {
                        $keterangan = isset($data[2]) ? trim($data[2]) : null;
                    }
                }
            }

            if (($namaAsset && in_array(strtolower($namaAsset), ['nama_asset', 'nama asset', 'nama barang', 'nama_barang', 'id_barang', 'id'], true)) ||
                ($idBarang && ! is_numeric($idBarang) && in_array(strtolower($idBarang), ['id_barang', 'id', 'id_pembelian'], true))) {
                continue;
            }

            if (empty($namaAsset) && empty($idBarang)) {
                continue;
            }

            if (! $jenisLokasi) {
                $jenisLokasi = in_array($defaultLokasi, ['Kantor', 'Gudang'], true) ? $defaultLokasi : 'Kantor';
            }

            $barang = null;
            if (! empty($idBarang) && is_numeric($idBarang)) {
                $barang = Barang::find((int) $idBarang);
            }
            if (! $barang && ! empty($namaAsset)) {
                $barang = Barang::where('nama_asset', $namaAsset)->first();
            }

            if ($barang) {
                $barang->update([
                    'nama_asset' => ! empty($namaAsset) ? $namaAsset : $barang->nama_asset,
                    'satuan' => ! empty($satuan) ? $satuan : $barang->satuan,
                    'jenis_lokasi' => $jenisLokasi ?: $barang->jenis_lokasi,
                    'lokasi' => $jenisLokasi ?: $barang->lokasi,
                    'keterangan' => $keterangan !== null ? $keterangan : $barang->keterangan,
                ]);
            } else {
                $attributes = [
                    'nama_asset' => ! empty($namaAsset) ? $namaAsset : "Asset #{$idBarang}",
                    'satuan' => ! empty($satuan) ? $satuan : 'Unit',
                    'jenis_lokasi' => $jenisLokasi,
                    'lokasi' => $jenisLokasi,
                    'keterangan' => $keterangan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (! empty($idBarang) && is_numeric($idBarang)) {
                    $attributes['id'] = (int) $idBarang;
                    DB::table('barang')->insert($attributes);
                } else {
                    Barang::create($attributes);
                }
            }

            $imported++;
        }

        $fileName = $request->file('file')?->getClientOriginalName() ?? 'file.csv';
        ActivityLog::log(
            'Import',
            'Inventaris Barang',
            "Mengimport {$imported} data barang inventaris dari file ".$fileName,
            ['filename' => $fileName, 'count' => $imported]
        );

        return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data barang!");
    }

    /* =========================================================================
     * 2. PEMBELIAN TAB (GUDANG & KANTOR UNIFIED)
     * ========================================================================= */
    public function pembelianIndex(Request $request)
    {
        $search = $request->query('search');
        $selectedYear = $request->query('year');
        $jenisLokasiFilter = $request->query('jenis_lokasi');
        $lokasiSpesifikFilter = $request->query('lokasi_spesifik');

        if (! $selectedYear) {
            $selectedYear = 'all';
        }

        $pembelianYearQuery = Pembelian::query();
        $this->applyJenisLokasiFilter($pembelianYearQuery, $jenisLokasiFilter, 'barang');
        $pembelianYears = $pembelianYearQuery
            ->selectRaw('YEAR(tanggal_pembelian) as yr')
            ->whereNotNull('tanggal_pembelian')
            ->pluck('yr')->map(fn ($y) => (int) $y)->toArray();

        $soYearQuery = StockOpname::query();
        $this->applyJenisLokasiFilter($soYearQuery, $jenisLokasiFilter, 'barang');
        $soYears = $soYearQuery
            ->selectRaw('YEAR(tanggal_stock_opname) as yr')
            ->whereNotNull('tanggal_stock_opname')
            ->pluck('yr')->map(fn ($y) => (int) $y)->toArray();

        $currentYear = (int) date('Y');
        $defaultYears = [2017, 2018, 2020, 2021, 2022, 2023, 2024, 2025, 2026, $currentYear];
        $allYears = array_values(array_unique(array_merge($defaultYears, $pembelianYears, $soYears)));
        sort($allYears);

        $query = Pembelian::with('barang');
        $this->applyJenisLokasiFilter($query, $jenisLokasiFilter, 'barang');

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

        $pembelianList = $query->orderByDesc('tanggal_pembelian')->paginate(15)->withQueryString();

        $kpiQuery = Pembelian::query();
        $this->applyJenisLokasiFilter($kpiQuery, $jenisLokasiFilter, 'barang');

        if ($selectedYear !== 'all' && $selectedYear !== 'Semua Tahun' && $selectedYear !== 'all_years') {
            $kpiQuery->whereYear('tanggal_pembelian', $selectedYear);
        }

        $totalJenisAset = (clone $kpiQuery)->distinct('barang_id')->count('barang_id');
        $totalBarangDibeli = (clone $kpiQuery)->sum('jumlah');

        $barangQuery = Barang::orderBy('nama_asset', 'asc');
        $this->applyJenisLokasiFilter($barangQuery, $jenisLokasiFilter);
        $allBarang = $barangQuery->get();

        $availableRooms = StockOpname::whereNotNull('lokasi_spesifik')
            ->where('lokasi_spesifik', '!=', '')
            ->distinct()
            ->pluck('lokasi_spesifik')
            ->sort()
            ->values()
            ->toArray();

        return view('Administrasi.Pages.Inventaris.inventaris_pembelian', [
            'pembelianList' => $pembelianList,
            'allBarang' => $allBarang,
            'availableRooms' => $availableRooms,
            'years' => $allYears,
            'selectedYear' => $selectedYear,
            'search' => $search,
            'jenisLokasiFilter' => $jenisLokasiFilter,
            'lokasiSpesifikFilter' => $lokasiSpesifikFilter,
            'totalJenisAset' => $totalJenisAset,
            'totalBarangDibeli' => $totalBarangDibeli,
            'activeTab' => 'pembelian',
        ]);
    }

    public function pembelianStore(Request $request)
    {
        $barang = Barang::findOrFail($request->input('barang_id'));
        $isKantor = in_array(strtolower($barang->jenis_lokasi ?? $barang->lokasi ?? ''), ['kantor'], true);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'tanggal_pembelian' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'lokasi_spesifik' => $isKantor ? 'required|string|max:255' : 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'lokasi_spesifik.required' => 'Lokasi spesifik / ruangan kantor wajib diisi untuk barang Kantor.',
        ]);

        if (! $isKantor) {
            $validated['lokasi_spesifik'] = null;
        }

        Pembelian::create($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;
        $redirectParams = ['year' => $year];
        if ($request->filled('jenis_lokasi')) {
            $redirectParams['jenis_lokasi'] = $request->input('jenis_lokasi');
        }

        return redirect()->route('administrasi.inventaris.pembelian', $redirectParams)
            ->with('success', 'Data pembelian aset berhasil disimpan!');
    }

    public function pembelianUpdate(Request $request, $id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $barang = Barang::findOrFail($request->input('barang_id'));
        $isKantor = in_array(strtolower($barang->jenis_lokasi ?? $barang->lokasi ?? ''), ['kantor'], true);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'tanggal_pembelian' => 'required|date',
            'jumlah' => 'required|integer|min:1',
            'lokasi_spesifik' => $isKantor ? 'required|string|max:255' : 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ], [
            'lokasi_spesifik.required' => 'Lokasi spesifik / ruangan kantor wajib diisi untuk barang Kantor.',
        ]);

        if (! $isKantor) {
            $validated['lokasi_spesifik'] = null;
        }

        $pembelian->update($validated);

        $year = Carbon::parse($validated['tanggal_pembelian'])->year;
        $redirectParams = ['year' => $year];
        if ($request->filled('jenis_lokasi')) {
            $redirectParams['jenis_lokasi'] = $request->input('jenis_lokasi');
        }

        return redirect()->route('administrasi.inventaris.pembelian', $redirectParams)
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
        $headers = ['nama_asset', 'tanggal_pembelian', 'jumlah', 'jenis_lokasi', 'lokasi_spesifik', 'keterangan'];
        $sampleRows = [
            ['Kursi Kerja Ergonomis', date('Y-m-d'), '5', 'Kantor', 'Planter', 'Pengadaan staf'],
            ['Semen Padang 50kg', date('Y-m-d'), '10', 'Gudang', '', 'Stok material gudang'],
        ];

        return ExcelExporter::download('Template_Import_Pembelian.xlsx', $headers, $sampleRows, 'Template Pembelian');
    }

    public function pembelianImport(Request $request)
    {
        $defaultLokasi = $request->input('jenis_lokasi_default', 'auto');
        $csvData = $this->parseCsvFile($request);
        if (empty($csvData)) {
            return redirect()->back()->withErrors(['file' => 'Gagal membaca file import.']);
        }

        $headerMap = $csvData['headerMap'];
        $hasHeader = isset($headerMap['idpembelian']) || isset($headerMap['idbarang']) || isset($headerMap['namaasset']) || isset($headerMap['nama']) || isset($headerMap['barang']) || isset($headerMap['tanggalpembelian']) || isset($headerMap['tanggal']) || isset($headerMap['tahun']);
        $rows = $csvData['rows'];

        $imported = 0;
        foreach ($rows as $data) {
            $idBarang = null;
            $namaAsset = null;
            $tanggalRaw = null;
            $jumlahRaw = null;
            $jenisLokasiRow = null;
            $lokasiSpesifik = null;
            $keterangan = null;

            if ($hasHeader) {
                if (isset($headerMap['idbarang']) && isset($data[$headerMap['idbarang']])) {
                    $idBarang = trim($data[$headerMap['idbarang']]);
                }

                foreach (['namaasset', 'namabarang', 'nama', 'barang'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $namaAsset = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                foreach (['tanggalpembelian', 'tanggal', 'tahunpembelian', 'tahun'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $tanggalRaw = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                foreach (['jumlah', 'qty', 'jumlahpembelian'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $jumlahRaw = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                foreach (['jenislokasi', 'lokasi'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $rawLokasi = trim($data[$headerMap[$k]]);
                        if (in_array(ucfirst(strtolower($rawLokasi)), ['Kantor', 'Gudang'], true)) {
                            $jenisLokasiRow = ucfirst(strtolower($rawLokasi));
                        }
                        break;
                    }
                }

                foreach (['lokasispesifik', 'ruangan'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $lokasiSpesifik = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                if (isset($headerMap['keterangan']) && isset($data[$headerMap['keterangan']])) {
                    $keterangan = trim($data[$headerMap['keterangan']]);
                }
            } else {
                if (isset($data[0])) {
                    $namaAsset = trim($data[0]);
                }
                if (isset($data[1])) {
                    $tanggalRaw = trim($data[1]);
                }
                if (isset($data[2])) {
                    $jumlahRaw = trim($data[2]);
                }
                if (isset($data[3])) {
                    if (in_array(ucfirst(strtolower(trim($data[3]))), ['Kantor', 'Gudang'], true)) {
                        $jenisLokasiRow = ucfirst(strtolower(trim($data[3])));
                        if (isset($data[4])) {
                            $lokasiSpesifik = trim($data[4]);
                        }
                        if (isset($data[5])) {
                            $keterangan = trim($data[5]);
                        }
                    } else {
                        $lokasiSpesifik = trim($data[3]);
                        if (isset($data[4])) {
                            $keterangan = trim($data[4]);
                        }
                    }
                }
            }

            if (($namaAsset && in_array(strtolower($namaAsset), ['nama_asset', 'nama asset', 'nama barang', 'id_barang', 'id_pembelian'], true)) ||
                ($idBarang && ! is_numeric($idBarang) && in_array(strtolower($idBarang), ['id_barang', 'id_pembelian', 'id'], true))) {
                continue;
            }

            if (empty($idBarang) && empty($namaAsset)) {
                continue;
            }

            $barang = $this->findOrCreateBarang($idBarang, $namaAsset, $jenisLokasiRow, $defaultLokasi);
            $tanggal = $this->parseDateString($tanggalRaw);
            $jumlah = max(1, (int) $jumlahRaw);

            $pembelianAttr = [
                'barang_id' => $barang->id,
                'tanggal_pembelian' => $tanggal,
                'jumlah' => $jumlah,
                'lokasi_spesifik' => $lokasiSpesifik,
                'keterangan' => $keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (! empty($idPembelian) && is_numeric($idPembelian)) {
                $pembelian = Pembelian::find((int) $idPembelian);
                if ($pembelian) {
                    $pembelian->update($pembelianAttr);
                } else {
                    $pembelianAttr['id'] = (int) $idPembelian;
                    DB::table('pembelian')->insert($pembelianAttr);
                }
            } else {
                Pembelian::create($pembelianAttr);
            }

            $imported++;
        }

        $fileName = $request->file('file')?->getClientOriginalName() ?? 'file.csv';
        ActivityLog::log(
            'Import',
            'Inventaris Pembelian',
            "Mengimport {$imported} data pembelian inventaris dari file ".$fileName,
            ['filename' => $fileName, 'count' => $imported]
        );

        return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data pembelian!");
    }

    /* =========================================================================
     * 3. STOCK OPNAME SESSIONS LIST & DETAIL
     * ========================================================================= */
    public function stockOpnameIndex(Request $request)
    {
        $jenisLokasiFilter = $request->query('jenis_lokasi');

        $query = StockOpname::query();
        $this->applyJenisLokasiFilter($query, $jenisLokasiFilter, 'barang');

        $sessions = $query->select(
            'tanggal_stock_opname',
            DB::raw('MAX(status) as status'),
            DB::raw('MAX(catatan_sesi) as catatan_sesi'),
            DB::raw('COUNT(DISTINCT barang_id) as total_aset_diperiksa'),
            DB::raw('SUM(jumlah_rusak) as total_masalah')
        )
            ->groupBy('tanggal_stock_opname')
            ->orderByDesc('tanggal_stock_opname')
            ->get();

        return view('Administrasi.Pages.Inventaris.inventaris_stock_opname', [
            'sessions' => $sessions,
            'jenisLokasiFilter' => $jenisLokasiFilter,
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

        $existing = StockOpname::where('tanggal_stock_opname', $validated['tanggal_stock_opname'])->first();

        $redirectParams = ['date' => $validated['tanggal_stock_opname']];
        if ($request->filled('jenis_lokasi')) {
            $redirectParams['jenis_lokasi'] = $request->input('jenis_lokasi');
        }

        if ($existing) {
            return redirect()->route('administrasi.inventaris.stock-opname.detail', $redirectParams)
                ->with('info', 'Sesi Stock Opname untuk tanggal tersebut sudah ada.');
        }

        $barangQuery = Barang::query();
        if ($request->filled('jenis_lokasi')) {
            $this->applyJenisLokasiFilter($barangQuery, $request->input('jenis_lokasi'));
        }
        $firstBarang = $barangQuery->first() ?? Barang::first();

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

        return redirect()->route('administrasi.inventaris.stock-opname.detail', $redirectParams)
            ->with('success', 'Sesi Stock Opname berhasil dibuat!');
    }

    public function stockOpnameSessionUpdate(Request $request, $date)
    {
        $validated = $request->validate([
            'status' => 'required|in:Selesai,Draf',
            'catatan_sesi' => 'nullable|string',
        ]);

        StockOpname::where('tanggal_stock_opname', $date)
            ->update([
                'status' => $validated['status'],
                'catatan_sesi' => $validated['catatan_sesi'],
            ]);

        return redirect()->route('administrasi.inventaris.stock-opname.detail', ['date' => $date])
            ->with('success', 'Informasi sesi Stock Opname berhasil diperbarui!');
    }

    public function stockOpnameSessionDestroy($date)
    {
        StockOpname::where('tanggal_stock_opname', $date)->delete();

        return redirect()->route('administrasi.inventaris.stock-opname')
            ->with('success', 'Sesi Stock Opname tanggal '.Carbon::parse($date)->translatedFormat('d F Y').' berhasil dihapus!');
    }

    public function stockOpnameDetail(Request $request, $date)
    {
        $search = $request->query('search');
        $jenisLokasiFilter = $request->query('jenis_lokasi');
        $lokasiSpesifikFilter = $request->query('lokasi_spesifik');

        $query = StockOpname::with('barang')
            ->where('tanggal_stock_opname', $date);

        $this->applyJenisLokasiFilter($query, $jenisLokasiFilter, 'barang');

        if ($lokasiSpesifikFilter) {
            $query->where('lokasi_spesifik', $lokasiSpesifikFilter);
        }

        if ($search) {
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where('nama_asset', 'like', "%{$search}%");
            });
        }

        $items = $query->get();

        $sessionMeta = StockOpname::where('tanggal_stock_opname', $date)->first();

        $barangQuery = Barang::orderBy('nama_asset', 'asc');
        $this->applyJenisLokasiFilter($barangQuery, $jenisLokasiFilter);
        $allBarang = $barangQuery->get();

        $availableRooms = StockOpname::whereNotNull('lokasi_spesifik')
            ->where('lokasi_spesifik', '!=', '')
            ->distinct()
            ->pluck('lokasi_spesifik')
            ->sort()
            ->values()
            ->toArray();

        return view('Administrasi.Pages.Inventaris.inventaris_stock_opname_detail', [
            'date' => $date,
            'formattedDate' => Carbon::parse($date)->translatedFormat('d F Y'),
            'sessionMeta' => $sessionMeta,
            'items' => $items,
            'allBarang' => $allBarang,
            'availableRooms' => $availableRooms,
            'search' => $search,
            'jenisLokasiFilter' => $jenisLokasiFilter,
            'lokasiSpesifikFilter' => $lokasiSpesifikFilter,
            'activeTab' => 'stock_opname',
        ]);
    }

    public function stockOpnameItemStore(Request $request, $date)
    {
        $barang = Barang::findOrFail($request->input('barang_id'));
        $isKantor = in_array(strtolower($barang->jenis_lokasi ?? $barang->lokasi ?? ''), ['kantor'], true);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'lokasi_spesifik' => $isKantor ? 'required|string|max:255' : 'nullable|string|max:255',
            'jumlah_baik' => 'nullable|integer|min:0',
            'jumlah_rusak' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'lokasi_spesifik.required' => 'Lokasi spesifik / ruangan kantor wajib diisi untuk barang Kantor.',
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
            'lokasi_spesifik' => $isKantor ? ($validated['lokasi_spesifik'] ?? null) : null,
            'jumlah_baik' => $baik,
            'jumlah_rusak' => $rusak,
            'grand_total' => $baik + $rusak,
            'keterangan' => $validated['keterangan'] ?? null,
            'foto_kerusakan' => $fotoPath,
        ]);

        $redirectParams = ['date' => $date];
        if ($request->filled('jenis_lokasi')) {
            $redirectParams['jenis_lokasi'] = $request->input('jenis_lokasi');
        }

        return redirect()->route('administrasi.inventaris.stock-opname.detail', $redirectParams)
            ->with('success', 'Data aset berhasil ditambahkan ke Stock Opname!');
    }

    public function stockOpnameItemUpdate(Request $request, $id)
    {
        $item = StockOpname::findOrFail($id);
        $barang = Barang::findOrFail($request->input('barang_id'));
        $isKantor = in_array(strtolower($barang->jenis_lokasi ?? $barang->lokasi ?? ''), ['kantor'], true);

        $validated = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'lokasi_spesifik' => $isKantor ? 'required|string|max:255' : 'nullable|string|max:255',
            'jumlah_baik' => 'nullable|integer|min:0',
            'jumlah_rusak' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'lokasi_spesifik.required' => 'Lokasi spesifik / ruangan kantor wajib diisi untuk barang Kantor.',
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
        $item->lokasi_spesifik = $isKantor ? ($validated['lokasi_spesifik'] ?? null) : null;
        $item->jumlah_baik = $baik;
        $item->jumlah_rusak = $rusak;
        $item->grand_total = $baik + $rusak;
        $item->keterangan = $validated['keterangan'] ?? null;
        $item->save();

        $redirectParams = ['date' => $item->tanggal_stock_opname->format('Y-m-d')];
        if ($request->filled('jenis_lokasi')) {
            $redirectParams['jenis_lokasi'] = $request->input('jenis_lokasi');
        }

        return redirect()->route('administrasi.inventaris.stock-opname.detail', $redirectParams)
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

        $redirectParams = ['date' => $date];
        if (request()->filled('jenis_lokasi')) {
            $redirectParams['jenis_lokasi'] = request('jenis_lokasi');
        }

        return redirect()->route('administrasi.inventaris.stock-opname.detail', $redirectParams)
            ->with('success', 'Data barang berhasil dihapus dari Stock Opname!');
    }

    public function stockOpnameExport($date)
    {
        $items = StockOpname::with('barang')
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

        $filename = "Stock_Opname_{$date}.xlsx";

        return ExcelExporter::download($filename, $headers, $dataRows, 'Stock Opname');
    }

    public function stockOpnameTemplate()
    {
        $headers = ['nama_asset', 'tanggal_stock_opname', 'jenis_lokasi', 'lokasi_spesifik', 'jumlah_baik', 'jumlah_rusak', 'keterangan'];
        $sampleRows = [
            ['Kursi Kerja Ergonomis', date('Y-m-d'), 'Kantor', 'Planter', '45', '5', 'Kondisi baik & 5 rusak roda'],
            ['Semen Padang 50kg', date('Y-m-d'), 'Gudang', '', '100', '0', 'Stok utuh'],
        ];

        return ExcelExporter::download('Template_Import_Stock_Opname.xlsx', $headers, $sampleRows, 'Template Stock Opname');
    }

    public function stockOpnameImport(Request $request)
    {
        $defaultLokasi = $request->input('jenis_lokasi_default', 'auto');
        $csvData = $this->parseCsvFile($request);
        if (empty($csvData)) {
            return redirect()->back()->withErrors(['file' => 'Gagal membaca file import.']);
        }

        $headerMap = $csvData['headerMap'];
        $hasHeader = isset($headerMap['idstockopname']) || isset($headerMap['idbarang']) || isset($headerMap['namaasset']) || isset($headerMap['namabarang']) || isset($headerMap['nama']) || isset($headerMap['barang']) || isset($headerMap['tanggalstockopname']) || isset($headerMap['tanggal']);
        $rows = $csvData['rows'];

        $imported = 0;
        foreach ($rows as $data) {
            $idStockOpname = null;
            $idBarang = null;
            $namaAsset = null;
            $tanggalRaw = null;
            $jenisLokasiRow = null;
            $lokasiSpesifik = null;
            $jumlahBaik = 0;
            $jumlahRusak = 0;
            $keterangan = null;

            if ($hasHeader) {
                if (isset($headerMap['idstockopname']) && isset($data[$headerMap['idstockopname']])) {
                    $idStockOpname = trim($data[$headerMap['idstockopname']]);
                }
                if (isset($headerMap['idbarang']) && isset($data[$headerMap['idbarang']])) {
                    $idBarang = trim($data[$headerMap['idbarang']]);
                }

                foreach (['namaasset', 'namabarang', 'nama', 'barang'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $namaAsset = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                foreach (['tanggalstockopname', 'tanggal', 'tgl'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $tanggalRaw = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                foreach (['jenislokasi', 'lokasi'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $rawLokasi = trim($data[$headerMap[$k]]);
                        if (in_array(ucfirst(strtolower($rawLokasi)), ['Kantor', 'Gudang'], true)) {
                            $jenisLokasiRow = ucfirst(strtolower($rawLokasi));
                        }
                        break;
                    }
                }

                foreach (['lokasispesifik', 'ruangan'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $lokasiSpesifik = trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                foreach (['jumlahbaik', 'baik'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $jumlahBaik = (int) trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                foreach (['jumlahrusak', 'rusak'] as $k) {
                    if (isset($headerMap[$k]) && isset($data[$headerMap[$k]])) {
                        $jumlahRusak = (int) trim($data[$headerMap[$k]]);
                        break;
                    }
                }

                if (isset($headerMap['keterangan']) && isset($data[$headerMap['keterangan']])) {
                    $keterangan = trim($data[$headerMap['keterangan']]);
                }
            } else {
                if (count($data) >= 4 && is_numeric(trim($data[0])) && is_numeric(trim($data[1]))) {
                    $idStockOpname = trim($data[0]);
                    $idBarang = trim($data[1]);
                    $tanggalRaw = trim($data[2]);
                    $jumlahBaik = (int) trim($data[3] ?? 0);
                    $jumlahRusak = (int) trim($data[4] ?? 0);
                    $keterangan = isset($data[6]) ? trim($data[6]) : (isset($data[5]) && ! is_numeric(trim($data[5])) ? trim($data[5]) : null);
                } else {
                    if (isset($data[0])) {
                        $namaAsset = trim($data[0]);
                    }
                    if (isset($data[1])) {
                        $tanggalRaw = trim($data[1]);
                    }
                    if (isset($data[2])) {
                        $lokasiSpesifik = trim($data[2]);
                    }
                    if (isset($data[3])) {
                        $jumlahBaik = (int) trim($data[3]);
                    }
                    if (isset($data[4])) {
                        $jumlahRusak = (int) trim($data[4]);
                    }
                    if (isset($data[5])) {
                        $keterangan = trim($data[5]);
                    }
                }
            }

            if (($namaAsset && in_array(strtolower($namaAsset), ['nama_asset', 'nama asset', 'nama barang', 'id_barang', 'id_stock_opname'], true)) ||
                ($idBarang && ! is_numeric($idBarang) && in_array(strtolower($idBarang), ['id_barang', 'id_stock_opname', 'id'], true))) {
                continue;
            }

            if (empty($idBarang) && empty($namaAsset)) {
                continue;
            }

            $barang = $this->findOrCreateBarang($idBarang, $namaAsset, $jenisLokasiRow, $defaultLokasi);
            $tanggal = $this->parseDateString($tanggalRaw);

            $baik = max(0, $jumlahBaik);
            $rusak = max(0, $jumlahRusak);

            $soAttr = [
                'barang_id' => $barang->id,
                'tanggal_stock_opname' => $tanggal,
                'status' => 'Selesai',
                'catatan_sesi' => 'Hasil Import File Stock Opname',
                'lokasi_spesifik' => $lokasiSpesifik,
                'jumlah_baik' => $baik,
                'jumlah_rusak' => $rusak,
                'grand_total' => $baik + $rusak,
                'keterangan' => $keterangan,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (! empty($idStockOpname) && is_numeric($idStockOpname)) {
                $so = StockOpname::find((int) $idStockOpname);
                if ($so) {
                    $so->update($soAttr);
                } else {
                    $soAttr['id'] = (int) $idStockOpname;
                    DB::table('stock_opname')->insert($soAttr);
                }
            } else {
                StockOpname::create($soAttr);
            }

            $imported++;
        }

        $fileName = $request->file('file')?->getClientOriginalName() ?? 'file.csv';
        ActivityLog::log(
            'Import',
            'Inventaris Stock Opname',
            "Mengimport {$imported} data stock opname inventaris dari file ".$fileName,
            ['filename' => $fileName, 'count' => $imported]
        );

        return redirect()->back()->with('success', "Berhasil mengimpor {$imported} data stock opname!");
    }

    /* =========================================================================
     * BULK DELETE (DESTROY ALL) METHODS
     * ========================================================================= */
    public function barangDestroyAll(Request $request)
    {
        $jenisLokasi = $request->get('jenis_lokasi');
        $msg = '';

        DB::transaction(function () use ($jenisLokasi, &$msg) {
            if ($jenisLokasi && in_array($jenisLokasi, ['Gudang', 'Kantor'], true)) {
                $barangIds = Barang::where('jenis_lokasi', $jenisLokasi)
                    ->orWhere('lokasi', $jenisLokasi)
                    ->pluck('id');

                StockOpname::whereIn('barang_id', $barangIds)->delete();
                Pembelian::whereIn('barang_id', $barangIds)->delete();
                $deleted = Barang::whereIn('id', $barangIds)->delete();
                $msg = "Seluruh data Barang inventaris lokasi {$jenisLokasi} ({$deleted} data) berhasil dihapus.";
            } else {
                StockOpname::query()->delete();
                Pembelian::query()->delete();
                $deleted = Barang::query()->delete();
                $msg = "Seluruh data Barang inventaris ({$deleted} data) beserta riwayatnya berhasil dihapus.";
            }

            ActivityLog::log(
                'Hapus',
                'Inventaris Barang',
                $msg,
                ['jenis_lokasi' => $jenisLokasi, 'count' => $deleted]
            );
        });

        return redirect()->route('administrasi.inventaris.barang', array_filter(['jenis_lokasi' => $jenisLokasi]))
            ->with('success', $msg);
    }

    public function pembelianDestroyAll(Request $request)
    {
        $jenisLokasi = $request->get('jenis_lokasi');

        if ($jenisLokasi && in_array($jenisLokasi, ['Gudang', 'Kantor'], true)) {
            $barangIds = Barang::where('jenis_lokasi', $jenisLokasi)
                ->orWhere('lokasi', $jenisLokasi)
                ->pluck('id');
            $deleted = Pembelian::whereIn('barang_id', $barangIds)->delete();
            $msg = "Seluruh data Pembelian inventaris lokasi {$jenisLokasi} ({$deleted} data) berhasil dihapus.";
        } else {
            $deleted = Pembelian::query()->delete();
            $msg = "Seluruh data Pembelian inventaris ({$deleted} data) berhasil dihapus.";
        }

        ActivityLog::log(
            'Hapus',
            'Inventaris Pembelian',
            $msg,
            ['jenis_lokasi' => $jenisLokasi, 'count' => $deleted]
        );

        return redirect()->route('administrasi.inventaris.pembelian', array_filter(['jenis_lokasi' => $jenisLokasi]))
            ->with('success', $msg);
    }

    public function stockOpnameDestroyAll(Request $request)
    {
        $jenisLokasi = $request->get('jenis_lokasi');

        if ($jenisLokasi && in_array($jenisLokasi, ['Gudang', 'Kantor'], true)) {
            $barangIds = Barang::where('jenis_lokasi', $jenisLokasi)
                ->orWhere('lokasi', $jenisLokasi)
                ->pluck('id');
            $deleted = StockOpname::whereIn('barang_id', $barangIds)->delete();
            $msg = "Seluruh data Stock Opname inventaris lokasi {$jenisLokasi} ({$deleted} data) berhasil dihapus.";
        } else {
            $deleted = StockOpname::query()->delete();
            $msg = "Seluruh data Stock Opname inventaris ({$deleted} data) berhasil dihapus.";
        }

        ActivityLog::log(
            'Hapus',
            'Inventaris Stock Opname',
            $msg,
            ['jenis_lokasi' => $jenisLokasi, 'count' => $deleted]
        );

        return redirect()->route('administrasi.inventaris.stock-opname', array_filter(['jenis_lokasi' => $jenisLokasi]))
            ->with('success', $msg);
    }
}
