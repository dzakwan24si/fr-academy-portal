<?php

namespace App\Models\Inventaris;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';

    protected $fillable = [
        'barang_id',
        'tanggal_stock_opname',
        'status',
        'catatan_sesi',
        'lokasi_spesifik',
        'jumlah_baik',
        'jumlah_rusak',
        'grand_total',
        'keterangan',
        'foto_kerusakan',
    ];

    protected $casts = [
        'tanggal_stock_opname' => 'date',
        'jumlah_baik' => 'integer',
        'jumlah_rusak' => 'integer',
        'grand_total' => 'integer',
    ];

    /**
     * Relationship to Barang.
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    /**
     * Automatically compute grand_total before saving.
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            $model->grand_total = ($model->jumlah_baik ?? 0) + ($model->jumlah_rusak ?? 0);
        });
    }
}
