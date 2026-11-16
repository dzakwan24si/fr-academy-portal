<?php

namespace App\Http\Controllers\Talent;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Talent\ProfilTalent;
use App\Services\ExcelExporter;
use App\Services\SpreadsheetReader;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfilTalentController extends Controller
{
    /**
     * Display the Talent Dossier Dashboard.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $kategori = trim((string) $request->input('kategori', 'all'));
        $region = trim((string) $request->input('region', 'all'));
        $selectedId = $request->input('selected_id');

        // Base Query for Talent List
        $query = ProfilTalent::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%")
                    ->orWhere('talent_to', 'like', "%{$search}%")
                    ->orWhere('lokasi_ojt', 'like', "%{$search}%")
                    ->orWhere('mentor', 'like', "%{$search}%")
                    ->orWhere('mentor_frlc', 'like', "%{$search}%")
                    ->orWhere('alumni', 'like', "%{$search}%");
            });
        }

        if (!empty($kategori) && $kategori !== 'all') {
            $query->where('talent', $kategori);
        }

        if (!empty($region) && $region !== 'all') {
            $query->region($region);
        }

        // Get list of talents for the sidebar
        $talents = $query->orderBy('no', 'asc')->orderBy('id', 'asc')->get();

        // Selected Talent for Detail View
        $selectedTalent = null;
        if (!empty($selectedId)) {
            $selectedTalent = ProfilTalent::find($selectedId);
        }

        // Fallback to first in list or first in DB
        if (!$selectedTalent && $talents->isNotEmpty()) {
            $selectedTalent = $talents->first();
        } elseif (!$selectedTalent) {
            $selectedTalent = ProfilTalent::first();
        }

        // Distinct categories for filter
        $categories = ProfilTalent::distinct()
            ->whereNotNull('talent')
            ->where('talent', '!=', '')
            ->pluck('talent')
            ->sort()
            ->values();

        // Distinct regions for filter
        $regions = ProfilTalent::distinct()
            ->whereNotNull('region')
            ->where('region', '!=', '')
            ->pluck('region')
            ->sort()
            ->values();

        return view('Administrasi.Pages.Talent.profil_talent', compact(
            'talents',
            'selectedTalent',
            'categories',
            'regions',
            'search',
            'kategori',
            'region'
        ));
    }

    /**
     * Store a newly created talent.
     */
    public function store(Request $request)
    {
        $validated = $this->validateInput($request);

        if ($request->hasFile('foto_file')) {
            $path = $request->file('foto_file')->store('profil-talents', 'public');
            $validated['foto_profil'] = 'storage/' . $path;
        }

        $talent = ProfilTalent::create($validated);

        ActivityLog::log(
            action: 'Tambah Data',
            module: 'Profil Talent',
            description: "Menambahkan Profil Talent baru: {$talent->nama} ({$talent->employee_id})",
            properties: ['talent_id' => $talent->id, 'nama' => $talent->nama]
        );

        return redirect()->route('administrasi.profil-talent.index', ['selected_id' => $talent->id])
            ->with('success', "Talent {$talent->nama} berhasil ditambahkan.");
    }

    /**
     * Update the specified talent data.
     */
    public function update(Request $request, $id)
    {
        $talent = ProfilTalent::findOrFail($id);
        $validated = $this->validateInput($request, $id);

        if ($request->hasFile('foto_file')) {
            // Delete old local photo if exists
            if (!empty($talent->foto_profil) && str_starts_with($talent->foto_profil, 'storage/profil-talents/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $talent->foto_profil));
            }
            $path = $request->file('foto_file')->store('profil-talents', 'public');
            $validated['foto_profil'] = 'storage/' . $path;
        }

        $talent->update($validated);

        ActivityLog::log(
            action: 'Edit Data',
            module: 'Profil Talent',
            description: "Mengupdate Profil Talent: {$talent->nama} ({$talent->employee_id})",
            properties: ['talent_id' => $talent->id, 'nama' => $talent->nama]
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data {$talent->nama} berhasil diperbarui.",
                'data' => $talent
            ]);
        }

        return redirect()->route('administrasi.profil-talent.index', ['selected_id' => $talent->id])
            ->with('success', "Data {$talent->nama} berhasil diperbarui.");
    }

    /**
     * Remove the specified talent.
     */
    public function destroy($id)
    {
        $talent = ProfilTalent::findOrFail($id);
        $nama = $talent->nama;
        $nik = $talent->employee_id;

        $talent->delete();

        ActivityLog::log(
            action: 'Hapus Data',
            module: 'Profil Talent',
            description: "Menghapus Profil Talent: {$nama}",
            properties: ['talent_id' => $id, 'nama' => $nama]
        );

        $msg = "Data talent {$nama} berhasil dihapus.";
        if (!empty($nik)) {
            $msg = "Data talent {$nama} (NIK: {$nik}) berhasil dihapus dan disinkronkan dari Monitoring Talent.";
        }

        return redirect()->route('administrasi.profil-talent.index')
            ->with('success', $msg);
    }

    /**
     * Delete all talents.
     */
    public function deleteAll()
    {
        $count = ProfilTalent::count();
        ProfilTalent::truncate();

        ActivityLog::log(
            action: 'Hapus Semua',
            module: 'Profil Talent',
            description: "Menghapus seluruh {$count} data Profil Talent.",
            properties: ['count' => $count]
        );

        return redirect()->route('administrasi.profil-talent.index')
            ->with('success', "Seluruh data ({$count} talent) berhasil dihapus.");
    }

    /**
     * Download Excel template.
     */
    public function downloadTemplate()
    {
        $headers = $this->getColumnHeaderList();

        $sampleRow = [
            1, '2023120481', 'DARMA YUDHI',
            'Darma Yudhi memiliki kecerdasan rata-rata...',
            '25 Tahun', 'Universitas Sumatera Utara', 'K/1', 'Islam', 'Estate', 'Asisten Kebun', 'Askep',
            'Sudah Penugasan', 'Plt. Askep di ATS 1', 'Riau', 'Jhon Setiawan', 'Henri Hutajulu', '2 Tahun',
            "1.Des 2023 - Mei 2024 : asisten kebun PT SAM 2\n2.Mei 2024 - maret 2026 : asisten kebun ATS 1",
            '1.Selalu capai produksi secara 1 tahun selama menjabat',
            'Plt. Askep di ATS 1', '1 Agustus - 30 November 2026', 'Tetap',
            100, 100, 75, 91.7, '1.Tidak ada data', '1.Tidak ada data', '1.Tidak ada data',
            'Kemampuan teknis dan penyampaian program sudah cukup baik...',
            // Nilai Jan - Des
            '', '', '', '', '', '', 78.5, 68.5, '', '', '', '',
            'Kompetensi Teknis & Operasional', 'Penguatan Role Askep', 'Buat analisa why...',
            '', '', '', 'Memenuhi Syarat',
            'https://drive.google.com/...',
            // Catatan Mentor & Dev Suggestion per bulan
            '', '', '', '', '', '', '', '', '', '', '', '',
            'Catatan Juli...', 'Saran Juli...',
            'Catatan Agustus...', 'Saran Agustus...',
            '', '', '', '', '', '', '', '',
        ];

        return ExcelExporter::download('Template_Import_Profil_Talent.xlsx', $headers, [$sampleRow], 'Template Profil Talent');
    }

    /**
     * Export individual summary report as PDF matching executive summary format.
     */
    public function exportPdf(Request $request, $id)
    {
        @ini_set('memory_limit', '256M');
        @set_time_limit(120);

        // Ensure storage/fonts directory exists for DomPDF font cache
        $fontDir = storage_path('fonts');
        if (!is_dir($fontDir)) {
            @mkdir($fontDir, 0775, true);
        }

        $talent = ProfilTalent::findOrFail($id);

        // Prepare Base64 Logo for DomPDF to avoid path/network issues
        $logoBase64 = null;
        $logoPath = public_path('images/logofr.png');
        if (file_exists($logoPath)) {
            $logoData = @file_get_contents($logoPath);
            if ($logoData !== false) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
            }
        }

        // Prepare Photo (Base64 for DomPDF to ensure flawless embedding)
        $photoBase64 = $talent->photo_base64;

        // Grade evaluation info
        $grade = $talent->evaluation_grade;

        // Split text fields into numbered lists
        $historiList = $this->splitNumberedList($talent->histori_pekerjaan);
        $achievementList = $this->splitNumberedList($talent->achievement);
        $strengthList = $this->splitNumberedList($talent->strength);
        $weaknessList = $this->splitNumberedList($talent->weakness);
        $catatanMentorList = $this->splitNumberedList($talent->catatan_mentor_general);
        $devSuggestionList = $this->splitNumberedList($talent->development_suggestion_general);

        // Determine months to show: If Jan-Jun have non-empty values, show all 12, else show Jul-Dec (Semester 2)
        $hasJanJun = false;
        foreach (['januari', 'februari', 'maret', 'april', 'mei', 'juni'] as $m) {
            if ($talent->{"nilai_{$m}"} !== null && $talent->{"nilai_{$m}"} !== '') {
                $hasJanJun = true;
                break;
            }
        }

        $allMonths = [
            'januari' => 'Januari',
            'februari' => 'Februari',
            'maret' => 'Maret',
            'april' => 'April',
            'mei' => 'Mei',
            'juni' => 'Juni',
            'juli' => 'Juli',
            'agustus' => 'Agustus',
            'september' => 'September',
            'oktober' => 'Oktober',
            'november' => 'November',
            'desember' => 'Desember',
        ];

        $displayedMonths = $hasJanJun ? $allMonths : [
            'juli' => 'Juli',
            'agustus' => 'Agustus',
            'september' => 'September',
            'oktober' => 'Oktober',
            'november' => 'November',
            'desember' => 'Desember',
        ];

        if ($request->query('html') == '1') {
            return view('Administrasi.Pages.Talent.pdf_report', compact(
                'talent',
                'logoBase64',
                'photoBase64',
                'grade',
                'historiList',
                'achievementList',
                'strengthList',
                'weaknessList',
                'catatanMentorList',
                'devSuggestionList',
                'displayedMonths'
            ));
        }

        try {
            $pdf = Pdf::loadView('Administrasi.Pages.Talent.pdf_report', compact(
                'talent',
                'logoBase64',
                'photoBase64',
                'grade',
                'historiList',
                'achievementList',
                'strengthList',
                'weaknessList',
                'catatanMentorList',
                'devSuggestionList',
                'displayedMonths'
            ))->setPaper('a4', 'portrait')
              ->setOption('fontDir', $fontDir)
              ->setOption('fontCache', $fontDir);

            $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $talent->nama);
            $filename = "Individual_Summary_Report_{$safeName}.pdf";

            ActivityLog::log(
                action: 'Export PDF',
                module: 'Profil Talent',
                description: "Export Individual Summary Report PDF untuk {$talent->nama}",
                properties: ['talent_id' => $talent->id, 'nama' => $talent->nama]
            );

            if ($request->query('download') == '1') {
                return $pdf->download($filename);
            }

            return $pdf->stream($filename);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DomPDF Export Error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response("<h3>Gagal Mengekspor PDF (Error 500)</h3><p><strong>Pesan Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p><p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " (Baris: " . $e->getLine() . ")</p><p><em>Tips: Pastikan folder <code>storage/fonts</code> dan <code>storage/framework/cache</code> memiliki izin tulis (chmod 775/777) dan ekstensi PHP GD aktif di server hosting.</em></p>", 500)
                ->header('Content-Type', 'text/html; charset=utf-8');
        }
    }

    /**
     * Stream or serve talent photo with caching and Google Drive resolution.
     */
    public function photo($id)
    {
        $talent = ProfilTalent::findOrFail($id);
        $diskPath = $talent->ensureLocalPhoto(true);

        if ($diskPath && file_exists($diskPath)) {
            $mime = mime_content_type($diskPath) ?: 'image/png';
            return response()->file($diskPath, [
                'Content-Type' => $mime,
                'Cache-Control' => 'public, max-age=86400, immutable',
            ]);
        }

        // If it's a direct web link that couldn't be cached, redirect to it
        $url = $talent->photo_url;
        if (!empty($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'))) {
            return redirect()->away($url);
        }

        // Fallback default avatar
        $avatarUrl = 'https://ui-avatars.com/api/?name=' . urlencode($talent->nama) . '&background=1F3E22&color=fff&size=200';
        return redirect()->away($avatarUrl);
    }

    /**
     * Export all data to Excel.
     */
    public function export(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $kategori = trim((string) $request->input('kategori', 'all'));
        $region = trim((string) $request->input('region', 'all'));

        $query = ProfilTalent::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        if (!empty($kategori) && $kategori !== 'all') {
            $query->where('talent', $kategori);
        }

        if (!empty($region) && $region !== 'all') {
            $query->region($region);
        }

        $talents = $query->orderBy('no', 'asc')->orderBy('id', 'asc')->get();

        $headers = $this->getColumnHeaderList();
        $rows = [];

        foreach ($talents as $t) {
            $rows[] = [
                $t->no,
                $t->employee_id,
                $t->nama,
                $t->profilling,
                $t->umur,
                $t->alumni,
                $t->status_pernikahan,
                $t->agama,
                $t->talent,
                $t->jabatan,
                $t->talent_to,
                $t->status_ojt,
                $t->lokasi_ojt,
                $t->region,
                $t->mentor,
                $t->mentor_frlc,
                $t->masa_kerja,
                $t->histori_pekerjaan,
                $t->achievement,
                $t->keterangan_penugasan,
                $t->periode_penugasan,
                $t->alasan,
                $t->refreshment,
                $t->softskill,
                $t->ojt,
                $t->overall_progress,
                $t->teknis,
                $t->leadership,
                $t->training,
                $t->catatan_mentor_general,
                // Nilai Jan - Des
                $t->nilai_januari,
                $t->nilai_februari,
                $t->nilai_maret,
                $t->nilai_april,
                $t->nilai_mei,
                $t->nilai_juni,
                $t->nilai_juli,
                $t->nilai_agustus,
                $t->nilai_september,
                $t->nilai_oktober,
                $t->nilai_november,
                $t->nilai_desember,
                $t->strength,
                $t->weakness,
                $t->development_suggestion_general,
                $t->nilai_ojt,
                $t->nilai_evaluasi_panelis,
                $t->nilai_akhir,
                $t->keterangan_evaluasi,
                $t->foto_profil,
                // Catatan Mentor & Dev Suggestion per bulan
                $t->catatan_mentor_januari, $t->dev_suggestion_januari,
                $t->catatan_mentor_februari, $t->dev_suggestion_februari,
                $t->catatan_mentor_maret, $t->dev_suggestion_maret,
                $t->catatan_mentor_april, $t->dev_suggestion_april,
                $t->catatan_mentor_mei, $t->dev_suggestion_mei,
                $t->catatan_mentor_juni, $t->dev_suggestion_juni,
                $t->catatan_mentor_juli, $t->dev_suggestion_juli,
                $t->catatan_mentor_agustus, $t->dev_suggestion_agustus,
                $t->catatan_mentor_september, $t->dev_suggestion_september,
                $t->catatan_mentor_oktober, $t->dev_suggestion_oktober,
                $t->catatan_mentor_november, $t->dev_suggestion_november,
                $t->catatan_mentor_desember, $t->dev_suggestion_desember,
            ];
        }

        return ExcelExporter::download('Data_Profil_Talent_' . date('Ymd_His') . '.xlsx', $headers, $rows, 'Profil Talent');
    }

    /**
     * Import CSV or Excel into Database.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx|max:20480',
        ]);

        $file = $request->file('file');
        $rows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());

        if (empty($rows) || count($rows) < 2) {
            return back()->with('error', 'File tidak memiliki baris data untuk diimport.');
        }

        $rawHeaders = array_shift($rows);
        $headerMap = [];
        foreach ($rawHeaders as $idx => $h) {
            $norm = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $h)));
            $headerMap[$norm] = $idx;
        }

        $imported = 0;
        $months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

        DB::transaction(function () use ($rows, $headerMap, $months, &$imported) {
            foreach ($rows as $row) {
                // Skip empty row
                if (empty(array_filter($row, fn ($c) => trim((string) $c) !== ''))) {
                    continue;
                }

                $get = function ($patterns) use ($row, $headerMap) {
                    if (!is_array($patterns)) {
                        $patterns = [$patterns];
                    }
                    foreach ($patterns as $p) {
                        $norm = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $p)));
                        if (isset($headerMap[$norm])) {
                            $val = $row[$headerMap[$norm]] ?? null;
                            if ($val !== null) {
                                return trim((string) $val);
                            }
                        }
                    }
                    return null;
                };

                $parsePercent = function ($patterns) use ($get) {
                    $raw = $get($patterns);
                    if ($raw === null || $raw === '') {
                        return null;
                    }
                    $clean = trim(str_replace(['%', ' '], '', (string) $raw));
                    if (is_numeric($clean)) {
                        $num = (float) $clean;
                        if ($num > 0 && $num <= 1.0) {
                            $num = round($num * 100, 2);
                        }
                        return $num;
                    }
                    return null;
                };

                $nama = $get(['Nama', 'Nama Talent', 'Employee Name']);
                if (empty($nama)) {
                    continue;
                }

                $data = [
                    'no' => is_numeric($get(['No'])) ? (int) $get(['No']) : ($imported + 1),
                    'employee_id' => $get(['Employee ID', 'NIK', 'ID']),
                    'nama' => $nama,
                    'profilling' => $get(['Profilling', 'Profiling', 'Aspek Kepribadian']),
                    'umur' => $get(['Umur', 'Usia']),
                    'alumni' => $get(['Alumni', 'Pendidikan', 'Universitas']),
                    'status_pernikahan' => $get(['Status', 'Status Pernikahan']),
                    'agama' => $get(['Agama']),
                    'talent' => $get(['Talent', 'Kategori', 'Divisi']),
                    'jabatan' => $get(['Jabatan', 'Posisi Saat Ini']),
                    'talent_to' => $get(['Talent to', 'Target', 'Target Ke']),
                    'status_ojt' => $get(['Status OJT']),
                    'lokasi_ojt' => $get(['Lokasi OJT', 'Lokasi Penugasan']),
                    'region' => $get(['Region', 'Wilayah']),
                    'mentor' => $get(['Mentor', 'Mentor Lapangan', 'Mentor Utama']),
                    'mentor_frlc' => $get(['Mentor FRLC']),
                    'masa_kerja' => $get(['Masa Kerja']),
                    'histori_pekerjaan' => $this->normalizeNumberedList($get(['Histori Pekerjaan', 'Riwayat Pekerjaan'])),
                    'achievement' => $this->normalizeNumberedList($get(['Achievement', 'Prestasi'])),
                    'keterangan_penugasan' => $get(['Keterangan']),
                    'periode_penugasan' => $get(['Periode Penugasan']),
                    'alasan' => $get(['Alasan']),
                    'refreshment' => $parsePercent(['Refreshment', 'Refreshment (%)', 'Progress Refreshment']),
                    'softskill' => $parsePercent(['Softskill', 'Soft Skill', 'Softskill (%)', 'Soft Skill (%)']),
                    'ojt' => $parsePercent(['OJT', 'OJT Task', 'OJT (%)', 'OJT Task (%)']),
                    'overall_progress' => $parsePercent(['Overall Progress', 'Overall Progress (%)', 'Overall']),
                    'teknis' => $get(['Teknis']),
                    'leadership' => $get(['Leadership']),
                    'training' => $get(['Training']),
                    'catatan_mentor_general' => $this->normalizeNumberedList($get(['Catatan Mentor'])),
                    'strength' => $this->normalizeNumberedList($get(['Strength'])),
                    'weakness' => $this->normalizeNumberedList($get(['Weakness'])),
                    'development_suggestion_general' => $this->normalizeNumberedList($get(['Development Suggestion'])),
                    'nilai_ojt' => is_numeric($get(['Nilai OJT'])) ? (float) $get(['Nilai OJT']) : null,
                    'nilai_evaluasi_panelis' => is_numeric($get(['Nilai Evaluasi Panelis', 'Nilai Panelis'])) ? (float) $get(['Nilai Evaluasi Panelis', 'Nilai Panelis']) : null,
                    'nilai_akhir' => is_numeric($get(['Nilai Ahir', 'Nilai Akhir'])) ? (float) $get(['Nilai Ahir', 'Nilai Akhir']) : null,
                    'keterangan_evaluasi' => $get(['Keterangan Evaluasi']),
                    'foto_profil' => $get(['Foto Profil', 'Foto', 'Photo']),
                ];

                // Auto calculate Overall Progress if empty but components exist
                if ($data['overall_progress'] === null && ($data['refreshment'] !== null || $data['softskill'] !== null || $data['ojt'] !== null)) {
                    $progList = array_filter([$data['refreshment'], $data['softskill'], $data['ojt']], fn ($v) => $v !== null && $v !== '');
                    if (!empty($progList)) {
                        $data['overall_progress'] = round(array_sum($progList) / count($progList), 1);
                    }
                }

                // Auto calculate Nilai Akhir & Keterangan Evaluasi if OJT or Panelis present
                if ($data['nilai_ojt'] !== null || $data['nilai_evaluasi_panelis'] !== null) {
                    $ojt = (float) ($data['nilai_ojt'] ?? 0);
                    $panelis = (float) ($data['nilai_evaluasi_panelis'] ?? 0);
                    $data['nilai_akhir'] = round(($ojt * 0.40) + ($panelis * 0.60), 2);
                    $grade = ProfilTalent::getGradeInfo($data['nilai_akhir']);
                    if ($grade && empty($data['keterangan_evaluasi'])) {
                        $data['keterangan_evaluasi'] = $grade['title'] . ' (Grade ' . $grade['grade'] . ')';
                    }
                }

                // Monthly Scores & Notes
                foreach ($months as $m) {
                    $valScore = $get([$m]);
                    $data["nilai_{$m}"] = (is_numeric($valScore)) ? (float) $valScore : null;

                    $catatan = $get(["Catatan Mentor {$m}", "Catatan {$m}"]);
                    $data["catatan_mentor_{$m}"] = $this->normalizeNumberedList($catatan);

                    $suggestion = $get(["Development Suggestion {$m}", "Suggestion {$m}", "Saran {$m}"]);
                    $data["dev_suggestion_{$m}"] = $this->normalizeNumberedList($suggestion);
                }

                // Update if employee_id exists, otherwise create
                if (!empty($data['employee_id'])) {
                    ProfilTalent::updateOrCreate(['employee_id' => $data['employee_id']], $data);
                } else {
                    ProfilTalent::create($data);
                }

                $imported++;
            }
        });

        ActivityLog::log(
            action: 'Import',
            module: 'Profil Talent',
            description: "Mengimport {$imported} data Profil Talent dari file " . $file->getClientOriginalName(),
            properties: ['filename' => $file->getClientOriginalName(), 'count' => $imported]
        );

        // Pre-cache Google Drive photos in parallel for instant rendering
        try {
            ProfilTalent::batchCacheGoogleDrivePhotos();
        } catch (\Throwable $e) {
            // Silently continue if network unavailable
        }

        return back()->with('success', "{$imported} data Profil Talent berhasil diimport dan disimpan.");
    }

    /**
     * Validate common input for store & update.
     */
    protected function validateInput(Request $request, $id = null): array
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'employee_id' => 'nullable|string|max:100',
            'talent' => 'nullable|string|max:100',
            'jabatan' => 'nullable|string|max:150',
            'talent_to' => 'nullable|string|max:150',
            'status_ojt' => 'nullable|string|max:100',
            'lokasi_ojt' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:100',
            'mentor' => 'nullable|string|max:255',
            'mentor_frlc' => 'nullable|string|max:255',
            'umur' => 'nullable|string|max:50',
            'masa_kerja' => 'nullable|string|max:50',
            'alumni' => 'nullable|string|max:255',
            'status_pernikahan' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:50',
            'profilling' => 'nullable|string',
            'histori_pekerjaan' => 'nullable|string',
            'achievement' => 'nullable|string',
            'strength' => 'nullable|string',
            'weakness' => 'nullable|string',
            'refreshment' => 'nullable|numeric|min:0|max:100',
            'softskill' => 'nullable|numeric|min:0|max:100',
            'ojt' => 'nullable|numeric|min:0|max:100',
            'overall_progress' => 'nullable|numeric|min:0|max:100',
            'teknis' => 'nullable|string|max:255',
            'leadership' => 'nullable|string|max:255',
            'training' => 'nullable|string|max:255',
            'nilai_ojt' => 'nullable|numeric',
            'nilai_evaluasi_panelis' => 'nullable|numeric',
            'nilai_akhir' => 'nullable|numeric',
            'keterangan_evaluasi' => 'nullable|string',
            'keterangan_penugasan' => 'nullable|string',
            'periode_penugasan' => 'nullable|string|max:255',
            'alasan' => 'nullable|string',
            'foto_profil' => 'nullable|string',
            'foto_file' => 'nullable|image|max:5120',
        ];

        $months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];
        foreach ($months as $m) {
            $rules["nilai_{$m}"] = 'nullable|numeric';
            $rules["catatan_mentor_{$m}"] = 'nullable|string';
            $rules["dev_suggestion_{$m}"] = 'nullable|string';
        }

        $validated = $request->validate($rules);
        unset($validated['foto_file']);

        // Rapikan teks multi-baris (Shift+Enter) menjadi daftar bernomor urut
        $listFields = [
            'histori_pekerjaan',
            'achievement',
            'strength',
            'weakness',
            'catatan_mentor_general',
            'development_suggestion_general',
        ];
        foreach ($listFields as $lf) {
            if (isset($validated[$lf])) {
                $validated[$lf] = $this->normalizeNumberedList($validated[$lf]);
            }
        }

        foreach ($months as $m) {
            if (isset($validated["catatan_mentor_{$m}"])) {
                $validated["catatan_mentor_{$m}"] = $this->normalizeNumberedList($validated["catatan_mentor_{$m}"]);
            }
            if (isset($validated["dev_suggestion_{$m}"])) {
                $validated["dev_suggestion_{$m}"] = $this->normalizeNumberedList($validated["dev_suggestion_{$m}"]);
            }
        }

        // Auto calculate Overall Progress if empty but components exist
        if (empty($validated['overall_progress'])) {
            $progs = array_filter([$validated['refreshment'] ?? null, $validated['softskill'] ?? null, $validated['ojt'] ?? null], fn ($v) => $v !== null && $v !== '');
            if (!empty($progs)) {
                $validated['overall_progress'] = round(array_sum($progs) / count($progs), 1);
            }
        }

        // Auto calculate Nilai Akhir: (OJT * 40%) + (Panelis * 60%)
        $hasOjt = isset($validated['nilai_ojt']) && $validated['nilai_ojt'] !== '' && is_numeric($validated['nilai_ojt']);
        $hasPanelis = isset($validated['nilai_evaluasi_panelis']) && $validated['nilai_evaluasi_panelis'] !== '' && is_numeric($validated['nilai_evaluasi_panelis']);

        if ($hasOjt || $hasPanelis) {
            $ojt = $hasOjt ? (float) $validated['nilai_ojt'] : 0;
            $panelis = $hasPanelis ? (float) $validated['nilai_evaluasi_panelis'] : 0;
            $validated['nilai_akhir'] = round(($ojt * 0.40) + ($panelis * 0.60), 2);

            $gradeInfo = ProfilTalent::getGradeInfo($validated['nilai_akhir']);
            if ($gradeInfo) {
                $validated['keterangan_evaluasi'] = $gradeInfo['title'] . ' (Grade ' . $gradeInfo['grade'] . ')';
            } else {
                $validated['keterangan_evaluasi'] = null;
            }
        } else {
            $validated['nilai_ojt'] = null;
            $validated['nilai_evaluasi_panelis'] = null;
            $validated['nilai_akhir'] = null;
            $validated['keterangan_evaluasi'] = null;
        }

        return $validated;
    }

    /**
     * Standard list of headers for Template and Export.
     */
    protected function getColumnHeaderList(): array
    {
        $headers = [
            'No', 'Employee ID', 'Nama', 'Profilling', 'Umur', 'Alumni', 'Status', 'Agama',
            'Talent', 'Jabatan', 'Talent to', 'Status OJT', 'Lokasi OJT', 'Region', 'Mentor',
            'Mentor FRLC', 'Masa Kerja', 'Histori Pekerjaan', 'Achievement', 'Keterangan',
            'Periode Penugasan', 'Alasan', 'Refreshment', 'Softskill', 'OJT', 'Overall Progress',
            'Teknis', 'Leadership', 'Training', 'Catatan Mentor',
            // Nilai Jan - Des
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Strength', 'Weakness', 'Development Suggestion',
            'Nilai OJT', 'Nilai Evaluasi Panelis', 'Nilai Ahir', 'Keterangan Evaluasi', 'Foto Profil',
        ];

        // Catatan & Suggestion per Bulan
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        foreach ($months as $m) {
            $headers[] = "Catatan Mentor {$m}";
            $headers[] = "Development Suggestion {$m}";
        }

        return $headers;
    }

    /**
     * Rapikan baris teks (Shift+Enter / Alt+Enter) menjadi array list bernomor (1. ..., 2. ...)
     */
    protected function splitNumberedList(?string $text): array
    {
        if (empty($text)) {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($text));
        $result = [];
        $no = 1;

        foreach ($lines as $line) {
            $clean = trim($line);
            if ($clean === '') {
                continue;
            }

            // Bersihkan nomor atau bullet lama jika ada (1., 1), -, *, •)
            $clean = preg_replace('/^(\d+[\.\-\)]|\-|\*|•)\s*/u', '', $clean);

            $result[] = "{$no}. {$clean}";
            $no++;
        }

        return $result;
    }

    /**
     * Rapikan teks dari Excel / input menjadi string list bernomor urut dengan spasi antar poin.
     */
    protected function normalizeNumberedList(?string $text): ?string
    {
        $list = $this->splitNumberedList($text);
        return !empty($list) ? implode("\n\n", $list) : null;
    }
}
