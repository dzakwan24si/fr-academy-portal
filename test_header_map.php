<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use Illuminate\Support\Facades\DB;

$csvPath = __DIR__ . '/storage/app/alumni_monitoring.csv';
$handle = fopen($csvPath, 'r');
$header = fgetcsv($handle, 0, ';', '"', '\\');

// Clean header
$cleanHeaders = [];
foreach ($header as $col) {
    $c = strtolower(trim($col));
    $c = preg_replace('/[\s_\-\(\)\/\\\.]/', '', $c);
    $c = str_replace(["\xEF\xBB\xBF", "\xEF", "\xBB", "\xBF"], '', $c);
    $cleanHeaders[] = $c;
}

echo "Clean headers count: " . count($cleanHeaders) . "\n";

$headerMap = [
    'no' => ['no', 'nomor'],
    'nik' => ['niktraineektp', 'niktrainee', 'nikktp', 'nik'],
    'nama' => ['nama', 'namalengkap', 'namapeserta', 'namatrainee'],
    'jenis_kelamin' => ['jeniskelamin', 'jk', 'gender'],
    'tempat_lahir' => ['tempatlahir'],
    'tanggal_lahir' => ['tanggallahir', 'tgllahir'],
    'usia' => ['usia', 'umur'],
    'status_pernikahan' => ['statuspernikahan', 'statusnikah', 'maritalstatus', 'status'],
    'suku' => ['suku'],
    'agama' => ['agama'],
    'latar_belakang_ortu' => ['latarbelakangayahibu', 'latarbelakangortu', 'pekerjaanortu', 'latarbelakangorangtua'],
    'universitas' => ['alumni', 'universitas', 'kampus', 'perguruantinggi'],
    'jurusan' => ['jurusan', 'programstudi', 'prodi'],
    'ipk' => ['ipkkampus', 'ipk'],
    'tahun_lulus' => ['tahunluluskampus', 'tahunlulus'],
    'jenjang_pendidikan' => ['jenjangpendidikan', 'jenjang', 'pendidikanterakhir', 'pendidikan'],
    'no_ijazah' => ['noijazah', 'nomorijazah'],
    'asal_rekrut' => ['asalrekrut', 'lokasirekrut', 'kotarekrut'],
    'alamat_domisili' => ['domisili', 'alamatdomisili'],
    'no_ktp' => ['alamatktp', 'noktp', 'ktp'],
    'program' => ['program', 'namaprogram'],
    'batch' => ['batch', 'gelombang', 'angkatan'],
    'periode_pelatihan' => ['periodepelatihan', 'periode'],
    'tahun_angkatan' => ['tahun', 'tahunangkatan', 'tahuntraining'],
    'nilai_krepelin' => ['nilaikrepelinculturefit', 'nilaikrepelin', 'krepelin'],
    'nilai_ist' => ['nilaiistintelektualaq', 'nilaiist', 'ist'],
    'nilai_disc' => ['nilaidisckepribadian', 'nilaidisc', 'disc'],
    'catatan_interview' => ['catataninterview', 'interviewnotes'],
    'tanggal_mulai_training' => ['joindatetraining', 'tglmulaitraining', 'tanggalmulai'],
    'tanggal_selesai_training' => ['selesaitraining', 'tglselesaitraining', 'tanggalselesai'],
    'ranking_seleksi' => ['ranking', 'rankingseleksi'],
    'nilai_psikotest' => ['psikotest', 'nilaipsikotest'],
    'nilai_interview_hr' => ['interviewhr', 'nilaiinterviewhr'],
    'nilai_interview_user' => ['interviewuser', 'nilaiinterviewuser'],
    'status_mcu' => ['mcu', 'statusmcu'],
    'nilai_akademik' => ['akademik', 'nilaiakademik'],
    'nilai_praktek' => ['praktek', 'nilaipraktek'],
    'nilai_sikap' => ['sikap', 'nilaisikap'],
    'disiplin' => ['disiplin'],
    'keaktifan' => ['keaktifan'],
    'catatan_trainer' => ['catatantrainer', 'catatan'],
    'nilai_akhir_training' => ['nilaiakhirtraining', 'nilaiakhir'],
    'status_kelulusan' => ['statuskelulusan'],
    'tanggal_kelulusan' => ['tanggalkelulusan', 'tgllulus'],
    'jabatan_awal' => ['jabatan', 'jabatanawal'],
    'golongan_awal' => ['gol', 'golonganawal'],
    'pt_penempatan' => ['pt', 'penempatanpt', 'ptpenempatan'],
    'regional_penempatan_training' => ['regional', 'regionalpenempatantraining'],
    'unit_kerja_penempatan_training' => ['unitkerja', 'unitkerjapenempatantraining'],
    'nik_employee' => ['nikemployee', 'nikkaryawan'],
    'tanggal_sk_awal' => ['tanggalskawal', 'tglskawal'],
    'status_karyawan' => ['statuskaryawan'],
    'jabatan_terakhir' => ['jabatanterakhir', 'jabatansaatini'],
    'level_jabatan' => ['lvl', 'leveljabatan', 'level'],
    'golongan_terakhir' => ['golonganterakhir'],
    'unit_kerja_saat_ini' => ['unitkerjasaatini', 'unitkerja'],
    'region_saat_ini' => ['regionsaatini', 'region'],
    'atasan_langsung' => ['atasanlangsung', 'atasan'],
    'tanggal_pengangkatan' => ['tanggalpengangkatan', 'tglpengangkatan'],
    'status_resign' => ['statusresign'],
    'tanggal_resign' => ['tanggalresign', 'tglresign'],
    'alasan_resign' => ['keteranganresign', 'alasanresign'],
    'status_ijazah' => ['statusijazah'],
    'tanggal_pengambilan_ijazah' => ['tanggalpengembalianijazah', 'tanggalpengambilanijazah', 'tglambilijazah'],
    'penerima_ijazah' => ['penerimaijazah'],
    'no_tanda_terima_ijazah' => ['notandaterimaijazah', 'tandaterimaijazah'],
    'no_bpjs_tk' => ['nobpjstk', 'bpjstk'],
    'no_bpjs_kes' => ['nobpjskes', 'bpjskes'],
    'npwp' => ['npwp'],
    'keterangan' => ['keterangan', 'notes', 'catatan'],
];

$columnIndices = [];
$seenUnitKerja = 0;
$seenRegion = 0;
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
        if (in_array($colName, $aliases, true)) {
            if (!isset($columnIndices[$field])) {
                $columnIndices[$field] = $colIdx;
            }
        }
    }
}

echo "Mapped columns: " . count($columnIndices) . "\n";
foreach (['level_jabatan', 'keterangan', 'status_resign', 'tanggal_resign', 'program', 'nama'] as $f) {
    echo "  $f index: " . ($columnIndices[$f] ?? 'NOT FOUND') . "\n";
}
fclose($handle);
