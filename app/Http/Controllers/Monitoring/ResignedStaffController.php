<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Monitoring\ResignedStaff;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ResignedStaffController extends Controller
{
    public function index(Request $request)
    {
        // ==== 1. Filter Data ====
        $query = ResignedStaff::query();
        
        if ($request->has('region') && $request->region != 'All Region' && $request->region != '') {
            $query->where('region', $request->region);
        }

        $availableYears = ResignedStaff::selectRaw('YEAR(resign_date) as year')
                            ->whereNotNull('resign_date')
                            ->distinct()
                            ->pluck('year')
                            ->sort()
                            ->values()
                            ->toArray();
        if (empty($availableYears)) {
            $availableYears = [2025, 2026];
        }

        $selectedYears = $request->input('years', $availableYears);
        if (!is_array($selectedYears)) {
            $selectedYears = [$selectedYears];
        }

        if ($request->has('years')) {
            $query->whereIn(DB::raw('YEAR(resign_date)'), $selectedYears);
        }

        // Clone query for Data Karyawan (Table)
        $tableQuery = clone $query;
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $tableQuery->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('kebun', 'like', "%{$search}%");
            });
        }
        $perPage = $request->input('per_page', 10);
        $resignedStaffs = $tableQuery->orderBy('resign_date', 'desc')->paginate($perPage)->withQueryString();

        // ==== 2. Dashboard Aggregations (Top Cards) ====
        $stats = (clone $query)->select(
            DB::raw('YEAR(resign_date) as year'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN LOWER(jenis_resign) = 'voluntary' THEN 1 ELSE 0 END) as total_vt"),
            DB::raw("SUM(CASE WHEN LOWER(jenis_resign) = 'involuntary' THEN 1 ELSE 0 END) as total_it"),
            DB::raw("SUM(CASE WHEN LOWER(alumni) = 'alumni' THEN 1 ELSE 0 END) as total_alumni"),
            DB::raw("SUM(CASE WHEN LOWER(alumni) = 'non alumni' THEN 1 ELSE 0 END) as total_non_alumni")
        )->groupBy(DB::raw('YEAR(resign_date)'))
         ->get()
         ->keyBy('year');

        $statTotal = (object)[
            'total' => 0, 'total_vt' => 0, 'total_it' => 0, 'total_alumni' => 0, 'total_non_alumni' => 0
        ];
        
        foreach ($selectedYears as $year) {
            if ($stats->has($year)) {
                $s = $stats->get($year);
                $statTotal->total += $s->total;
                $statTotal->total_vt += $s->total_vt;
                $statTotal->total_it += $s->total_it;
                $statTotal->total_alumni += $s->total_alumni;
                $statTotal->total_non_alumni += $s->total_non_alumni;
            }
        }

        // Safe percentage function
        $calcPct = function($part, $total) {
            return $total > 0 ? round(($part / $total) * 100, 1) : 0;
        };

        // ==== 3. Chart: Cluster Penyebab Resign ====
        $clusterSelect = ['cluster_resign'];
        foreach ($selectedYears as $year) {
            $clusterSelect[] = DB::raw("SUM(CASE WHEN YEAR(resign_date) = {$year} THEN 1 ELSE 0 END) as count_{$year}");
        }
        $clusterStats = (clone $query)->select($clusterSelect)
         ->whereNotNull('cluster_resign')
         ->groupBy('cluster_resign')
         ->orderBy(DB::raw('COUNT(*)'), 'desc')
         ->limit(10)
         ->get();

        // ==== 4. Chart: Resign by Jabatan ====
        $jabatanSelect = ['jabatan'];
        foreach ($selectedYears as $year) {
            $jabatanSelect[] = DB::raw("SUM(CASE WHEN YEAR(resign_date) = {$year} THEN 1 ELSE 0 END) as count_{$year}");
        }
        $jabatanStats = (clone $query)->select($jabatanSelect)
         ->whereNotNull('jabatan')
         ->groupBy('jabatan')
         ->orderBy(DB::raw('COUNT(*)'), 'asc')
         ->limit(10)
         ->get();

        // ==== 5. Table: Top 10 Kebun ====
        $getTopKebun = function($year) use ($query) {
            return (clone $query)->select(
                'kebun',
                DB::raw("SUM(CASE WHEN LOWER(jenis_resign) = 'involuntary' THEN 1 ELSE 0 END) as it"),
                DB::raw("SUM(CASE WHEN LOWER(jenis_resign) = 'voluntary' THEN 1 ELSE 0 END) as vt"),
                DB::raw("COUNT(*) as total")
            )->whereYear('resign_date', $year)
             ->whereNotNull('kebun')
             ->groupBy('kebun')
             ->orderBy('total', 'desc')
             ->limit(10)
             ->get();
        };
        
        $topKebunTables = [];
        foreach ($selectedYears as $year) {
            $topKebunTables[$year] = $getTopKebun($year);
        }

        // ==== 6. Trend Turnover ====
        $trendStats = (clone $query)->select(
            DB::raw("DATE_FORMAT(resign_date, '%Y-%m') as month_year"),
            DB::raw("SUM(CASE WHEN LOWER(jenis_resign) = 'involuntary' THEN 1 ELSE 0 END) as it"),
            DB::raw("SUM(CASE WHEN LOWER(jenis_resign) = 'voluntary' THEN 1 ELSE 0 END) as vt"),
            DB::raw("COUNT(*) as total")
        )->groupBy('month_year')
         ->orderBy('month_year', 'asc')
         ->get();

        return view('Administrasi.Pages.Monitoring.staf_resign', compact(
            'resignedStaffs', 'availableYears', 'selectedYears',
            'stats', 'statTotal', 'calcPct',
            'clusterStats', 'jabatanStats', 'topKebunTables', 'trendStats'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'kebun' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'jenis_resign' => 'nullable|string|max:255',
            'cluster_resign' => 'nullable|string|max:255',
            'alumni' => 'nullable|string|max:255',
            'join_date' => 'nullable|date',
            'resign_date' => 'nullable|date',
            'deskripsi_resign' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        ResignedStaff::create($request->all());

        return redirect()->route('administrasi.staf-resign.index')->with('success', 'Data karyawan resign berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $staff = ResignedStaff::findOrFail($id);
        $staff->update($request->all());
        return redirect()->route('administrasi.staf-resign.index')->with('success', 'Data karyawan resign berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $staff = ResignedStaff::findOrFail($id);
        $staff->delete();
        return redirect()->route('administrasi.staf-resign.index')->with('success', 'Data karyawan resign berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=template_import_staf_resign.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['employee_id', 'nama', 'jabatan', 'kebun', 'region', 'join_date', 'resign_date', 'jenis_resign', 'cluster_resign', 'alumni', 'deskripsi_resign', 'keterangan'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['123', 'Test Staff', 'Asisten', 'MRE', 'Kubar', '2022-01-15', '2025-06-10', 'VT', 'Pindah Perusahaan', 'ALUMNI', 'Penjelasan singkat', 'Catatan opsional']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        if (!in_array($extension, ['csv', 'txt', 'xls', 'xlsx'])) {
            return redirect()->route('administrasi.staf-resign.index')->with('error', 'Format file tidak didukung! Pastikan file berformat Excel (.xlsx/.xls) atau CSV.');
        }

        $filename = 'import_resign_'.time().'.'.$extension;
        $file->move(storage_path('app/temp'), $filename);
        $fullPath = storage_path('app/temp/' . $filename);
        
        $spreadsheet = IOFactory::load($fullPath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $rawHeader = array_shift($rows);
        
        $aliasMap = [
            'id' => 'employee_id', 'id_karyawan' => 'employee_id', 'nik' => 'employee_id',
            'nama_karyawan' => 'nama', 'name' => 'nama',
            'tgl_join' => 'join_date', 'tanggal_join' => 'join_date', 'join' => 'join_date',
            'tgl_resign' => 'resign_date', 'tanggal_resign' => 'resign_date', 'resign' => 'resign_date',
            'desc_resign' => 'deskripsi_resign', 'alasan_resign' => 'deskripsi_resign', 'alasan' => 'deskripsi_resign',
            'jenis' => 'jenis_resign', 'tipe_resign' => 'jenis_resign',
            'cluster' => 'cluster_resign', 'kategori_resign' => 'cluster_resign'
        ];

        $header = array_map(function($col) use ($aliasMap) {
            $col = strtolower(trim(str_replace([' ', '-'], '_', $col)));
            return $aliasMap[$col] ?? $col;
        }, $rawHeader);
        
        $previewData = [];
        $validRowsCount = 0;
        foreach ($rows as $row) {
            if (count($header) == count($row)) {
                $rowData = array_combine($header, $row);
                if (!empty(array_filter($rowData))) {
                    if ($validRowsCount < 3) {
                        $previewData[] = $rowData;
                    }
                    $validRowsCount++;
                }
            }
        }

        return redirect()->back()->with([
            'preview_data' => $previewData,
            'temp_file' => 'temp/' . $filename,
            'total_rows' => $validRowsCount
        ]);
    }

    public function processImport(Request $request)
    {
        $tempFile = $request->input('temp_file');
        if (!$tempFile) {
            return redirect()->back()->with('error', 'File import tidak ditemukan atau kadaluarsa.');
        }

        $fullPath = storage_path('app/' . $tempFile);
        if (!file_exists($fullPath)) {
            return redirect()->back()->with('error', 'File import tidak ditemukan atau kadaluarsa.');
        }

        $spreadsheet = IOFactory::load($fullPath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $rawHeader = array_shift($rows);

        $aliasMap = [
            'id' => 'employee_id', 'id_karyawan' => 'employee_id', 'nik' => 'employee_id',
            'nama_karyawan' => 'nama', 'name' => 'nama',
            'tgl_join' => 'join_date', 'tanggal_join' => 'join_date', 'join' => 'join_date',
            'tgl_resign' => 'resign_date', 'tanggal_resign' => 'resign_date', 'resign' => 'resign_date',
            'desc_resign' => 'deskripsi_resign', 'alasan_resign' => 'deskripsi_resign', 'alasan' => 'deskripsi_resign',
            'jenis' => 'jenis_resign', 'tipe_resign' => 'jenis_resign',
            'cluster' => 'cluster_resign', 'kategori_resign' => 'cluster_resign'
        ];

        $header = array_map(function($col) use ($aliasMap) {
            $col = strtolower(trim(str_replace([' ', '-'], '_', $col)));
            return $aliasMap[$col] ?? $col;
        }, $rawHeader);

        foreach ($rows as $row) {
            if (count($header) == count($row)) {
                $rowData = array_combine($header, $row);
                
                if (empty(array_filter($rowData))) {
                    continue;
                }

                $joinDate = $this->parseDate($rowData['join_date'] ?? null);
                $resignDate = $this->parseDate($rowData['resign_date'] ?? null);

                ResignedStaff::create([
                    'employee_id' => $rowData['employee_id'] ?? null,
                    'nama' => $rowData['nama'] ?? null,
                    'jabatan' => $rowData['jabatan'] ?? null,
                    'kebun' => $rowData['kebun'] ?? null,
                    'region' => $rowData['region'] ?? null,
                    'join_date' => $joinDate,
                    'resign_date' => $resignDate,
                    'jenis_resign' => $rowData['jenis_resign'] ?? null,
                    'cluster_resign' => $rowData['cluster_resign'] ?? null,
                    'alumni' => $rowData['alumni'] ?? null,
                    'deskripsi_resign' => $rowData['deskripsi_resign'] ?? null,
                    'keterangan' => $rowData['keterangan'] ?? null,
                ]);
            }
        }

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        return redirect()->route('administrasi.staf-resign.index')->with('success', 'Data berhasil di-import!');
    }

    private function parseDate($dateValue) {
        if (!$dateValue) return null;
        try {
            if (is_numeric($dateValue)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue)->format('Y-m-d');
            }
            if (strpos($dateValue, '/') !== false) {
                // assume d/m/Y
                return Carbon::createFromFormat('d/m/Y', $dateValue)->format('Y-m-d');
            }
            return Carbon::parse($dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
