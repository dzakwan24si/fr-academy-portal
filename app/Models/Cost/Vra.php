<?php

namespace App\Models\Cost;

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
        'tahun',
        'upah',
        'BBM',
        'BBM_tahun',
        'preventif_maintenance',
        'biaya_lainnya',
        'total_biaya_bulan',
        'KM_awal',
        'KM_akhir',
        'KM',
        'BBM_liter',
        'BBM_KM_liter',
        'VRA',
        'budget_tahun',
    ];

    protected $casts = [
        'bulan' => 'integer',
        'tahun' => 'integer',
        'upah' => 'float',
        'BBM' => 'float',
        'BBM_tahun' => 'float',
        'preventif_maintenance' => 'float',
        'biaya_lainnya' => 'float',
        'total_biaya_bulan' => 'float',
        'KM_awal' => 'float',
        'KM_akhir' => 'float',
        'KM' => 'float',
        'BBM_liter' => 'float',
        'BBM_KM_liter' => 'float',
        'VRA' => 'float',
        'budget_tahun' => 'float',
    ];

    /**
     * Indonesian Month Name
     */
    public function getBulanNamaAttribute(): string
    {
        $names = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $names[$this->bulan] ?? '-';
    }
}
