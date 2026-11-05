<?php

namespace Database\Seeders;

use App\Models\Talent\ProfilTalent;
use App\Services\SpreadsheetReader;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProfilTalentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('data/Data_Profil_Talent.csv');
        if (!File::exists($csvPath)) {
            return;
        }

        $rows = SpreadsheetReader::read($csvPath, 'csv');
        if (empty($rows) || count($rows) < 2) {
            return;
        }

        $rawHeaders = array_shift($rows);
        $headerMap = [];
        foreach ($rawHeaders as $idx => $h) {
            $norm = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $h)));
            $headerMap[$norm] = $idx;
        }

        $months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];

        foreach ($rows as $row) {
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

            $nama = $get(['Nama', 'Nama Talent', 'Employee Name']);
            if (empty($nama)) {
                continue;
            }

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

            $data = [
                'no' => is_numeric($get(['No'])) ? (int) $get(['No']) : null,
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
                'histori_pekerjaan' => $get(['Histori Pekerjaan', 'Riwayat Pekerjaan']),
                'achievement' => $get(['Achievement', 'Prestasi']),
                'keterangan_penugasan' => $get(['Keterangan']),
                'periode_penugasan' => $get(['Periode Penugasan']),
                'alasan' => $get(['Alasan']),
                'refreshment' => $parsePercent(['Refreshment', 'Refreshment (%)']),
                'softskill' => $parsePercent(['Softskill', 'Soft Skill']),
                'ojt' => $parsePercent(['OJT', 'OJT Task']),
                'overall_progress' => $parsePercent(['Overall Progress', 'Overall']),
                'teknis' => $get(['Teknis']),
                'leadership' => $get(['Leadership']),
                'training' => $get(['Training']),
                'catatan_mentor_general' => $get(['Catatan Mentor']),
                'strength' => $get(['Strength']),
                'weakness' => $get(['Weakness']),
                'development_suggestion_general' => $get(['Development Suggestion']),
                'nilai_ojt' => is_numeric($get(['Nilai OJT'])) ? (float) $get(['Nilai OJT']) : null,
                'nilai_evaluasi_panelis' => is_numeric($get(['Nilai Evaluasi Panelis', 'Nilai Panelis'])) ? (float) $get(['Nilai Evaluasi Panelis', 'Nilai Panelis']) : null,
                'nilai_akhir' => is_numeric($get(['Nilai Ahir', 'Nilai Akhir'])) ? (float) $get(['Nilai Ahir', 'Nilai Akhir']) : null,
                'keterangan_evaluasi' => $get(['Keterangan Evaluasi']),
                'foto_profil' => $get(['Foto Profil', 'Foto', 'Photo']),
            ];

            // Monthly Scores & Notes
            foreach ($months as $m) {
                $valScore = $get([$m]);
                $data["nilai_{$m}"] = (is_numeric($valScore)) ? (float) $valScore : null;

                $catatan = $get(["Catatan Mentor {$m}", "Catatan {$m}"]);
                $data["catatan_mentor_{$m}"] = !empty($catatan) ? $catatan : null;

                $suggestion = $get(["Development Suggestion {$m}", "Suggestion {$m}", "Saran {$m}"]);
                $data["dev_suggestion_{$m}"] = !empty($suggestion) ? $suggestion : null;
            }

            ProfilTalent::updateOrCreate(
                ['employee_id' => $data['employee_id']],
                $data
            );
        }
    }
}
