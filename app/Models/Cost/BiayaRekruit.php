<?php

namespace App\Models\Cost;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaRekruit extends Model
{
    use HasFactory;

    protected $table = 'biaya_rekruit';
    protected $primaryKey = 'id_biaya_rekruit';

    protected $fillable = [
        'tahun',
        'tanggal',
        'nama',
        'total_biaya',
        'jenis_rekruit',
        'penanggung_jawab',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal' => 'date',
        'total_biaya' => 'float',
    ];
}
