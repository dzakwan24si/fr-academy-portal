<?php

namespace App\Models\Monitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenProgramMonitoring extends Model
{
    use HasFactory;

    protected $table = 'asisten_program_monitoring';

    protected $fillable = [
        'no',
        'kode_kategori',
        'kategori_program',
        'nama_program',
        'tahun_rekrut',
        'tahun_training',
        'tahun_lulus',
        'jumlah_trainee',
        'jumlah_culled',
        'catatan',
    ];

    protected $casts = [
        'no' => 'integer',
        'tahun_rekrut' => 'integer',
        'tahun_training' => 'integer',
        'tahun_lulus' => 'integer',
        'jumlah_trainee' => 'integer',
        'jumlah_culled' => 'integer',
    ];
}
