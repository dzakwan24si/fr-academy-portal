<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AppSetting;
use App\Models\Monitoring\MonitoringTalent;
use App\Services\ExcelExporter;
use App\Services\RegionHelper;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringTalentController extends Controller
{
    /**
     * Display a listing of talent monitoring records.
     */
    public function index(Request $request)
    {
        $kategori = trim((string) $request->input('kategori', 'all'));
        $status = trim((string) $request->input('status', 'all'));
        $region = trim((string) $request->input('region', 'all'));
        $talentTo = trim((string) $request->input('talent_to', 'all'));
        $search = trim((string) $request->input('search', ''));

        $query = MonitoringTalent::query()
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
                    ->orWhere('lokasi_penugasan_baru', 'like', "%{$search}%");
            });
        }

        // Base Query without status for category pills & contextual scope
        $baseScopeQuery = MonitoringTalent::query()->region($region);
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
                    ->orWhere('lokasi_penugasan_baru', 'like', "%{$search}%");
            });
        }

        // Dynamic Category counts (scoped to active region, talent_to, search)
        $categories = MonitoringTalent::distinct()->pluck('kategori')->filter()->values();
        $categoryCounts = [];
        $totalAllCategories = (clone $baseScopeQuery)->count();
        foreach ($categories as $cat) {
            $categoryCounts[$cat] = (clone $baseScopeQuery)->category($cat)->count();
        }

        // Summary KPI Metrics (updated according to active filters)
        $kpiBaseQuery = (clone $baseScopeQuery)->category($kategori);
        $kpiTalents = ($status !== 'all' && $status !== '')
            ? (clone $kpiBaseQuery)->status($status)->get()
            : (clone $kpiBaseQuery)->get();

        $totalAll = $kpiTalents->count();

        $isProjectKhusus = fn ($s) => str_contains($s, 'project khusus') || str_contains($s, 'akan penugasan');

        $countSudah = $kpiTalents->filter(fn ($t) => str_contains(strtolower($t->status ?? ''), 'sudah penugasan'))->count();
        $countProjectKhusus = $kpiTalents->filter(fn ($t) => $isProjectKhusus(strtolower($t->status ?? '')))->count();
        $countAkan = $countProjectKhusus;
        $countBelum = $kpiTalents->filter(fn ($t) => str_contains(strtolower($t->status ?? ''), 'belum penugasan'))->count();
        $countConfirm = $kpiTalents->filter(fn ($t) => str_contains(strtolower($t->status ?? ''), 'confirm'))->count();

        // Manual Progress Materi Setting
        $progressMateri = (float) AppSetting::get('talent_program_progress_materi', 83);

        // Filter Dropdown Lists
        $statuses = MonitoringTalent::distinct()->pluck('status')->filter()->values();
        $regions = RegionHelper::getCanonicalRegions(MonitoringTalent::distinct()->pluck('region'));
        $talentToList = MonitoringTalent::distinct()->pluck('talent_to')->filter()->values();

        $perPage = (int) $request->input('per_page', 25);
        $talents = $query->orderBy('no', 'asc')->paginate($perPage)->withQueryString();

        return view('Administrasi.Pages.Monitoring.monitoring_talent', compact(
            'talents',
            'kategori',
            'status',
            'region',
            'talentTo',
            'search',
            'totalAll',
            'totalAllCategories',
            'countSudah',
            'countAkan',
            'countProjectKhusus',
            'countBelum',
            'countConfirm',
            'progressMateri',
            'categoryCounts',
            'categories',
            'statuses',
            'regions',
            'talentToList'
        ));
    }

    /**
     * Update manual Progress Materi for Talent Program.
     */
    public function updateProgressMateri(Request $request)
    {
        $request->validate([
            'progress_materi' => 'required|numeric|min:0|max:100',
        ]);

        AppSetting::set('talent_program_progress_materi', $request->input('progress_materi'));

        return redirect()->back()->with('success', 'Progres Materi Talent Program berhasil diperbarui.');
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
            'status' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'periode_penugasan_lama' => 'nullable|string|max:255',
            'alasan' => 'nullable|string',
            'periode_penugasan_baru' => 'nullable|string|max:255',
            'lokasi_penugasan_baru' => 'nullable|string|max:255',
            'catatan_tambahan' => 'nullable|string',
            'kategori' => 'required|string|max:255',
        ];
    }

    /**
     * Store a newly created talent record.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());

        if (empty($data['no'])) {
            $data['no'] = ((int) MonitoringTalent::max('no')) + 1;
        }

        MonitoringTalent::create($data);

        return back()->with('success', 'Data peserta talent berhasil ditambahkan.');
    }

    /**
     * Update an existing talent record.
     */
    public function update(Request $request, $id)
    {
        $talent = MonitoringTalent::findOrFail($id);
        $data = $request->validate($this->validationRules());

        $talent->update($data);

        return back()->with('success', 'Data peserta talent berhasil diperbarui.');
    }

    /**
     * Delete a single talent record.
     */
    public function destroy($id)
    {
        $talent = MonitoringTalent::findOrFail($id);
        $talent->delete();

        return back()->with('success', 'Data peserta talent berhasil dihapus.');
    }

    /**
     * Delete all talent records.
     */
    public function destroyAll()
    {
        $count = MonitoringTalent::count();
        MonitoringTalent::truncate();

        ActivityLog::log(
            'Hapus',
            'Monitoring Talent',
            "Menghapus semua data monitoring talent ({$count} data)",
            ['count' => $count]
        );

        return back()->with('success', "Seluruh data monitoring talent ({$count} data) berhasil dihapus.");
    }

    /**
     * Import talent data from CSV/Excel via SpreadsheetReader.
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
            return back()->withErrors(['file' => 'Gagal membaca file: '.$e->getMessage()]);
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
            } elseif (in_array($clean, ['status', 'status penugasan'])) {
                $headerMap['status'] = $idx;
            } elseif (in_array($clean, ['keterangan', 'ket', 'notes', 'note'])) {
                $headerMap['keterangan'] = $idx;
            } elseif (in_array($clean, ['periode penugasan lama', 'periode lama', 'periode asal'])) {
                $headerMap['periode_penugasan_lama'] = $idx;
            } elseif (in_array($clean, ['alasan', 'alasan penugasan'])) {
                $headerMap['alasan'] = $idx;
            } elseif (in_array($clean, ['periode penugasan baru', 'periode baru', 'periode tujuan'])) {
                $headerMap['periode_penugasan_baru'] = $idx;
            } elseif (in_array($clean, ['lokasi penugasan baru', 'lokasi baru', 'lokasi tugas baru', 'penugasan baru'])) {
                $headerMap['lokasi_penugasan_baru'] = $idx;
            } elseif (in_array($clean, ['catatan tambahan', 'catatan', 'keterangan tambahan'])) {
                $headerMap['catatan_tambahan'] = $idx;
            } elseif (in_array($clean, ['kategori', 'category', 'kategori talent'])) {
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
                $status = $getVal('status', 9) ?: null;
                $keterangan = $getVal('keterangan', 10) ?: null;
                $periodeLama = $getVal('periode_penugasan_lama', 11) ?: null;
                $alasan = $getVal('alasan', 12) ?: null;
                $periodeBaru = $getVal('periode_penugasan_baru', 13) ?: null;
                $lokasiBaru = $getVal('lokasi_penugasan_baru', 14) ?: null;

                $kategori = $getVal('kategori', count($row) - 1);
                if (empty($kategori) || ! str_starts_with(strtoupper($kategori), 'TALENT')) {
                    $kategori = 'TALENT ESTATE TAHUN 2026';
                }

                if (strcasecmp((string) $talentTo, 'PC') === 0) {
                    if (str_contains(strtoupper($kategori), 'MILL') || str_contains($posisiAsal ?? '', 'Mill')) {
                        $talentTo = 'PC (Production Controller)';
                    } else {
                        $talentTo = 'PC (Plantation Controller)';
                    }
                }

                $catatanVal = $getVal('catatan_tambahan', 15);
                if ($catatanVal !== '' && $catatanVal !== '-') {
                    $catatan = $catatanVal;
                } else {
                    $notes = [];
                    for ($i = 15; $i < count($row) - 1; $i++) {
                        $val = trim((string) ($row[$i] ?? ''));
                        if (! empty($val) && $val !== '-') {
                            $notes[] = $val;
                        }
                    }
                    $catatan = ! empty($notes) ? implode(' | ', $notes) : null;
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
                    'periode_penugasan_lama' => $periodeLama,
                    'alasan' => $alasan,
                    'periode_penugasan_baru' => $periodeBaru,
                    'lokasi_penugasan_baru' => $lokasiBaru,
                    'catatan_tambahan' => $catatan,
                    'kategori' => $kategori,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $imported++;

                if (count($insertBatch) >= 100) {
                    MonitoringTalent::insert($insertBatch);
                    $insertBatch = [];
                }
            }

            if (! empty($insertBatch)) {
                MonitoringTalent::insert($insertBatch);
            }

            DB::commit();

            ActivityLog::log(
                'Import',
                'Monitoring Talent',
                "Mengimport {$imported} data peserta talent dari file ".$file->getClientOriginalName(),
                ['filename' => $file->getClientOriginalName(), 'count' => $imported]
            );

            return back()->with('success', "{$imported} data peserta talent berhasil diimpor.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['file' => 'Gagal mengimpor file: '.$e->getMessage()]);
        }
    }

    /**
     * Export talent data to CSV.
     */
    public function export(Request $request)
    {
        $kategori = trim((string) $request->input('kategori', 'all'));
        $status = trim((string) $request->input('status', 'all'));
        $region = trim((string) $request->input('region', 'all'));
        $search = trim((string) $request->input('search', ''));

        $query = MonitoringTalent::query();

        if ($kategori !== 'all' && $kategori !== '') {
            $query->where('kategori', $kategori);
        }
        if ($status !== 'all' && $status !== '') {
            $query->where('status', 'like', "%{$status}%");
        }
        if ($region !== 'all' && $region !== '') {
            $query->where('region', $region);
        }
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('employee_name', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('posisi_asal', 'like', "%{$search}%")
                    ->orWhere('unit', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('no', 'asc')->get();
        $headers = [
            'No',
            'Employee ID',
            'Employee Name',
            'Posisi Asal',
            'Talent To',
            'Unit',
            'Region',
            'Mentor Lapangan',
            'Mentor FRLC',
            'Status',
            'Keterangan',
            'Periode Penugasan Lama',
            'Alasan',
            'Periode Penugasan Baru',
            'Lokasi Penugasan Baru',
            'Catatan Tambahan',
            'Kategori',
        ];

        $dataRows = [];
        foreach ($records as $r) {
            $dataRows[] = [
                $r->no,
                $r->employee_id,
                $r->employee_name,
                $r->posisi_asal,
                $r->talent_to,
                $r->unit,
                $r->region,
                $r->mentor_lapangan,
                $r->mentor_frlc,
                $r->status,
                $r->keterangan,
                $r->periode_penugasan_lama,
                $r->alasan,
                $r->periode_penugasan_baru,
                $r->lokasi_penugasan_baru,
                $r->catatan_tambahan,
                $r->kategori,
            ];
        }

        $filename = 'Monitoring_Talent_'.date('Ymd_His').'.xlsx';

        return ExcelExporter::download($filename, $headers, $dataRows, 'Monitoring Talent');
    }

    /**
     * Download template Excel.
     */
    public function template()
    {
        $headers = [
            'No',
            'Employee ID',
            'Employee Name',
            'Posisi Asal',
            'Talent To',
            'Unit',
            'Region',
            'Mentor Lapangan',
            'Mentor FRLC',
            'Status',
            'Keterangan',
            'Periode Penugasan Lama',
            'Alasan',
            'Periode Penugasan Baru',
            'Lokasi Penugasan Baru',
            'Catatan Tambahan',
            'Kategori',
        ];

        $sampleRow = [
            '1',
            '2023120481',
            'CONTOH NAMA KARYAWAN',
            'Asisten Kebun',
            'Askep',
            'ATS1',
            'Riau',
            'Nama Mentor Lapangan',
            'Nama Mentor FRLC',
            'Sudah Penugasan',
            'Plt. Askep di ATS 1',
            'Periode 1 April - 30 Juli 2026',
            'Alasan Penugasan',
            'Periode 1 Agustus - 30 November 2026',
            'Tetap',
            'Catatan opsional',
            'Struktur / Operasional',
        ];

        return ExcelExporter::download('Template_Monitoring_Talent.xlsx', $headers, [$sampleRow], 'Template Talent');
    }
}
