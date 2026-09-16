<?php

namespace App\Models\Monitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asisten extends Model
{
    use HasFactory;

    protected $table = 'asisten';

    protected $fillable = [
        'no',
        // 1. Identitas Pribadi
        'nik',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'usia',
        'agama',
        'status_pernikahan',
        'no_hp',
        'email',
        'alamat_domisili',
        'no_ktp',
        // 2. Pendidikan
        'jenjang_pendidikan',
        'universitas',
        'fakultas',
        'jurusan',
        'ipk',
        'tahun_lulus',
        // 3. Rekrutmen & Program Training
        'asal_rekrut',
        'jalur_rekrutmen',
        'program',
        'batch',
        'tanggal_mulai_training',
        'tanggal_selesai_training',
        'tahun_angkatan',
        // 4. Penilaian Seleksi
        'nilai_psikotest',
        'nilai_interview_hr',
        'nilai_interview_user',
        'status_mcu',
        'ranking_seleksi',
        // 5. Progress & Kelulusan Training
        'nilai_akademik',
        'nilai_praktek',
        'nilai_sikap',
        'nilai_akhir_training',
        'status_kelulusan',
        'tanggal_kelulusan',
        // 6. Penempatan Awal (Setelah Training)
        'jabatan_awal',
        'pt_penempatan',
        'regional_penempatan_training',
        'unit_kerja_penempatan_training',
        'tanggal_sk_awal',
        // 7. Kepegawaian Saat Ini
        'status_karyawan',
        'jabatan_terakhir',
        'level_jabatan',
        'region_saat_ini',
        'unit_kerja_saat_ini',
        'atasan_langsung',
        'tanggal_pengangkatan',
        // 8. Resign & Ijazah
        'status_resign',
        'tanggal_resign',
        'alasan_resign',
        'status_ijazah',
        'tanggal_pengambilan_ijazah',
        'no_tanda_terima_ijazah',
        // 9. Lainnya
        'no_bpjs_tk',
        'no_bpjs_kes',
        'npwp',
        'keterangan',
    ];

    protected $casts = [
        'no' => 'integer',
        'usia' => 'integer',
        'tahun_lulus' => 'integer',
        'tahun_angkatan' => 'integer',
        'ipk' => 'float',
        'nilai_psikotest' => 'float',
        'nilai_interview_hr' => 'float',
        'nilai_interview_user' => 'float',
        'nilai_akademik' => 'float',
        'nilai_praktek' => 'float',
        'nilai_sikap' => 'float',
        'nilai_akhir_training' => 'float',
        'ranking_seleksi' => 'integer',
    ];

    protected $appends = [
        'unit_kerja_display',
        'is_unit_kerja_fallback',
    ];

    /**
     * Get computed Unit Kerja (Utamakan Unit Kerja Saat Ini, fallback ke Unit Kerja Penempatan Training)
     */
    public function getUnitKerjaDisplayAttribute(): string
    {
        if (!empty(trim($this->unit_kerja_saat_ini ?? ''))) {
            return trim($this->unit_kerja_saat_ini);
        }

        if (!empty(trim($this->unit_kerja_penempatan_training ?? ''))) {
            return trim($this->unit_kerja_penempatan_training);
        }

        return '-';
    }

    /**
     * Flag whether the displayed Unit Kerja is from fallback (Penempatan Training)
     */
    public function getIsUnitKerjaFallbackAttribute(): bool
    {
        return empty(trim($this->unit_kerja_saat_ini ?? '')) && !empty(trim($this->unit_kerja_penempatan_training ?? ''));
    }

    /**
     * Normalize Keterangan to: 'Aktif', 'Resign', or 'Gagal'
     */
    public function getKeteranganAttribute($val): string
    {
        $v = strtolower(trim((string) $val));
        if (str_contains($v, 'gagal')) {
            return 'Gagal';
        }
        if (str_contains($v, 'resign') || str_contains($v, 'phk')) {
            return 'Resign';
        }
        return 'Aktif';
    }

    public function setKeteranganAttribute($val): void
    {
        $v = strtolower(trim((string) $val));
        if (str_contains($v, 'gagal')) {
            $this->attributes['keterangan'] = 'Gagal';
        } elseif (str_contains($v, 'resign') || str_contains($v, 'phk')) {
            $this->attributes['keterangan'] = 'Resign';
        } else {
            $this->attributes['keterangan'] = 'Aktif';
        }
    }
}
