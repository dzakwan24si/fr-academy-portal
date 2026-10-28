<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Talent\TalentPool;
use App\Services\ExcelExporter;
use App\Services\RegionHelper;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TalentPoolController extends Controller
{
    /**
     * Display a listing of talent pool records.
     */
    public function index(Request $request)
    {
        $rawYear = $request->input('year', 'all');
        $year = ($rawYear === 'all' || $rawYear === '' || $rawYear === '0') ? 'all' : (int) $rawYear;
        $jenisKategori = trim((string) $request->input('jenis_kategori', 'all'));
        $status = trim((string) $request->input('status', 'all'));
        $region = trim((string) $request->input('region', 'all'));
        $talentTo = trim((string) $request->input('talent_to', 'all'));
        $search = trim((string) $request->input('search', ''));

        // Base Scoped Query for Dependent Filter Options & Counts (Year, Region, Status)
        $baseScopeQuery = TalentPool::query()
            ->year($year)
            ->region($region)
            ->status($status);

        if ($search !== '') {
            $baseScopeQuery->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('posisi_asal', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%")
                    ->orWhere('mentor_lapangan', 'like', "%{$search}%")
                    ->orWhere('mentor_frlc', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('lokasi_penempatan', 'like', "%{$search}%");
            });
        }

        // Available Years: strictly based on values existing in the database
        $years = TalentPool::distinct()
            ->pluck('tahun')
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->filter(fn ($y) => $y >= 2000 && $y <= 2100)
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (empty($years)) {
            $years = [(int) date('Y')];
        }

        // Available Jenis Kategori
        $defaultJenis = ['Estate', 'Mill', 'Traksi', 'Administrasi', 'Downstream'];
        $dbJenis = TalentPool::distinct()->pluck('jenis_kategori')->filter()->values();
        $jenisKategoriList = collect($defaultJenis)->merge($dbJenis)->unique()->values()->all();

        // Counts for Jenis Kategori within active Year, Region & Status
        $totalAllJenis = (clone $baseScopeQuery)->count();
        $jenisCounts = [];
        foreach ($jenisKategoriList as $jk) {
            $jenisCounts[$jk] = (clone $baseScopeQuery)->jenisKategori($jk)->count();
        }

        // 1. Available Target Posisi (talent_to): Dynamically scoped by active filters (Status, Year, Jenis Kategori, Region)
        $talentToScopeQuery = TalentPool::query()
            ->year($year)
            ->region($region)
            ->jenisKategori($jenisKategori);

        if ($status !== 'all' && $status !== '') {
            $talentToScopeQuery->status($status);
        }

        $talentToList = $talentToScopeQuery
            ->whereNotNull('talent_to')
            ->where('talent_to', '!=', '')
            ->distinct()
            ->orderBy('talent_to')
            ->pluck('talent_to')
            ->values();

        // Reset talent_to to 'all' if selected option is not available under the active filters
        if ($talentTo !== 'all' && $talentTo !== '' && ! $talentToList->contains($talentTo)) {
            $talentTo = 'all';
        }

        // 2. Available Status: Dynamically scoped by active filters (Year, Jenis Kategori, Region, talent_to)
        $statusScopeQuery = TalentPool::query()
            ->year($year)
            ->region($region)
            ->jenisKategori($jenisKategori);

        if ($talentTo !== 'all' && $talentTo !== '') {
            $statusScopeQuery->where('talent_to', $talentTo);
        }

        $statusList = $statusScopeQuery
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->distinct()
            ->pluck('status')
            ->values();

        if ($statusList->isEmpty()) {
            $statusList = collect(['Sudah penempatan', 'Belum Penempatan']);
        }

        // Reset status to 'all' if selected option is not available under the active filters
        if ($status !== 'all' && $status !== '' && ! $statusList->contains($status)) {
            $status = 'all';
        }

        // 3. Available Regions: Scoped by Year, Jenis Kategori, Status, talent_to
        $regionScopeQuery = TalentPool::query()
            ->year($year)
            ->jenisKategori($jenisKategori)
            ->status($status);

        if ($talentTo !== 'all' && $talentTo !== '') {
            $regionScopeQuery->where('talent_to', $talentTo);
        }

        $regions = RegionHelper::getCanonicalRegions($regionScopeQuery->distinct()->pluck('region'));
        if ($region !== 'all' && $region !== '' && ! in_array($region, $regions, true)) {
            $region = 'all';
        }

        // Query Utama for Table Data
        $query = TalentPool::query()
            ->year($year)
            ->jenisKategori($jenisKategori)
            ->region($region)
            ->status($status);

        if ($talentTo !== 'all' && $talentTo !== '') {
            $query->where('talent_to', $talentTo);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('posisi_asal', 'like', "%{$search}%")
                    ->orWhere('talent_to', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%")
                    ->orWhere('mentor_lapangan', 'like', "%{$search}%")
                    ->orWhere('mentor_frlc', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('lokasi_penempatan', 'like', "%{$search}%");
            });
        }

        // Summary KPI Metrics
        // Note: As requested, KPI Total Talent Pool only counts 'Belum Penempatan'
        $kpiBaseQuery = TalentPool::query()
            ->year($year)
            ->region($region)
            ->jenisKategori($jenisKategori);

        if ($talentTo !== 'all' && $talentTo !== '') {
            $kpiBaseQuery->where('talent_to', $talentTo);
        }

        if ($search !== '') {
            $kpiBaseQuery->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('posisi_asal', 'like', "%{$search}%")
                    ->orWhere('talent_to', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%")
                    ->orWhere('mentor_lapangan', 'like', "%{$search}%")
                    ->orWhere('mentor_frlc', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('lokasi_penempatan', 'like', "%{$search}%");
            });
        }

        if ($status !== 'all' && $status !== '') {
            $kpiTalents = (clone $kpiBaseQuery)->status($status)->get();
        } else {
            $kpiTalents = (clone $kpiBaseQuery)->get();
        }

        $countSudah = $kpiTalents->filter(fn ($t) => strcasecmp(trim($t->status ?? ''), 'Sudah penempatan') === 0 || str_contains(strtolower($t->status ?? ''), 'sudah'))->count();
        $countBelum = $kpiTalents->filter(fn ($t) => strcasecmp(trim($t->status ?? ''), 'Belum Penempatan') === 0 || str_contains(strtolower($t->status ?? ''), 'belum'))->count();

        // Chart Metrics: Total count per Target Posisi (talent_to), obeying ALL active filters
        $chartQuery = TalentPool::query()
            ->year($year)
            ->region($region)
            ->jenisKategori($jenisKategori);

        if ($status !== 'all' && $status !== '') {
            $chartQuery->status($status);
        }

        if ($talentTo !== 'all' && $talentTo !== '') {
            $chartQuery->where('talent_to', $talentTo);
        }

        if ($search !== '') {
            $chartQuery->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('posisi_asal', 'like', "%{$search}%")
                    ->orWhere('talent_to', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%")
                    ->orWhere('mentor_lapangan', 'like', "%{$search}%")
                    ->orWhere('mentor_frlc', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhere('lokasi_penempatan', 'like', "%{$search}%");
            });
        }

        $chartRows = $chartQuery
            ->select('talent_to', DB::raw('count(*) as total'))
            ->whereNotNull('talent_to')
            ->where('talent_to', '!=', '')
            ->groupBy('talent_to')
            ->orderByDesc('total')
            ->get();

        $chartLabels = $chartRows->pluck('talent_to')->values()->all();
        $chartTotals = $chartRows->pluck('total')->map(fn ($v) => (int) $v)->values()->all();

        $perPage = (int) $request->input('per_page', 25);
        $talents = $query->orderBy('no', 'asc')->paginate($perPage)->withQueryString();

        return view('Administrasi.Pages.Talent.talent_pool', compact(
            'talents',
            'year',
            'rawYear',
            'years',
            'jenisKategori',
            'jenisKategoriList',
            'jenisCounts',
            'totalAllJenis',
            'status',
            'region',
            'talentTo',
            'search',
            'countSudah',
            'countBelum',
            'regions',
            'talentToList',
            'statusList',
            'chartLabels',
            'chartTotals'
        ));
    }

    /**
     * Shared validation rules for store and update.
     */
    protected function validationRules(): array
    {
        return [
            'employee_id' => 'nullable|string|max:100',
            'employee_name' => 'required|string|max:255',
            'posisi_asal' => 'nullable|string|max:255',
            'talent_to' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'mentor_lapangan' => 'nullable|string|max:255',
            'mentor_frlc' => 'nullable|string|max:255',
            'status' => 'required|string|in:Sudah penempatan,Belum Penempatan',
            'keterangan' => 'nullable|string',
            'lokasi_penempatan' => 'nullable|string|max:255',
            'tanggal_penempatan' => 'nullable|date',
            'kategori' => 'nullable|string|max:255',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'jenis_kategori' => 'nullable|string|max:100',
        ];
    }

    /**
     * Store a newly created talent pool record.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());

        if (empty($data['no'])) {
            $data['no'] = ((int) TalentPool::max('no')) + 1;
        }

        if (empty($data['kategori'])) {
            $data['kategori'] = 'ESTATE TAHUN ' . date('Y');
        }

        TalentPool::create($data);

        return back()->with('success', 'Data kandidat Talent Pool berhasil ditambahkan.');
    }

    /**
     * Update an existing talent pool record.
     */
    public function update(Request $request, $id)
    {
        $talent = TalentPool::findOrFail($id);
        $data = $request->validate($this->validationRules());

        $talent->update($data);

        return back()->with('success', 'Data kandidat Talent Pool berhasil diperbarui.');
    }

    /**
     * Delete a single talent pool record.
     */
    public function destroy($id)
    {
        $talent = TalentPool::findOrFail($id);
        $talent->delete();

        return back()->with('success', 'Data kandidat Talent Pool berhasil dihapus.');
    }

    /**
     * Delete all talent pool records.
     */
    public function destroyAll()
    {
        $count = TalentPool::count();
        TalentPool::truncate();

        ActivityLog::log(
            'Hapus',
            'Talent Pool',
            "Menghapus semua data talent pool ({$count} data)",
            ['count' => $count]
        );

        return back()->with('success', "Seluruh data Talent Pool ({$count} data) berhasil dihapus.");
    }

    /**
     * Import talent pool data from CSV/Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480',
        ]);

        try {
            $file = $request->file('file');
            $rows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Gagal membaca file: ' . $e->getMessage()]);
        }

        if (empty($rows)) {
            return back()->withErrors(['file' => 'File tidak memiliki baris data valid.']);
        }

        $rawHeaders = array_shift($rows);
        $headerMap = [];

        foreach ($rawHeaders as $idx => $h) {
            $clean = SpreadsheetReader::cleanString($h);
            if (in_array($clean, ['no', 'id', 'nomor'])) {
                $headerMap['no'] = $idx;
            } elseif (in_array($clean, ['employee id', 'nik', 'id karyawan', 'nip', 'badge'])) {
                $headerMap['employee_id'] = $idx;
            } elseif (in_array($clean, ['employee name', 'nama', 'nama karyawan', 'nama peserta', 'name'])) {
                $headerMap['employee_name'] = $idx;
            } elseif (in_array($clean, ['posisi asal', 'posisi', 'jabatan', 'jabatan asal'])) {
                $headerMap['posisi_asal'] = $idx;
            } elseif (in_array($clean, ['talent to', 'posisi tujuan', 'target posisi', 'to'])) {
                $headerMap['talent_to'] = $idx;
            } elseif (in_array($clean, ['unit', 'kebun/pabrik', 'lokasi', 'work location', 'estate'])) {
                $headerMap['unit'] = $idx;
            } elseif (in_array($clean, ['region', 'wilayah'])) {
                $headerMap['region'] = $idx;
            } elseif (in_array($clean, ['mentor lapangan', 'mentor lap', 'mentor lap.'])) {
                $headerMap['mentor_lapangan'] = $idx;
            } elseif (in_array($clean, ['mentor frlc', 'mentor fr', 'mentor'])) {
                $headerMap['mentor_frlc'] = $idx;
            } elseif (in_array($clean, ['status', 'status penempatan'])) {
                $headerMap['status'] = $idx;
            } elseif (in_array($clean, ['keterangan', 'ket', 'notes', 'note'])) {
                $headerMap['keterangan'] = $idx;
            } elseif (in_array($clean, ['lokasi penempatan', 'lokasi baru', 'unit penempatan'])) {
                $headerMap['lokasi_penempatan'] = $idx;
            } elseif (in_array($clean, ['tanggal penempatan', 'tgl penempatan', 'tgl'])) {
                $headerMap['tanggal_penempatan'] = $idx;
            } elseif (in_array($clean, ['kategori', 'category', 'batch'])) {
                $headerMap['kategori'] = $idx;
            } elseif (in_array($clean, ['tahun', 'year', 'thn'])) {
                $headerMap['tahun'] = $idx;
            } elseif (in_array($clean, ['jenis kategori', 'jenis_kategori', 'jenis', 'tipe kategori', 'category type'])) {
                $headerMap['jenis_kategori'] = $idx;
            }
        }

        $imported = 0;
        $insertBatch = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (empty(array_filter($row, fn ($v) => trim((string) $v) !== ''))) {
                    continue;
                }

                $getVal = function ($key, $fallbackIndex = null) use ($row, $headerMap) {
                    if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                        return trim((string) $row[$headerMap[$key]]);
                    }
                    if ($fallbackIndex !== null && isset($row[$fallbackIndex])) {
                        return trim((string) $row[$fallbackIndex]);
                    }

                    return '';
                };

                $empName = $getVal('employee_name', 2);
                if (empty($empName)) {
                    continue;
                }

                $noVal = $getVal('no', 0);
                $no = (! empty($noVal) && is_numeric($noVal)) ? (int) $noVal : ($imported + 1);
                $empId = $getVal('employee_id', 1) ?: null;
                $posisiAsal = $getVal('posisi_asal', 3) ?: null;
                $talentTo = $getVal('talent_to', 4) ?: null;
                $unit = $getVal('unit', 5) ?: null;
                $region = $getVal('region', 6) ?: null;
                $mentorLap = $getVal('mentor_lapangan', 7) ?: null;
                $mentorFrlc = $getVal('mentor_frlc', 8) ?: null;

                $rawStatus = $getVal('status', 9);
                $status = 'Belum Penempatan';
                if (! empty($rawStatus)) {
                    if (str_contains(strtolower($rawStatus), 'sudah')) {
                        $status = 'Sudah penempatan';
                    } elseif (str_contains(strtolower($rawStatus), 'belum')) {
                        $status = 'Belum Penempatan';
                    }
                }

                $keterangan = $getVal('keterangan', 10) ?: null;
                $lokasiPenempatan = $getVal('lokasi_penempatan', 11) ?: null;
                $tanggalPenempatan = $getVal('tanggal_penempatan', 12) ?: null;
                $kategori = $getVal('kategori', 13) ?: ('ESTATE TAHUN ' . date('Y'));

                $rawTahun = $getVal('tahun', 14);
                $tahun = (! empty($rawTahun) && is_numeric($rawTahun)) ? (int) $rawTahun : null;
                $jenisKategori = $getVal('jenis_kategori', 15) ?: null;

                if (! $tahun) {
                    if (preg_match('/\b(20\d{2})\b/', $kategori, $m)) {
                        $tahun = (int) $m[1];
                    } elseif ($tanggalPenempatan) {
                        $tahun = (int) date('Y', strtotime($tanggalPenempatan));
                    } else {
                        $tahun = (int) date('Y');
                    }
                }
                if (! $jenisKategori) {
                    $lowerCat = strtolower($kategori);
                    if (str_contains($lowerCat, 'estate') || str_contains($lowerCat, 'kebun')) {
                        $jenisKategori = 'Estate';
                    } elseif (str_contains($lowerCat, 'mill') || str_contains($lowerCat, 'mil') || str_contains($lowerCat, 'pks')) {
                        $jenisKategori = 'Mill';
                    } elseif (str_contains($lowerCat, 'traksi')) {
                        $jenisKategori = 'Traksi';
                    } elseif (str_contains($lowerCat, 'administrasi') || str_contains($lowerCat, 'admin')) {
                        $jenisKategori = 'Administrasi';
                    } elseif (str_contains($lowerCat, 'downstream')) {
                        $jenisKategori = 'Downstream';
                    } else {
                        $jenisKategori = 'Estate';
                    }
                }

                $insertBatch[] = [
                    'no' => $no,
                    'employee_id' => $empId,
                    'employee_name' => $empName,
                    'posisi_asal' => $posisiAsal,
                    'talent_to' => $talentTo,
                    'unit' => $unit,
                    'region' => $region,
                    'mentor_lapangan' => $mentorLap,
                    'mentor_frlc' => $mentorFrlc,
                    'status' => $status,
                    'keterangan' => $keterangan,
                    'lokasi_penempatan' => $lokasiPenempatan,
                    'tanggal_penempatan' => $tanggalPenempatan ? date('Y-m-d', strtotime($tanggalPenempatan)) : null,
                    'kategori' => $kategori,
                    'tahun' => $tahun,
                    'jenis_kategori' => $jenisKategori,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $imported++;
            }

            if (! empty($insertBatch)) {
                foreach (array_chunk($insertBatch, 200) as $chunk) {
                    TalentPool::insert($chunk);
                }
            }

            DB::commit();

            ActivityLog::log(
                'Import',
                'Talent Pool',
                "Mengimpor {$imported} data kandidat Talent Pool dari file: " . $file->getClientOriginalName(),
                ['total' => $imported]
            );

            return back()->with('success', "Berhasil mengimpor {$imported} data kandidat Talent Pool.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['file' => 'Gagal memproses data import: ' . $e->getMessage()]);
        }
    }

    /**
     * Export talent pool data to Excel.
     */
    public function export(Request $request)
    {
        $rawYear = $request->input('year', 'all');
        $year = ($rawYear === 'all' || $rawYear === '' || $rawYear === '0') ? 'all' : (int) $rawYear;
        $jenisKategori = trim((string) $request->input('jenis_kategori', 'all'));
        $status = trim((string) $request->input('status', 'all'));
        $region = trim((string) $request->input('region', 'all'));
        $talentTo = trim((string) $request->input('talent_to', 'all'));
        $search = trim((string) $request->input('search', ''));

        $query = TalentPool::query()
            ->year($year)
            ->jenisKategori($jenisKategori)
            ->region($region)
            ->status($status);

        if ($talentTo !== 'all' && $talentTo !== '') {
            $query->where('talent_to', $talentTo);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('posisi_asal', 'like', "%{$search}%")
                    ->orWhere('talent_to', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('no', 'asc')->get();

        $headers = [
            'No',
            'NIK',
            'Nama Karyawan',
            'Posisi Asal',
            'Target Posisi (Talent To)',
            'Unit',
            'Region',
            'Mentor Lapangan',
            'Mentor FRLC',
            'Status Penempatan',
            'Lokasi Penempatan',
            'Tanggal Penempatan',
            'Tahun',
            'Jenis Kategori',
            'Keterangan',
        ];

        $dataRows = [];
        foreach ($records as $idx => $r) {
            $dataRows[] = [
                $r->no ?? ($idx + 1),
                $r->employee_id,
                $r->employee_name,
                $r->posisi_asal,
                $r->talent_to,
                $r->unit,
                $r->region,
                $r->mentor_lapangan,
                $r->mentor_frlc,
                $r->status,
                $r->lokasi_penempatan,
                $r->tanggal_penempatan ? $r->tanggal_penempatan->format('d/m/Y') : '',
                $r->tahun,
                $r->jenis_kategori,
                $r->keterangan,
            ];
        }

        $filename = 'Talent_Pool_Report_' . date('Ymd_His') . '.xlsx';

        return ExcelExporter::download($filename, $headers, $dataRows, 'Talent Pool');
    }

    /**
     * Download Excel template for Talent Pool import.
     */
    public function template()
    {
        $headers = [
            'No',
            'NIK',
            'Nama Karyawan',
            'Posisi Asal',
            'Target Posisi',
            'Unit',
            'Region',
            'Mentor Lapangan',
            'Mentor FRLC',
            'Status',
            'Lokasi Penempatan',
            'Tanggal Penempatan',
            'Tahun',
            'Jenis Kategori',
            'Keterangan',
        ];

        $sampleRows = [
            [
                1,
                'EMP001',
                'Ahmad Fauzi',
                'Asisten Agronomi',
                'Askep Agronomi',
                'Kebun Sukamaju',
                'RIAU',
                'Budi Santoso',
                'FRLC Team',
                'Belum Penempatan',
                '-',
                '',
                2026,
                'Estate',
                'Kandidat berpotensi tinggi',
            ],
            [
                2,
                'EMP002',
                'Siti Nurhaliza',
                'KTU Mill',
                'Mill Manager',
                'PKS Lubuk Dalam',
                'RIAU',
                'Hendro W.',
                'FRLC Team',
                'Sudah penempatan',
                'PKS Riau 2',
                '2026-02-15',
                2026,
                'Mill',
                'Telah promosi',
            ],
        ];

        return ExcelExporter::download('Template_Talent_Pool.xlsx', $headers, $sampleRows, 'Template Talent Pool');
    }
}
