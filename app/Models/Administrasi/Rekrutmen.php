<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekrutmen extends Model
{
    use HasFactory;

    protected $table = 'rekrutmen';

    protected $fillable = [
        'no',
        'lokasi_rekrut',
        'metode_rekrutmen',
        'program',
        'tanggal',
        'tahun',
        'jumlah_peserta_hadir',
        'jumlah_lolos_psikotest',
        'jumlah_hadir_interview_awal',
        'jumlah_lolos_interview_awal',
        'jumlah_lolos_interview_user',
        'jumlah_ikut_mcu',
        'jumlah_lolos_mcu',
        'join_fr',
        'persen_lulus',
    ];

    protected $casts = [
        'no' => 'integer',
        'tahun' => 'integer',
        'jumlah_peserta_hadir' => 'integer',
        'jumlah_lolos_psikotest' => 'integer',
        'jumlah_hadir_interview_awal' => 'integer',
        'jumlah_lolos_interview_awal' => 'integer',
        'jumlah_lolos_interview_user' => 'integer',
        'jumlah_ikut_mcu' => 'integer',
        'jumlah_lolos_mcu' => 'integer',
        'join_fr' => 'integer',
        'persen_lulus' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $hadir = (int) $model->jumlah_peserta_hadir;
            $join = (int) $model->join_fr;
            if ($hadir > 0) {
                $model->persen_lulus = round($join / $hadir, 6);
            } else {
                $model->persen_lulus = 0;
            }

            if (empty($model->metode_rekrutmen)) {
                $model->metode_rekrutmen = 'Offline';
            }

            if (empty($model->tahun) && !empty($model->tanggal)) {
                if (preg_match('/\b(20\d{2})\b/', $model->tanggal, $matches)) {
                    $model->tahun = (int) $matches[1];
                }
            }
        });
    }
}
