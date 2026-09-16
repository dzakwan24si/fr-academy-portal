<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\Administrasi\Rekrutmen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\ExcelExporter;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RekrutmenController extends Controller
{
    /**
     * Display the Rekrutmen management page.
     */
    public function index()
    {
        return view('Administrasi.Pages.Administrasi.rekrutmen');
    }

    /**
     * Get data with filters, summary KPIs, charts aggregation, and distinct lists.
     */
    public function getData(Request $request)
    {
        $query = Rekrutmen::query();

        // 1. Filter Lokasi (Single or Array / Multi-select)
        if ($request->filled('lokasi')) {
            $lokasi = $request->input('lokasi');
            if (is_array($lokasi)) {
                $lokasiClean = array_filter($lokasi, fn($l) => $l !== 'all' && $l !== 'Semua Lokasi' && !empty($l));
                if (!empty($lokasiClean)) {
                    $query->whereIn('lokasi_rekrut', $lokasiClean);
                }
            } elseif ($lokasi !== 'all' && $lokasi !== 'Semua Lokasi') {
                $query->where('lokasi_rekrut', $lokasi);
            }
        }

        // 2. Filter Metode Rekrutmen (Online / Offline / Semua)
        if ($request->filled('metode_rekrutmen') && $request->input('metode_rekrutmen') !== 'all') {
            $metode = ucfirst(strtolower($request->input('metode_rekrutmen')));
            if (in_array($metode, ['Online', 'Offline'])) {
                $query->where('metode_rekrutmen', $metode);
            }
        } elseif ($request->filled('mode') && $request->input('mode') !== 'all') {
            $metode = ucfirst(strtolower($request->input('mode')));
            if (in_array($metode, ['Online', 'Offline'])) {
                $query->where('metode_rekrutmen', $metode);
            }
        }

        // 3. Filter Tahun
        if ($request->filled('tahun')) {
            $tahun = $request->input('tahun');
            if (is_array($tahun)) {
                $tahunClean = array_filter($tahun, fn($t) => $t !== 'all' && !empty($t));
                if (!empty($tahunClean)) {
                    $query->whereIn('tahun', $tahunClean);
                }
            } elseif ($tahun !== 'all' && !empty($tahun)) {
                $query->where('tahun', (int) $tahun);
            }
        }

        // 4. Filter Program
        if ($request->filled('program') && $request->input('program') !== 'all') {
            $query->where('program', $request->input('program'));
        }

        // 5. Filter Keyword Search
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('lokasi_rekrut', 'like', "%{$search}%")
                  ->orWhere('metode_rekrutmen', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%")
                  ->orWhere('tanggal', 'like', "%{$search}%")
                  ->orWhere('tahun', 'like', "%{$search}%")
                  ->orWhere('no', 'like', "%{$search}%");
            });
        }

        // 6. Filter Tanggal Range (optional)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->input('start_date'), $request->input('end_date')]);
        }

        // Calculate Totals & Summary for the filtered dataset
        $allFiltered = (clone $query)->get();

        $totalHadir = (int) $allFiltered->sum('jumlah_peserta_hadir');
        $totalLolosPsikotest = (int) $allFiltered->sum('jumlah_lolos_psikotest');
        $totalHadirInterviewAwal = (int) $allFiltered->sum('jumlah_hadir_interview_awal');
        $totalLolosInterviewAwal = (int) $allFiltered->sum('jumlah_lolos_interview_awal');
        $totalLolosInterviewUser = (int) $allFiltered->sum('jumlah_lolos_interview_user');
        $totalIkutMcu = (int) $allFiltered->sum('jumlah_ikut_mcu');
        $totalLolosMcu = (int) $allFiltered->sum('jumlah_lolos_mcu');
        $totalJoinFr = (int) $allFiltered->sum('join_fr');
        $batchCount = $allFiltered->count();

        $overallPassRate = $totalHadir > 0 ? round($totalJoinFr / $totalHadir, 6) : 0;
        $avgPassRate = $batchCount > 0 ? round($allFiltered->avg('persen_lulus'), 6) : 0;

        // Funnel calculation with step-by-step conversion percentages
        $funnel = [
            [
                'stage' => 'Peserta Hadir',
                'count' => $totalHadir,
                'pct_of_total' => 100,
                'pct_of_prev' => 100,
            ],
            [
                'stage' => 'Lolos Psikotest',
                'count' => $totalLolosPsikotest,
                'pct_of_total' => $totalHadir > 0 ? round(($totalLolosPsikotest / $totalHadir) * 100, 2) : 0,
                'pct_of_prev' => $totalHadir > 0 ? round(($totalLolosPsikotest / $totalHadir) * 100, 2) : 0,
            ],
            [
                'stage' => 'Hadir Interview Awal',
                'count' => $totalHadirInterviewAwal,
                'pct_of_total' => $totalHadir > 0 ? round(($totalHadirInterviewAwal / $totalHadir) * 100, 2) : 0,
                'pct_of_prev' => $totalLolosPsikotest > 0 ? round(($totalHadirInterviewAwal / $totalLolosPsikotest) * 100, 2) : 0,
            ],
            [
                'stage' => 'Lolos Interview Awal',
                'count' => $totalLolosInterviewAwal,
                'pct_of_total' => $totalHadir > 0 ? round(($totalLolosInterviewAwal / $totalHadir) * 100, 2) : 0,
                'pct_of_prev' => $totalHadirInterviewAwal > 0 ? round(($totalLolosInterviewAwal / $totalHadirInterviewAwal) * 100, 2) : 0,
            ],
            [
                'stage' => 'Lolos Interview User',
                'count' => $totalLolosInterviewUser,
                'pct_of_total' => $totalHadir > 0 ? round(($totalLolosInterviewUser / $totalHadir) * 100, 2) : 0,
                'pct_of_prev' => $totalLolosInterviewAwal > 0 ? round(($totalLolosInterviewUser / $totalLolosInterviewAwal) * 100, 2) : 0,
            ],
            [
                'stage' => 'Ikut MCU',
                'count' => $totalIkutMcu,
                'pct_of_total' => $totalHadir > 0 ? round(($totalIkutMcu / $totalHadir) * 100, 2) : 0,
                'pct_of_prev' => $totalLolosInterviewUser > 0 ? round(($totalIkutMcu / $totalLolosInterviewUser) * 100, 2) : 0,
            ],
            [
                'stage' => 'Lolos MCU',
                'count' => $totalLolosMcu,
                'pct_of_total' => $totalHadir > 0 ? round(($totalLolosMcu / $totalHadir) * 100, 2) : 0,
                'pct_of_prev' => $totalIkutMcu > 0 ? round(($totalLolosMcu / $totalIkutMcu) * 100, 2) : 0,
            ],
            [
                'stage' => 'Join FR',
                'count' => $totalJoinFr,
                'pct_of_total' => $totalHadir > 0 ? round(($totalJoinFr / $totalHadir) * 100, 2) : 0,
                'pct_of_prev' => $totalLolosMcu > 0 ? round(($totalJoinFr / $totalLolosMcu) * 100, 2) : 0,
            ],
        ];

        // Summary & Breakdown per Metode Rekrutmen (Online vs Offline)
        $byMetode = collect(['Online', 'Offline'])->map(function ($metodeName) use ($allFiltered) {
            $items = $allFiltered->where('metode_rekrutmen', $metodeName);
            $hadir = (int) $items->sum('jumlah_peserta_hadir');
            $join = (int) $items->sum('join_fr');
            $psiko = (int) $items->sum('jumlah_lolos_psikotest');
            $intUser = (int) $items->sum('jumlah_lolos_interview_user');
            return [
                'metode' => $metodeName,
                'batch_count' => $items->count(),
                'total_peserta' => $hadir,
                'total_psikotest' => $psiko,
                'total_interview_user' => $intUser,
                'total_join' => $join,
                'persen_lulus' => $hadir > 0 ? round(($join / $hadir) * 100, 2) : 0,
                'persen_lulus_raw' => $hadir > 0 ? round($join / $hadir, 6) : 0,
            ];
        })->values();

        // Group by Lokasi for Bar Chart & Summary Table
        $byLocation = $allFiltered->groupBy('lokasi_rekrut')->map(function ($items, $lokasi) {
            $hadir = (int) $items->sum('jumlah_peserta_hadir');
            $join = (int) $items->sum('join_fr');
            return [
                'lokasi' => $lokasi,
                'batch_count' => $items->count(),
                'total_peserta' => $hadir,
                'total_psikotest' => (int) $items->sum('jumlah_lolos_psikotest'),
                'total_interview_user' => (int) $items->sum('jumlah_lolos_interview_user'),
                'total_join' => $join,
                'persen_lulus' => $hadir > 0 ? round(($join / $hadir) * 100, 2) : 0,
                'persen_lulus_raw' => $hadir > 0 ? round($join / $hadir, 6) : 0,
            ];
        })->values()->sortByDesc('total_peserta')->values();

        // Group by Tahun for Trend Line Chart
        $byYear = $allFiltered->groupBy('tahun')->map(function ($items, $year) {
            $hadir = (int) $items->sum('jumlah_peserta_hadir');
            $join = (int) $items->sum('join_fr');
            return [
                'tahun' => (int) $year,
                'batch_count' => $items->count(),
                'total_peserta' => $hadir,
                'total_psikotest' => (int) $items->sum('jumlah_lolos_psikotest'),
                'total_interview_user' => (int) $items->sum('jumlah_lolos_interview_user'),
                'total_join' => $join,
                'persen_lulus' => $hadir > 0 ? round(($join / $hadir) * 100, 2) : 0,
            ];
        })->values()->sortBy('tahun')->values();

        // Sorting for table
        $sortBy = $request->input('sort_by', 'no');
        $sortOrder = $request->input('sort_order', 'asc');
        $validColumns = [
            'id', 'no', 'lokasi_rekrut', 'metode_rekrutmen', 'program', 'tanggal', 'tahun',
            'jumlah_peserta_hadir', 'jumlah_lolos_psikotest', 'jumlah_hadir_interview_awal',
            'jumlah_lolos_interview_awal', 'jumlah_lolos_interview_user', 'jumlah_ikut_mcu',
            'jumlah_lolos_mcu', 'join_fr', 'persen_lulus',
        ];

        if (in_array($sortBy, $validColumns)) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('no', 'asc');
        }

        // Pagination or All
        $perPage = (int) $request->input('per_page', 25);
        if ($perPage === -1 || $request->input('all') === 'true') {
            $paginated = $query->get();
            $dataItems = $paginated;
            $paginationInfo = [
                'total' => $paginated->count(),
                'per_page' => $paginated->count(),
                'current_page' => 1,
                'last_page' => 1,
            ];
        } else {
            $paginated = $query->paginate($perPage);
            $dataItems = $paginated->items();
            $paginationInfo = [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ];
        }

        // Distinct options for dropdowns
        $locations = Rekrutmen::select('lokasi_rekrut')
            ->distinct()
            ->orderBy('lokasi_rekrut')
            ->pluck('lokasi_rekrut');

        $years = Rekrutmen::select('tahun')
            ->whereNotNull('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $programs = Rekrutmen::select('program')
            ->distinct()
            ->orderBy('program')
            ->pluck('program');

        return response()->json([
            'status' => 'success',
            'data' => $dataItems,
            'pagination' => $paginationInfo,
            'summary' => [
                'total_peserta_hadir' => $totalHadir,
                'total_lolos_psikotest' => $totalLolosPsikotest,
                'total_hadir_interview_awal' => $totalHadirInterviewAwal,
                'total_lolos_interview_awal' => $totalLolosInterviewAwal,
                'total_lolos_interview_user' => $totalLolosInterviewUser,
                'total_ikut_mcu' => $totalIkutMcu,
                'total_lolos_mcu' => $totalLolosMcu,
                'total_join_fr' => $totalJoinFr,
                'overall_pass_rate' => $overallPassRate,
                'overall_pass_rate_pct' => round($overallPassRate * 100, 2),
                'avg_pass_rate' => $avgPassRate,
                'avg_pass_rate_pct' => round($avgPassRate * 100, 2),
                'batch_count' => $batchCount,
            ],
            'funnel' => $funnel,
            'charts' => [
                'by_location' => $byLocation,
                'by_year' => $byYear,
                'by_metode' => $byMetode,
            ],
            'filters' => [
                'locations' => $locations,
                'years' => $years,
                'programs' => $programs,
                'methods' => ['Online', 'Offline'],
            ],
        ]);
    }

    /**
     * Store a newly created recruitment batch.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lokasi_rekrut' => 'required|string|max:255',
            'metode_rekrutmen' => 'required|in:Online,Offline,online,offline',
            'program' => 'required|string|max:255',
            'tanggal' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2099',
            'jumlah_peserta_hadir' => 'required|integer|min:0',
            'jumlah_lolos_psikotest' => 'required|integer|min:0',
            'jumlah_hadir_interview_awal' => 'required|integer|min:0',
            'jumlah_lolos_interview_awal' => 'required|integer|min:0',
            'jumlah_lolos_interview_user' => 'required|integer|min:0',
            'jumlah_ikut_mcu' => 'required|integer|min:0',
            'jumlah_lolos_mcu' => 'required|integer|min:0',
            'join_fr' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['metode_rekrutmen'] = ucfirst(strtolower($validated['metode_rekrutmen']));

        // Stage validations (A step can't exceed previous step)
        $stageErrors = $this->validateStages($validated);
        if (!empty($stageErrors)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi tahapan seleksi gagal.',
                'errors' => $stageErrors,
            ], 422);
        }

        // Auto determine 'no'
        $nextNo = ((int) Rekrutmen::max('no')) + 1;
        $validated['no'] = $request->input('no', $nextNo);

        $hadir = (int) $validated['jumlah_peserta_hadir'];
        $join = (int) $validated['join_fr'];
        $validated['persen_lulus'] = $hadir > 0 ? round($join / $hadir, 6) : 0;

        $record = Rekrutmen::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data rekrutmen berhasil ditambahkan!',
            'data' => $record,
        ], 201);
    }

    /**
     * Update an existing recruitment batch.
     */
    public function update(Request $request, $id)
    {
        $record = Rekrutmen::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'lokasi_rekrut' => 'required|string|max:255',
            'metode_rekrutmen' => 'required|in:Online,Offline,online,offline',
            'program' => 'required|string|max:255',
            'tanggal' => 'required|string|max:255',
            'tahun' => 'required|integer|min:2000|max:2099',
            'jumlah_peserta_hadir' => 'required|integer|min:0',
            'jumlah_lolos_psikotest' => 'required|integer|min:0',
            'jumlah_hadir_interview_awal' => 'required|integer|min:0',
            'jumlah_lolos_interview_awal' => 'required|integer|min:0',
            'jumlah_lolos_interview_user' => 'required|integer|min:0',
            'jumlah_ikut_mcu' => 'required|integer|min:0',
            'jumlah_lolos_mcu' => 'required|integer|min:0',
            'join_fr' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $validated['metode_rekrutmen'] = ucfirst(strtolower($validated['metode_rekrutmen']));

        $stageErrors = $this->validateStages($validated);
        if (!empty($stageErrors)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi tahapan seleksi gagal.',
                'errors' => $stageErrors,
            ], 422);
        }

        $hadir = (int) $validated['jumlah_peserta_hadir'];
        $join = (int) $validated['join_fr'];
        $validated['persen_lulus'] = $hadir > 0 ? round($join / $hadir, 6) : 0;

        if ($request->filled('no')) {
            $validated['no'] = (int) $request->input('no');
        }

        $record->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data rekrutmen berhasil diperbarui!',
            'data' => $record,
        ]);
    }

    /**
     * Remove a recruitment batch.
     */
    public function destroy($id)
    {
        $record = Rekrutmen::findOrFail($id);
        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data rekrutmen berhasil dihapus!',
        ]);
    }

    /**
     * Remove all recruitment data.
     */
    public function destroyAll()
    {
        Rekrutmen::truncate();

        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh data rekrutmen berhasil dikosongkan.',
        ]);
    }

    /**
     * Import data from CSV file with preview, validation, and mode (append / replace).
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
            'mode' => 'nullable|in:append,replace',
        ]);

        $file = $request->file('file');
        $mode = $request->input('mode', 'append');

        $filePath = $file->getRealPath();
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membaca file CSV.',
            ], 400);
        }

        $header = fgetcsv($handle, 10000, ',');
        if (!$header) {
            fclose($handle);
            return response()->json([
                'status' => 'error',
                'message' => 'File CSV kosong atau format tidak valid.',
            ], 400);
        }

        // Clean headers
        $cleanHeader = array_map(function ($col) {
            $clean = trim($col);
            $clean = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $clean);
            return strtoupper($clean);
        }, $header);

        // Required column signatures
        $requiredSignatures = [
            'LOKASI' => ['LOKASI REKRUT', 'LOKASI', 'LOKASI_REKRUT'],
            'METODE' => ['METODE REKRUTMEN', 'METODE', 'METODE_REKRUTMEN', 'TIPE REKRUTMEN', 'TIPE'],
            'PROGRAM' => ['PROGRAM', 'NAMA PROGRAM', 'BATCH'],
            'TANGGAL' => ['TANGGAL', 'DATE'],
            'HADIR' => ['JUMLAH PESERTA HADIR', 'PESERTA HADIR', 'HADIR'],
            'JOIN' => ['JOIN FR', 'JOIN', 'JOIN_FR', 'LULUS'],
        ];

        // Map column indices
        $columnIndex = [];
        foreach ($cleanHeader as $idx => $name) {
            foreach ($requiredSignatures as $key => $aliases) {
                if (in_array($name, $aliases)) {
                    $columnIndex[$key] = $idx;
                }
            }
            if (str_contains($name, 'PSIKOTEST')) $columnIndex['PSIKOTEST'] = $idx;
            if (str_contains($name, 'HADIR INTERVIEW AWAL') || str_contains($name, 'HADIR INT AWAL')) $columnIndex['HADIR_INT_AWAL'] = $idx;
            if (str_contains($name, 'LOLOS INTERVIEW AWAL') || str_contains($name, 'LOLOS INT AWAL')) $columnIndex['LOLOS_INT_AWAL'] = $idx;
            if (str_contains($name, 'INTERVIEW USER') || str_contains($name, 'INT USER')) $columnIndex['INT_USER'] = $idx;
            if (str_contains($name, 'IKUT MCU')) $columnIndex['IKUT_MCU'] = $idx;
            if (str_contains($name, 'LOLOS MCU')) $columnIndex['LOLOS_MCU'] = $idx;
            if (str_contains($name, 'TAHUN') || str_contains($name, 'YEAR')) $columnIndex['TAHUN'] = $idx;
            if (str_contains($name, 'NO') || str_contains($name, 'NOMOR')) $columnIndex['NO'] = $idx;
        }

        $rowsToInsert = [];
        $errors = [];
        $rowNumber = 1; // 1 is header

        while (($row = fgetcsv($handle, 10000, ',')) !== false) {
            $rowNumber++;
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                continue; // Skip empty row
            }

            // Extract values
            $lokasi = trim($row[$columnIndex['LOKASI'] ?? 1] ?? '');
            
            // Extract & strictly validate METODE REKRUTMEN
            $metodeRaw = trim($row[$columnIndex['METODE'] ?? 2] ?? '');
            $metodeNormalized = strtolower($metodeRaw);
            if (!in_array($metodeNormalized, ['online', 'offline'])) {
                // If column was not found by name, check if index 2 is indeed Online/Offline or if it's program
                if (!in_array($metodeNormalized, ['online', 'offline'])) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'reason' => 'Kolom METODE REKRUTMEN tidak valid (wajib bernilai "Online" atau "Offline", ditemukan: "' . $metodeRaw . '")'
                    ];
                    continue;
                }
            }
            $metode = ucfirst($metodeNormalized);

            $program = trim($row[$columnIndex['PROGRAM'] ?? 3] ?? '');
            $tanggal = trim($row[$columnIndex['TANGGAL'] ?? 4] ?? '');
            $tahunStr = trim($row[$columnIndex['TAHUN'] ?? 5] ?? '');
            $hadirStr = trim($row[$columnIndex['HADIR'] ?? 6] ?? '0');
            $psikoStr = trim($row[$columnIndex['PSIKOTEST'] ?? 7] ?? '0');
            $hadirIntAwalStr = trim($row[$columnIndex['HADIR_INT_AWAL'] ?? 8] ?? '0');
            $lolosIntAwalStr = trim($row[$columnIndex['LOLOS_INT_AWAL'] ?? 9] ?? '0');
            $intUserStr = trim($row[$columnIndex['INT_USER'] ?? 10] ?? '0');
            $ikutMcuStr = trim($row[$columnIndex['IKUT_MCU'] ?? 11] ?? '0');
            $lolosMcuStr = trim($row[$columnIndex['LOLOS_MCU'] ?? 12] ?? '0');
            $joinStr = trim($row[$columnIndex['JOIN'] ?? 13] ?? '0');
            $noStr = trim($row[$columnIndex['NO'] ?? 0] ?? '');

            // Validations
            if (empty($lokasi)) {
                $errors[] = ['row' => $rowNumber, 'reason' => 'Lokasi rekrut kosong'];
                continue;
            }
            if (empty($program)) {
                $errors[] = ['row' => $rowNumber, 'reason' => 'Program kosong'];
                continue;
            }
            if (empty($tanggal)) {
                $errors[] = ['row' => $rowNumber, 'reason' => 'Tanggal kosong'];
                continue;
            }

            $hadir = (int) round((float) $hadirStr);
            $psiko = (int) round((float) $psikoStr);
            $hadirIntAwal = (int) round((float) $hadirIntAwalStr);
            $lolosIntAwal = (int) round((float) $lolosIntAwalStr);
            $intUser = (int) round((float) $intUserStr);
            $ikutMcu = (int) round((float) $ikutMcuStr);
            $lolosMcu = (int) round((float) $lolosMcuStr);
            $join = (int) round((float) $joinStr);

            $tahun = (int) $tahunStr;
            if ($tahun <= 0 && preg_match('/\b(20\d{2})\b/', $tanggal, $m)) {
                $tahun = (int) $m[1];
            }
            if ($tahun <= 0) {
                $tahun = (int) date('Y');
            }

            // Stage validation logic check
            if ($hadir < 0 || $join < 0) {
                $errors[] = ['row' => $rowNumber, 'reason' => 'Angka peserta atau join tidak boleh negatif'];
                continue;
            }

            $persenLulus = $hadir > 0 ? round($join / $hadir, 6) : 0;

            $rowsToInsert[] = [
                'no' => !empty($noStr) ? (int) $noStr : $rowNumber - 1,
                'lokasi_rekrut' => $lokasi,
                'metode_rekrutmen' => $metode,
                'program' => $program,
                'tanggal' => $tanggal,
                'tahun' => $tahun,
                'jumlah_peserta_hadir' => $hadir,
                'jumlah_lolos_psikotest' => $psiko,
                'jumlah_hadir_interview_awal' => $hadirIntAwal,
                'jumlah_lolos_interview_awal' => $lolosIntAwal,
                'jumlah_lolos_interview_user' => $intUser,
                'jumlah_ikut_mcu' => $ikutMcu,
                'jumlah_lolos_mcu' => $lolosMcu,
                'join_fr' => $join,
                'persen_lulus' => $persenLulus,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        if (empty($rowsToInsert) && !empty($errors)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Semua baris dalam file CSV gagal diproses karena validasi kolom atau format.',
                'errors' => $errors,
                'success_count' => 0,
                'failed_count' => count($errors),
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                Rekrutmen::truncate();
            }

            // Insert in chunks of 100
            foreach (array_chunk($rowsToInsert, 100) as $chunk) {
                Rekrutmen::insert($chunk);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Import CSV berhasil! {$mode} " . count($rowsToInsert) . " data.",
                'success_count' => count($rowsToInsert),
                'failed_count' => count($errors),
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export filtered data to CSV.
     */
    public function exportCsv(Request $request)
    {
        $query = Rekrutmen::query();

        if ($request->filled('lokasi')) {
            $lokasi = $request->input('lokasi');
            if (is_array($lokasi)) {
                $lokasiClean = array_filter($lokasi, fn($l) => $l !== 'all' && $l !== 'Semua Lokasi' && !empty($l));
                if (!empty($lokasiClean)) {
                    $query->whereIn('lokasi_rekrut', $lokasiClean);
                }
            } elseif ($lokasi !== 'all' && $lokasi !== 'Semua Lokasi') {
                $query->where('lokasi_rekrut', $lokasi);
            }
        }

        if ($request->filled('metode_rekrutmen') && $request->input('metode_rekrutmen') !== 'all') {
            $metode = ucfirst(strtolower($request->input('metode_rekrutmen')));
            if (in_array($metode, ['Online', 'Offline'])) {
                $query->where('metode_rekrutmen', $metode);
            }
        } elseif ($request->filled('mode') && $request->input('mode') !== 'all') {
            $metode = ucfirst(strtolower($request->input('mode')));
            if (in_array($metode, ['Online', 'Offline'])) {
                $query->where('metode_rekrutmen', $metode);
            }
        }

        if ($request->filled('tahun')) {
            $tahun = $request->input('tahun');
            if (is_array($tahun)) {
                $tahunClean = array_filter($tahun, fn($t) => $t !== 'all' && !empty($t));
                if (!empty($tahunClean)) {
                    $query->whereIn('tahun', $tahunClean);
                }
            } elseif ($tahun !== 'all' && !empty($tahun)) {
                $query->where('tahun', (int) $tahun);
            }
        }

        if ($request->filled('program') && $request->input('program') !== 'all') {
            $query->where('program', $request->input('program'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('lokasi_rekrut', 'like', "%{$search}%")
                  ->orWhere('metode_rekrutmen', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%")
                  ->orWhere('tanggal', 'like', "%{$search}%")
                  ->orWhere('tahun', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('no', 'asc')->get();

        $filename = 'Data_Rekrutmen_Export_' . date('Ymd_His') . '.xlsx';
        $headers = [
            'NO', 'LOKASI REKRUT', 'METODE REKRUTMEN', 'PROGRAM', 'TANGGAL', 'TAHUN',
            'JUMLAH PESERTA HADIR', 'JUMLAH LOLOS PSIKOTEST', 'JUMLAH HADIR INTERVIEW AWAL',
            'JUMLAH LOLOS INTERVIEW AWAL', 'JUMLAH LOLOS INTERVIEW USER', 'JUMLAH IKUT MCU',
            'JUMLAH LOLOS MCU', 'JOIN FR', '% LULUS',
        ];

        $totalHadir = 0; $totalPsiko = 0; $totalHadirInt = 0; $totalLolosInt = 0;
        $totalIntUser = 0; $totalIkutMcu = 0; $totalLolosMcu = 0; $totalJoin = 0;

        $dataRows = [];
        foreach ($records as $index => $row) {
            $totalHadir += $row->jumlah_peserta_hadir;
            $totalPsiko += $row->jumlah_lolos_psikotest;
            $totalHadirInt += $row->jumlah_hadir_interview_awal;
            $totalLolosInt += $row->jumlah_lolos_interview_awal;
            $totalIntUser += $row->jumlah_lolos_interview_user;
            $totalIkutMcu += $row->jumlah_ikut_mcu;
            $totalLolosMcu += $row->jumlah_lolos_mcu;
            $totalJoin += $row->join_fr;

            $dataRows[] = [
                $row->no ?? ($index + 1),
                $row->lokasi_rekrut,
                $row->metode_rekrutmen,
                $row->program,
                $row->tanggal,
                $row->tahun,
                $row->jumlah_peserta_hadir,
                $row->jumlah_lolos_psikotest,
                $row->jumlah_hadir_interview_awal,
                $row->jumlah_lolos_interview_awal,
                $row->jumlah_lolos_interview_user,
                $row->jumlah_ikut_mcu,
                $row->jumlah_lolos_mcu,
                $row->join_fr,
                $row->persen_lulus,
            ];
        }

        $totalPersen = $totalHadir > 0 ? round($totalJoin / $totalHadir, 6) : 0;
        $dataRows[] = [
            'TOTAL', '', '', '', '', '',
            $totalHadir, $totalPsiko, $totalHadirInt, $totalLolosInt,
            $totalIntUser, $totalIkutMcu, $totalLolosMcu, $totalJoin, $totalPersen,
        ];

        return ExcelExporter::download($filename, $headers, $dataRows, 'Data Rekrutmen');
    }

    /**
     * Helper to validate stage logic (Step counts cannot exceed previous steps).
     */
    private function validateStages(array $data): array
    {
        $errors = [];
        $hadir = (int) ($data['jumlah_peserta_hadir'] ?? 0);
        $psiko = (int) ($data['jumlah_lolos_psikotest'] ?? 0);
        $hadirIntAwal = (int) ($data['jumlah_hadir_interview_awal'] ?? 0);
        $lolosIntAwal = (int) ($data['jumlah_lolos_interview_awal'] ?? 0);
        $intUser = (int) ($data['jumlah_lolos_interview_user'] ?? 0);
        $ikutMcu = (int) ($data['jumlah_ikut_mcu'] ?? 0);
        $lolosMcu = (int) ($data['jumlah_lolos_mcu'] ?? 0);
        $join = (int) ($data['join_fr'] ?? 0);

        if ($psiko > $hadir) {
            $errors['jumlah_lolos_psikotest'] = ['Jumlah lolos psikotest (' . $psiko . ') tidak boleh melebihi jumlah peserta hadir (' . $hadir . ').'];
        }
        if ($hadirIntAwal > $hadir) {
            $errors['jumlah_hadir_interview_awal'] = ['Jumlah hadir interview awal (' . $hadirIntAwal . ') tidak boleh melebihi peserta hadir (' . $hadir . ').'];
        }
        if ($lolosIntAwal > $hadirIntAwal && $hadirIntAwal > 0) {
            $errors['jumlah_lolos_interview_awal'] = ['Jumlah lolos interview awal (' . $lolosIntAwal . ') tidak boleh melebihi hadir interview awal (' . $hadirIntAwal . ').'];
        }
        if ($intUser > $hadir) {
            $errors['jumlah_lolos_interview_user'] = ['Jumlah lolos interview user (' . $intUser . ') tidak boleh melebihi peserta hadir (' . $hadir . ').'];
        }
        if ($ikutMcu > $hadir) {
            $errors['jumlah_ikut_mcu'] = ['Jumlah ikut MCU (' . $ikutMcu . ') tidak boleh melebihi peserta hadir (' . $hadir . ').'];
        }
        if ($lolosMcu > $ikutMcu && ikutMcu > 0) {
            $errors['jumlah_lolos_mcu'] = ['Jumlah lolos MCU (' . $lolosMcu . ') tidak boleh melebihi ikut MCU (' . $ikutMcu . ').'];
        }
        if ($join > $hadir) {
            $errors['join_fr'] = ['Jumlah Join FR (' . $join . ') tidak boleh melebihi peserta hadir (' . $hadir . ').'];
        }

        return $errors;
    }
}
