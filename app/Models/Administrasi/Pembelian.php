<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = [
        'barang_id',
        'tanggal_pembelian',
        'jumlah',
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
