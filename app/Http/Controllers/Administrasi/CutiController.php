<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Cuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Available Years
        $dbYears = Cuti::select('tahun')->distinct()->pluck('tahun')->toArray();
        $defaultYears = [2024, 2025, 2026, 2027, 2028];
        $years = array_unique(array_merge($defaultYears, $dbYears));
        sort($years);

        // 2. Selected Year & Filters
        $selectedYear = (int) $request->get('year', 2026);
        $search = $request->get('search', '');
        $filterJabatan = $request->get('jabatan', '');
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

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if (!empty($filterJabatan)) {
            $query->where('jabatan', $filterJabatan);
        }

        // Distinct positions for filter dropdown
        $positions = Cuti::whereNotNull('jabatan')
            ->where('jabatan', '!=', '')
            ->select('jabatan')
            ->distinct()
            ->pluck('jabatan')
            ->toArray();

        // Fetch records
        $allYearRecords = (clone $query)->get();

        // Calculate carryover dynamically if not set or update display
        foreach ($allYearRecords as $item) {
            // If historical carryover exists in previous years, we can sync/display it
            $historicalCarryover = Cuti::getHistoricalCarryover($item->nik, $selectedYear);
            if ($historicalCarryover > 0 && $item->sisa_hak_cuti_carryover == 0) {
                $item->sisa_hak_cuti_carryover = $historicalCarryover;
            }
        }

        // Apply status filter in collection if needed
        if (!empty($filterStatus)) {
            $records = $query->orderBy('nama', 'asc')->get();
            if ($filterStatus === 'positif') {
                $records = $records->filter(fn($r) => $r->sisa_cuti > 0);
            } elseif ($filterStatus === 'habis') {
                $records = $records->filter(fn($r) => $r->sisa_cuti === 0);
            } elseif ($filterStatus === 'minus') {
                $records = $records->filter(fn($r) => $r->sisa_cuti < 0);
            }
            // Paginate manually from filtered collection
            $page = (int) $request->get('page', 1);
            $perPage = 15;
            $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                $records->forPage($page, $perPage),
                $records->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $records = $paginated;
        } else {
            $records = $query->orderBy('nama', 'asc')
                ->paginate(15)
                ->withQueryString();
        }

        // 4. Calculate Summary Totals
        $totals = [
            'total_karyawan' => $allYearRecords->count(),
            'total_hak_cuti' => $allYearRecords->sum('hak_cuti'),
            'total_cuti_diambil' => $allYearRecords->sum('cuti_diambil'),
            'total_sisa_cuti' => $allYearRecords->sum(function($r) {
                return $r->sisa_cuti;
            }),
        ];

        return view('Administrasi.Pages.cuti', compact(
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
            'search',
            'filterJabatan',
            'filterStatus'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:50',
            'nama' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:100',
            'tmk' => 'nullable|date',
            'tahun' => 'required|integer',
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

        // Default numeric zeros
        $numericFields = ['sisa_hak_cuti_carryover', 'cuti_diambil', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
        foreach ($numericFields as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field] !== '' ? (int) $validated[$field] : 0;
        }

        // If carryover is not provided, try to fetch from historical balance (2 years prior)
        if ($validated['sisa_hak_cuti_carryover'] === 0) {
            $historical = Cuti::getHistoricalCarryover($validated['nik'], $validated['tahun']);
            if ($historical > 0) {
                $validated['sisa_hak_cuti_carryover'] = $historical;
            }
        }

        // Check if total monthly usage was provided and cuti_diambil was 0, auto-fill cuti_diambil if monthly sum exists
        $monthlySum = $validated['jan'] + $validated['feb'] + $validated['mar'] + $validated['apr'] +
                      $validated['mei'] + $validated['jun'] + $validated['jul'] + $validated['agu'] +
                      $validated['sep'] + $validated['okt'] + $validated['nov'] + $validated['des'];
        if ($validated['cuti_diambil'] === 0 && $monthlySum > 0) {
            $validated['cuti_diambil'] = $monthlySum;
        }

        // Upsert or create check
        $existing = Cuti::where('nik', $validated['nik'])
            ->where('tahun', $validated['tahun'])
            ->first();

        if ($existing) {
            $existing->update($validated);
            $cuti = $existing;
            $message = "Data cuti untuk karyawan {$validated['nama']} ({$validated['nik']}) tahun {$validated['tahun']} berhasil diperbarui.";
        } else {
            $cuti = Cuti::create($validated);
            $message = "Data cuti untuk karyawan {$validated['nama']} ({$validated['nik']}) tahun {$validated['tahun']} berhasil ditambahkan.";
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $cuti
            ]);
        }

        return redirect()->route('administrasi.cuti.index', [
            'year' => $validated['tahun']
        ])->with('success', $message);
    }

    /**
     * Display the specified resource (for detail modal/AJAX).
     */
    public function show($id)
    {
        $cuti = Cuti::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'cuti_id' => $cuti->cuti_id,
                'nik' => $cuti->nik,
                'nama' => $cuti->nama,
                'jabatan' => $cuti->jabatan ?? '-',
                'tmk' => $cuti->tmk ? $cuti->tmk->format('d M Y') : '-',
                'tmk_raw' => $cuti->tmk ? $cuti->tmk->format('Y-m-d') : '',
                'tahun' => $cuti->tahun,
                'periode_hak_cuti' => $cuti->periode_hak_cuti,
                'hak_cuti' => $cuti->hak_cuti,
                'sisa_hak_cuti_carryover' => $cuti->sisa_hak_cuti_carryover,
                'periode_sisa_carryover_label' => $cuti->periode_sisa_carryover_label,
                'cuti_diambil' => $cuti->cuti_diambil,
                'sisa_cuti' => $cuti->sisa_cuti,
                'monthly' => [
                    'jan' => $cuti->jan,
                    'feb' => $cuti->feb,
                    'mar' => $cuti->mar,
                    'apr' => $cuti->apr,
                    'mei' => $cuti->mei,
                    'jun' => $cuti->jun,
                    'jul' => $cuti->jul,
                    'agu' => $cuti->agu,
                    'sep' => $cuti->sep,
                    'okt' => $cuti->okt,
                    'nov' => $cuti->nov,
                    'des' => $cuti->des,
                ],
                'total_bulanan' => $cuti->total_bulanan,
                'keterangan' => $cuti->keterangan ?? '-',
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);

        $validated = $request->validate([
            'nik' => 'required|string|max:50',
            'nama' => 'required|string|max:150',
            'jabatan' => 'nullable|string|max:100',
            'tmk' => 'nullable|date',
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

        $numericFields = ['sisa_hak_cuti_carryover', 'cuti_diambil', 'jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];
        foreach ($numericFields as $field) {
            $validated[$field] = isset($validated[$field]) && $validated[$field] !== '' ? (int) $validated[$field] : 0;
        }

        $cuti->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data cuti berhasil diperbarui.',
                'data' => $cuti
            ]);
        }

        return redirect()->route('administrasi.cuti.index', [
            'year' => $cuti->tahun
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
                'message' => "Data cuti {$nama} ({$nik}) berhasil dihapus."
            ]);
        }

        return redirect()->route('administrasi.cuti.index', [
            'year' => $tahun
        ])->with('success', "Data cuti karyawan {$nama} ({$nik}) berhasil dihapus.");
    }

    /**
     * Store new year navigation
     */
    public function storeYear(Request $request)
    {
        $request->validate([
            'new_year' => 'required|integer|min:2000|max:2099'
        ]);

        $newYear = (int) $request->new_year;

        return redirect()->route('administrasi.cuti.index', [
            'year' => $newYear
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

        $filename = "Laporan_Cuti_{$year}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($year, $periodeHakCuti, $sisaStart, $sisaEnd) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

            fputcsv($handle, [
                'NIK',
                'Nama Karyawan',
                'Jabatan',
                'TMK',
                'Periode Hak Cuti',
                "Hak Cuti {$periodeHakCuti}",
                "Sisa Hak Cuti {$sisaStart}-{$sisaEnd}",
                'Cuti yang Diambil',
                'Sisa Cuti',
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
                'Total Rincian Bulanan',
                'Tahun Periode'
            ]);

            $data = Cuti::where('tahun', $year)->orderBy('nama', 'asc')->get();

            foreach ($data as $row) {
                fputcsv($handle, [
                    $row->nik,
                    $row->nama,
                    $row->jabatan ?? '-',
                    $row->tmk ? $row->tmk->format('d/m/Y') : '-',
                    $row->periode_hak_cuti,
                    $row->hak_cuti,
                    $row->sisa_hak_cuti_carryover,
                    $row->cuti_diambil,
                    $row->sisa_cuti,
                    $row->jan, $row->feb, $row->mar, $row->apr, $row->mei, $row->jun,
                    $row->jul, $row->agu, $row->sep, $row->okt, $row->nov, $row->des,
                    $row->total_bulanan,
                    $row->tahun
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
