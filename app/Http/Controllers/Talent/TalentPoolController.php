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
        $kategori = trim((string) $request->input('kategori', 'all'));
        $status = trim((string) $request->input('status', 'all'));
        $region = trim((string) $request->input('region', 'all'));
        $talentTo = trim((string) $request->input('talent_to', 'all'));
        $search = trim((string) $request->input('search', ''));

        $query = TalentPool::query()
            ->category($kategori)
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

        // Base Scope Query for contextual counts
        $baseScopeQuery = TalentPool::query()->region($region);
        if ($talentTo !== 'all' && $talentTo !== '') {
            $baseScopeQuery->where('talent_to', $talentTo);
        }
        if ($search !== '') {
            $baseScopeQuery->where(function ($q) use ($search) {
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

        // Categories & category counts
        $categories = TalentPool::distinct()->pluck('kategori')->filter()->values();
        $categoryCounts = [];
        $totalAllCategories = (clone $baseScopeQuery)->count();
        foreach ($categories as $cat) {
            $categoryCounts[$cat] = (clone $baseScopeQuery)->category($cat)->count();
        }

        // Summary KPI Metrics
        $kpiBaseQuery = (clone $baseScopeQuery)->category($kategori);
        $kpiTalents = ($status !== 'all' && $status !== '')
            ? (clone $kpiBaseQuery)->status($status)->get()
            : (clone $kpiBaseQuery)->get();

        $totalAll = $kpiTalents->count();

        $countSudah = $kpiTalents->filter(fn ($t) => strcasecmp(trim($t->status ?? ''), 'Sudah penempatan') === 0 || str_contains(strtolower($t->status ?? ''), 'sudah'))->count();
        $countBelum = $kpiTalents->filter(fn ($t) => strcasecmp(trim($t->status ?? ''), 'Belum Penempatan') === 0 || str_contains(strtolower($t->status ?? ''), 'belum'))->count();
        $pctPenempatan = $totalAll > 0 ? round(($countSudah / $totalAll) * 100, 1) : 0;

        // Filter Dropdowns
        $regions = RegionHelper::getCanonicalRegions(TalentPool::distinct()->pluck('region'));
        $talentToList = TalentPool::distinct()->pluck('talent_to')->filter()->values();

        $perPage = (int) $request->input('per_page', 25);
        $talents = $query->orderBy('no', 'asc')->paginate($perPage)->withQueryString();

        return view('Administrasi.Pages.Talent.talent_pool', compact(
            'talents',
            'kategori',
            'status',
            'region',
            'talentTo',
            'search',
            'totalAll',
            'totalAllCategories',
            'countSudah',
            'countBelum',
            'pctPenempatan',
            'categoryCounts',
            'categories',
            'regions',
            'talentToList'
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
                $kategori = $getVal('kategori', 13) ?: ('TALENT POOL ' . date('Y'));

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
        $kategori = trim((string) $request->input('kategori', 'all'));
        $status = trim((string) $request->input('status', 'all'));
        $region = trim((string) $request->input('region', 'all'));
        $search = trim((string) $request->input('search', ''));

        $query = TalentPool::query()
            ->category($kategori)
            ->region($region)
            ->status($status);

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
            'Kategori',
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
                $r->kategori,
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
            'Kategori',
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
                'Irfan Setiawan',
                'Sudah penempatan',
                'Kebun Sejahtera 1',
                '2026-05-01',
                'ESTATE TAHUN 2026',
                'Penempatan definitif askep',
            ],
            [
                2,
                'EMP002',
                'Siti Rahmawati',
                'KTU',
                'Staff Keuangan Kebun',
                'PKS Makmur',
                'KALBAR',
                'Hendro Wijaya',
                'Irfan Setiawan',
                'Belum Penempatan',
                '',
                '',
                'Mill tahun 2026',
                'Siap penempatan Q3 2026',
            ],
        ];

        $filename = 'Template_Import_Talent_Pool.xlsx';

        return ExcelExporter::download($filename, $headers, $sampleRows, 'Template Talent Pool');
    }
}
