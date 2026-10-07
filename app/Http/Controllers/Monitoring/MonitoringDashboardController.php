<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Monitoring\MonitoringAfdeling;
use App\Models\Monitoring\MonitoringAngkatan;
use App\Models\Monitoring\MonitoringCulled;
use App\Models\Monitoring\MonitoringEmployee;
use App\Services\ExcelExporter;
use App\Services\SpreadsheetReader;
use Database\Seeders\Monitoring\MonitoringSeeder;
use Database\Seeders\MonitoringSeeder as RootMonitoringSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringDashboardController extends Controller
{
    /**
     * Display the Monitoring SPA index view.
     */
    public function index()
    {
        return view('Administrasi.Pages.Monitoring_Spesifik.index');
    }

    /**
     * Map request payload / row array to MonitoringEmployee attributes.
     */
    protected function normalizeEmployeePayload(array $data): array
    {
        return [
            'nik' => $data['NIK'] ?? $data['nik'] ?? null,
            'nama' => $data['NAMA'] ?? $data['nama'] ?? 'Tanpa Nama',
            'join_date' => $data['JOIN DATE'] ?? $data['join_date'] ?? null,
            'jabatan' => $data['JABATAN'] ?? $data['jabatan'] ?? null,
            'level_jabatan' => $data['LEVEL JABATAN'] ?? $data['level_jabatan'] ?? null,
            'angkatan_fr_academy' => $data['ANGKATAN FR ACADEMY'] ?? $data['angkatan_fr_academy'] ?? null,
            'kategori' => $data['KATEGORI'] ?? $data['kategori'] ?? null,
            'alumni' => $data['ALUMNI'] ?? $data['alumni'] ?? null,
            'suku' => $data['SUKU'] ?? $data['suku'] ?? null,
            'agama' => $data['AGAMA'] ?? $data['agama'] ?? null,
            'lokasi_terakhir' => $data['LOKASI TERAKHIR'] ?? $data['lokasi_terakhir'] ?? null,
            'region_terakhir' => $data['REGION TERAKHIR'] ?? $data['region_terakhir'] ?? null,
            'status' => $data['STATUS'] ?? $data['status'] ?? 'Aktif',
            'date_terminate' => $data['DATE TERMINATE'] ?? $data['date_terminate'] ?? null,
            'bulan_terminate' => $data['BULAN TERMINATE'] ?? $data['bulan_terminate'] ?? null,
            'tahun_terminate' => $data['TAHUN TERMINATE'] ?? $data['tahun_terminate'] ?? null,
            'lama_bekerja_bulan' => $data['LAMA BEKERJA (BULAN)'] ?? $data['lama_bekerja_bulan'] ?? null,
            'lama_bekerja_tahun' => $data['LAMA BEKERJA (TAHUN)'] ?? $data['lama_bekerja_tahun'] ?? null,
            'lama_bekerja' => $data['LAMA BEKERJA'] ?? $data['lama_bekerja'] ?? null,
            'alasan_resign' => $data['Alasan Resign'] ?? $data['alasan_resign'] ?? null,
            'detail' => $data['Detail'] ?? $data['detail'] ?? null,
            'lokasi_awal_penempatan' => $data['LOKASI AWAL PENEMPATAN'] ?? $data['lokasi_awal_penempatan'] ?? null,
            'region_awal_penempatan' => $data['REGION AWAL PENEMPATAN'] ?? $data['region_awal_penempatan'] ?? null,
            'gm' => $data['GM'] ?? $data['gm'] ?? null,
            'mk' => $data['MK'] ?? $data['mk'] ?? null,
        ];
    }

    // -------------------------------------------------------------
    // EMPLOYEES API
    // -------------------------------------------------------------
    public function getEmployees(Request $request)
    {
        $query = MonitoringEmployee::query()->orderBy('id_monitoring', 'desc');

        if ($request->filled('status')) {
            $status = $request->input('status');
            if (is_array($status)) {
                $query->whereIn('status', $status);
            } else {
                $query->where('status', $status);
            }
        }

        $employees = $query->get()->map(fn ($emp) => $emp->toFrontendArray());

        return response()->json([
            'status' => 'success',
            'data' => $employees,
            'total' => count($employees),
        ]);
    }

    public function storeEmployee(Request $request)
    {
        $attributes = $this->normalizeEmployeePayload($request->all());
        $employee = MonitoringEmployee::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $employee->toFrontendArray(),
        ], 201);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = MonitoringEmployee::findOrFail($id);
        $attributes = $this->normalizeEmployeePayload($request->all());

        // Only update non-null attributes present in the request
        $data = $request->all();
        $filtered = [];
        foreach ($attributes as $col => $val) {
            $upperKey = strtoupper(str_replace('_', ' ', $col));
            if (array_key_exists($col, $data) || array_key_exists($upperKey, $data)) {
                $filtered[$col] = $val;
            }
        }

        $employee->update(! empty($filtered) ? $filtered : $attributes);

        return response()->json([
            'status' => 'success',
            'data' => $employee->fresh()->toFrontendArray(),
        ]);
    }

    public function destroyEmployee($id)
    {
        $employee = MonitoringEmployee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Karyawan berhasil dihapus',
        ]);
    }

    public function destroyAllEmployees()
    {
        $count = DB::table('monitoring_employees')->count();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('monitoring_employees')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ActivityLog::log(
            'Hapus',
            'Monitoring Alumni',
            "Menghapus seluruh data karyawan alumni ({$count} data)",
            ['count' => $count]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh data karyawan berhasil dihapus.',
        ]);
    }

    // -------------------------------------------------------------
    // ANGKATAN API
    // -------------------------------------------------------------
    public function getAngkatan()
    {
        $angkatan = MonitoringAngkatan::orderBy('id_angkatan', 'desc')
            ->get()
            ->map(fn ($item) => $item->toFrontendArray());

        return response()->json([
            'status' => 'success',
            'data' => $angkatan,
        ]);
    }

    public function storeAngkatan(Request $request)
    {
        $data = $request->all();
        $jumlahAwal = (int) ($data['Jumlah Awal'] ?? $data['jumlah_awal'] ?? 0);
        $resignInClass = (int) ($data['Resign_In_Class'] ?? $data['resign_in_class'] ?? 0);
        $resignOjt = (int) ($data['Resign_OJT'] ?? $data['resign_ojt'] ?? 0);
        $tidakLulus = (int) ($data['tidak_lulus'] ?? $data['tidakLulus'] ?? 0);
        $lulus = max($jumlahAwal - $resignInClass - $resignOjt - $tidakLulus, 0);

        $record = MonitoringAngkatan::create([
            'angkatan' => $data['angkatan'] ?? '',
            'jumlah_awal' => $jumlahAwal,
            'lulus' => $lulus,
            'resign_in_class' => $resignInClass,
            'resign_ojt' => $resignOjt,
            'tidak_lulus' => $tidakLulus,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $record->toFrontendArray(),
        ], 201);
    }

    public function updateAngkatan(Request $request, $id)
    {
        $record = MonitoringAngkatan::findOrFail($id);
        $data = $request->all();

        $jumlahAwal = isset($data['Jumlah Awal']) || isset($data['jumlah_awal'])
            ? (int) ($data['Jumlah Awal'] ?? $data['jumlah_awal'])
            : (int) $record->jumlah_awal;

        $resignInClass = isset($data['Resign_In_Class']) || isset($data['resign_in_class'])
            ? (int) ($data['Resign_In_Class'] ?? $data['resign_in_class'])
            : (int) $record->resign_in_class;

        $resignOjt = isset($data['Resign_OJT']) || isset($data['resign_ojt'])
            ? (int) ($data['Resign_OJT'] ?? $data['resign_ojt'])
            : (int) $record->resign_ojt;

        $tidakLulus = isset($data['tidak_lulus']) || isset($data['tidakLulus'])
            ? (int) ($data['tidak_lulus'] ?? $data['tidakLulus'])
            : (int) $record->tidak_lulus;

        $lulus = max($jumlahAwal - $resignInClass - $resignOjt - $tidakLulus, 0);

        $record->update([
            'angkatan' => $data['angkatan'] ?? $record->angkatan,
            'jumlah_awal' => $jumlahAwal,
            'lulus' => $lulus,
            'resign_in_class' => $resignInClass,
            'resign_ojt' => $resignOjt,
            'tidak_lulus' => $tidakLulus,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $record->fresh()->toFrontendArray(),
        ]);
    }

    public function destroyAngkatan($id)
    {
        $record = MonitoringAngkatan::findOrFail($id);
        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Angkatan berhasil dihapus',
        ]);
    }

    public function destroyAllAngkatan()
    {
        $count = DB::table('monitoring_angkatan')->count();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('monitoring_angkatan')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ActivityLog::log(
            'Hapus',
            'Monitoring Alumni',
            "Menghapus seluruh data angkatan alumni ({$count} data)",
            ['count' => $count]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Seluruh data angkatan berhasil dihapus.',
        ]);
    }

    // -------------------------------------------------------------
    // CULLED API
    // -------------------------------------------------------------
    public function getCulled()
    {
        $culled = MonitoringCulled::with('angkatan')
            ->orderBy('id_culled', 'desc')
            ->get()
            ->map(fn ($item) => $item->toFrontendArray());

        return response()->json([
            'status' => 'success',
            'data' => $culled,
        ]);
    }

    public function storeCulled(Request $request)
    {
        $data = $request->all();
        $record = MonitoringCulled::create([
            'id_angkatan' => $data['id_angkatan'] ?? null,
            'nama' => $data['nama'] ?? 'Tanpa Nama',
            'alasan' => $data['alasan'] ?? null,
            'kategori_status' => $data['kategori_status'] ?? 'Culled',
            'jenis_culled' => $data['Jenis_culled'] ?? $data['jenis_culled'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $record->load('angkatan')->toFrontendArray(),
        ], 201);
    }

    public function updateCulled(Request $request, $id)
    {
        $record = MonitoringCulled::findOrFail($id);
        $data = $request->all();

        $record->update([
            'id_angkatan' => $data['id_angkatan'] ?? $record->id_angkatan,
            'nama' => $data['nama'] ?? $record->nama,
            'alasan' => $data['alasan'] ?? $record->alasan,
            'kategori_status' => $data['kategori_status'] ?? $record->kategori_status,
            'jenis_culled' => $data['Jenis_culled'] ?? $data['jenis_culled'] ?? $record->jenis_culled,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $record->fresh()->load('angkatan')->toFrontendArray(),
        ]);
    }

    public function destroyCulled($id)
    {
        $record = MonitoringCulled::findOrFail($id);
        $record->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data culled berhasil dihapus',
        ]);
    }

    public function destroyAllCulled()
    {
        $count = DB::table('monitoring_culled')->count();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('monitoring_culled')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        ActivityLog::log(
            'Hapus',
            'Monitoring Alumni',
            "Menghapus seluruh data peserta culled ({$count} data)",
            ['count' => $count]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Semua data culled berhasil dihapus.',
        ]);
    }

    // -------------------------------------------------------------
    // AFDELING API
    // -------------------------------------------------------------
    public function getAfdeling()
    {
        $afdeling = MonitoringAfdeling::all()->map(fn ($item) => $item->toFrontendArray());

        return response()->json([
            'status' => 'success',
            'data' => $afdeling,
        ]);
    }

    public function updateAfdeling(Request $request)
    {
        $regionMap = $request->all();

        foreach ($regionMap as $region => $total) {
            MonitoringAfdeling::updateOrCreate(
                ['region' => $region],
                ['total' => (int) $total]
            );
        }

        $afdeling = MonitoringAfdeling::all()->map(fn ($item) => $item->toFrontendArray());

        return response()->json([
            'status' => 'success',
            'data' => $afdeling,
        ]);
    }

    // -------------------------------------------------------------
    // IMPORT APIS
    // -------------------------------------------------------------
    public function importEmployees(Request $request)
    {
        $mode = $request->input('mode', 'replace'); // 'replace' or 'append'
        $rows = [];

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $rawRows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());
                if (! empty($rawRows)) {
                    $header = array_shift($rawRows);
                    foreach ($rawRows as $data) {
                        $row = [];
                        foreach ($header as $i => $col) {
                            $row[trim((string) $col)] = isset($data[$i]) ? trim((string) $data[$i]) : null;
                        }
                        if (! empty($row['NAMA']) || ! empty($row['NIK']) || ! empty($row['ID_MONITORING'])) {
                            $rows[] = $row;
                        }
                    }
                }
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Gagal membaca file: '.$e->getMessage()], 422);
            }
        } elseif ($request->filled('rows') && is_array($request->input('rows'))) {
            $rows = $request->input('rows');
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'File CSV atau data rows tidak ditemukan.',
            ], 422);
        }

        if (empty($rows)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada baris data valid untuk diimport.',
            ], 422);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if ($mode === 'replace') {
            DB::table('monitoring_employees')->truncate();
            $seqId = 1;
        } else {
            $maxId = (int) DB::table('monitoring_employees')->max('id_monitoring');
            $seqId = $maxId + 1;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $batch = [];
        $insertedCount = 0;

        foreach ($rows as $row) {
            $nama = $row['NAMA'] ?? $row['nama'] ?? '';
            if (empty($nama) && empty($row['NIK'] ?? $row['nik'] ?? '')) {
                continue;
            }

            $payload = $this->normalizeEmployeePayload($row);
            $payload['id_monitoring'] = $seqId++;
            $payload['created_at'] = now();
            $payload['updated_at'] = now();

            $batch[] = $payload;

            if (count($batch) >= 100) {
                DB::table('monitoring_employees')->insert($batch);
                $insertedCount += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('monitoring_employees')->insert($batch);
            $insertedCount += count($batch);
        }

        ActivityLog::log(
            'Import',
            'Monitoring Alumni',
            "Mengimport {$insertedCount} data karyawan alumni",
            ['count' => $insertedCount, 'mode' => $mode]
        );

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil mengimport {$insertedCount} data karyawan.",
            'total' => DB::table('monitoring_employees')->count(),
        ]);
    }

    public function importAngkatan(Request $request)
    {
        $mode = $request->input('mode', 'replace');
        $rows = [];

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $rawRows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());
                if (! empty($rawRows)) {
                    $header = array_shift($rawRows);
                    foreach ($rawRows as $data) {
                        $row = [];
                        foreach ($header as $i => $col) {
                            $row[trim((string) $col)] = isset($data[$i]) ? trim((string) $data[$i]) : null;
                        }
                        if (! empty($row['angkatan']) || ! empty($row['Angkatan'])) {
                            $rows[] = $row;
                        }
                    }
                }
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Gagal membaca file: '.$e->getMessage()], 422);
            }
        } elseif ($request->filled('rows') && is_array($request->input('rows'))) {
            $rows = $request->input('rows');
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'File CSV atau data rows tidak ditemukan.',
            ], 422);
        }

        if (empty($rows)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada baris data valid untuk diimport.',
            ], 422);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if ($mode === 'replace') {
            DB::table('monitoring_angkatan')->truncate();
            $seqId = 1;
        } else {
            $maxId = (int) DB::table('monitoring_angkatan')->max('id_angkatan');
            $seqId = $maxId + 1;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $batch = [];
        $insertedCount = 0;

        foreach ($rows as $row) {
            $angkatan = $row['angkatan'] ?? $row['Angkatan'] ?? '';
            if (empty($angkatan)) {
                continue;
            }

            $batch[] = [
                'id_angkatan' => $seqId++,
                'angkatan' => $angkatan,
                'jumlah_awal' => (int) ($row['Jumlah Awal'] ?? $row['jumlah_awal'] ?? 0),
                'lulus' => (int) ($row['lulus'] ?? $row['Lulus'] ?? 0),
                'resign_in_class' => (int) ($row['Resign_In_Class'] ?? $row['resign_in_class'] ?? 0),
                'resign_ojt' => (int) ($row['Resign_OJT'] ?? $row['resign_ojt'] ?? 0),
                'tidak_lulus' => (int) ($row['tidak_lulus'] ?? $row['tidak_lulus_calculated'] ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 50) {
                DB::table('monitoring_angkatan')->insert($batch);
                $insertedCount += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('monitoring_angkatan')->insert($batch);
            $insertedCount += count($batch);
        }

        ActivityLog::log(
            'Import',
            'Monitoring Alumni',
            "Mengimport {$insertedCount} data angkatan",
            ['count' => $insertedCount, 'mode' => $mode]
        );

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil mengimport {$insertedCount} data angkatan.",
            'total' => DB::table('monitoring_angkatan')->count(),
        ]);
    }

    public function importCulled(Request $request)
    {
        $mode = $request->input('mode', 'replace');
        $rows = [];

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                $rawRows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());
                if (! empty($rawRows)) {
                    $header = array_shift($rawRows);
                    foreach ($rawRows as $data) {
                        $row = [];
                        foreach ($header as $i => $col) {
                            $row[trim((string) $col)] = isset($data[$i]) ? trim((string) $data[$i]) : null;
                        }
                        if (! empty($row['NAMA']) || ! empty($row['nama']) || ! empty($row['Nama']) || ! empty($row['Nama Lengkap'])) {
                            $rows[] = $row;
                        }
                    }
                }
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Gagal membaca file: '.$e->getMessage()], 422);
            }
        } elseif ($request->filled('rows') && is_array($request->input('rows'))) {
            $rows = $request->input('rows');
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'File CSV atau data rows tidak ditemukan.',
            ], 422);
        }

        if (empty($rows)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada baris data valid untuk diimport.',
            ], 422);
        }

        $angkatanMap = DB::table('monitoring_angkatan')->pluck('id_angkatan', 'angkatan')->mapWithKeys(function ($id, $name) {
            return [strtolower(trim((string) $name)) => $id];
        })->toArray();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        if ($mode === 'replace') {
            DB::table('monitoring_culled')->truncate();
            $seqId = 1;
        } else {
            $maxId = (int) DB::table('monitoring_culled')->max('id_culled');
            $seqId = $maxId + 1;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $batch = [];
        $insertedCount = 0;

        foreach ($rows as $row) {
            $nama = $row['NAMA'] ?? $row['nama'] ?? $row['Nama'] ?? $row['Nama Lengkap'] ?? '';
            if (empty($nama)) {
                continue;
            }

            $idAngkatan = null;
            $angkatanName = $row['ANGKATAN'] ?? $row['angkatan'] ?? $row['Angkatan'] ?? $row['id_angkatan'] ?? $row['ID_ANGKATAN'] ?? null;
            if ($angkatanName) {
                if (is_numeric($angkatanName) && in_array((int) $angkatanName, $angkatanMap, true)) {
                    $idAngkatan = (int) $angkatanName;
                } else {
                    $cleanName = strtolower(trim((string) $angkatanName));
                    if (isset($angkatanMap[$cleanName])) {
                        $idAngkatan = $angkatanMap[$cleanName];
                    }
                }
            }

            $status = $row['KATEGORI_STATUS'] ?? $row['kategori_status'] ?? $row['Kategori Status'] ?? $row['STATUS'] ?? $row['status'] ?? 'Culled';
            $jenisCulled = $row['JENIS_CULLED'] ?? $row['jenis_culled'] ?? $row['Jenis_culled'] ?? $row['Jenis Culled'] ?? null;
            $alasan = $row['ALASAN'] ?? $row['alasan'] ?? $row['Alasan'] ?? $row['Alasan Detail'] ?? null;

            $batch[] = [
                'id_culled' => $seqId++,
                'id_angkatan' => $idAngkatan,
                'nama' => $nama,
                'alasan' => $alasan,
                'kategori_status' => $status,
                'jenis_culled' => $jenisCulled,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 100) {
                DB::table('monitoring_culled')->insert($batch);
                $insertedCount += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('monitoring_culled')->insert($batch);
            $insertedCount += count($batch);
        }

        ActivityLog::log(
            'Import',
            'Monitoring Alumni',
            "Mengimport {$insertedCount} data peserta culled",
            ['count' => $insertedCount, 'mode' => $mode]
        );

        return response()->json([
            'status' => 'success',
            'message' => "Berhasil mengimport {$insertedCount} data peserta culled.",
            'total' => DB::table('monitoring_culled')->count(),
        ]);
    }

    // -------------------------------------------------------------
    // EXPORT & TEMPLATE APIS
    // -------------------------------------------------------------
    public function exportEmployees(Request $request)
    {
        $employees = MonitoringEmployee::orderBy('id_monitoring', 'asc')->get();

        $headers = [
            'NIK', 'NAMA', 'JOIN DATE', 'JABATAN', 'LEVEL JABATAN',
            'ANGKATAN FR ACADEMY', 'KATEGORI', 'ALUMNI', 'SUKU', 'AGAMA',
            'LOKASI TERAKHIR', 'REGION TERAKHIR', 'STATUS', 'DATE TERMINATE',
            'BULAN TERMINATE', 'TAHUN TERMINATE', 'LAMA BEKERJA (BULAN)',
            'LAMA BEKERJA (TAHUN)', 'LAMA BEKERJA', 'Alasan Resign', 'Detail',
            'LOKASI AWAL PENEMPATAN', 'REGION AWAL PENEMPATAN', 'GM', 'MK',
        ];

        $dataRows = [];
        foreach ($employees as $emp) {
            $dataRows[] = [
                $emp->nik,
                $emp->nama,
                $emp->join_date,
                $emp->jabatan,
                $emp->level_jabatan,
                $emp->angkatan_fr_academy,
                $emp->kategori,
                $emp->alumni,
                $emp->suku,
                $emp->agama,
                $emp->lokasi_terakhir,
                $emp->region_terakhir,
                $emp->status,
                $emp->date_terminate,
                $emp->bulan_terminate,
                $emp->tahun_terminate,
                $emp->lama_bekerja_bulan,
                $emp->lama_bekerja_tahun,
                $emp->lama_bekerja,
                $emp->alasan_resign,
                $emp->detail,
                $emp->lokasi_awal_penempatan,
                $emp->region_awal_penempatan,
                $emp->gm,
                $emp->mk,
            ];
        }

        return ExcelExporter::download('Export_Master_Data_Karyawan_'.date('Ymd_His').'.xlsx', $headers, $dataRows, 'Data Karyawan');
    }

    public function templateEmployees()
    {
        $headers = [
            'NIK', 'NAMA', 'JOIN DATE', 'JABATAN', 'LEVEL JABATAN',
            'ANGKATAN FR ACADEMY', 'KATEGORI', 'ALUMNI', 'SUKU', 'AGAMA',
            'LOKASI TERAKHIR', 'REGION TERAKHIR', 'STATUS', 'DATE TERMINATE',
            'BULAN TERMINATE', 'TAHUN TERMINATE', 'LAMA BEKERJA (BULAN)',
            'LAMA BEKERJA (TAHUN)', 'LAMA BEKERJA', 'Alasan Resign', 'Detail',
            'LOKASI AWAL PENEMPATAN', 'REGION AWAL PENEMPATAN', 'GM', 'MK',
        ];

        $sampleRows = [
            [
                '10001234', 'Budi Santoso', '2022-01-15', 'Field Assistant', 'Assistant',
                'FAT 1 2022', 'FAT', 'Universitas Gadjah Mada', 'Jawa', 'Islam',
                'Kebun Sei Buatan', 'Riau', 'Aktif', '',
                '', '', '',
                '', '', '', '',
                'Kebun Sei Buatan', 'Riau', 'Bambang S', 'M. Kurniawan',
            ],
        ];

        return ExcelExporter::download('Template_Import_Master_Karyawan.xlsx', $headers, $sampleRows, 'Template Karyawan');
    }

    public function exportAngkatan(Request $request)
    {
        $angkatan = MonitoringAngkatan::orderBy('id_angkatan', 'asc')->get();

        $headers = ['angkatan', 'Jumlah Awal', 'lulus', 'Resign_In_Class', 'Resign_OJT', 'tidak_lulus'];

        $dataRows = [];
        foreach ($angkatan as $item) {
            $dataRows[] = [
                $item->angkatan,
                $item->jumlah_awal,
                $item->lulus,
                $item->resign_in_class,
                $item->resign_ojt,
                $item->tidak_lulus,
            ];
        }

        return ExcelExporter::download('Export_Master_Data_Angkatan_'.date('Ymd_His').'.xlsx', $headers, $dataRows, 'Data Angkatan');
    }

    public function templateAngkatan()
    {
        $headers = ['angkatan', 'Jumlah Awal', 'lulus', 'Resign_In_Class', 'Resign_OJT', 'tidak_lulus'];

        $sampleRows = [
            ['FAT 1 2024', 30, 25, 2, 2, 1],
            ['AAT 2 2024', 25, 22, 1, 1, 1],
        ];

        return ExcelExporter::download('Template_Import_Master_Angkatan.xlsx', $headers, $sampleRows, 'Template Angkatan');
    }

    public function exportCulled(Request $request)
    {
        $culled = MonitoringCulled::with('angkatan')->orderBy('id_culled', 'asc')->get();

        $headers = ['nama', 'angkatan', 'kategori_status', 'jenis_culled', 'alasan'];

        $dataRows = [];
        foreach ($culled as $item) {
            $dataRows[] = [
                $item->nama,
                $item->angkatan ? $item->angkatan->angkatan : '',
                $item->kategori_status,
                $item->jenis_culled,
                $item->alasan,
            ];
        }

        return ExcelExporter::download('Export_Data_Peserta_Culled_'.date('Ymd_His').'.xlsx', $headers, $dataRows, 'Peserta Culled');
    }

    public function templateCulled()
    {
        $headers = ['nama', 'angkatan', 'kategori_status', 'jenis_culled', 'alasan'];

        $sampleRows = [
            ['Ahmad Fauzi', 'FAT 1 2024', 'Culled', 'Resign_In_Class', 'Alasan kesehatan / keluarga'],
            ['Siti Rahma', 'AAT 2 2024', 'Tidak Lulus', 'Resign_OJT', 'Kualifikasi tidak memenuhi syarat'],
        ];

        return ExcelExporter::download('Template_Import_Peserta_Culled.xlsx', $headers, $sampleRows, 'Template Culled');
    }

    // -------------------------------------------------------------
    // RESET TO FACTORY CSV SEED DATA
    // -------------------------------------------------------------
    public function resetData()
    {
        if (class_exists(MonitoringSeeder::class)) {
            $seeder = new MonitoringSeeder;
            $seeder->run();
        } elseif (class_exists(RootMonitoringSeeder::class)) {
            $seeder = new RootMonitoringSeeder;
            $seeder->run();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Database Monitoring berhasil di-reset ke data awal master CSV',
        ]);
    }
}
