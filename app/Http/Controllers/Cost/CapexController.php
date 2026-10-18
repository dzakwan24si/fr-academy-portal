<?php

namespace App\Http\Controllers\Cost;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Cost\CapexRab;
use App\Models\Cost\CapexRealisasi;
use App\Services\ExcelExporter;
use App\Traits\HandlesCsvImports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CapexController extends Controller
{
    use HandlesCsvImports;

    public const MONTHS = [
        1 => 'Januari',   2 => 'Februari', 3 => 'Maret',
        4 => 'April',     5 => 'Mei',       6 => 'Juni',
        7 => 'Juli',      8 => 'Agustus',   9 => 'September',
        10 => 'Oktober',   11 => 'November',  12 => 'Desember',
    ];

    /**
     * Display the main CAPEX management page.
     */
    public function index(Request $request)
    {
        // 1. Dynamic Year Selection
        $selectedYear = (int) $request->get('year', 2026);
        $rabYears = CapexRab::select('tahun')->distinct()->pluck('tahun')->toArray();
        $realisasiYears = CapexRealisasi::select('tahun')->distinct()->pluck('tahun')->toArray();
        $baseYears = range(date('Y') - 3, date('Y') + 2);
        $years = array_values(array_unique(array_filter(array_merge($baseYears, $rabYears, $realisasiYears, [$selectedYear]))));
        sort($years);

        $selectedMonth = $request->get('month', 'all'); // 'all' or 1..12
        $selectedLocation = $request->get('location', 'all'); // 'all', 'kantor', 'mess'
        $search = trim($request->get('search', ''));
        $months = self::MONTHS;

        // 2. Base Queries for RAB
        $rabKantorQuery = CapexRab::where('tahun', $selectedYear)->where('lokasi', 'kantor');
        $rabMessQuery = CapexRab::where('tahun', $selectedYear)->where('lokasi', 'mess');

        // 3. Base Queries for Realisasi
        $realisasiKantorQuery = CapexRealisasi::with('rab')->where('tahun', $selectedYear)->where('lokasi', 'kantor');
        $realisasiMessQuery = CapexRealisasi::with('rab')->where('tahun', $selectedYear)->where('lokasi', 'mess');

        if (is_numeric($selectedMonth) && (int) $selectedMonth >= 1 && (int) $selectedMonth <= 12) {
            $monthInt = (int) $selectedMonth;
            $realisasiKantorQuery->whereMonth('tanggal', $monthInt);
            $realisasiMessQuery->whereMonth('tanggal', $monthInt);
        }

        // Apply Search if provided
        if (! empty($search)) {
            $rabFilter = function ($q) use ($search) {
                $q->where('kel_barang_kapital', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('satuan', 'like', "%{$search}%");
            };
            $realisasiFilter = function ($q) use ($search) {
                $q->where('kel_barang_kapital', 'like', "%{$search}%")
                    ->orWhere('nama_barang', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%");
            };

            $rabKantorQuery->where($rabFilter);
            $rabMessQuery->where($rabFilter);
            $realisasiKantorQuery->where($realisasiFilter);
            $realisasiMessQuery->where($realisasiFilter);
        }

        // 4. Summary Totals (Independent of pagination)
        $rabKantorTotal = (float) CapexRab::where('tahun', $selectedYear)->where('lokasi', 'kantor')->sum('total_rp');
        $rabMessTotal = (float) CapexRab::where('tahun', $selectedYear)->where('lokasi', 'mess')->sum('total_rp');

        $realKantorQuery = CapexRealisasi::where('tahun', $selectedYear)->where('lokasi', 'kantor');
        $realMessQuery = CapexRealisasi::where('tahun', $selectedYear)->where('lokasi', 'mess');

        if (is_numeric($selectedMonth) && (int) $selectedMonth >= 1 && (int) $selectedMonth <= 12) {
            $monthInt = (int) $selectedMonth;
            $realKantorQuery->whereMonth('tanggal', $monthInt);
            $realMessQuery->whereMonth('tanggal', $monthInt);
        }

        $debitKantorTotal = (float) (clone $realKantorQuery)->sum('debit');
        $kreditKantorTotal = (float) (clone $realKantorQuery)->sum('kredit');
        $realisasiKantorTotal = (float) (clone $realKantorQuery)->sum('total_rp');

        $debitMessTotal = (float) (clone $realMessQuery)->sum('debit');
        $kreditMessTotal = (float) (clone $realMessQuery)->sum('kredit');
        $realisasiMessTotal = (float) (clone $realMessQuery)->sum('total_rp');

        if ($selectedLocation === 'kantor') {
            $totalRab = $rabKantorTotal;
            $totalDebit = $debitKantorTotal;
            $totalKredit = $kreditKantorTotal;
            $totalRealisasi = $realisasiKantorTotal;
        } elseif ($selectedLocation === 'mess') {
            $totalRab = $rabMessTotal;
            $totalDebit = $debitMessTotal;
            $totalKredit = $kreditMessTotal;
            $totalRealisasi = $realisasiMessTotal;
        } else {
            $totalRab = $rabKantorTotal + $rabMessTotal;
            $totalDebit = $debitKantorTotal + $debitMessTotal;
            $totalKredit = $kreditKantorTotal + $kreditMessTotal;
            $totalRealisasi = $realisasiKantorTotal + $realisasiMessTotal;
        }
        $sisaRab = $totalRab - $totalRealisasi;

        $summary = compact(
            'rabKantorTotal',
            'rabMessTotal',
            'debitKantorTotal',
            'kreditKantorTotal',
            'realisasiKantorTotal',
            'debitMessTotal',
            'kreditMessTotal',
            'realisasiMessTotal',
            'totalRab',
            'totalDebit',
            'totalKredit',
            'totalRealisasi',
            'sisaRab'
        );

        // 5. Fetch Paginated Data
        $rabKantor = $rabKantorQuery->orderBy('rab_id', 'asc')->paginate(15, ['*'], 'rab_kantor_page')->withQueryString();
        $realisasiKantor = $realisasiKantorQuery->orderBy('realisasi_id', 'asc')->paginate(15, ['*'], 'rel_kantor_page')->withQueryString();
        $rabMess = $rabMessQuery->orderBy('rab_id', 'asc')->paginate(15, ['*'], 'rab_mess_page')->withQueryString();
        $realisasiMess = $realisasiMessQuery->orderBy('realisasi_id', 'asc')->paginate(15, ['*'], 'rel_mess_page')->withQueryString();

        // 6. Available RAB options for dropdown linkage in Realisasi forms
        $availableRabs = CapexRab::where('tahun', $selectedYear)
            ->orderBy('lokasi', 'asc')
            ->orderBy('kel_barang_kapital', 'asc')
            ->get();

        return view('Administrasi.Pages.Cost.capex', compact(
            'years',
            'selectedYear',
            'selectedMonth',
            'months',
            'selectedLocation',
            'search',
            'rabKantor',
            'realisasiKantor',
            'rabMess',
            'realisasiMess',
            'summary',
            'availableRabs'
        ));
    }

    /**
     * Store a newly created RAB item.
     */
    public function storeRab(Request $request)
    {
        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'lokasi' => 'required|in:kantor,mess',
            'kel_barang_kapital' => 'required|string|max:255',
            'foto_barang' => 'nullable|string|max:255',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'type' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'jumlah_unit' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('foto_file')) {
            $path = $this->handleUpload($request->file('foto_file'));
            $validated['foto_barang'] = $path;
        }

        $validated['total_rp'] = (float) $validated['jumlah_unit'] * (float) $validated['harga_satuan'];

        CapexRab::create($validated);
        (new CostController)->syncAggregatedCosts((int) $validated['tahun']);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $validated['tahun'], 'location' => $validated['lokasi']])
            ->with('success', 'Data RAB CAPEX ('.ucfirst($validated['lokasi']).') berhasil ditambahkan.');
    }

    /**
     * Get single RAB item for Edit / Detail modal.
     */
    public function showRab($id)
    {
        $item = CapexRab::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    /**
     * Update RAB item.
     */
    public function updateRab(Request $request, $id)
    {
        $item = CapexRab::findOrFail($id);

        $validated = $request->validate([
            'tahun' => 'required|integer|min:2000|max:2099',
            'lokasi' => 'required|in:kantor,mess',
            'kel_barang_kapital' => 'required|string|max:255',
            'foto_barang' => 'nullable|string|max:255',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'type' => 'nullable|string|max:255',
            'satuan' => 'nullable|string|max:100',
            'jumlah_unit' => 'required|numeric|min:0',
            'harga_satuan' => 'required|numeric|min:0',
        ]);

        if ($request->hasFile('foto_file')) {
            $path = $this->handleUpload($request->file('foto_file'));
            $validated['foto_barang'] = $path;
        }

        $validated['total_rp'] = (float) $validated['jumlah_unit'] * (float) $validated['harga_satuan'];

        $item->update($validated);
        (new CostController)->syncAggregatedCosts((int) $item->tahun);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $item->tahun, 'location' => $item->lokasi])
            ->with('success', 'Data RAB CAPEX berhasil diperbarui.');
    }

    /**
     * Delete RAB item.
     */
    public function destroyRab($id)
    {
        $item = CapexRab::findOrFail($id);
        $tahun = $item->tahun;
        $lokasi = $item->lokasi;

        // Safely set null on linked realisasis
        CapexRealisasi::where('rab_id', $id)->update(['rab_id' => null]);

        $item->delete();
        (new CostController)->syncAggregatedCosts((int) $tahun);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $tahun, 'location' => $lokasi])
            ->with('success', 'Data RAB CAPEX berhasil dihapus.');
    }

    /**
     * Store a newly created Realisasi item.
     */
    public function storeRealisasi(Request $request)
    {
        $validated = $request->validate([
            'rab_id' => 'nullable|integer|exists:capex_rab,rab_id',
            'tahun' => 'required|integer|min:2000|max:2099',
            'lokasi' => 'required|in:kantor,mess',
            'kel_barang_kapital' => 'required|string|max:255',
            'foto_barang' => 'nullable|string|max:255',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'nama_barang' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'debit' => 'nullable|numeric|min:0',
            'kredit' => 'nullable|numeric|min:0',
            'total_rp' => 'nullable|numeric',
        ]);

        if ($request->hasFile('foto_file')) {
            $path = $this->handleUpload($request->file('foto_file'));
            $validated['foto_barang'] = $path;
        }

        $debit = (float) ($validated['debit'] ?? 0);
        $kredit = (float) ($validated['kredit'] ?? 0);
        $validated['total_rp'] = $debit - $kredit;

        CapexRealisasi::create($validated);
        (new CostController)->syncAggregatedCosts((int) $validated['tahun']);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $validated['tahun'], 'location' => $validated['lokasi']])
            ->with('success', 'Data Realisasi CAPEX ('.ucfirst($validated['lokasi']).') berhasil ditambahkan.');
    }

    /**
     * Get single Realisasi item for Edit / Detail modal.
     */
    public function showRealisasi($id)
    {
        $item = CapexRealisasi::with('rab')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    /**
     * Update Realisasi item.
     */
    public function updateRealisasi(Request $request, $id)
    {
        $item = CapexRealisasi::findOrFail($id);

        $validated = $request->validate([
            'rab_id' => 'nullable|integer|exists:capex_rab,rab_id',
            'tahun' => 'required|integer|min:2000|max:2099',
            'lokasi' => 'required|in:kantor,mess',
            'kel_barang_kapital' => 'required|string|max:255',
            'foto_barang' => 'nullable|string|max:255',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'nama_barang' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
            'debit' => 'nullable|numeric|min:0',
            'kredit' => 'nullable|numeric|min:0',
            'total_rp' => 'nullable|numeric',
        ]);

        if ($request->hasFile('foto_file')) {
            $path = $this->handleUpload($request->file('foto_file'));
            $validated['foto_barang'] = $path;
        }

        $debit = (float) ($validated['debit'] ?? 0);
        $kredit = (float) ($validated['kredit'] ?? 0);
        $validated['total_rp'] = $debit - $kredit;

        $item->update($validated);
        (new CostController)->syncAggregatedCosts((int) $item->tahun);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $item->tahun, 'location' => $item->lokasi])
            ->with('success', 'Data Realisasi CAPEX berhasil diperbarui.');
    }

    /**
     * Delete Realisasi item.
     */
    public function destroyRealisasi($id)
    {
        $item = CapexRealisasi::findOrFail($id);
        $tahun = $item->tahun;
        $lokasi = $item->lokasi;

        $item->delete();
        (new CostController)->syncAggregatedCosts((int) $tahun);

        return redirect()
            ->route('administrasi.capex.index', ['year' => $tahun, 'location' => $lokasi])
            ->with('success', 'Data Realisasi CAPEX berhasil dihapus.');
    }

    /**
     * Import CSV file with auto-detection of RAB vs Realisasi.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,text|max:10240',
            'import_type' => 'nullable|string|in:auto,rab,realisasi',
            'import_mode' => 'nullable|string|in:append,replace',
            'tahun' => 'nullable|integer|min:2000|max:2099',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $importType = $request->get('import_type', 'auto');
        $importMode = $request->get('import_mode', 'append');
        $defaultYear = (int) $request->get('tahun', date('Y'));

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return back()->with('error', 'Gagal membaca file CSV yang diunggah.');
        }

        $delimiter = $this->detectCsvDelimiter($filePath);
        $rawHeaders = fgetcsv($handle, 0, $delimiter);
        if (! $rawHeaders) {
            fclose($handle);

            return back()->with('error', 'Format file CSV tidak valid atau kosong.');
        }

        $cleanHeaders = $this->cleanCsvHeaders($rawHeaders);

        // Determine if file is RAB or Realisasi
        $detectedType = 'rab';
        if ($importType === 'rab') {
            $detectedType = 'rab';
        } elseif ($importType === 'realisasi') {
            $detectedType = 'realisasi';
        } else {
            // Auto detection based on column tokens
            $hasDebit = in_array('debit', $cleanHeaders);
            $hasKredit = in_array('kredit', $cleanHeaders);
            $hasNamaBarang = in_array('namabarang', $cleanHeaders);
            $hasRealisasiId = in_array('realisasiid', $cleanHeaders);
            $hasHargaSatuan = in_array('hargasatuan', $cleanHeaders);
            $hasJumlahUnit = in_array('jumlahunit', $cleanHeaders);

            if ($hasDebit || $hasKredit || $hasNamaBarang || $hasRealisasiId) {
                $detectedType = 'realisasi';
            } elseif ($hasHargaSatuan || $hasJumlahUnit) {
                $detectedType = 'rab';
            }
        }

        // Map column indices
        $headerMap = [];
        foreach ($cleanHeaders as $idx => $col) {
            // Common
            if (in_array($col, ['tahun', 'year', 'thn', 'periode'])) {
                $headerMap['tahun'] = $idx;
            }
            if (in_array($col, ['lokasi', 'location', 'jeniscapex'])) {
                $headerMap['lokasi'] = $idx;
            }
            if (in_array($col, ['kelbarangkapital', 'kelompokbarang', 'kelompok', 'kategoribarang'])) {
                $headerMap['kel_barang_kapital'] = $idx;
            }
            if (in_array($col, ['fotobarang', 'foto', 'image', 'gambar', 'urlfoto'])) {
                $headerMap['foto_barang'] = $idx;
            }
            if (in_array($col, ['type', 'tipe', 'typemerk', 'merk', 'model'])) {
                $headerMap['type'] = $idx;
            }
            if (in_array($col, ['totalrp', 'total', 'totalbiaya', 'totalrab'])) {
                $headerMap['total_rp'] = $idx;
            }

            // RAB specific
            if (in_array($col, ['rabid', 'idrab', 'id'])) {
                $headerMap['rab_id'] = $idx;
            }
            if (in_array($col, ['satuan', 'unit', 'uom'])) {
                $headerMap['satuan'] = $idx;
            }
            if (in_array($col, ['jumlahunit', 'jumlah', 'qty', 'kuantitas', 'unitcount'])) {
                $headerMap['jumlah_unit'] = $idx;
            }
            if (in_array($col, ['hargasatuan', 'harga', 'price', 'unitprice'])) {
                $headerMap['harga_satuan'] = $idx;
            }

            // Realisasi specific
            if (in_array($col, ['realisasiid', 'idrealisasi'])) {
                $headerMap['realisasi_id'] = $idx;
            }
            if (in_array($col, ['namabarang', 'namatransaksi', 'barang', 'keterangan', 'deskripsi'])) {
                $headerMap['nama_barang'] = $idx;
            }
            if (in_array($col, ['tanggal', 'date', 'tgl', 'tgltransaksi', 'bulan', 'month', 'bln'])) {
                $headerMap['tanggal'] = $idx;
            }
            if (in_array($col, ['debit', 'pengeluaran', 'masuk', 'debet'])) {
                $headerMap['debit'] = $idx;
            }
            if (in_array($col, ['kredit', 'penerimaan', 'keluar'])) {
                $headerMap['kredit'] = $idx;
            }
        }

        // Handle Replace mode
        if ($importMode === 'replace') {
            if ($detectedType === 'rab') {
                // Delete existing RAB and clear foreign key references
                CapexRealisasi::where('tahun', $defaultYear)->update(['rab_id' => null]);
                CapexRab::where('tahun', $defaultYear)->delete();
            } else {
                CapexRealisasi::where('tahun', $defaultYear)->delete();
            }
        }

        $importedCount = 0;
        $updatedCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Skip empty rows
                if (empty(array_filter($row, fn ($v) => trim($v) !== ''))) {
                    continue;
                }

                $getVal = function ($key, $default = null) use ($row, $headerMap) {
                    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                        return trim($row[$headerMap[$key]]);
                    }

                    return $default;
                };

                $tahun = (int) $getVal('tahun', $defaultYear);
                if ($tahun <= 0) {
                    $tahun = $defaultYear;
                }

                $rawLokasi = strtolower((string) $getVal('lokasi', 'kantor'));
                $lokasi = (str_contains($rawLokasi, 'mess') || str_contains($rawLokasi, 'mes')) ? 'mess' : 'kantor';

                $kelBarang = $getVal('kel_barang_kapital', 'Umum');
                $fotoBarang = $getVal('foto_barang', null);
                $type = $getVal('type', null);

                if ($detectedType === 'rab') {
                    $rabId = $getVal('rab_id');
                    $satuan = $getVal('satuan', 'Unit');
                    $jumlahUnit = $this->cleanNumeric($getVal('jumlah_unit', 1), 1);
                    $hargaSatuan = $this->cleanNumeric($getVal('harga_satuan', 0), 0);
                    $totalRp = $this->cleanNumeric($getVal('total_rp', $jumlahUnit * $hargaSatuan), $jumlahUnit * $hargaSatuan);

                    $data = [
                        'tahun' => $tahun,
                        'lokasi' => $lokasi,
                        'kel_barang_kapital' => $kelBarang,
                        'foto_barang' => $fotoBarang,
                        'type' => $type,
                        'satuan' => $satuan,
                        'jumlah_unit' => $jumlahUnit,
                        'harga_satuan' => $hargaSatuan,
                        'total_rp' => $totalRp,
                    ];

                    if ($rabId && is_numeric($rabId)) {
                        $existing = CapexRab::find((int) $rabId);
                        if ($existing) {
                            $existing->update($data);
                            $updatedCount++;
                        } else {
                            $data['rab_id'] = (int) $rabId;
                            CapexRab::create($data);
                            $importedCount++;
                        }
                    } else {
                        CapexRab::create($data);
                        $importedCount++;
                    }
                } else {
                    // Realisasi
                    $realisasiId = $getVal('realisasi_id');
                    $rabId = $getVal('rab_id');
                    $namaBarang = $getVal('nama_barang', $kelBarang);
                    $rawDate = $getVal('tanggal');
                    $debit = $this->cleanNumeric($getVal('debit', 0), 0);
                    $kredit = $this->cleanNumeric($getVal('kredit', 0), 0);
                    $totalRp = ($debit != 0 || $kredit != 0) ? ($debit - $kredit) : $this->cleanNumeric($getVal('total_rp', 0), 0);

                    // Parse Date / Month
                    $tanggal = null;
                    if (! empty($rawDate)) {
                        if (is_numeric($rawDate) && (int) $rawDate >= 1 && (int) $rawDate <= 12) {
                            $mStr = str_pad((int) $rawDate, 2, '0', STR_PAD_LEFT);
                            $tanggal = "{$tahun}-{$mStr}-01";
                        } else {
                            $lowerDate = strtolower($rawDate);
                            $monthNamesMap = [
                                'januari' => 1, 'january' => 1, 'jan' => 1,
                                'februari' => 2, 'february' => 2, 'feb' => 2,
                                'maret' => 3, 'march' => 3, 'mar' => 3,
                                'april' => 4, 'apr' => 4,
                                'mei' => 5, 'may' => 5,
                                'juni' => 6, 'june' => 6, 'jun' => 6,
                                'juli' => 7, 'july' => 7, 'jul' => 7,
                                'agustus' => 8, 'august' => 8, 'aug' => 8,
                                'september' => 9, 'sep' => 9,
                                'oktober' => 10, 'october' => 10, 'okt' => 10, 'oct' => 10,
                                'november' => 11, 'nov' => 11,
                                'desember' => 12, 'december' => 12, 'des' => 12, 'dec' => 12,
                            ];
                            if (isset($monthNamesMap[$lowerDate])) {
                                $mStr = str_pad($monthNamesMap[$lowerDate], 2, '0', STR_PAD_LEFT);
                                $tanggal = "{$tahun}-{$mStr}-01";
                            } else {
                                try {
                                    $parsedTs = strtotime(str_replace('/', '-', $rawDate));
                                    $tanggal = $parsedTs ? date('Y-m-d', $parsedTs) : null;
                                } catch (\Exception $e) {
                                    $tanggal = null;
                                }
                            }
                        }
                    }

                    $data = [
                        'rab_id' => ($rabId && is_numeric($rabId)) ? (int) $rabId : null,
                        'tahun' => $tahun,
                        'lokasi' => $lokasi,
                        'kel_barang_kapital' => $kelBarang,
                        'foto_barang' => $fotoBarang,
                        'nama_barang' => $namaBarang,
                        'type' => $type,
                        'tanggal' => $tanggal,
                        'debit' => $debit,
                        'kredit' => $kredit,
                        'total_rp' => $totalRp,
                    ];

                    if ($realisasiId && is_numeric($realisasiId)) {
                        $existing = CapexRealisasi::find((int) $realisasiId);
                        if ($existing) {
                            $existing->update($data);
                            $updatedCount++;
                        } else {
                            $data['realisasi_id'] = (int) $realisasiId;
                            CapexRealisasi::create($data);
                            $importedCount++;
                        }
                    } else {
                        CapexRealisasi::create($data);
                        $importedCount++;
                    }
                }
            }

            DB::commit();
            fclose($handle);

            $typeLabel = $detectedType === 'rab' ? 'RAB' : 'Realisasi';

            ActivityLog::log(
                'Import',
                'CAPEX',
                "Mengimport data {$typeLabel} CAPEX dari file ".$file->getClientOriginalName()." ({$importedCount} baru, {$updatedCount} diperbarui)",
                ['filename' => $file->getClientOriginalName(), 'imported' => $importedCount, 'updated' => $updatedCount, 'type' => $typeLabel]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            if (is_resource($handle)) {
                fclose($handle);
            }

            return back()->with('error', 'Terjadi kesalahan saat import data: '.$e->getMessage());
        }

        $typeLabel = $detectedType === 'rab' ? 'RAB' : 'Realisasi';
        $msg = "Import {$typeLabel} CAPEX berhasil! ({$importedCount} data baru ditambahkan, {$updatedCount} data diperbarui).";

        return redirect()
            ->route('administrasi.capex.index', ['year' => $defaultYear])
            ->with('success', $msg);
    }

    /**
     * Download CSV template for import (RAB or Realisasi).
     */
    public function downloadTemplate(Request $request)
    {
        $type = $request->get('type', 'rab'); // 'rab' or 'realisasi'

        if ($type === 'realisasi') {
            $headers = ['realisasi_id', 'rab_id', 'Lokasi', 'Kel_barang_kapital', 'Foto_barang', 'nama_barang', 'type', 'tanggal', 'debit', 'kredit', 'total_rp'];
            $sampleRows = [
                [1, 1, 'kantor', 'Kendaraan Operasional', '', 'Hilux Single Cabin', 'Toyota', '2026-01-15', 350000000, 0, 350000000],
                [2, 2, 'kantor', 'Komputer & Laptop', '', 'MacBook Air M2', 'Apple', '2026-02-10', 18500000, 0, 18500000],
                [3, 11, 'mess', 'AC & Pendingin Ruangan', '', 'AC Inverter 1 PK', 'Daikin', '2026-03-05', 5500000, 0, 5500000],
            ];

            return ExcelExporter::download('Template_Import_Capex_Realisasi.xlsx', $headers, $sampleRows, 'Realisasi Capex');
        }

        // Default: RAB Template
        $headers = ['rab_id', 'Lokasi', 'Kel_barang_kapital', 'Foto_barang', 'type', 'satuan', 'jumlah_unit', 'harga_satuan', 'total_rp'];
        $sampleRows = [
            [1, 'kantor', 'Kendaraan Operasional', '', 'Toyota Hilux SC', 'Unit', 1, 350000000, 350000000],
            [2, 'kantor', 'Komputer & Laptop', '', 'MacBook Air M2 16GB', 'Unit', 2, 19000000, 38000000],
            [3, 'mess', 'AC & Pendingin Ruangan', '', 'Daikin 1 PK Inverter', 'Unit', 4, 5500000, 22000000],
        ];

        return ExcelExporter::download('Template_Import_Capex_RAB.xlsx', $headers, $sampleRows, 'RAB Capex');
    }

    /**
     * Export CAPEX data as CSV.
     */
    public function export(Request $request)
    {
        $year = (int) $request->get('year', date('Y'));
        $month = $request->get('month', 'all');
        $type = $request->get('type', 'all'); // 'all', 'rab', 'realisasi'

        $realisasiQuery = CapexRealisasi::where('tahun', $year);
        if (is_numeric($month) && (int) $month >= 1 && (int) $month <= 12) {
            $realisasiQuery->whereMonth('tanggal', (int) $month);
        }

        if ($type === 'realisasi') {
            $headers = ['realisasi_id', 'rab_id', 'tahun', 'lokasi', 'kel_barang_kapital', 'foto_barang', 'nama_barang', 'type', 'tanggal', 'debit', 'kredit', 'total_rp'];
            $records = $realisasiQuery->orderBy('lokasi')->orderBy('realisasi_id')->get();
            $dataRows = [];
            foreach ($records as $rel) {
                $dataRows[] = [
                    $rel->realisasi_id,
                    $rel->rab_id,
                    $rel->tahun,
                    $rel->lokasi,
                    $rel->kel_barang_kapital,
                    $rel->foto_barang,
                    $rel->nama_barang,
                    $rel->type,
                    $rel->tanggal ? $rel->tanggal->format('Y-m-d') : '',
                    $rel->debit,
                    $rel->kredit,
                    $rel->total_rp,
                ];
            }
            $filename = "CAPEX_Realisasi_Report_{$year}.xlsx";

            return ExcelExporter::download($filename, $headers, $dataRows, 'Realisasi Capex');
        } elseif ($type === 'rab') {
            $headers = ['rab_id', 'tahun', 'lokasi', 'kel_barang_kapital', 'foto_barang', 'type', 'satuan', 'jumlah_unit', 'harga_satuan', 'total_rp'];
            $records = CapexRab::where('tahun', $year)->orderBy('lokasi')->orderBy('rab_id')->get();
            $dataRows = [];
            foreach ($records as $r) {
                $dataRows[] = [
                    $r->rab_id,
                    $r->tahun,
                    $r->lokasi,
                    $r->kel_barang_kapital,
                    $r->foto_barang,
                    $r->type,
                    $r->satuan,
                    $r->jumlah_unit,
                    $r->harga_satuan,
                    $r->total_rp,
                ];
            }
            $filename = "CAPEX_RAB_Report_{$year}.xlsx";

            return ExcelExporter::download($filename, $headers, $dataRows, 'RAB Capex');
        } else {
            $headers = ['Tipe Data', 'ID', 'RAB ID', 'Tahun', 'Lokasi', 'Kelompok Barang', 'Foto Barang', 'Nama Barang / Type', 'Satuan / Tanggal', 'Jumlah Unit / Debit', 'Harga Satuan / Kredit', 'Total Rp'];
            $dataRows = [];

            $rabRecords = CapexRab::where('tahun', $year)->orderBy('lokasi')->orderBy('rab_id')->get();
            foreach ($rabRecords as $r) {
                $dataRows[] = [
                    'RAB',
                    $r->rab_id,
                    '-',
                    $r->tahun,
                    $r->lokasi,
                    $r->kel_barang_kapital,
                    $r->foto_barang,
                    $r->type,
                    $r->satuan,
                    $r->jumlah_unit,
                    $r->harga_satuan,
                    $r->total_rp,
                ];
            }

            $realisasiRecords = $realisasiQuery->orderBy('lokasi')->orderBy('realisasi_id')->get();
            foreach ($realisasiRecords as $rel) {
                $dataRows[] = [
                    'REALISASI',
                    $rel->realisasi_id,
                    $rel->rab_id,
                    $rel->tahun,
                    $rel->lokasi,
                    $rel->kel_barang_kapital,
                    $rel->foto_barang,
                    $rel->nama_barang,
                    $rel->tanggal ? $rel->tanggal->format('Y-m-d') : '',
                    $rel->debit,
                    $rel->kredit,
                    $rel->total_rp,
                ];
            }

            $filename = "CAPEX_Report_All_{$year}.xlsx";

            return ExcelExporter::download($filename, $headers, $dataRows, 'Capex Report');
        }
    }

    /**
     * Helper to handle uploaded image file
     */
    private function handleUpload($file)
    {
        $uploadDir = public_path('uploads/capex');
        if (! File::exists($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true);
        }

        $filename = 'capex_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $file->move($uploadDir, $filename);

        return '/uploads/capex/'.$filename;
    }

    /**
     * Delete all CAPEX records (RAB & Realisasi).
     */
    public function destroyAll(Request $request)
    {
        $location = $request->get('location', 'all');

        $rabQuery = CapexRab::query();
        $realQuery = CapexRealisasi::query();

        if (in_array($location, ['kantor', 'mess'])) {
            $rabQuery->where('lokasi', $location);
            $realQuery->where('lokasi', $location);
            $deletedRab = $rabQuery->delete();
            $deletedReal = $realQuery->delete();
            $msg = 'Seluruh data CAPEX lokasi '.ucfirst($location)." ({$deletedRab} RAB, {$deletedReal} Realisasi) berhasil dihapus.";
        } else {
            $deletedRab = $rabQuery->delete();
            $deletedReal = $realQuery->delete();
            $msg = "Seluruh data CAPEX ({$deletedRab} RAB, {$deletedReal} Realisasi) berhasil dihapus.";
        }

        ActivityLog::log('Hapus', 'Capex', $msg, [
            'deleted_rab' => $deletedRab,
            'deleted_realisasi' => $deletedReal,
            'location' => $location,
        ]);

        return redirect()
            ->route('administrasi.capex.index', ['location' => $location])
            ->with('success', $msg);
    }
}
