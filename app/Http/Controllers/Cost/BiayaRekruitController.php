<?php

namespace App\Http\Controllers\Cost;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\Cost\BiayaRekruit;
use App\Services\ExcelExporter;
use App\Traits\HandlesCsvImports;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BiayaRekruitController extends Controller
{
    use HandlesCsvImports;

    public const MONTHS = [
        1 => 'Januari',   2 => 'Februari', 3 => 'Maret',
        4 => 'April',     5 => 'Mei',       6 => 'Juni',
        7 => 'Juli',      8 => 'Agustus',   9 => 'September',
        10 => 'Oktober',   11 => 'November',  12 => 'Desember',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));

        $selectedYear = $request->get('year', 2026);
        $selectedMonth = $request->get('month', 'all');
        $months = self::MONTHS;

        // 1. Dynamic Year Selection
        $dbYears = BiayaRekruit::select('tahun')->distinct()->whereNotNull('tahun')->pluck('tahun')->toArray();
        $dateYears = BiayaRekruit::selectRaw('YEAR(tanggal) as yr')->distinct()->whereNotNull('tanggal')->pluck('yr')->toArray();
        $baseYears = range(date('Y') - 3, date('Y') + 2);
        $extraYear = ($selectedYear !== 'all' && is_numeric($selectedYear)) ? [(int) $selectedYear] : [];
        $years = array_values(array_unique(array_filter(array_merge($baseYears, $dbYears, $dateYears, $extraYear))));
        sort($years);

        // 2. Query records with optional year, month and search filters
        $query = BiayaRekruit::query()
            ->orderBy('jenis_rekruit')
            ->orderBy('penanggung_jawab')
            ->orderBy('tanggal', 'desc');

        if ($selectedYear !== 'all' && ! empty($selectedYear)) {
            $yearInt = (int) $selectedYear;
            $query->where(function ($q) use ($yearInt) {
                $q->where('tahun', $yearInt)
                    ->orWhereYear('tanggal', $yearInt);
            });
        }

        if (is_numeric($selectedMonth) && (int) $selectedMonth >= 1 && (int) $selectedMonth <= 12) {
            $monthInt = (int) $selectedMonth;
            $query->whereMonth('tanggal', $monthInt);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('jenis_rekruit', 'like', "%{$search}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            });
        }

        $allRecords = $query->get();

        // Group: jenis_rekruit → penanggung_jawab → items
        $grouped = [];
        foreach ($allRecords as $row) {
            $jenis = $row->jenis_rekruit ?: 'Biaya Operasional Rekrutmen';
            $pj = $row->penanggung_jawab ?: 'Umum / Non-PIC';
            $grouped[$jenis][$pj][] = $row;
        }

        // Annual calculation for Peserta Join & Cost per Person
        $targetYear = ($selectedYear !== 'all' && is_numeric($selectedYear)) ? (int) $selectedYear : (int) date('Y');

        $totalBiayaTahunan = (float) BiayaRekruit::where(function ($q) use ($targetYear) {
            $q->where('tahun', $targetYear)
                ->orWhereYear('tanggal', $targetYear);
        })->sum('total_biaya');

        $pesertaJoin = (int) AppSetting::get("biaya_rekruit_peserta_{$targetYear}", 0);
        $biayaPerOrang = $pesertaJoin > 0 ? round($totalBiayaTahunan / $pesertaJoin) : 0;

        // Summary totals
        $summary = [
            'total_biaya' => (float) $allRecords->sum('total_biaya'),
            'total_transaksi' => $allRecords->count(),
            'total_biaya_tahunan' => $totalBiayaTahunan,
            'peserta_join' => $pesertaJoin,
            'biaya_per_orang' => $biayaPerOrang,
            'target_year' => $targetYear,
        ];

        return view('Administrasi.Pages.Cost.biaya_rekruit', compact(
            'grouped', 'summary', 'search', 'years', 'selectedYear', 'selectedMonth', 'months'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'total_biaya' => 'required|numeric|min:0',
            'tahun' => 'nullable|integer',
            'jenis_rekruit' => 'nullable|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        if (empty($validated['tahun'])) {
            $validated['tahun'] = Carbon::parse($validated['tanggal'])->year;
        }

        if (empty($validated['jenis_rekruit'])) {
            $validated['jenis_rekruit'] = $this->detectJenisRekruit($validated['nama']);
        }

        $record = BiayaRekruit::create($validated);

        (new CostController)->syncAggregatedCosts((int) $validated['tahun']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Biaya rekruit berhasil ditambahkan.',
                'data' => $record,
            ]);
        }

        return redirect()->route('administrasi.biaya-rekruit.index')
            ->with('success', 'Biaya rekruit "'.$validated['nama'].'" berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $record = BiayaRekruit::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $record,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $record = BiayaRekruit::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'nama' => 'required|string|max:255',
            'total_biaya' => 'required|numeric|min:0',
            'jenis_rekruit' => 'nullable|string|max:100',
            'penanggung_jawab' => 'nullable|string|max:100',
        ]);

        $validated['tahun'] = Carbon::parse($validated['tanggal'])->year;

        if (empty($validated['jenis_rekruit'])) {
            $validated['jenis_rekruit'] = $this->detectJenisRekruit($validated['nama']);
        }

        $record->update($validated);

        (new CostController)->syncAggregatedCosts((int) $validated['tahun']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Biaya rekruit berhasil diperbarui.',
                'data' => $record,
            ]);
        }

        return redirect()->route('administrasi.biaya-rekruit.index')
            ->with('success', 'Biaya rekruit "'.$record->nama.'" berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $record = BiayaRekruit::findOrFail($id);
        $name = $record->nama;
        $tahun = (int) ($record->tahun ?: ($record->tanggal ? $record->tanggal->year : date('Y')));

        $record->delete();

        (new CostController)->syncAggregatedCosts($tahun);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Biaya rekruit '{$name}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('administrasi.biaya-rekruit.index')
            ->with('success', "Biaya rekruit '{$name}' berhasil dihapus.");
    }

    /**
     * Download CSV Template for Biaya Rekruit Import
     */
    public function downloadTemplate()
    {
        $headers = ['cost_rekruit_id', 'tanggal', 'nama', 'pic_rekruiter', 'total_biaya'];
        $sampleRows = [
            [1, '2026-05-31', 'REKRUT FAT II 2026 : REALISASI LPJ AN JOSEFHINE E DINAS REKRUT (Sewa Ruangan Meeting Betaive Coworking)', 'Josefhine E', 709091],
            [3, '2026-01-31', 'MAT I 2026 : KLAIM BY PERJDIN AN.BENYAMIN BY DLM RANGKA REKRUT MAT I 2026 & TAT I 2026 KE MEDAN DAN YOGYA', 'Benyamin Basuki Yulianto & Pinondang Lindarto Simanungkalit', 2460660],
        ];

        return ExcelExporter::download('Template_Import_Biaya_Rekruit.xlsx', $headers, $sampleRows, 'Biaya Rekruit');
    }

    /**
     * Import CSV file into Biaya Rekruit
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,text|max:10240',
            'import_mode' => 'nullable|string|in:append,replace',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $importMode = $request->get('import_mode', 'append');

        if ($importMode === 'replace') {
            BiayaRekruit::truncate();
        }

        try {
            $dataset = $this->parseCsvDataset($filePath);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membuka file CSV yang diunggah.');
        }

        if (empty($dataset['headers'])) {
            return back()->with('error', 'Format file CSV tidak valid atau file kosong.');
        }

        $cleanHeaders = $dataset['cleanHeaders'];

        // Normalize header mapping
        $headerMap = [];
        foreach ($cleanHeaders as $index => $clean) {
            if (in_array($clean, ['costrekruitid', 'idbiayarekruit', 'id', 'no', 'vraid'])) {
                $headerMap['id'] = $index;
            } elseif (in_array($clean, ['tanggal', 'date', 'tgl', 'waktu'])) {
                $headerMap['tanggal'] = $index;
            } elseif (in_array($clean, ['nama', 'namareferensi', 'referensi', 'uraian', 'keterangan', 'deskripsi', 'transaksi'])) {
                $headerMap['nama'] = $index;
            } elseif (in_array($clean, ['picrekruiter', 'pic', 'penanggungjawab', 'penanggung_jawab', 'pj', 'rekruiter', 'picrecruiter'])) {
                $headerMap['penanggung_jawab'] = $index;
            } elseif (in_array($clean, ['jenisrekruit', 'jenis', 'kategori', 'kategorirekruit'])) {
                $headerMap['jenis_rekruit'] = $index;
            } elseif (in_array($clean, ['totalbiaya', 'biaya', 'total', 'nominal', 'amount', 'harga'])) {
                $headerMap['total_biaya'] = $index;
            } elseif (in_array($clean, ['tahun', 'year', 'thn'])) {
                $headerMap['tahun'] = $index;
            }
        }

        $insertedCount = 0;
        $updatedCount = 0;

        foreach ($dataset['rows'] as $row) {
            if (empty(array_filter($row))) {
                continue;
            }

            $nama = isset($headerMap['nama']) && isset($row[$headerMap['nama']]) ? trim($row[$headerMap['nama']]) : '';
            if (empty($nama)) {
                continue;
            }

            // Parse Date & derive Year
            $rawDate = isset($headerMap['tanggal']) && isset($row[$headerMap['tanggal']]) ? trim($row[$headerMap['tanggal']]) : '';
            $tanggal = null;
            $tahun = (int) date('Y');

            if (! empty($rawDate)) {
                try {
                    $parsed = Carbon::parse($rawDate);
                    $tanggal = $parsed->format('Y-m-d');
                    $tahun = $parsed->year;
                } catch (\Exception $e) {
                    $tanggal = date('Y-m-d');
                }
            } else {
                $tanggal = date('Y-m-d');
            }

            if (isset($headerMap['tahun']) && ! empty($row[$headerMap['tahun']])) {
                $tahun = (int) $row[$headerMap['tahun']];
            }

            $totalBiaya = isset($headerMap['total_biaya']) ? $this->cleanNumeric($row[$headerMap['total_biaya']], 0) : 0;
            $penanggungJawab = isset($headerMap['penanggung_jawab']) && isset($row[$headerMap['penanggung_jawab']]) ? trim($row[$headerMap['penanggung_jawab']]) : '';

            if (empty($penanggungJawab)) {
                $penanggungJawab = 'Umum / Non-PIC';
            }

            $jenisRekruit = isset($headerMap['jenis_rekruit']) && isset($row[$headerMap['jenis_rekruit']]) ? trim($row[$headerMap['jenis_rekruit']]) : '';
            if (empty($jenisRekruit)) {
                $jenisRekruit = $this->detectJenisRekruit($nama);
            }

            // Check existing record
            $existing = null;
            if (isset($headerMap['id']) && ! empty($row[$headerMap['id']])) {
                $id = (int) $row[$headerMap['id']];
                $existing = BiayaRekruit::find($id);
            }

            if (! $existing) {
                $existing = BiayaRekruit::where('nama', $nama)
                    ->where('tanggal', $tanggal)
                    ->first();
            }

            $dataArray = [
                'tanggal' => $tanggal,
                'nama' => $nama,
                'total_biaya' => $totalBiaya,
                'tahun' => $tahun,
                'jenis_rekruit' => $jenisRekruit,
                'penanggung_jawab' => $penanggungJawab,
            ];

            if ($existing) {
                $existing->update($dataArray);
                $updatedCount++;
            } else {
                BiayaRekruit::create($dataArray);
                $insertedCount++;
            }
        }

        (new CostController)->syncAggregatedCosts((int) date('Y'));

        $msg = "Import CSV Biaya Rekruit berhasil: {$insertedCount} data baru ditambahkan";
        if ($updatedCount > 0) {
            $msg .= ", {$updatedCount} data diperbarui";
        }
        $msg .= '.';

        ActivityLog::log(
            'Import',
            'Biaya Rekruit',
            'Mengimport data Biaya Rekruit dari file '.$file->getClientOriginalName()." ({$insertedCount} baru, {$updatedCount} diperbarui)",
            ['filename' => $file->getClientOriginalName(), 'inserted' => $insertedCount, 'updated' => $updatedCount]
        );

        return redirect()->route('administrasi.biaya-rekruit.index')->with('success', $msg);
    }

    /**
     * Auto detect Jenis Rekruit category based on description keywords
     */
    private function detectJenisRekruit(string $nama): string
    {
        $upper = strtoupper($nama);

        if (str_contains($upper, 'MCU') || str_contains($upper, 'RS PRIMA') || str_contains($upper, 'KIMIA FARMA') || str_contains($upper, 'LABKLIN')) {
            return 'Medical Check Up (MCU) Calon Karyawan';
        }

        if (str_contains($upper, 'TIKET') || str_contains($upper, 'TRAVEL') || str_contains($upper, 'BUS') || str_contains($upper, 'GRAB') || str_contains($upper, 'GO CAR') || str_contains($upper, 'PANJAR')) {
            return 'Tiket & Transportasi Trainee / Kandidat';
        }

        if (str_contains($upper, 'DINAS REKRUT') || str_contains($upper, 'PERJDIN') || str_contains($upper, 'PERDIN') || str_contains($upper, 'MEETING') || str_contains($upper, 'COWORKING') || str_contains($upper, 'UANG SAKU') || str_contains($upper, 'UANG MAKAN') || str_contains($upper, 'PENGINAPAN') || str_contains($upper, 'HOTEL')) {
            return 'Perjalanan Dinas & Akomodasi Recruiter';
        }

        if (str_contains($upper, 'MESS') || str_contains($upper, 'BELANJA BASAH') || str_contains($upper, 'BELANJA KERING') || str_contains($upper, 'KONSUMSI') || str_contains($upper, 'HIKMAH GROUP') || str_contains($upper, 'INTERVIEW')) {
            return 'Konsumsi & Perlengkapan Mess Rekrutmen';
        }

        return 'Biaya Operasional Rekrutmen';
    }

    /**
     * Export recruitment expenses report.
     */
    public function export(Request $request)
    {
        $selectedYear = $request->get('year');
        $selectedMonth = $request->get('month', 'all');
        $headers = [
            'cost_rekruit_id',
            'tanggal',
            'nama',
            'pic_rekruiter',
            'jenis_rekruit',
            'total_biaya',
        ];

        $query = BiayaRekruit::orderBy('tanggal', 'desc');

        if ($selectedYear !== 'all' && ! empty($selectedYear)) {
            $yearInt = (int) $selectedYear;
            $query->where(function ($q) use ($yearInt) {
                $q->where('tahun', $yearInt)
                    ->orWhereYear('tanggal', $yearInt);
            });
        }

        if (is_numeric($selectedMonth) && (int) $selectedMonth >= 1 && (int) $selectedMonth <= 12) {
            $query->whereMonth('tanggal', (int) $selectedMonth);
        }

        $records = $query->get();
        $dataRows = [];

        foreach ($records as $row) {
            $dataRows[] = [
                $row->id_biaya_rekruit,
                $row->tanggal ? $row->tanggal->format('Y-m-d') : '-',
                $row->nama,
                $row->penanggung_jawab ?? '-',
                $row->jenis_rekruit ?? '-',
                $row->total_biaya,
            ];
        }

        $filename = 'Biaya_Rekruit_Report_'.date('Y-m-d').'.xlsx';

        return ExcelExporter::download($filename, $headers, $dataRows, 'Biaya Rekruit');
    }

    /**
     * Delete all Biaya Rekruit records.
     */
    public function destroyAll(Request $request)
    {
        $deleted = BiayaRekruit::query()->delete();

        (new CostController)->syncAggregatedCosts((int) date('Y'));

        ActivityLog::log('Hapus', 'Biaya Rekruit', "Seluruh data Biaya Rekruit ({$deleted} data) berhasil dihapus.", [
            'count' => $deleted,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Seluruh data Biaya Rekruit ({$deleted} data) berhasil dihapus.",
            ]);
        }

        return redirect()->route('administrasi.biaya-rekruit.index')
            ->with('success', "Seluruh data Biaya Rekruit ({$deleted} data) berhasil dihapus.");
    }

    /**
     * Update total peserta reguler hasil rekrutmen tahunan.
     */
    public function updatePeserta(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:2099',
            'jumlah_peserta' => 'required|integer|min:0',
        ]);

        $year = (int) $validated['year'];
        $peserta = (int) $validated['jumlah_peserta'];

        AppSetting::set("biaya_rekruit_peserta_{$year}", $peserta);

        ActivityLog::log('Update', 'Biaya Rekrut', "Memperbarui jumlah peserta reguler rekrutmen tahun {$year} menjadi {$peserta} orang", [
            'year' => $year,
            'jumlah_peserta' => $peserta,
        ]);

        return redirect()->back()->with('success', "Jumlah peserta reguler tahun {$year} berhasil diperbarui ({$peserta} orang).");
    }
}
