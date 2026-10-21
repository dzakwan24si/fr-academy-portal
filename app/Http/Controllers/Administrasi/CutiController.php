<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Administrasi\Cuti;
use App\Services\ExcelExporter;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Dynamic Year Selection & Filters
        $selectedYear = (int) $request->get('year', 2026);
        $dbYears = Cuti::select('tahun')->distinct()->whereNotNull('tahun')->pluck('tahun')->toArray();
        $baseYears = range(date('Y') - 3, date('Y') + 2);
        $years = array_values(array_unique(array_filter(array_merge($baseYears, $dbYears, [$selectedYear]))));
        sort($years);

        $search = $request->get('search', '');
        $filterJabatan = $request->get('jabatan', '');
        $filterJenisJabatan = $request->get('jenis_jabatan', '');
        $filterStatus = $request->get('status', ''); // 'positif', 'habis', 'minus'

        // Dynamic Year Labels
        $periodeHakCuti = $selectedYear - 1;
        $hakCutiLabel = "Hak Cuti {$periodeHakCuti}";
        $sisaCarryoverStart = $selectedYear - 3;
        $sisaCarryoverEnd = $selectedYear - 2;
        $sisaCarryoverLabel = "Sisa Hak Cuti {$sisaCarryoverStart}–{$sisaCarryoverEnd}";
        $periodePengambilanLabel = "Periode Pengambilan Cuti {$selectedYear}";

        // 3. Query Data
        $query = Cuti::where('tahun', $selectedYear);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhere('jenis_jabatan', 'like', "%{$search}%");
            });
        }

        if (! empty($filterJabatan)) {
            $query->where('jabatan', $filterJabatan);
        }

        if (! empty($filterJenisJabatan)) {
            $query->where('jenis_jabatan', $filterJenisJabatan);
        }

        // Distinct positions for filter dropdown
        $positions = Cuti::whereNotNull('jabatan')
            ->where('jabatan', '!=', '')
            ->select('jabatan')
            ->distinct()
            ->pluck('jabatan')
            ->toArray();

        $jenisJabatanList = ['Staf', 'Karyawan PBT', 'Karyawan KHT'];

        // Fetch records
        $allYearRecords = (clone $query)->get();

        // Calculate carryover dynamically if not set
        foreach ($allYearRecords as $item) {
            $historicalCarryover = Cuti::getHistoricalCarryover($item->nik, $selectedYear);
            if ($historicalCarryover > 0 && $item->sisa_hak_cuti_carryover == 0) {
                $item->sisa_hak_cuti_carryover = $historicalCarryover;
            }
        }

        // Apply status filter in collection if needed
        if (! empty($filterStatus)) {
            $records = $query->orderedHierarchy()->get();
            if ($filterStatus === 'positif') {
                $records = $records->filter(fn ($r) => $r->sisa_cuti > 0);
            } elseif ($filterStatus === 'habis') {
                $records = $records->filter(fn ($r) => $r->sisa_cuti === 0);
            } elseif ($filterStatus === 'minus') {
                $records = $records->filter(fn ($r) => $r->sisa_cuti < 0);
            }

            $page = (int) $request->get('page', 1);
            $perPage = 50;
            $paginated = new LengthAwarePaginator(
                $records->forPage($page, $perPage),
                $records->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $records = $paginated;
        } else {
            $records = $query->orderedHierarchy()
                ->paginate(50)
                ->withQueryString();
        }

        // 4. Calculate Summary Totals
        $totals = [
            'total_karyawan' => $allYearRecords->count(),
            'total_hak_cuti' => $allYearRecords->sum('hak_cuti'),
            'total_cuti_diambil' => $allYearRecords->sum('cuti_diambil'),
            'total_sisa_cuti' => $allYearRecords->sum(fn ($r) => $r->sisa_cuti),
        ];

        return view('Administrasi.Pages.Administrasi.cuti', compact(
            'years',
            'selectedYear',
            'periodeHakCuti',
            'hakCutiLabel',
            'sisaCarryoverLabel',
            'periodePengambilanLabel',
            'sisaCarryoverStart',
            'sisaCarryoverEnd',
            'records',
            'totals',
            'positions',
            'jenisJabatanList',
            'search',
            'filterJabatan',
            'filterJenisJabatan',
            'filterStatus'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateCuti($request);

        // If carryover is 0, attempt auto-fill from 2 years prior
        if ($validated['sisa_hak_cuti_carryover'] === 0) {
            $historical = Cuti::getHistoricalCarryover($validated['nik'], $validated['tahun']);
            if ($historical > 0) {
                $validated['sisa_hak_cuti_carryover'] = $historical;
            }
        }

        // Auto-fill cuti_diambil if monthly sum exists and cuti_diambil is 0
        $monthlySum = array_sum(array_map(fn ($m) => (int) ($validated[$m] ?? 0), Cuti::MONTHS));
        if ($validated['cuti_diambil'] === 0 && $monthlySum > 0) {
            $validated['cuti_diambil'] = $monthlySum;
        }

        $cuti = Cuti::updateOrCreate(
            ['nik' => $validated['nik'], 'tahun' => $validated['tahun']],
            $validated
        );

        $message = "Data cuti karyawan {$validated['nama']} ({$validated['nik']}) berhasil disimpan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $cuti,
            ]);
        }

        return redirect()->route('administrasi.cuti.index', [
            'year' => $validated['tahun'],
        ])->with('success', $message);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx|max:20480']);
        $file = $request->file('file');
        $rows = $this->readSpreadsheet($file->getRealPath(), $file->getClientOriginalExtension());
        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'File tidak memiliki data.']);
        }

        $rawHeaders = array_shift($rows);
        $headers = array_map(fn ($value) => strtolower(trim(str_replace(["\xEF\xBB\xBF", '"', "'"], '', (string) $value))), $rawHeaders);

        // Check format: Long format (contains 'bulan' or 'pengambilan_cuti') vs Wide format
        $isLongFormat = in_array('bulan', $headers, true) || in_array('pengambilan_cuti', $headers, true);

        $monthMap = [
            1 => 'jan', 2 => 'feb', 3 => 'mar', 4 => 'apr', 5 => 'mei', 6 => 'jun',
            7 => 'jul', 8 => 'agu', 9 => 'sep', 10 => 'okt', 11 => 'nov', 12 => 'des',
            'jan' => 'jan', 'feb' => 'feb', 'mar' => 'mar', 'apr' => 'apr', 'mei' => 'mei', 'jun' => 'jun',
            'jul' => 'jul', 'agu' => 'agu', 'sep' => 'sep', 'okt' => 'okt', 'nov' => 'nov', 'des' => 'des',
            'januari' => 'jan', 'februari' => 'feb', 'maret' => 'mar', 'april' => 'apr', 'mei' => 'mei',
            'juni' => 'jun', 'juli' => 'jul', 'agustus' => 'agu', 'september' => 'sep', 'oktober' => 'okt',
            'november' => 'nov', 'desember' => 'des',
        ];

        $imported = 0;

        DB::transaction(function () use ($rows, $headers, $isLongFormat, $monthMap, &$imported) {
            if ($isLongFormat) {
                // Group by employee and year
                $grouped = [];
                foreach ($rows as $row) {
                    if (empty(array_filter($row, fn ($c) => trim((string) $c) !== ''))) {
                        continue;
                    }
                    $values = array_pad($row, count($headers), null);
                    $data = array_combine($headers, array_slice($values, 0, count($headers)));

                    $nama = trim((string) ($data['nama'] ?? ''));
                    if (empty($nama)) {
                        continue;
                    }

                    $nik = trim((string) ($data['nik'] ?? ''));
                    if (empty($nik)) {
                        $sanitized = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($nama));
                        $nik = 'FRA-'.substr($sanitized, 0, 12);
                    }

                    $tahun = ! empty($data['tahun_cuti']) ? (int) $data['tahun_cuti'] : (! empty($data['tahun']) ? (int) $data['tahun'] : 2026);
                    $key = "{$nik}_{$tahun}";

                    if (! isset($grouped[$key])) {
                        $tmkVal = null;
                        if (! empty($data['tmk'])) {
                            $time = strtotime((string) $data['tmk']);
                            $tmkVal = $time ? date('Y-m-d', $time) : null;
                        }

                        $carryover = 0;
                        if (isset($data['sisa_hak_cuti_carryover'])) {
                            $carryover = (int) $data['sisa_hak_cuti_carryover'];
                        } else {
                            $carryover = Cuti::getHistoricalCarryover($nik, $tahun);
                        }

                        $cutiDiambil = 0;
                        if (isset($data['cuti_yang_sudah_diambil'])) {
                            $cutiDiambil = (int) $data['cuti_yang_sudah_diambil'];
                        } elseif (isset($data['cuti_diambil'])) {
                            $cutiDiambil = (int) $data['cuti_diambil'];
                        }

                        $hakCuti = 12;
                        if (isset($data['hak_cuti']) && is_numeric($data['hak_cuti'])) {
                            $hakCuti = (int) $data['hak_cuti'];
                        } elseif (isset($data['hak_cuti_tahun']) && is_numeric($data['hak_cuti_tahun'])) {
                            $hakCuti = (int) $data['hak_cuti_tahun'];
                        } elseif (isset($data['sisa_cuti']) && is_numeric($data['sisa_cuti'])) {
                            $hakCuti = max(0, (int) $data['sisa_cuti'] - $carryover + $cutiDiambil);
                        }

                        $grouped[$key] = [
                            'nik' => $nik,
                            'nama' => $nama,
                            'jabatan' => ! empty($data['jabatan']) ? trim((string) $data['jabatan']) : null,
                            'jenis_jabatan' => ! empty($data['jenis_jabatan']) ? trim((string) $data['jenis_jabatan']) : null,
                            'tmk' => $tmkVal,
                            'tahun' => $tahun,
                            'hak_cuti' => $hakCuti,
                            'sisa_hak_cuti_carryover' => $carryover,
                            'cuti_diambil' => $cutiDiambil,
                            'jan' => 0, 'feb' => 0, 'mar' => 0, 'apr' => 0, 'mei' => 0, 'jun' => 0,
                            'jul' => 0, 'agu' => 0, 'sep' => 0, 'okt' => 0, 'nov' => 0, 'des' => 0,
                            'keterangan' => ! empty($data['keterangan']) ? trim((string) $data['keterangan']) : null,
                        ];
                    }

                    // Add monthly pengambilan_cuti
                    $bulanRaw = strtolower(trim((string) ($data['bulan'] ?? '')));
                    $pengambilan = (int) ($data['pengambilan_cuti'] ?? 0);
                    if ($bulanRaw !== '' && isset($monthMap[$bulanRaw])) {
                        $col = $monthMap[$bulanRaw];
                        $grouped[$key][$col] += $pengambilan;
                    }
                }

                foreach ($grouped as $item) {
                    Cuti::updateOrCreate(
                        ['nik' => $item['nik'], 'tahun' => $item['tahun']],
                        $item
                    );
                    $imported++;
                }
            } else {
                // Wide format
                $allowed = ['nik', 'nama', 'jabatan', 'jenis_jabatan', 'tmk', 'tahun', 'hak_cuti', 'sisa_hak_cuti_carryover', 'cuti_diambil', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des', 'keterangan'];
                $numericFields = ['tahun', 'hak_cuti', 'sisa_hak_cuti_carryover', 'cuti_diambil', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];

                foreach ($rows as $row) {
                    if (empty(array_filter($row, fn ($c) => trim((string) $c) !== ''))) {
                        continue;
                    }
                    $values = array_pad($row, count($headers), null);
                    $record = array_combine($headers, array_slice($values, 0, count($headers)));

                    $nama = trim((string) ($record['nama'] ?? ''));
                    if (empty($nama)) {
                        continue;
                    }

                    $nik = trim((string) ($record['nik'] ?? ''));
                    if (empty($nik)) {
                        $sanitized = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($nama));
                        $nik = 'FRA-'.substr($sanitized, 0, 12);
                    }
                    $record['nik'] = $nik;

                    if (isset($record['hak_cuti_tahun']) && ! isset($record['hak_cuti'])) {
                        $record['hak_cuti'] = (int) $record['hak_cuti_tahun'];
                    }
                    if (isset($record['cuti_yang_sudah_diambil']) && ! isset($record['cuti_diambil'])) {
                        $record['cuti_diambil'] = (int) $record['cuti_yang_sudah_diambil'];
                    }

                    $record = array_intersect_key($record, array_flip($allowed));

                    foreach ($record as $field => $value) {
                        if ($value === null || strtolower(trim((string) $value)) === 'null' || trim((string) $value) === '') {
                            $record[$field] = null;
                        }
                    }
                    foreach ($numericFields as $field) {
                        if (array_key_exists($field, $record)) {
                            $record[$field] = is_numeric($record[$field]) ? (int) $record[$field] : 0;
                        }
                    }
                    if (! empty($record['tmk'])) {
                        $time = strtotime((string) $record['tmk']);
                        $record['tmk'] = $time ? date('Y-m-d', $time) : null;
                    }
                    $record['tahun'] ??= 2026;
                    $record['hak_cuti'] ??= 12;
                    $record['sisa_hak_cuti_carryover'] ??= Cuti::getHistoricalCarryover($record['nik'], $record['tahun']);
                    $record['cuti_diambil'] ??= 0;
                    foreach (Cuti::MONTHS as $month) {
                        $record[$month] ??= 0;
                    }

                    Cuti::updateOrCreate(['nik' => $record['nik'], 'tahun' => $record['tahun']], $record);
                    $imported++;
                }
            }
        });

        ActivityLog::log(
            'Import',
            'Cuti',
            "Mengimport {$imported} data karyawan Cuti dari file ".$file->getClientOriginalName(),
            ['filename' => $file->getClientOriginalName(), 'count' => $imported]
        );

        return back()->with('success', "{$imported} data karyawan Cuti berhasil diproses dan disimpan.");
    }

    private function readSpreadsheet(string $path, string $extension): array
    {
        return SpreadsheetReader::read($path, $extension);
    }

    /**
     * Display the specified resource (for detail modal/AJAX).
     */
    public function show($id)
    {
        $cuti = Cuti::findOrFail($id);

        $monthly = [];
        foreach (Cuti::MONTHS as $m) {
            $monthly[$m] = $cuti->$m;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'cuti_id' => $cuti->cuti_id,
                'nik' => $cuti->nik,
                'nama' => $cuti->nama,
                'jabatan' => $cuti->jabatan ?? '-',
                'jenis_jabatan' => $cuti->jenis_jabatan ?? '-',
                'tmk' => $cuti->tmk ? $cuti->tmk->format('d M Y') : '-',
                'tmk_raw' => $cuti->tmk ? $cuti->tmk->format('Y-m-d') : '',
                'tahun' => $cuti->tahun,
                'periode_hak_cuti' => $cuti->periode_hak_cuti,
                'hak_cuti' => $cuti->hak_cuti,
                'sisa_hak_cuti_carryover' => $cuti->sisa_hak_cuti_carryover,
                'periode_sisa_carryover_label' => $cuti->periode_sisa_carryover_label,
                'cuti_diambil' => $cuti->cuti_diambil,
                'sisa_cuti' => $cuti->sisa_cuti,
                'monthly' => $monthly,
                'total_bulanan' => $cuti->total_bulanan,
                'keterangan' => $cuti->keterangan ?? '-',
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $validated = $this->validateCuti($request);

        $cuti->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data cuti berhasil diperbarui.',
                'data' => $cuti,
            ]);
        }

        return redirect()->route('administrasi.cuti.index', [
            'year' => $cuti->tahun,
        ])->with('success', "Data cuti karyawan {$cuti->nama} ({$cuti->nik}) berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $tahun = $cuti->tahun;
        $nama = $cuti->nama;
        $nik = $cuti->nik;

        $cuti->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data cuti {$nama} ({$nik}) berhasil dihapus.",
            ]);
        }

        return redirect()->route('administrasi.cuti.index', [
            'year' => $tahun,
        ])->with('success', "Data cuti karyawan {$nama} ({$nik}) berhasil dihapus.");
    }

    /**
     * Store new year navigation
     */
    public function storeYear(Request $request)
    {
        $request->validate([
            'new_year' => 'required|integer|min:2000|max:2099',
        ]);

        $newYear = (int) $request->new_year;

        return redirect()->route('administrasi.cuti.index', [
            'year' => $newYear,
        ])->with('success', "Tahun {$newYear} berhasil dipilih sebagai periode aktif.");
    }

    /**
     * Export Report to CSV
     */
    public function export(Request $request)
    {
        $year = (int) $request->get('year', 2026);
        $periodeHakCuti = $year - 1;
        $sisaStart = $year - 3;
        $sisaEnd = $year - 2;

        $headers = [
            'NIK',
            'Nama Karyawan',
            'Jabatan',
            'Jenis Jabatan',
            'TMK',
            'Periode Hak Cuti',
            "Hak Cuti {$periodeHakCuti}",
            "Sisa Hak Cuti {$sisaStart}-{$sisaEnd}",
            'Cuti yang Diambil',
            'Sisa Cuti',
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
            'Total Rincian Bulanan',
            'Tahun Periode',
        ];

        $data = Cuti::where('tahun', $year)->orderedHierarchy()->get();
        $dataRows = [];

        foreach ($data as $row) {
            $dataRows[] = [
                $row->nik,
                $row->nama,
                $row->jabatan ?? '-',
                $row->jenis_jabatan ?? '-',
                $row->tmk ? $row->tmk->format('d/m/Y') : '-',
                $row->periode_hak_cuti,
                $row->hak_cuti,
                $row->sisa_hak_cuti_carryover,
                $row->cuti_diambil,
                $row->sisa_cuti,
                $row->jan, $row->feb, $row->mar, $row->apr, $row->mei, $row->jun,
                $row->jul, $row->agu, $row->sep, $row->okt, $row->nov, $row->des,
                $row->total_bulanan,
                $row->tahun,
            ];
        }

        $filename = "Laporan_Cuti_{$year}.xlsx";

        return ExcelExporter::download($filename, $headers, $dataRows, 'Laporan Cuti');
    }

    /**
     * Download Excel template for Cuti import
     */
    public function downloadTemplate()
    {
        $headers = [
            'nik', 'nama', 'jabatan', 'jenis_jabatan', 'tmk', 'hak_cuti',
            'sisa_hak_cuti_carryover', 'cuti_diambil',
            'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des',
            'tahun',
        ];

        $sampleRows = [
            [
                'FRA-001', 'Budi Santoso', 'Manager Ops', 'Staff', '2020-01-15', 12,
                0, 2,
                1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                2026,
            ],
            [
                'FRA-002', 'Siti Aminah', 'Asisten Agronomi', 'Staff', '2021-03-20', 12,
                2, 1,
                0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
                2026,
            ],
        ];

        return ExcelExporter::download('Template_Import_Cuti.xlsx', $headers, $sampleRows, 'Template Cuti');
    }

    /**
     * Validate common Cuti input request
     */
    private function validateCuti(Request $request): array
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:50',
            'nama' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:100',
            'jenis_jabatan' => 'nullable|string|max:50',
            'tmk' => 'nullable|date',
            'tahun' => 'nullable|integer',
            'hak_cuti' => 'required|integer|min:0',
            'sisa_hak_cuti_carryover' => 'nullable|integer',
            'cuti_diambil' => 'nullable|integer|min:0',
            'jan' => 'nullable|integer|min:0',
            'feb' => 'nullable|integer|min:0',
            'mar' => 'nullable|integer|min:0',
            'apr' => 'nullable|integer|min:0',
            'mei' => 'nullable|integer|min:0',
            'jun' => 'nullable|integer|min:0',
            'jul' => 'nullable|integer|min:0',
            'agu' => 'nullable|integer|min:0',
            'sep' => 'nullable|integer|min:0',
            'okt' => 'nullable|integer|min:0',
            'nov' => 'nullable|integer|min:0',
            'des' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        foreach (array_merge(['sisa_hak_cuti_carryover', 'cuti_diambil'], Cuti::MONTHS) as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field] !== '' ? (int) $validated[$field] : 0;
        }

        if (empty($validated['tahun'])) {
            $validated['tahun'] = (int) date('Y');
        } else {
            $validated['tahun'] = (int) $validated['tahun'];
        }

        return $validated;
    }
}
