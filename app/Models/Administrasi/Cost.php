<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    protected $table      = 'costs';
    protected $primaryKey = 'cost_id';

    protected $fillable = [
        'tahun',
        'bulan',
        'kategori',
        'sub_kategori',
        'nama_item',
        'keterangan',
        'budget',
        'realisasi',
        'tanggal',
        'no_bukti',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'budget'    => 'decimal:2',
        'realisasi' => 'decimal:2',
    ];

    /** Human-readable month name (Indonesian) */
    public function getBulanNamaAttribute(): string
    {
        $names = ['','Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];
        return $names[$this->bulan] ?? '';
    }
}
