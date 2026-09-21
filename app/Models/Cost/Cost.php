<?php

namespace App\Models\Cost;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'Cost';
    protected $table      = 'costs';
    protected $primaryKey = 'cost_id';

    protected $fillable = [
        'cost_id',
        'GL_ACCOUNT',
        'jenis_cost',
        'account_name',
        'tahun',
        'bulan',
        'budget',
        'realisasi',
        'budget_tahunan',
        'kategori',
        'sub_kategori',
        'nama_item',
        'keterangan',
        'tanggal',
        'no_bukti',
    ];

    protected $casts = [
        'tahun'          => 'integer',
        'bulan'          => 'integer',
        'tanggal'        => 'date',
        'budget'         => 'decimal:2',
        'realisasi'      => 'decimal:2',
        'budget_tahunan' => 'decimal:2',
    ];

    /** Human-readable month name (Indonesian) */
    public function getBulanNamaAttribute(): string
    {
        $names = ['','Januari','Februari','Maret','April','Mei','Juni',
                  'Juli','Agustus','September','Oktober','November','Desember'];
        return $names[$this->bulan] ?? '';
    }
}
