<?php

namespace App\Models\Cost;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Capex extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'CAPEX';

    protected $table = 'capex';

    protected $primaryKey = 'capex_id';

    protected $fillable = [
        'tahun',
        'jenis_capex',
        'jenis_catatan',
        'nama_barang',
        'type_merk',
        'satuan',
        'jumlah_unit',
        'harga_satuan',
        'total_rab',
        'tanggal',
        'nama_transaksi',
        'debit',
        'kredit',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_unit' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_rab' => 'decimal:2',
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];
}
