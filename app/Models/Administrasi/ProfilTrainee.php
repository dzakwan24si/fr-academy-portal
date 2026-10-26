<?php

namespace App\Models\Administrasi;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilTrainee extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Profil Trainee';

    protected $table = 'profil_trainees';

    protected $fillable = [
        'trainee_id',
        'nama_trainee',
        'nama_jabatan',
        'asal_universitas',
        'jurusan',
        'jenis_kelamin',
        'usia',
        'suku',
        'agama',
        'pendidikan_terakhir',
        'tinggi_badan',
        'berat_badan',
        'daerah_kabupaten_asal',
        'kota_provinsi_asal',
        'tahun_lulus',
        'masa_studi',
        'ipk',
        'pengalaman_organisasi',
        'pengalaman_praktek_kerja_magang',
        'prestasi_diri',
        'hobi',
        'riwayat_kesehatan',
        'catatan_riwayat_kesehatan',
        'pekerjaan_orangtua',
    ];

    protected $casts = [
        'usia' => 'integer',
        'tinggi_badan' => 'float',
        'berat_badan' => 'float',
        'tahun_lulus' => 'integer',
        'ipk' => 'float',
    ];

    /**
     * Label for activity logging
     */
    protected static function getActivityModelLabel($model): string
    {
        return $model->nama_trainee . ($model->trainee_id ? " ({$model->trainee_id})" : '');
    }
}
