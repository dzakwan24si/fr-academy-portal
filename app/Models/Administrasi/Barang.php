<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $fillable = [
        'nama_asset',
        'lokasi',
        'satuan',
        'keterangan',
    ];

    /**
     * Relationship to Pembelian history.
     */
    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'barang_id');
    }

    /**
     * Relationship to Stock Opname history.
     */
    public function stockOpname()
    {
        return $this->hasMany(StockOpname::class, 'barang_id');
    }

    /**
     * Helper to get latest stock opname condition.
     */
    public function latestStockOpname()
    {
        return $this->hasOne(StockOpname::class, 'barang_id')->latestOfMany('tanggal_stock_opname');
    }
}
