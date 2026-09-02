<?php

namespace App\Models\Administrasi;

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
        'jumlah_baik',
        'jumlah_rusak_ringan',
        'jumlah_rusak_berat',
        'jumlah_hilang',
        'grand_total',
        'keterangan',
        'foto_kerusakan',
    ];

    protected $casts = [
        'tanggal_stock_opname' => 'date',
        'jumlah_baik' => 'integer',
        'jumlah_rusak_ringan' => 'integer',
        'jumlah_rusak_berat' => 'integer',
        'jumlah_hilang' => 'integer',
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
            $model->grand_total = ($model->jumlah_baik ?? 0)
                + ($model->jumlah_rusak_ringan ?? 0)
                + ($model->jumlah_rusak_berat ?? 0)
                + ($model->jumlah_hilang ?? 0);
        });
    }
}
