<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vra extends Model
{
    use HasFactory;

    protected $table = 'vra';
    protected $primaryKey = 'vra_id';

    protected $fillable = [
        'nomor_polisi',
        'kendaraan',
        'bulan',
        'upah_bulan',
        'upah_tahun',
        'BBM_bulan',
        'BBM_tahun',
        'preventif_maintenance_bulan',
        'preventif_maintenance_tahun',
        'biaya_lainnya_bulan',
        'biaya_lainnya_tahun',
        'total_biaya_bulan',
        'total_biaya_tahun',
        'KM_awal',
        'KM_akhir',
        'KM_bulan',
        'KM_tahun',
        'tahun',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'upah_bulan' => 'float',
        'upah_tahun' => 'float',
        'BBM_bulan' => 'float',
        'BBM_tahun' => 'float',
        'preventif_maintenance_bulan' => 'float',
        'preventif_maintenance_tahun' => 'float',
        'biaya_lainnya_bulan' => 'float',
        'biaya_lainnya_tahun' => 'float',
        'total_biaya_bulan' => 'float',
        'total_biaya_tahun' => 'float',
        'KM_awal' => 'float',
        'KM_akhir' => 'float',
        'KM_bulan' => 'float',
        'KM_tahun' => 'float',
    ];
}
