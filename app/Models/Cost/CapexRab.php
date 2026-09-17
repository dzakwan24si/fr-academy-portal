<?php

namespace App\Models\Cost;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapexRab extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Capex RAB';

    protected $table = 'capex_rab';
    protected $primaryKey = 'rab_id';

    protected $fillable = [
        'rab_id',
        'tahun',
        'lokasi',
        'kel_barang_kapital',
        'foto_barang',
        'type',
        'satuan',
        'jumlah_unit',
        'harga_satuan',
        'total_rp',
    ];

    protected $casts = [
        'tahun'        => 'integer',
        'jumlah_unit'  => 'float',
        'harga_satuan' => 'float',
        'total_rp'     => 'float',
    ];

    public function realisasis()
    {
        return $this->hasMany(CapexRealisasi::class, 'rab_id', 'rab_id');
    }
}
