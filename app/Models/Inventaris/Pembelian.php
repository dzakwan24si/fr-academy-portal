<?php

namespace App\Models\Inventaris;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Inventaris Pembelian';

    protected $table = 'pembelian';

    protected $fillable = [
        'id',
        'barang_id',
        'tanggal_pembelian',
        'jumlah',
        'lokasi_spesifik',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'jumlah' => 'integer',
    ];

    /**
     * Relationship to Barang.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
