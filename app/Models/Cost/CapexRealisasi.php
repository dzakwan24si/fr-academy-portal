<?php

namespace App\Models\Cost;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapexRealisasi extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Capex Realisasi';

    protected $table = 'capex_realisasi';
    protected $primaryKey = 'realisasi_id';

    protected $fillable = [
        'realisasi_id',
        'rab_id',
        'tahun',
        'lokasi',
        'kel_barang_kapital',
        'foto_barang',
        'nama_barang',
        'type',
        'tanggal',
        'debit',
        'kredit',
        'total_rp',
    ];

    protected $casts = [
        'tahun'    => 'integer',
        'tanggal'  => 'date',
        'debit'    => 'float',
        'kredit'   => 'float',
        'total_rp' => 'float',
    ];

    public function rab()
    {
        return $this->belongsTo(CapexRab::class, 'rab_id', 'rab_id');
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->total_rp = (float) $model->debit - (float) $model->kredit;
        });
    }
}
