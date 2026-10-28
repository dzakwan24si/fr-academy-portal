<?php

namespace App\Http\Controllers\Administrasi;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Administrasi\ProfilTrainee;
use App\Services\ExcelExporter;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProfilTraineeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $filterGender = $request->get('gender', '');
        $filterPendidikan = $request->get('pendidikan', '');
        $filterUniversitas = $request->get('universitas', '');
        $filterTahunLulus = $request->get('tahun_lulusan', $request->get('tahun_lulus', ''));
        $filterJabatan = $request->get('nama_jabatan', $request->get('jabatan', ''));

        // Base Query
        $query = ProfilTrainee::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('trainee_id', 'like', "%{$search}%")
                    ->orWhere('nama_trainee', 'like', "%{$search}%")
                    ->orWhere('nama_jabatan', 'like', "%{$search}%")
                    ->orWhere('asal_universitas', 'like', "%{$search}%")
                    ->orWhere('jurusan', 'like', "%{$search}%")
                    ->orWhere('daerah_kabupaten_asal', 'like', "%{$search}%")
                    ->orWhere('kota_provinsi_asal', 'like', "%{$search}%");
            });
        }

        if (! empty($filterGender)) {
            $query->where('jenis_kelamin', $filterGender);
        }

        if (! empty($filterPendidikan)) {
            $query->where('pendidikan_terakhir', $filterPendidikan);
        }

        if (! empty($filterUniversitas)) {
            $query->where('asal_universitas', $filterUniversitas);
        }

        if (! empty($filterTahunLulus)) {
            $query->where('tahun_lulus', (int) $filterTahunLulus);
        }

        if (! empty($filterJabatan)) {
            $query->where('nama_jabatan', $filterJabatan);
        }

        // Summary Statistics (scoped by active filters)
        $totalTrainee = (clone $query)->count();
        $avgIpk = (clone $query)->whereNotNull('ipk')->where('ipk', '>', 0)->avg('ipk');
        $avgUsia = (clone $query)->whereNotNull('usia')->where('usia', '>', 0)->avg('usia');

        $stats = [
            'total_trainee' => $totalTrainee,
            'avg_ipk' => $avgIpk ? number_format($avgIpk, 2) : '0.00',
            'avg_usia' => $avgUsia ? number_format($avgUsia, 1) : '0',
        ];

        // Demographic Charts Data (All data distribution, scoped by active filters)
        $chartUniversitas = $this->getDistribution($query, 'asal_universitas');
        $chartPekerjaanOrtu = $this->getDistribution($query, 'pekerjaan_orangtua');
        $chartSuku = $this->getDistribution($query, 'suku');
        $chartProvinsi = $this->getDistribution($query, 'kota_provinsi_asal');

        // Filter Dropdown Options
        $universities = ProfilTrainee::whereNotNull('asal_universitas')
            ->where('asal_universitas', '!=', '')
            ->distinct()
            ->orderBy('asal_universitas')
            ->pluck('asal_universitas');

        $pendidikanList = ProfilTrainee::whereNotNull('pendidikan_terakhir')
            ->where('pendidikan_terakhir', '!=', '')
            ->distinct()
            ->orderBy('pendidikan_terakhir')
            ->pluck('pendidikan_terakhir');

        $years = ProfilTrainee::whereNotNull('tahun_lulus')
            ->distinct()
            ->orderByDesc('tahun_lulus')
            ->pluck('tahun_lulus');

        $jabatanList = ProfilTrainee::whereNotNull('nama_jabatan')
            ->where('nama_jabatan', '!=', '')
            ->distinct()
            ->orderBy('nama_jabatan')
            ->pluck('nama_jabatan');

        // Paginated records
        $trainees = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('Administrasi.Pages.Administrasi.profil_trainee', compact(
            'trainees',
            'stats',
            'search',
            'filterGender',
            'filterPendidikan',
            'filterUniversitas',
            'filterTahunLulus',
            'filterJabatan',
            'universities',
            'pendidikanList',
            'years',
            'jabatanList',
            'chartUniversitas',
            'chartPekerjaanOrtu',
            'chartSuku',
            'chartProvinsi'
        ));
    }

    /**
     * Get distribution of all categories for a given column (ordered by count descending).
     */
    protected function getDistribution($baseQuery, string $field): array
    {
        $rows = (clone $baseQuery)
            ->select($field, DB::raw('count(*) as total'))
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->groupBy($field)
            ->orderByDesc('total')
            ->get();

        if ($rows->isEmpty()) {
            return ['labels' => [], 'data' => []];
        }

        $labels = $rows->pluck($field)->map(fn ($val) => trim((string) $val))->values()->all();
        $data = $rows->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Backward-compatible alias for distribution.
     */
    protected function getTopDistribution($baseQuery, string $field, int $limit = 6): array
    {
        return $this->getDistribution($baseQuery, $field);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateInput($request);

        // Auto-generate Trainee ID if empty
        if (empty($validated['trainee_id'])) {
            $nextId = (ProfilTrainee::max('id') ?? 0) + 1;
            $validated['trainee_id'] = 'TRN-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        ProfilTrainee::create($validated);

        return redirect()->route('administrasi.profil-trainee.index')
            ->with('success', "Data profil trainee {$validated['nama_trainee']} berhasil ditambahkan.");
    }

    /**
     * Display the specified resource for JSON/AJAX modal.
     */
    public function show($id)
    {
        $trainee = ProfilTrainee::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $trainee,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $trainee = ProfilTrainee::findOrFail($id);
        $validated = $this->validateInput($request);

        $trainee->update($validated);

        return redirect()->route('administrasi.profil-trainee.index')
            ->with('success', "Data profil trainee {$trainee->nama_trainee} berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $trainee = ProfilTrainee::findOrFail($id);
        $name = $trainee->nama_trainee;
        $trainee->delete();

        return redirect()->route('administrasi.profil-trainee.index')
            ->with('success', "Data profil trainee {$name} berhasil dihapus.");
    }

    /**
     * Remove multiple or all resources from storage.
     */
    public function destroyAll(Request $request)
    {
        $ids = $request->input('ids');
        $deleteAll = $request->boolean('all');

        if ($deleteAll) {
            $count = ProfilTrainee::count();
            ProfilTrainee::truncate();

            ActivityLog::log(
                action: 'Hapus Semua',
                module: 'Profil Trainee',
                description: "Menghapus seluruh {$count} data Profil Trainee",
                properties: ['count' => $count]
            );

            return redirect()->route('administrasi.profil-trainee.index')
                ->with('success', "Seluruh data Profil Trainee ({$count} data) berhasil dihapus.");
        }

        if (is_array($ids) && count($ids) > 0) {
            $count = ProfilTrainee::whereIn('id', $ids)->delete();

            ActivityLog::log(
                action: 'Hapus Massal',
                module: 'Profil Trainee',
                description: "Menghapus {$count} data Profil Trainee terpilih",
                properties: ['ids' => $ids, 'count' => $count]
            );

            return redirect()->route('administrasi.profil-trainee.index')
                ->with('success', "{$count} data Profil Trainee terpilih berhasil dihapus.");
        }

        return redirect()->route('administrasi.profil-trainee.index')
            ->with('error', 'Tidak ada data yang dipilih untuk dihapus.');
    }

    /**
     * Export data to Excel/CSV.
     */
    public function export(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $filterGender = $request->get('gender', '');
        $filterPendidikan = $request->get('pendidikan', '');
        $filterUniversitas = $request->get('universitas', '');
        $filterTahunLulus = $request->get('tahun_lulusan', $request->get('tahun_lulus', ''));
        $filterJabatan = $request->get('nama_jabatan', $request->get('jabatan', ''));

        $query = ProfilTrainee::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('trainee_id', 'like', "%{$search}%")
                    ->orWhere('nama_trainee', 'like', "%{$search}%")
                    ->orWhere('nama_jabatan', 'like', "%{$search}%")
                    ->orWhere('asal_universitas', 'like', "%{$search}%")
                    ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }
        if (! empty($filterGender)) $query->where('jenis_kelamin', $filterGender);
        if (! empty($filterPendidikan)) $query->where('pendidikan_terakhir', $filterPendidikan);
        if (! empty($filterUniversitas)) $query->where('asal_universitas', $filterUniversitas);
        if (! empty($filterTahunLulus)) $query->where('tahun_lulus', (int) $filterTahunLulus);
        if (! empty($filterJabatan)) $query->where('nama_jabatan', $filterJabatan);

        $trainees = $query->orderBy('id')->get();

        $headers = [
            'Trainee ID',
            'Nama Trainee',
            'Nama Jabatan',
            'Asal Universitas',
            'Jurusan',
            'Jenis Kelamin',
            'Usia',
            'Suku',
            'Agama',
            'Pendidikan Terakhir',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Daerah / Kabupaten Asal',
            'Kota / Provinsi Asal',
            'Tahun Lulus',
            'Masa Studi',
            'IPK',
            'Pengalaman Organisasi',
            'Pengalaman Praktek Kerja / Magang',
            'Prestasi Diri',
            'Hobi',
            'Riwayat Kesehatan',
            'Catatan Riwayat Kesehatan',
            'Pekerjaan Orangtua',
        ];

        $dataRows = [];
        foreach ($trainees as $t) {
            $dataRows[] = [
                $t->trainee_id ?? '',
                $t->nama_trainee ?? '',
                $t->nama_jabatan ?? '',
                $t->asal_universitas ?? '',
                $t->jurusan ?? '',
                $t->jenis_kelamin ?? '',
                $t->usia ?? '',
                $t->suku ?? '',
                $t->agama ?? '',
                $t->pendidikan_terakhir ?? '',
                $t->tinggi_badan ?? '',
                $t->berat_badan ?? '',
                $t->daerah_kabupaten_asal ?? '',
                $t->kota_provinsi_asal ?? '',
                $t->tahun_lulus ?? '',
                $t->masa_studi ?? '',
                $t->ipk !== null ? number_format($t->ipk, 2) : '',
                $t->pengalaman_organisasi ?? '',
                $t->pengalaman_praktek_kerja_magang ?? '',
                $t->prestasi_diri ?? '',
                $t->hobi ?? '',
                $t->riwayat_kesehatan ?? '',
                $t->catatan_riwayat_kesehatan ?? '',
                $t->pekerjaan_orangtua ?? '',
            ];
        }

        $filename = 'Profil_Trainee_' . date('Ymd_His') . '.xlsx';
        return ExcelExporter::download($filename, $headers, $dataRows, 'Profil Trainee');
    }

    /**
     * Download Excel template for import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Trainee ID',
            'Nama Trainee',
            'Nama Jabatan',
            'Asal Universitas',
            'Jurusan',
            'Jenis Kelamin',
            'Usia',
            'Suku',
            'Agama',
            'Pendidikan Terakhir',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Daerah / Kabupaten Asal',
            'Kota / Provinsi Asal',
            'Tahun Lulus',
            'Masa Studi',
            'IPK',
            'Pengalaman Organisasi',
            'Pengalaman Praktek Kerja / Magang',
            'Prestasi Diri',
            'Hobi',
            'Riwayat Kesehatan',
            'Catatan Riwayat Kesehatan',
            'Pekerjaan Orangtua',
        ];

        $sampleRows = [
            [
                'TRN-0001',
                'Ahmad Fauzi',
                'Asisten Agronomi',
                'Universitas Gadjah Mada',
                'Agronomi & Hortikultura',
                'Laki-laki',
                23,
                'Jawa',
                'Islam',
                'S1',
                172,
                65,
                'Sleman',
                'D.I. Yogyakarta',
                2025,
                '4 Tahun',
                3.72,
                'Ketua Himpunan Mahasiswa Agronomi (2023-2024)',
                'Magang Agronomi di PT Perkebunan Nusantara (6 Bulan)',
                'Juara 1 Lomba Karya Tulis Ilmiah Pertanian Nasional',
                'Futsal, Membaca',
                'Tidak Ada',
                'Sehat, tidak memiliki alergi atau penyakit bawaan',
                'Wiraswasta',
            ],
            [
                'TRN-0002',
                'Siti Nurhaliza',
                'Asisten Proteksi Tanaman',
                'Institut Pertanian Bogor',
                'Proteksi Tanaman',
                'Sunda',
                22,
                'Sunda',
                'Islam',
                'S1',
                160,
                50,
                'Bogor',
                'Jawa Barat',
                2025,
                '3.5 Tahun',
                3.85,
                'Staf Departemen Keilmuan BEM Faperta',
                'Praktik Kerja Lapangan di Pusat Penelitian Bioteknologi',
                'Penerima Beasiswa Prestasi Akademik',
                'Menulis, Badminton',
                'Tidak Ada',
                'Sehat jasmani dan rohani',
                'PNS',
            ]
        ];

        return ExcelExporter::download('Template_Import_Profil_Trainee.xlsx', $headers, $sampleRows, 'Template Profil Trainee');
    }

    /**
     * Import data from Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:20480',
        ]);

        $file = $request->file('file');
        $rows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());

        if (count($rows) < 2) {
            return back()->with('error', 'File tidak memiliki baris data untuk diimport.');
        }

        $rawHeaders = array_shift($rows);
        $headerMap = [];

        // Flexible header mapping
        foreach ($rawHeaders as $index => $rawH) {
            $clean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $rawH)));
            $headerMap[$index] = match ($clean) {
                'traineeid', 'idtrainee', 'trainee_id', 'id' => 'trainee_id',
                'namatrainee', 'nama', 'namalengkap' => 'nama_trainee',
                'namajabatan', 'jabatan', 'posisi' => 'nama_jabatan',
                'asaluniversitas', 'universitas', 'kampus' => 'asal_universitas',
                'jurusan', 'programstudi', 'prodi' => 'jurusan',
                'jeniskelamin', 'gender', 'jk' => 'jenis_kelamin',
                'usia', 'umur' => 'usia',
                'suku' => 'suku',
                'agama' => 'agama',
                'pendidikanterakhir', 'pendidikan', 'strata' => 'pendidikan_terakhir',
                'tinggibadan', 'tinggibadancm', 'tb', 'tinggi' => 'tinggi_badan',
                'beratbadan', 'beratbadankg', 'bb', 'berat' => 'berat_badan',
                'daerahkabupatenasal', 'daerahkabupaten', 'kabupatenasal', 'kabupaten', 'daerah' => 'daerah_kabupaten_asal',
                'kotaprovinsiasal', 'kotaprovinsi', 'provinsiasal', 'provinsi', 'kota' => 'kota_provinsi_asal',
                'tahunlulus', 'lulus' => 'tahun_lulus',
                'masastudi', 'studi' => 'masa_studi',
                'ipk', 'gpa' => 'ipk',
                'pengalamanorganisasi', 'organisasi' => 'pengalaman_organisasi',
                'pengalamanpraktekkerjamagang', 'pengalamanmagang', 'magang', 'pkl' => 'pengalaman_praktek_kerja_magang',
                'prestasidiri', 'prestasi' => 'prestasi_diri',
                'hobi', 'hobby' => 'hobi',
                'riwayatkesehatan', 'kesehatan' => 'riwayat_kesehatan',
                'catatanriwayatkesehatan', 'catatankesehatan' => 'catatan_riwayat_kesehatan',
                'pekerjaanorangtua', 'pekerjaanortu', 'ortu' => 'pekerjaan_orangtua',
                default => null,
            };
        }

        $imported = 0;
        $nextMaxId = (ProfilTrainee::max('id') ?? 0);

        DB::transaction(function () use ($rows, $headerMap, &$imported, &$nextMaxId) {
            foreach ($rows as $row) {
                if (empty(array_filter($row, fn ($c) => trim((string) $c) !== ''))) {
                    continue;
                }

                $record = [];
                foreach ($row as $colIdx => $val) {
                    $field = $headerMap[$colIdx] ?? null;
                    if ($field) {
                        $record[$field] = is_string($val) ? trim($val) : $val;
                    }
                }

                if (empty($record['nama_trainee'])) {
                    continue;
                }

                // If trainee_id is empty, auto generate
                if (empty($record['trainee_id'])) {
                    $nextMaxId++;
                    $record['trainee_id'] = 'TRN-' . str_pad($nextMaxId, 4, '0', STR_PAD_LEFT);
                }

                // Numeric sanitation
                if (isset($record['usia']) && $record['usia'] !== '') {
                    $record['usia'] = (int) preg_replace('/[^0-9]/', '', (string) $record['usia']);
                }
                if (isset($record['tahun_lulus']) && $record['tahun_lulus'] !== '') {
                    $record['tahun_lulus'] = (int) preg_replace('/[^0-9]/', '', (string) $record['tahun_lulus']);
                }
                if (isset($record['tinggi_badan']) && $record['tinggi_badan'] !== '') {
                    $record['tinggi_badan'] = (float) str_replace(',', '.', (string) $record['tinggi_badan']);
                }
                if (isset($record['berat_badan']) && $record['berat_badan'] !== '') {
                    $record['berat_badan'] = (float) str_replace(',', '.', (string) $record['berat_badan']);
                }
                if (isset($record['ipk']) && $record['ipk'] !== '') {
                    $record['ipk'] = (float) str_replace(',', '.', (string) $record['ipk']);
                }

                ProfilTrainee::updateOrCreate(
                    ['trainee_id' => $record['trainee_id']],
                    $record
                );

                $imported++;
            }
        });

        ActivityLog::log(
            action: 'Import',
            module: 'Profil Trainee',
            description: "Mengimport {$imported} data Profil Trainee dari file " . $file->getClientOriginalName(),
            properties: ['filename' => $file->getClientOriginalName(), 'count' => $imported]
        );

        return back()->with('success', "{$imported} data Profil Trainee berhasil diimport dan disimpan.");
    }

    /**
     * Validate common input
     */
    private function validateInput(Request $request): array
    {
        return $request->validate([
            'trainee_id' => 'nullable|string|max:100',
            'nama_trainee' => 'required|string|max:255',
            'nama_jabatan' => 'nullable|string|max:255',
            'asal_universitas' => 'nullable|string|max:255',
            'jurusan' => 'nullable|string|max:255',
            'jenis_kelamin' => 'nullable|string|max:50',
            'usia' => 'nullable|integer|min:15|max:100',
            'suku' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'tinggi_badan' => 'nullable|numeric|min:50|max:250',
            'berat_badan' => 'nullable|numeric|min:20|max:250',
            'daerah_kabupaten_asal' => 'nullable|string|max:255',
            'kota_provinsi_asal' => 'nullable|string|max:255',
            'tahun_lulus' => 'nullable|integer|min:1990|max:2050',
            'masa_studi' => 'nullable|string|max:100',
            'ipk' => 'nullable|numeric|min:0|max:4',
            'pengalaman_organisasi' => 'nullable|string',
            'pengalaman_praktek_kerja_magang' => 'nullable|string',
            'prestasi_diri' => 'nullable|string',
            'hobi' => 'nullable|string|max:255',
            'riwayat_kesehatan' => 'nullable|string|max:255',
            'catatan_riwayat_kesehatan' => 'nullable|string',
            'pekerjaan_orangtua' => 'nullable|string|max:255',
        ]);
    }
}
