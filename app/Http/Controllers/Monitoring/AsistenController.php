<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;
use App\Services\SpreadsheetReader;
use App\Services\ExcelExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AsistenController extends Controller
{
    /**
     * Display the Asisten management page.
     */
    public function index()
    {
        return view('Administrasi.Pages.Monitoring.asisten');
    }

    /**
     * Display the Monitoring Summarized (Agregasi Per Program & Tahun Lulus) page.
     */
    public function indexMonitoringSummarized()
    {
        return view('Administrasi.Pages.Monitoring.monitoring-summarized');
    }

    /**
     * Display the Monitoring Alumni (Detail Per Gelombang / Batch) page.
     */
    public function indexMonitoringAlumni()
    {
        return view('Administrasi.Pages.Monitoring.monitoring-alumni');
    }

    /**
     * Display the Monitoring Lulusan Alumni page (legacy/backward compatibility).
     */
    public function indexMonitoringLulusan()
    {
        return view('Administrasi.Pages.Monitoring.monitoring-alumni');
    }

    /**
     * Get paginated data with filters, summary KPIs, and filter dropdown lists.
     */
    public function getData(Request $request)
    {
        $query = Asisten::query();

        // 1. Filter Asal Rekrut
        if ($request->filled('asal_rekrut') && $request->input('asal_rekrut') !== 'all') {
            $query->where('asal_rekrut', $request->input('asal_rekrut'));
        }

        // 2. Filter Program
        if ($request->filled('program') && $request->input('program') !== 'all') {
            $query->where('program', $request->input('program'));
        }

        // 3. Filter Region (Kepegawaian Saat Ini)
        if ($request->filled('region_saat_ini') && $request->input('region_saat_ini') !== 'all') {
            $query->where('region_saat_ini', $request->input('region_saat_ini'));
        }

        // 4. Filter Status Karyawan
        if ($request->filled('status_karyawan') && $request->input('status_karyawan') !== 'all') {
            $query->where('status_karyawan', $request->input('status_karyawan'));
        }

        // 5. Filter Level / Jabatan
        if ($request->filled('level_jabatan') && $request->input('level_jabatan') !== 'all') {
            $lvl = trim((string)$request->input('level_jabatan'));
            $query->where(function($q) use ($lvl) {
                if (strcasecmp($lvl, 'GM') === 0 || strcasecmp($lvl, 'General Manager') === 0) {
                    $q->where('level_jabatan', 'like', '%GM%');
                } elseif (strcasecmp($lvl, 'PC') === 0) {
                    $q->where('level_jabatan', 'like', '%PC%');
                } elseif (strcasecmp($lvl, 'Askep') === 0 || strcasecmp($lvl, 'Askep / KTU') === 0) {
                    $q->where('level_jabatan', 'like', '%Askep%')
                      ->orWhere('level_jabatan', 'like', '%KTU%');
                } elseif (strcasecmp($lvl, 'MK / MM') === 0 || strcasecmp($lvl, 'Manager') === 0) {
                    $q->where('level_jabatan', 'like', '%MK%')
                      ->orWhere('level_jabatan', 'like', '%MM%')
                      ->orWhere('level_jabatan', 'like', '%Manager%');
                } elseif (strcasecmp($lvl, 'Asisten / Staff') === 0 || strcasecmp($lvl, 'Asisten') === 0) {
                    $q->where('level_jabatan', 'like', '%Asisten%')
                      ->orWhere('level_jabatan', 'like', '%Staff%')
                      ->orWhere('level_jabatan', 'like', '%ASST%');
                } else {
                    $q->where('level_jabatan', 'like', "%{$lvl}%");
                }
            });
        }

        // 6. Filter Keterangan
        if ($request->filled('keterangan') && $request->input('keterangan') !== 'all') {
            $ket = $request->input('keterangan');
            if ($ket === 'Aktif') {
                $query->where(function ($q) {
                    $q->where('keterangan', 'Aktif')
                      ->orWhere('keterangan', 'Eksis')
                      ->orWhere('keterangan', 'like', '%aktif%')
                      ->orWhere('keterangan', 'like', '%eksis%');
                });
            } elseif ($ket === 'Resign') {
                $query->where(function ($q) {
                    $q->where('keterangan', 'Resign')
                      ->orWhere('keterangan', 'PHK')
                      ->orWhere('keterangan', 'like', '%resign%')
                      ->orWhere('keterangan', 'like', '%phk%');
                });
            } elseif ($ket === 'Gagal') {
                $query->where('keterangan', 'like', '%gagal%');
            } else {
                $query->where('keterangan', $ket);
            }
        }

        // 7. Search Nama (or NIK / Program / Unit Kerja)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%")
                  ->orWhere('asal_rekrut', 'like', "%{$search}%")
                  ->orWhere('jabatan_awal', 'like', "%{$search}%")
                  ->orWhere('jabatan_terakhir', 'like', "%{$search}%")
                  ->orWhere('pt_penempatan', 'like', "%{$search}%")
                  ->orWhere('unit_kerja_saat_ini', 'like', "%{$search}%")
                  ->orWhere('unit_kerja_penempatan_training', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        // Summary KPI calculations based on `keterangan` column
        $totalAll = Asisten::count();
        $totalAktif = Asisten::where(function ($q) {
            $q->where('keterangan', 'like', '%eksis%')
              ->orWhere('keterangan', 'like', '%aktif%');
        })->count();

        $totalResign = Asisten::where(function ($q) {
            $q->where('keterangan', 'like', '%resign%')
              ->orWhere('keterangan', 'like', '%phk%');
        })->count();

        $totalGagal = Asisten::where('keterangan', 'like', '%gagal%')->count();

        // Sorting
        $sortBy = $request->input('sort_by', 'no');
        $sortOrder = $request->input('sort_order', 'asc');
        $validSortColumns = [
            'id', 'no', 'nama', 'asal_rekrut', 'program', 'jabatan_awal',
            'pt_penempatan', 'region_saat_ini', 'keterangan', 'created_at'
        ];

        if (in_array($sortBy, $validSortColumns)) {
            $query->orderBy($sortBy, strtolower($sortOrder) === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('no', 'asc');
        }

        // Pagination
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

        // Distinct Filter options
        $asalRekrutList = Asisten::whereNotNull('asal_rekrut')->where('asal_rekrut', '!=', '')->distinct()->orderBy('asal_rekrut')->pluck('asal_rekrut');
        $programList = Asisten::whereNotNull('program')->where('program', '!=', '')->distinct()->orderBy('program')->pluck('program');
        $regionList = Asisten::whereNotNull('region_saat_ini')->where('region_saat_ini', '!=', '')->distinct()->orderBy('region_saat_ini')->pluck('region_saat_ini');
        $statusList = Asisten::whereNotNull('status_karyawan')->where('status_karyawan', '!=', '')->distinct()->orderBy('status_karyawan')->pluck('status_karyawan');
        
        $dbLevels = Asisten::whereNotNull('level_jabatan')->where('level_jabatan', '!=', '')->distinct()->orderBy('level_jabatan')->pluck('level_jabatan');
        
        $levelList = collect(['GM', 'Askep / KTU', 'MK / MM', 'PC', 'Asisten / Staff'])
            ->merge($dbLevels)
            ->filter()
            ->unique()
            ->values()
            ->all();

        $keteranganList = ['Aktif', 'Resign', 'Gagal'];

        return response()->json([
            'status' => 'success',
            'data' => $dataItems,
            'pagination' => $paginationInfo,
            'summary' => [
                'total_asisten' => $totalAll,
                'total_aktif' => $totalAktif,
                'total_resign' => $totalResign,
                'total_gagal' => $totalGagal,
            ],
            'filters' => [
                'asal_rekrut' => $asalRekrutList,
                'program' => $programList,
                'region_saat_ini' => $regionList,
                'status_karyawan' => $statusList,
                'level_jabatan' => $levelList,
                'keterangan' => $keteranganList,
            ],
        ]);
    }

    /**
     * Show detailed 56-attribute data of a single trainee/assistant.
     */
    public function show($id)
    {
        $asisten = Asisten::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $asisten,
        ]);
    }

    /**
     * Store newly created assistant.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:100',
            'program' => 'nullable|string|max:255',
            'asal_rekrut' => 'nullable|string|max:255',
            'jabatan_awal' => 'nullable|string|max:255',
            'pt_penempatan' => 'nullable|string|max:255',
            'region_saat_ini' => 'nullable|string|max:255',
            'unit_kerja_saat_ini' => 'nullable|string|max:255',
            'unit_kerja_penempatan_training' => 'nullable|string|max:255',
            'ipk' => 'nullable|numeric|between:0,4.00',
            'usia' => 'nullable|integer|min:15|max:90',
            'tahun_lulus' => 'nullable|integer|min:1950|max:2099',
            'tahun_angkatan' => 'nullable|integer|min:1950|max:2099',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        if (empty($data['no'])) {
            $data['no'] = ((int) Asisten::max('no')) + 1;
        }

        $record = Asisten::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data asisten berhasil ditambahkan!',
            'data' => $record,
        ], 201);
    }

    /**
     * Update an assistant record.
     */
    public function update(Request $request, $id)
    {
        $asisten = Asisten::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:100',
            'program' => 'nullable|string|max:255',
            'asal_rekrut' => 'nullable|string|max:255',
            'jabatan_awal' => 'nullable|string|max:255',
            'pt_penempatan' => 'nullable|string|max:255',
            'region_saat_ini' => 'nullable|string|max:255',
            'unit_kerja_saat_ini' => 'nullable|string|max:255',
            'unit_kerja_penempatan_training' => 'nullable|string|max:255',
            'ipk' => 'nullable|numeric|between:0,4.00',
            'usia' => 'nullable|integer|min:15|max:90',
            'tahun_lulus' => 'nullable|integer|min:1950|max:2099',
            'tahun_angkatan' => 'nullable|integer|min:1950|max:2099',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $asisten->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data asisten berhasil diperbarui!',
            'data' => $asisten,
        ]);
    }

    /**
     * Delete an assistant record.
     */
    public function destroy($id)
    {
        $asisten = Asisten::findOrFail($id);
        $asisten->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data asisten berhasil dihapus!',
        ]);
    }

    /**
     * Delete all assistant records.
     */
    public function destroyAll()
    {
        $count = Asisten::count();
        Asisten::truncate();

        ActivityLog::log(
            'Hapus',
            'Database Asisten',
            "Menghapus semua data asisten ({$count} data)",
            ['count' => $count]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh data asisten berhasil dikosongkan.',
        ]);
    }

    /**
     * Import assistant data from CSV.
     */
    public function importCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:30720',
            'mode' => 'nullable|in:append,replace',
        ]);

        $file = $request->file('file');
        $mode = $request->input('mode', 'append');

        // Auto-detect delimiter (semicolon vs comma)
        $sampleHandle = fopen($file->getRealPath(), 'r');
        $firstLine = fgets($sampleHandle);
        fclose($sampleHandle);
        $delimiter = (str_contains($firstLine, ';')) ? ';' : ',';

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuka file CSV.',
            ], 400);
        }

        $header = fgetcsv($handle, 10000, $delimiter);
        if (!$header) {
            fclose($handle);
            return response()->json([
                'status' => 'error',
                'message' => 'File CSV kosong atau tidak valid.',
            ], 400);
        }

        // Clean & normalize headers
        $cleanHeaders = array_map(function ($h) {
            $str = trim($h);
            $str = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str);
            $str = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $str));
            return $str;
        }, $header);

        // Header mapping dictionary for all 56 fields
        $headerMap = [
            'no' => ['no', 'nomor', 'nourut'],
            'nik' => ['niktraineektp', 'niktrainee', 'nik', 'nokaryawan', 'idkaryawan'],
            'nama' => ['nama', 'namalengkap', 'namakaryawan', 'namapeserta'],
            'jenis_kelamin' => ['jeniskelamin', 'jk', 'gender', 'sex'],
            'tempat_lahir' => ['tempatlahir', 'tmplahir', 'pob'],
            'tanggal_lahir' => ['tanggallahir', 'tgllahir', 'dob', 'birthdate'],
            'usia' => ['usia', 'umur', 'age'],
            'status_pernikahan' => ['status', 'statuspernikahan', 'statusperkawinan', 'maritalstatus'],
            'suku' => ['suku', 'ethnic', 'ethnicity'],
            'agama' => ['agama', 'religion'],
            'latar_belakang_ortu' => ['latarbelakangayahibu', 'latarbelakangorangtua', 'latarbelakangortu', 'backgroundortu'],
            'universitas' => ['alumni', 'universitas', 'institusi', 'kampus', 'perguruantinggi'],
            'fakultas' => ['fakultas'],
            'jurusan' => ['jurusan', 'programstudi', 'prodi'],
            'ipk' => ['ipkkampus', 'ipk', 'gpa'],
            'tahun_lulus' => ['tahunluluskampus', 'tahunlulus', 'thnlulus'],
            'tahun_angkatan' => ['tahuntraining', 'tahunangkatan', 'angkatan', 'tahun'],
            'jenjang_pendidikan' => ['jenjangpendidikan', 'jenjang', 'tingkatpendidikan', 'strata'],
            'no_ijazah' => ['noijazah', 'nomorijazah'],
            'asal_rekrut' => ['asalrekrut', 'lokasirekrut', 'kotarekrut', 'rekrutdari'],
            'alamat_domisili' => ['domisili', 'alamatdomisili', 'alamat', 'address'],
            'no_ktp' => ['alamatktp', 'noktp', 'ktp', 'nikktp', 'noidentitas'],
            'program' => ['program', 'programtraining', 'namaprogram'],
            'periode_pelatihan' => ['periodepelatihan', 'periode'],
            'batch' => ['prog', 'batch', 'gelombang', 'batchtraining'],
            'nilai_krepelin' => ['nilaikrepelinculturefit', 'nilaikrepelin', 'krepelin', 'culturefit'],
            'nilai_ist' => ['nilaiistintelektualaq', 'nilaiist', 'ist'],
            'nilai_disc' => ['nilaidisckepribadian', 'nilaidisc', 'disc'],
            'catatan_interview' => ['catataninterview', 'interviewnotes'],
            'tanggal_mulai_training' => ['joindatetraining', 'tanggalmulaitraining', 'tglmulaitraining', 'starttraining', 'mulaitraining'],
            'disiplin' => ['disiplin'],
            'keaktifan' => ['keaktifan'],
            'catatan_trainer' => ['catatantrainer', 'trainernotes'],
            'nilai_akademik' => ['rataratanilaiujian', 'nilaiakademik', 'nilaiteori', 'akademik'],
            'tanggal_kelulusan' => ['tanggalkelulusan', 'tglkelulusan', 'tgllulus'],
            'tahun_lulus_training' => ['tahunlulustraining'],
            'nilai_akhir_training' => ['ipktraining', 'nilaiakhirtraining', 'nilaiakhir', 'finalscore'],
            'ranking_seleksi' => ['rankingkelulusan', 'rankingseleksi', 'ranking', 'peringkat'],
            'pt_penempatan' => ['penempatanpt', 'ptpenempatan', 'pt', 'perusahaan'],
            'jabatan_awal' => ['jabatan', 'jabatanawal', 'posisiawal', 'penempatanposisi'],
            'golongan_awal' => ['gol', 'golonganawal'],
            'regional_penempatan_training' => ['regional', 'regionalpenempatantraining', 'regionalpenempatan', 'regionaltraining', 'regiontraining'],
            'unit_kerja_penempatan_training' => ['unitkerja', 'unitkerjapenempatantraining', 'unitkerjatraining', 'unitpenempatantraining'],
            'nik_employee' => ['nikemployee', 'nikkaryawan'],
            'jabatan_terakhir' => ['jabatanterakhir', 'jabatansaatini', 'posisiterakhir', 'posisisaatini'],
            'level_jabatan' => ['lvl', 'leveljabatan', 'level'],
            'golongan_terakhir' => ['golonganterakhir'],
            'unit_kerja_saat_ini' => ['unitkerja', 'unitkerjakepegawaiansaatini', 'unitkerjasaatini'],
            'region_saat_ini' => ['region', 'regionkepegawaiansaatini', 'regionsaatini'],
            'alasan_resign' => ['keteranganresign', 'alasanresign'],
            'tanggal_resign' => ['tanggalresign', 'tglresign'],
            'tanggal_pengambilan_ijazah' => ['tanggalpengembalianijazah', 'tanggalpengambilanijazah', 'tglpengambilanijazah', 'tglambilijazah'],
            'penerima_ijazah' => ['penerimaijazah'],
            'no_tanda_terima_ijazah' => ['notandaterimaijazah', 'tandaterimaijazah', 'notandaterima'],
            'no_hp' => ['notelp', 'nohp', 'telepon', 'hp', 'phone', 'notelepon'],
            'keterangan' => ['keterangan', 'notes', 'catatan', 'remark'],
        ];

        $seenUnitKerja = 0;
        $seenRegion = 0;
        $columnIndices = [];

        foreach ($cleanHeaders as $colIdx => $colName) {
            if ($colName === 'unitkerja') {
                $seenUnitKerja++;
                if ($seenUnitKerja === 1) {
                    $columnIndices['unit_kerja_penempatan_training'] = $colIdx;
                } else {
                    $columnIndices['unit_kerja_saat_ini'] = $colIdx;
                }
                continue;
            }
            if ($colName === 'regional' || $colName === 'region') {
                $seenRegion++;
                if ($seenRegion === 1) {
                    $columnIndices['regional_penempatan_training'] = $colIdx;
                } else {
                    $columnIndices['region_saat_ini'] = $colIdx;
                }
                continue;
            }

            foreach ($headerMap as $field => $aliases) {
                if (in_array($colName, $aliases)) {
                    if (!isset($columnIndices[$field])) {
                        $columnIndices[$field] = $colIdx;
                    }
                }
            }
        }

        $rowsToInsert = [];
        $errors = [];
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 10000, $delimiter)) !== false) {
            $rowNumber++;
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                continue;
            }

            // Extract Nama
            $nama = trim($row[$columnIndices['nama'] ?? 2] ?? '');
            if (empty($nama)) {
                $errors[] = ['row' => $rowNumber, 'reason' => 'Kolom Nama kosong / wajib diisi'];
                continue;
            }

            $ipk = $this->parseNumeric($row[$columnIndices['ipk'] ?? -1] ?? null);
            $usia = $this->parseInteger($row[$columnIndices['usia'] ?? -1] ?? null);
            $tahunLulus = $this->parseInteger($row[$columnIndices['tahun_lulus'] ?? -1] ?? null);
            $tahunAngkatan = $this->parseInteger($row[$columnIndices['tahun_angkatan'] ?? -1] ?? null);
            $rankingSeleksi = $this->parseInteger($row[$columnIndices['ranking_seleksi'] ?? -1] ?? null);

            $nilaiPsikotest = $this->parseNumeric($row[$columnIndices['nilai_psikotest'] ?? -1] ?? null);
            $nilaiInterviewHr = $this->parseNumeric($row[$columnIndices['nilai_interview_hr'] ?? -1] ?? null);
            $nilaiInterviewUser = $this->parseNumeric($row[$columnIndices['nilai_interview_user'] ?? -1] ?? null);
            $nilaiAkademik = $this->parseNumeric($row[$columnIndices['nilai_akademik'] ?? -1] ?? null);
            $nilaiPraktek = $this->parseNumeric($row[$columnIndices['nilai_praktek'] ?? -1] ?? null);
            $nilaiSikap = $this->parseNumeric($row[$columnIndices['nilai_sikap'] ?? -1] ?? null);
            $nilaiAkhir = $this->parseNumeric($row[$columnIndices['nilai_akhir_training'] ?? -1] ?? null);

            $tglLahir = $this->parseDate($row[$columnIndices['tanggal_lahir'] ?? -1] ?? null);
            $tglMulai = $this->parseDate($row[$columnIndices['tanggal_mulai_training'] ?? -1] ?? null);
            $tglSelesai = $this->parseDate($row[$columnIndices['tanggal_selesai_training'] ?? -1] ?? null);
            $tglLulus = $this->parseDate($row[$columnIndices['tanggal_kelulusan'] ?? -1] ?? null);
            $tglSkAwal = $this->parseDate($row[$columnIndices['tanggal_sk_awal'] ?? -1] ?? null);
            $tglPengangkatan = $this->parseDate($row[$columnIndices['tanggal_pengangkatan'] ?? -1] ?? null);
            $tglResign = $this->parseDate($row[$columnIndices['tanggal_resign'] ?? -1] ?? null);
            $tglAmbilIjazah = $this->parseDate($row[$columnIndices['tanggal_pengambilan_ijazah'] ?? -1] ?? null);

            $rowObj = [
                'no' => !empty($row[$columnIndices['no'] ?? 0]) ? (int) $row[$columnIndices['no'] ?? 0] : $rowNumber - 1,
                'nama' => $nama,
                'nik' => trim($row[$columnIndices['nik'] ?? -1] ?? '') ?: null,
                'jenis_kelamin' => trim($row[$columnIndices['jenis_kelamin'] ?? -1] ?? '') ?: null,
                'tempat_lahir' => trim($row[$columnIndices['tempat_lahir'] ?? -1] ?? '') ?: null,
                'tanggal_lahir' => $tglLahir,
                'usia' => $usia,
                'agama' => trim($row[$columnIndices['agama'] ?? -1] ?? '') ?: null,
                'status_pernikahan' => trim($row[$columnIndices['status_pernikahan'] ?? -1] ?? '') ?: null,
                'suku' => trim($row[$columnIndices['suku'] ?? -1] ?? '') ?: null,
                'latar_belakang_ortu' => trim($row[$columnIndices['latar_belakang_ortu'] ?? -1] ?? '') ?: null,
                'no_hp' => trim($row[$columnIndices['no_hp'] ?? -1] ?? '') ?: null,
                'email' => trim($row[$columnIndices['email'] ?? -1] ?? '') ?: null,
                'alamat_domisili' => trim($row[$columnIndices['alamat_domisili'] ?? -1] ?? '') ?: null,
                'no_ktp' => trim($row[$columnIndices['no_ktp'] ?? -1] ?? '') ?: null,
                'jenjang_pendidikan' => trim($row[$columnIndices['jenjang_pendidikan'] ?? -1] ?? '') ?: null,
                'universitas' => trim($row[$columnIndices['universitas'] ?? -1] ?? '') ?: null,
                'fakultas' => trim($row[$columnIndices['fakultas'] ?? -1] ?? '') ?: null,
                'jurusan' => trim($row[$columnIndices['jurusan'] ?? -1] ?? '') ?: null,
                'ipk' => $ipk,
                'tahun_lulus' => $tahunLulus,
                'no_ijazah' => trim($row[$columnIndices['no_ijazah'] ?? -1] ?? '') ?: null,
                'asal_rekrut' => trim($row[$columnIndices['asal_rekrut'] ?? -1] ?? '') ?: null,
                'jalur_rekrutmen' => trim($row[$columnIndices['jalur_rekrutmen'] ?? -1] ?? '') ?: null,
                'program' => trim($row[$columnIndices['program'] ?? -1] ?? '') ?: null,
                'batch' => trim($row[$columnIndices['batch'] ?? -1] ?? '') ?: null,
                'periode_pelatihan' => trim($row[$columnIndices['periode_pelatihan'] ?? -1] ?? '') ?: null,
                'tanggal_mulai_training' => $tglMulai,
                'tanggal_selesai_training' => $tglSelesai,
                'tahun_angkatan' => $tahunAngkatan,
                'nilai_psikotest' => $nilaiPsikotest,
                'nilai_krepelin' => trim($row[$columnIndices['nilai_krepelin'] ?? -1] ?? '') ?: null,
                'nilai_ist' => trim($row[$columnIndices['nilai_ist'] ?? -1] ?? '') ?: null,
                'nilai_disc' => trim($row[$columnIndices['nilai_disc'] ?? -1] ?? '') ?: null,
                'catatan_interview' => trim($row[$columnIndices['catatan_interview'] ?? -1] ?? '') ?: null,
                'nilai_interview_hr' => $nilaiInterviewHr,
                'nilai_interview_user' => $nilaiInterviewUser,
                'status_mcu' => trim($row[$columnIndices['status_mcu'] ?? -1] ?? '') ?: null,
                'ranking_seleksi' => $rankingSeleksi,
                'nilai_akademik' => $nilaiAkademik,
                'nilai_praktek' => $nilaiPraktek,
                'nilai_sikap' => $nilaiSikap,
                'disiplin' => trim($row[$columnIndices['disiplin'] ?? -1] ?? '') ?: null,
                'keaktifan' => trim($row[$columnIndices['keaktifan'] ?? -1] ?? '') ?: null,
                'catatan_trainer' => trim($row[$columnIndices['catatan_trainer'] ?? -1] ?? '') ?: null,
                'nilai_akhir_training' => $nilaiAkhir,
                'status_kelulusan' => trim($row[$columnIndices['status_kelulusan'] ?? -1] ?? '') ?: null,
                'tanggal_kelulusan' => $tglLulus,
                'jabatan_awal' => trim($row[$columnIndices['jabatan_awal'] ?? -1] ?? '') ?: null,
                'golongan_awal' => trim($row[$columnIndices['golongan_awal'] ?? -1] ?? '') ?: null,
                'pt_penempatan' => trim($row[$columnIndices['pt_penempatan'] ?? -1] ?? '') ?: null,
                'regional_penempatan_training' => trim($row[$columnIndices['regional_penempatan_training'] ?? -1] ?? '') ?: null,
                'unit_kerja_penempatan_training' => trim($row[$columnIndices['unit_kerja_penempatan_training'] ?? -1] ?? '') ?: null,
                'nik_employee' => trim($row[$columnIndices['nik_employee'] ?? -1] ?? '') ?: null,
                'tanggal_sk_awal' => $tglSkAwal,
                'status_karyawan' => trim($row[$columnIndices['status_karyawan'] ?? -1] ?? '') ?: null,
                'jabatan_terakhir' => trim($row[$columnIndices['jabatan_terakhir'] ?? -1] ?? '') ?: null,
                'level_jabatan' => trim($row[$columnIndices['level_jabatan'] ?? -1] ?? '') ?: null,
                'golongan_terakhir' => trim($row[$columnIndices['golongan_terakhir'] ?? -1] ?? '') ?: null,
                'region_saat_ini' => trim($row[$columnIndices['region_saat_ini'] ?? -1] ?? '') ?: null,
                'unit_kerja_saat_ini' => trim($row[$columnIndices['unit_kerja_saat_ini'] ?? -1] ?? '') ?: null,
                'atasan_langsung' => trim($row[$columnIndices['atasan_langsung'] ?? -1] ?? '') ?: null,
                'tanggal_pengangkatan' => $tglPengangkatan,
                'status_resign' => trim($row[$columnIndices['status_resign'] ?? -1] ?? '') ?: 'Aktif',
                'tanggal_resign' => $tglResign,
                'alasan_resign' => trim($row[$columnIndices['alasan_resign'] ?? -1] ?? '') ?: null,
                'status_ijazah' => trim($row[$columnIndices['status_ijazah'] ?? -1] ?? '') ?: null,
                'tanggal_pengambilan_ijazah' => $tglAmbilIjazah,
                'penerima_ijazah' => trim($row[$columnIndices['penerima_ijazah'] ?? -1] ?? '') ?: null,
                'no_tanda_terima_ijazah' => trim($row[$columnIndices['no_tanda_terima_ijazah'] ?? -1] ?? '') ?: null,
                'no_bpjs_tk' => trim($row[$columnIndices['no_bpjs_tk'] ?? -1] ?? '') ?: null,
                'no_bpjs_kes' => trim($row[$columnIndices['no_bpjs_kes'] ?? -1] ?? '') ?: null,
                'npwp' => trim($row[$columnIndices['npwp'] ?? -1] ?? '') ?: null,
                'keterangan' => (function() use ($row, $columnIndices) {
                    $raw = trim($row[$columnIndices['keterangan'] ?? -1] ?? '');
                    if (stripos($raw, 'gagal') !== false) return 'Gagal';
                    if (stripos($raw, 'resign') !== false || stripos($raw, 'phk') !== false) return 'Resign';
                    return 'Aktif';
                })(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $rowsToInsert[] = $rowObj;
        }

        fclose($handle);

        if (empty($rowsToInsert) && !empty($errors)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Semua baris dalam file CSV gagal diproses.',
                'errors' => $errors,
                'success_count' => 0,
                'failed_count' => count($errors),
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($mode === 'replace') {
                Asisten::query()->delete();
            }

            foreach (array_chunk($rowsToInsert, 100) as $chunk) {
                Asisten::insert($chunk);
            }

            DB::commit();

            ActivityLog::log(
                'Import',
                'Database Asisten',
                "Mengimport " . count($rowsToInsert) . " data asisten dari file " . $file->getClientOriginalName(),
                ['filename' => $file->getClientOriginalName(), 'count' => count($rowsToInsert), 'mode' => $mode]
            );

            return response()->json([
                'status' => 'success',
                'message' => "Import CSV berhasil! {$mode} " . count($rowsToInsert) . " data.",
                'success_count' => count($rowsToInsert),
                'failed_count' => count($errors),
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper to safely parse dates in various formats to YYYY-MM-DD.
     */
    private function parseDate($value)
    {
        if (empty($value)) return null;
        $v = trim((string) $value);
        if ($v === '' || $v === '-' || strtolower($v) === 'null' || strtolower($v) === 'n/a') {
            return null;
        }

        // Already YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
            return $v;
        }

        // Try standard strtotime
        try {
            $timestamp = strtotime($v);
            if ($timestamp !== false && $timestamp > 0) {
                return date('Y-m-d', $timestamp);
            }
        } catch (\Throwable $e) {
            // continue
        }

        // Try DD/MM/YYYY or DD-MM-YYYY
        $parts = preg_split('/[\/\-\.]/', $v);
        if (count($parts) === 3) {
            if (strlen($parts[2]) === 4 && checkdate((int)$parts[1], (int)$parts[0], (int)$parts[2])) {
                return sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
            }
            if (strlen($parts[0]) === 4 && checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0])) {
                return sprintf('%04d-%02d-%02d', $parts[0], $parts[1], $parts[2]);
            }
        }

        return $v;
    }

    /**
     * Helper to safely parse numeric / float values.
     */
    private function parseNumeric($value)
    {
        if (empty($value) && $value !== '0' && $value !== 0) return null;
        $v = trim((string) $value);
        if ($v === '' || $v === '-' || strtolower($v) === 'null') return null;
        $v = str_replace(',', '.', $v);
        return is_numeric($v) ? (float) $v : null;
    }

    /**
     * Helper to safely parse integer values.
     */
    private function parseInteger($value)
    {
        if (empty($value) && $value !== '0' && $value !== 0) return null;
        $v = trim((string) $value);
        if ($v === '' || $v === '-' || strtolower($v) === 'null') return null;
        return is_numeric($v) ? (int) $v : null;
    }

    /**
     * Export data to CSV (mode: ringkas 8 kolom vs lengkap 56 kolom).
     */
    public function exportCsv(Request $request)
    {
        $query = Asisten::query();

        if ($request->filled('asal_rekrut') && $request->input('asal_rekrut') !== 'all') {
            $query->where('asal_rekrut', $request->input('asal_rekrut'));
        }
        if ($request->filled('program') && $request->input('program') !== 'all') {
            $query->where('program', $request->input('program'));
        }
        if ($request->filled('region_saat_ini') && $request->input('region_saat_ini') !== 'all') {
            $query->where('region_saat_ini', $request->input('region_saat_ini'));
        }
        if ($request->filled('status_karyawan') && $request->input('status_karyawan') !== 'all') {
            $query->where('status_karyawan', $request->input('status_karyawan'));
        }
        if ($request->filled('level_jabatan') && $request->input('level_jabatan') !== 'all') {
            $lvl = trim((string)$request->input('level_jabatan'));
            $query->where('level_jabatan', 'like', "%{$lvl}%");
        }
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('program', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('no', 'asc')->get();
        $exportType = $request->input('type', 'ringkas'); // 'ringkas' | 'lengkap'

        if ($exportType === 'ringkas') {
            $headers = [
                'NO',
                'NAMA',
                'ASAL REKRUT',
                'PROGRAM',
                'JABATAN (AWAL)',
                'PENEMPATAN (PT)',
                'REGION (SAAT INI)',
                'UNIT KERJA',
                'KETERANGAN',
            ];

            $dataRows = [];
            foreach ($records as $idx => $r) {
                $unitKerja = $r->unit_kerja_display . ($r->is_unit_kerja_fallback ? ' (Penempatan Awal)' : '');
                $dataRows[] = [
                    $r->no ?? ($idx + 1),
                    $r->nama,
                    $r->asal_rekrut,
                    $r->program,
                    $r->jabatan_awal,
                    $r->pt_penempatan,
                    $r->region_saat_ini,
                    $unitKerja,
                    $r->keterangan ?: $r->status_karyawan,
                ];
            }

            $filename = 'Database_Asisten_Ringkas_' . date('Ymd_His') . '.xlsx';
            return ExcelExporter::download($filename, $headers, $dataRows, 'Asisten Ringkas');
        } else {
            $headers = [
                'NO', 'NIK', 'NAMA', 'JENIS KELAMIN', 'TEMPAT LAHIR', 'TANGGAL LAHIR', 'USIA', 'AGAMA', 'STATUS PERNIKAHAN', 'NO HP', 'EMAIL', 'ALAMAT DOMISILI', 'NO KTP',
                'JENJANG PENDIDIKAN', 'UNIVERSITAS', 'FAKULTAS', 'JURUSAN', 'IPK', 'TAHUN LULUS',
                'ASAL REKRUT', 'JALUR REKRUTMEN', 'PROGRAM', 'BATCH', 'TANGGAL MULAI TRAINING', 'TANGGAL SELESAI TRAINING', 'TAHUN ANGKATAN',
                'NILAI PSIKOTEST', 'NILAI INTERVIEW HR', 'NILAI INTERVIEW USER', 'STATUS MCU', 'RANKING SELEKSI',
                'NILAI AKADEMIK', 'NILAI PRAKTEK', 'NILAI SIKAP', 'NILAI AKHIR TRAINING', 'STATUS KELULUSAN', 'TANGGAL KELULUSAN',
                'JABATAN (AWAL)', 'PENEMPATAN (PT)', 'REGIONAL (PENEMPATAN TRAINING)', 'UNIT KERJA (PENEMPATAN TRAINING)', 'TANGGAL SK AWAL',
                'STATUS KARYAWAN', 'JABATAN TERAKHIR', 'LEVEL JABATAN', 'REGION (KEPEGAWAIAN SAAT INI)', 'UNIT KERJA (KEPEGAWAIAN SAAT INI)', 'ATASAN LANGSUNG', 'TANGGAL PENGANGKATAN',
                'STATUS RESIGN', 'TANGGAL RESIGN', 'ALASAN RESIGN', 'STATUS IJAZAH', 'TANGGAL PENGAMBILAN IJAZAH', 'NO TANDA TERIMA IJAZAH',
                'NO BPJS TK', 'NO BPJS KES', 'NPWP', 'KETERANGAN',
            ];

            $dataRows = [];
            foreach ($records as $idx => $r) {
                $dataRows[] = [
                    $r->no ?? ($idx + 1), $r->nik, $r->nama, $r->jenis_kelamin, $r->tempat_lahir, $r->tanggal_lahir, $r->usia, $r->agama, $r->status_pernikahan, $r->no_hp, $r->email, $r->alamat_domisili, $r->no_ktp,
                    $r->jenjang_pendidikan, $r->universitas, $r->fakultas, $r->jurusan, $r->ipk, $r->tahun_lulus,
                    $r->asal_rekrut, $r->jalur_rekrutmen, $r->program, $r->batch, $r->tanggal_mulai_training, $r->tanggal_selesai_training, $r->tahun_angkatan,
                    $r->nilai_psikotest, $r->nilai_interview_hr, $r->nilai_interview_user, $r->status_mcu, $r->ranking_seleksi,
                    $r->nilai_akademik, $r->nilai_praktek, $r->nilai_sikap, $r->nilai_akhir_training, $r->status_kelulusan, $r->tanggal_kelulusan,
                    $r->jabatan_awal, $r->pt_penempatan, $r->regional_penempatan_training, $r->unit_kerja_penempatan_training, $r->tanggal_sk_awal,
                    $r->status_karyawan, $r->jabatan_terakhir, $r->level_jabatan, $r->region_saat_ini, $r->unit_kerja_saat_ini, $r->atasan_langsung, $r->tanggal_pengangkatan,
                    $r->status_resign, $r->tanggal_resign, $r->alasan_resign, $r->status_ijazah, $r->tanggal_pengambilan_ijazah, $r->no_tanda_terima_ijazah,
                    $r->no_bpjs_tk, $r->no_bpjs_kes, $r->npwp, $r->keterangan,
                ];
            }

            $filename = 'Database_Asisten_Lengkap_' . date('Ymd_His') . '.xlsx';
            return ExcelExporter::download($filename, $headers, $dataRows, 'Asisten Lengkap');
        }
    }

    /**
     * =========================================================================
     * MONITORING PROGRAM & ANGKATAN (124 BATCH & 21 KOLOM PERHITUNGAN)
     * =========================================================================
     */

    /**
     * Get Monitoring Program & Angkatan list with live Database Asisten calculations.
     */
    public function getMonitoringProgramData(Request $request)
    {
        $query = AsistenProgramMonitoring::query();

        // Filter Kategori (Kode A, B, C, D, E atau Nama Kategori)
        if ($request->filled('kategori') && $request->input('kategori') !== 'all') {
            $kat = $request->input('kategori');
            $query->where(function ($q) use ($kat) {
                $q->where('kode_kategori', $kat)
                  ->orWhere('kategori_program', $kat);
            });
        }

        // Filter Tahun
        if ($request->filled('tahun') && $request->input('tahun') !== 'all') {
            $thn = (int) $request->input('tahun');
            $query->where(function ($q) use ($thn) {
                $q->where('tahun_rekrut', $thn)
                  ->orWhere('tahun_training', $thn)
                  ->orWhere('tahun_lulus', $thn);
            });
        }

        // Search Nama Program
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('nama_program', 'like', "%{$search}%")
                  ->orWhere('kategori_program', 'like', "%{$search}%")
                  ->orWhere('tahun_training', 'like', "%{$search}%")
                  ->orWhere('tahun_lulus', 'like', "%{$search}%");
            });
        }

        $programs = $query->orderBy('kode_kategori', 'asc')->orderBy('no', 'asc')->get();

        // Pre-fetch Asisten records grouped by program & status & jabatan for instant performance
        $asistenRecords = Asisten::select(['program', 'keterangan', 'status_kelulusan', 'status_resign', 'jabatan_terakhir', 'status_karyawan'])
            ->whereNotNull('program')
            ->get();

        // Helper normalizer for program strings (handles acronyms like FAT <-> Field Assistant)
        $norm = function ($s) {
            $str = strtolower((string) $s);
            $str = str_replace(['field assistant', 'fat'], 'fat', $str);
            $str = str_replace(['mill assistant', 'mat'], 'mat', $str);
            $str = str_replace(['admin assistant', 'aat'], 'aat', $str);
            $str = str_replace(['traction assistant', 'tat'], 'tat', $str);
            $str = str_replace(['downstream management', 'dmt'], 'dmt', $str);
            return preg_replace('/[^a-zA-Z0-9]/', '', $str);
        };

        // Group asisten by normalized program name
        $asistenByNormProgram = [];
        foreach ($asistenRecords as $a) {
            $key = $norm($a->program);
            if ($key !== '') {
                $asistenByNormProgram[$key][] = $a;
            }
        }

        // Transform 124 batch rows with 21 columns
        $transformedData = [];
        $totalTraineeAll = 0;
        $totalCulledAll = 0;
        $totalPassedAll = 0;
        $totalResignAll = 0;
        $totalEksisAll = 0;
        $totalPcAll = 0;
        $totalGmAll = 0;
        $totalMgrAll = 0;
        $totalAskepAll = 0;
        $totalAsistenAll = 0;

        foreach ($programs as $prog) {
            $normKey = $norm($prog->nama_program);
            $matchingAsisten = $asistenByNormProgram[$normKey] ?? [];

            // If no exact norm match, try prefix/suffix match
            if (empty($matchingAsisten)) {
                foreach ($asistenByNormProgram as $k => $list) {
                    if (str_contains($k, $normKey) || str_contains($normKey, $k)) {
                        $matchingAsisten = array_merge($matchingAsisten, $list);
                    }
                }
            }

            // Live counts from Database Asisten (tab Monitoring Asisten)
            $culledCountFromDb = 0;
            $resignCount = 0;
            $eksisCount = 0;
            $posPC = 0;
            $posGM = 0;
            $posMGR = 0;
            $posAskep = 0;
            $posAsisten = 0;

            foreach ($matchingAsisten as $m) {
                $ket = strtolower(trim((string) $m->keterangan));
                $statusKel = strtolower(trim((string) $m->status_kelulusan));
                $jab = strtoupper(trim((string) $m->jabatan_terakhir));

                if (str_contains($ket, 'gagal') || str_contains($statusKel, 'gagal')) {
                    $culledCountFromDb++;
                } elseif (str_contains($ket, 'resign') || str_contains($ket, 'phk') || str_contains(strtolower($m->status_resign ?? ''), 'resign')) {
                    $resignCount++;
                } else {
                    $eksisCount++;

                    // Position breakdown among active/eksis
                    if (str_contains($jab, 'PC') || $jab === 'PROJECT COORDINATOR') {
                        $posPC++;
                    } elseif (str_contains($jab, 'GM') || str_contains($jab, 'GENERAL MANAGER')) {
                        $posGM++;
                    } elseif (str_contains($jab, 'MGR') || str_contains($jab, 'MANAGER')) {
                        $posMGR++;
                    } elseif (str_contains($jab, 'ASKEP') || str_contains($jab, 'ASSISTANT KEPALA')) {
                        $posAskep++;
                    } else {
                        $posAsisten++;
                    }
                }
            }

            // Derive Trainee & Culled: If records exist in tab Monitoring Asisten, take counts from DB Asisten dynamically
            $asistenCountInDb = count($matchingAsisten);
            if ($asistenCountInDb > 0) {
                $trainee = max((int) $prog->jumlah_trainee, $asistenCountInDb);
                $culled = $culledCountFromDb > 0 ? $culledCountFromDb : (int) $prog->jumlah_culled;
            } else {
                $trainee = (int) $prog->jumlah_trainee;
                $culled = (int) $prog->jumlah_culled;
            }

            $passed = max(0, $trainee - $culled);

            // Percentages (All calculated against Jumlah Trainee)
            $pctCulled = $trainee > 0 ? round(($culled / $trainee) * 100, 2) : 0;
            $pctPassed = $trainee > 0 ? round(($passed / $trainee) * 100, 2) : 0;
            $pctResign = $trainee > 0 ? round(($resignCount / $trainee) * 100, 2) : 0;
            $pctExisting = $trainee > 0 ? round(($eksisCount / $trainee) * 100, 2) : 0;

            // Totals accumulation
            $totalTraineeAll += $trainee;
            $totalCulledAll += $culled;
            $totalPassedAll += $passed;
            $totalResignAll += $resignCount;
            $totalEksisAll += $eksisCount;
            $totalPcAll += $posPC;
            $totalGmAll += $posGM;
            $totalMgrAll += $posMGR;
            $totalAskepAll += $posAskep;
            $totalAsistenAll += $posAsisten;

            $transformedData[] = [
                'id' => $prog->id,
                'no' => $prog->no,
                'kode_kategori' => $prog->kode_kategori,
                'kategori_program' => $prog->kategori_program,
                'nama_program' => $prog->nama_program,
                'tahun_rekrut' => $prog->tahun_rekrut,
                'tahun_training' => $prog->tahun_training,
                'tahun_lulus' => $prog->tahun_lulus,
                'jumlah_trainee' => $trainee,
                'jumlah_culled' => $culled,
                'jumlah_passed' => $passed,
                'jumlah_resign' => $resignCount,
                'jumlah_eksis' => $eksisCount,
                'pct_culled' => $pctCulled,
                'pct_passed' => $pctPassed,
                'pct_resign' => $pctResign,
                'pct_existing' => $pctExisting,
                'posisi_pc' => $posPC,
                'posisi_gm' => $posGM,
                'posisi_mgr' => $posMGR,
                'posisi_askep' => $posAskep,
                'posisi_asisten' => $posAsisten,
                'asisten_count_in_db' => count($matchingAsisten),
                'batch_count' => 1,
                'batch_names' => [$prog->nama_program],
                'batch_names_str' => $prog->nama_program,
            ];
        }

        // Check if view_mode is aggregated by base program name & graduation year
        $viewMode = $request->input('view_mode', 'batch'); // 'batch' | 'aggregated'
        $finalData = $transformedData;

        if ($viewMode === 'aggregated') {
            $aggregatedMap = [];

            foreach ($transformedData as $item) {
                // Determine effective graduation year
                $effYear = $item['tahun_lulus'] ?? $item['tahun_training'] ?? $item['tahun_rekrut'];
                if (empty($effYear)) {
                    if (preg_match('/(20\d{2}|19\d{2})/', (string) $item['nama_program'], $m)) {
                        $effYear = (int) $m[1];
                    }
                }

                $catCode = $item['kode_kategori'] ?: 'OTHER';
                $groupKey = $catCode . '_' . ($effYear ?: 'UNKNOWN');

                if (!isset($aggregatedMap[$groupKey])) {
                    $shortCode = match($catCode) {
                        'A' => 'FAT',
                        'B' => 'MAT',
                        'C' => 'AAT',
                        'D' => 'TAT',
                        'E' => 'DMT',
                        default => $catCode
                    };

                    $aggregatedMap[$groupKey] = [
                        'id' => $groupKey,
                        'no' => 0,
                        'kode_kategori' => $catCode,
                        'kategori_program' => $item['kategori_program'],
                        'nama_program' => "{$item['kategori_program']} ({$shortCode}) - {$effYear}",
                        'tahun_rekrut' => $item['tahun_rekrut'],
                        'tahun_training' => $item['tahun_training'],
                        'tahun_lulus' => $effYear,
                        'jumlah_trainee' => 0,
                        'jumlah_culled' => 0,
                        'jumlah_passed' => 0,
                        'jumlah_resign' => 0,
                        'jumlah_eksis' => 0,
                        'posisi_pc' => 0,
                        'posisi_gm' => 0,
                        'posisi_mgr' => 0,
                        'posisi_askep' => 0,
                        'posisi_asisten' => 0,
                        'batch_count' => 0,
                        'batch_names' => [],
                        'batch_names_str' => '',
                        'asisten_count_in_db' => 0,
                    ];
                }

                $aggregatedMap[$groupKey]['batch_count']++;
                $aggregatedMap[$groupKey]['batch_names'][] = $item['nama_program'];
                $aggregatedMap[$groupKey]['jumlah_trainee'] += $item['jumlah_trainee'];
                $aggregatedMap[$groupKey]['jumlah_culled'] += $item['jumlah_culled'];
                $aggregatedMap[$groupKey]['jumlah_passed'] += $item['jumlah_passed'];
                $aggregatedMap[$groupKey]['jumlah_resign'] += $item['jumlah_resign'];
                $aggregatedMap[$groupKey]['jumlah_eksis'] += $item['jumlah_eksis'];
                $aggregatedMap[$groupKey]['posisi_pc'] += $item['posisi_pc'];
                $aggregatedMap[$groupKey]['posisi_gm'] += $item['posisi_gm'];
                $aggregatedMap[$groupKey]['posisi_mgr'] += $item['posisi_mgr'];
                $aggregatedMap[$groupKey]['posisi_askep'] += $item['posisi_askep'];
                $aggregatedMap[$groupKey]['posisi_asisten'] += $item['posisi_asisten'];
                $aggregatedMap[$groupKey]['asisten_count_in_db'] += $item['asisten_count_in_db'];

                // Track minimum rekrut / training year
                if ($item['tahun_rekrut'] && (!$aggregatedMap[$groupKey]['tahun_rekrut'] || $item['tahun_rekrut'] < $aggregatedMap[$groupKey]['tahun_rekrut'])) {
                    $aggregatedMap[$groupKey]['tahun_rekrut'] = $item['tahun_rekrut'];
                }
                if ($item['tahun_training'] && (!$aggregatedMap[$groupKey]['tahun_training'] || $item['tahun_training'] < $aggregatedMap[$groupKey]['tahun_training'])) {
                    $aggregatedMap[$groupKey]['tahun_training'] = $item['tahun_training'];
                }
            }

            $aggRows = [];
            $counterByCategory = [];
            foreach ($aggregatedMap as $gKey => $g) {
                $cat = $g['kode_kategori'];
                $counterByCategory[$cat] = ($counterByCategory[$cat] ?? 0) + 1;
                $g['no'] = $counterByCategory[$cat];

                $tr = $g['jumlah_trainee'];
                $g['pct_culled'] = $tr > 0 ? round(($g['jumlah_culled'] / $tr) * 100, 2) : 0;
                $g['pct_passed'] = $tr > 0 ? round(($g['jumlah_passed'] / $tr) * 100, 2) : 0;
                $g['pct_resign'] = $tr > 0 ? round(($g['jumlah_resign'] / $tr) * 100, 2) : 0;
                $g['pct_existing'] = $tr > 0 ? round(($g['jumlah_eksis'] / $tr) * 100, 2) : 0;
                $g['batch_names_str'] = implode(', ', $g['batch_names']);

                $aggRows[] = $g;
            }

            $finalData = $aggRows;
        }

        // Global / Filtered Totals
        $globalSummary = [
            'total_batches' => count($finalData),
            'total_trainee' => $totalTraineeAll,
            'total_culled' => $totalCulledAll,
            'total_passed' => $totalPassedAll,
            'total_resign' => $totalResignAll,
            'total_eksis' => $totalEksisAll,
            'pct_culled_weighted' => $totalTraineeAll > 0 ? round(($totalCulledAll / $totalTraineeAll) * 100, 2) : 0,
            'pct_passed_weighted' => $totalTraineeAll > 0 ? round(($totalPassedAll / $totalTraineeAll) * 100, 2) : 0,
            'pct_resign_weighted' => $totalTraineeAll > 0 ? round(($totalResignAll / $totalTraineeAll) * 100, 2) : 0,
            'pct_existing_weighted' => $totalTraineeAll > 0 ? round(($totalEksisAll / $totalTraineeAll) * 100, 2) : 0,
            'posisi_pc_total' => $totalPcAll,
            'posisi_gm_total' => $totalGmAll,
            'posisi_mgr_total' => $totalMgrAll,
            'posisi_askep_total' => $totalAskepAll,
            'posisi_asisten_total' => $totalAsistenAll,
        ];

        // Unique filter options
        $kategoriList = AsistenProgramMonitoring::whereNotNull('kategori_program')
            ->distinct()
            ->orderBy('kode_kategori')
            ->get(['kode_kategori', 'kategori_program']);

        $tahunList = AsistenProgramMonitoring::whereNotNull('tahun_training')
            ->distinct()
            ->orderBy('tahun_training', 'desc')
            ->pluck('tahun_training');

        return response()->json([
            'status' => 'success',
            'view_mode' => $viewMode,
            'data' => $finalData,
            'summary' => $globalSummary,
            'filters' => [
                'kategori' => $kategoriList,
                'tahun' => $tahunList,
            ],
        ]);
    }

    /**
     * Get individual asisten records belonging to a program for the drilldown modal.
     */
    public function getDrilldownAsisten(Request $request)
    {
        $programInput = trim((string) $request->input('program', ''));
        $filterType = $request->input('filter_type', 'all'); // 'all', 'eksis', 'resign', 'pc', 'gm', 'mgr', 'askep', 'asisten'

        if (empty($programInput)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter nama program wajib diisi.',
            ], 400);
        }

        $programList = array_filter(array_map('trim', explode(',', $programInput)));

        $query = Asisten::query();

        // Match Program Names flexibly (support single or multiple comma-separated batches)
        $query->where(function ($q) use ($programList) {
            foreach ($programList as $idx => $pName) {
                if ($idx === 0) {
                    $q->where('program', $pName)
                      ->orWhere('program', 'like', "%{$pName}%");
                } else {
                    $q->orWhere('program', $pName)
                      ->orWhere('program', 'like', "%{$pName}%");
                }
            }
        });

        if ($filterType === 'resign') {
            $query->where(function ($q) {
                $q->where('keterangan', 'Resign')
                  ->orWhere('status_resign', 'Resign')
                  ->orWhere('status_resign', 'PHK');
            });
        } elseif ($filterType === 'eksis') {
            $query->where(function ($q) {
                $q->where('keterangan', 'Aktif')
                  ->orWhere('keterangan', 'Eksis');
            });
        } elseif ($filterType === 'pc') {
            $query->where(function ($q) {
                $q->where('keterangan', 'Aktif')
                  ->where('level_jabatan', 'like', '%PC%');
            });
        } elseif ($filterType === 'gm') {
            $query->where(function ($q) {
                $q->where('keterangan', 'Aktif')
                  ->where('level_jabatan', 'like', '%GM%');
            });
        } elseif ($filterType === 'mgr') {
            $query->where(function ($q) {
                $q->where('keterangan', 'Aktif')
                  ->where(fn($sub) => $sub->where('level_jabatan', 'like', '%MK%')->orWhere('level_jabatan', 'like', '%MM%')->orWhere('level_jabatan', 'like', '%MGR%')->orWhere('level_jabatan', 'like', '%Manager%'));
            });
        } elseif ($filterType === 'askep') {
            $query->where(function ($q) {
                $q->where('keterangan', 'Aktif')
                  ->where(fn($sub) => $sub->where('level_jabatan', 'like', '%ASKEP%')->orWhere('level_jabatan', 'like', '%KTU%'));
            });
        } elseif ($filterType === 'asisten') {
            $query->where(function ($q) {
                $q->where('keterangan', 'Aktif')
                  ->where(fn($sub) => $sub->where('level_jabatan', 'like', '%ASISTEN%')->orWhere('level_jabatan', 'like', '%ASST%')->orWhere('level_jabatan', 'like', '%Staff%'));
            });
        }

        $records = $query->orderBy('nama', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'program' => $programInput,
            'filter_type' => $filterType,
            'total' => $records->count(),
            'data' => $records,
        ]);
    }

    /**
     * Store new batch program monitoring.
     */
    public function storeMonitoringProgram(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'kategori_program' => 'nullable|string|max:150',
            'kode_kategori' => 'nullable|string|max:10',
            'tahun' => 'nullable|integer',
            'tahun_rekrut' => 'nullable|integer',
            'tahun_training' => 'nullable|integer',
            'tahun_lulus' => 'nullable|integer',
            'jumlah_trainee' => 'nullable|integer|min:0',
            'jumlah_culled' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        if (empty($data['no'])) {
            $data['no'] = ((int) AsistenProgramMonitoring::max('no')) + 1;
        }

        // Fill years if simplified single 'tahun' is sent
        if (!empty($data['tahun'])) {
            $data['tahun_training'] = $data['tahun_training'] ?? (int) $data['tahun'];
            $data['tahun_lulus'] = $data['tahun_lulus'] ?? (int) $data['tahun'];
            $data['tahun_rekrut'] = $data['tahun_rekrut'] ?? (int) $data['tahun'];
        }

        if (empty($data['kategori_program']) && !empty($data['kode_kategori'])) {
            $data['kategori_program'] = match($data['kode_kategori']) {
                'A' => 'Field Assistant Training',
                'B' => 'Mill Assistant Training',
                'C' => 'Admin Assistant Training',
                'D' => 'Traction Assistant Training',
                'E' => 'Downstream Management Training',
                default => 'Program Training'
            };
        }

        $data['jumlah_trainee'] = (int) ($data['jumlah_trainee'] ?? 0);
        $data['jumlah_culled'] = (int) ($data['jumlah_culled'] ?? 0);

        $record = AsistenProgramMonitoring::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data program monitoring berhasil ditambahkan!',
            'data' => $record,
        ], 201);
    }

    /**
     * Update existing batch program monitoring.
     */
    public function updateMonitoringProgram(Request $request, $id)
    {
        $record = AsistenProgramMonitoring::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama_program' => 'required|string|max:255',
            'kategori_program' => 'nullable|string|max:150',
            'kode_kategori' => 'nullable|string|max:10',
            'tahun' => 'nullable|integer',
            'tahun_rekrut' => 'nullable|integer',
            'tahun_training' => 'nullable|integer',
            'tahun_lulus' => 'nullable|integer',
            'jumlah_trainee' => 'nullable|integer|min:0',
            'jumlah_culled' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi data gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        if (!empty($data['tahun'])) {
            $data['tahun_training'] = $data['tahun_training'] ?? (int) $data['tahun'];
            $data['tahun_lulus'] = $data['tahun_lulus'] ?? (int) $data['tahun'];
            $data['tahun_rekrut'] = $data['tahun_rekrut'] ?? (int) $data['tahun'];
        }

        if (empty($data['kategori_program']) && !empty($data['kode_kategori'])) {
            $data['kategori_program'] = match($data['kode_kategori']) {
                'A' => 'Field Assistant Training',
                'B' => 'Mill Assistant Training',
                'C' => 'Admin Assistant Training',
                'D' => 'Traction Assistant Training',
                'E' => 'Downstream Management Training',
                default => $record->kategori_program ?? 'Program Training'
            };
        }

        $record->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data program monitoring berhasil diperbarui!',
            'data' => $record,
        ]);
    }

    /**
     * Delete a batch program monitoring.
     */
    public function destroyMonitoringProgram($id)
    {
        $record = AsistenProgramMonitoring::findOrFail($id);
        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data program monitoring berhasil dihapus!',
        ]);
    }

    /**
     * Export 21 columns Monitoring Program & Angkatan to CSV.
     */
    public function exportMonitoringSummarized(Request $request): StreamedResponse
    {
        $res = $this->getMonitoringProgramData($request);
        $json = $res->getData(true);
        $records = $json['data'] ?? [];
        $summary = $json['summary'] ?? [];

        $filename = 'Monitoring_Program_Angkatan_' . date('Ymd_His') . '.xlsx';
        $headers = [
            'No', 'Kode_Kategori', 'Kategori_Program', 'Nama_Program',
            'Tahun_Rekrut', 'Tahun_Training', 'Tahun_Lulus',
            'Jumlah_Trainee', 'Jumlah_Culled', 'Jumlah_Passed',
            'Jumlah_Resign', 'Jumlah_Eksis',
            'Pct_Culled', 'Pct_Passed', 'Pct_Resign', 'Pct_Existing',
            'Posisi_PC', 'Posisi_GM', 'Posisi_MGR', 'Posisi_Askep', 'Posisi_Asisten'
        ];

        $dataRows = [];
        foreach ($records as $r) {
            $dataRows[] = [
                $r['no'],
                $r['kode_kategori'],
                $r['kategori_program'],
                $r['nama_program'],
                $r['tahun_rekrut'],
                $r['tahun_training'],
                $r['tahun_lulus'],
                $r['jumlah_trainee'],
                $r['jumlah_culled'],
                $r['jumlah_passed'],
                $r['jumlah_resign'],
                $r['jumlah_eksis'],
                number_format($r['pct_culled'] / 100, 4),
                number_format($r['pct_passed'] / 100, 4),
                number_format($r['pct_resign'] / 100, 4),
                number_format($r['pct_existing'] / 100, 4),
                $r['posisi_pc'],
                $r['posisi_gm'],
                $r['posisi_mgr'],
                $r['posisi_askep'],
                $r['posisi_asisten'],
            ];
        }

        // Summary row
        $dataRows[] = [
            'TOTAL', '', '', count($records) . ' Batch Programs',
            '', '', '',
            $summary['total_trainee'] ?? 0,
            $summary['total_culled'] ?? 0,
            $summary['total_passed'] ?? 0,
            $summary['total_resign'] ?? 0,
            $summary['total_eksis'] ?? 0,
            number_format(($summary['pct_culled_weighted'] ?? 0) / 100, 4),
            number_format(($summary['pct_passed_weighted'] ?? 0) / 100, 4),
            number_format(($summary['pct_resign_weighted'] ?? 0) / 100, 4),
            number_format(($summary['pct_existing_weighted'] ?? 0) / 100, 4),
            $summary['posisi_pc_total'] ?? 0,
            $summary['posisi_gm_total'] ?? 0,
            $summary['posisi_mgr_total'] ?? 0,
            $summary['posisi_askep_total'] ?? 0,
            $summary['posisi_asisten_total'] ?? 0,
        ];

        return ExcelExporter::download($filename, $headers, $dataRows, 'Monitoring Summarized');
    }

    /**
     * Import / Update Master Monitoring Program CSV via SpreadsheetReader.
     */
    public function importMonitoringProgramCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'mode' => 'nullable|in:append,replace',
        ]);

        $mode = $request->input('mode', 'replace');

        try {
            $file = $request->file('file');
            $rawRows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal membaca file: ' . $e->getMessage()], 400);
        }

        if (empty($rawRows)) {
            return response()->json(['status' => 'error', 'message' => 'File kosong atau tidak memiliki baris data.'], 400);
        }

        // Shift header row
        array_shift($rawRows);

        $rows = [];
        $rowNum = 1;

        foreach ($rawRows as $row) {
            $rowNum++;
            if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) continue;

            $namaProg = trim((string)($row[3] ?? ''));
            if (empty($namaProg)) continue;

            $rows[] = [
                'no' => !empty($row[0]) && is_numeric($row[0]) ? (int) $row[0] : $rowNum - 1,
                'kode_kategori' => trim((string)($row[1] ?? '')) ?: null,
                'kategori_program' => trim((string)($row[2] ?? '')) ?: null,
                'nama_program' => $namaProg,
                'tahun_rekrut' => !empty($row[4]) && is_numeric($row[4]) ? (int) $row[4] : null,
                'tahun_training' => !empty($row[5]) && is_numeric($row[5]) ? (int) $row[5] : null,
                'tahun_lulus' => !empty($row[6]) && is_numeric($row[6]) ? (int) $row[6] : null,
                'jumlah_trainee' => !empty($row[7]) && is_numeric($row[7]) ? (int) $row[7] : 0,
                'jumlah_culled' => !empty($row[8]) && is_numeric($row[8]) ? (int) $row[8] : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($mode === 'replace') {
            AsistenProgramMonitoring::query()->delete();
        }

        foreach (array_chunk($rows, 50) as $chunk) {
            AsistenProgramMonitoring::insert($chunk);
        }

        ActivityLog::log(
            'Import',
            'Monitoring Alumni',
            "Mengimport " . count($rows) . " data batch program dari file " . $file->getClientOriginalName(),
            ['filename' => $file->getClientOriginalName(), 'count' => count($rows), 'mode' => $mode]
        );

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil mengimpor " . count($rows) . " data batch program!",
        ]);
    }
}
