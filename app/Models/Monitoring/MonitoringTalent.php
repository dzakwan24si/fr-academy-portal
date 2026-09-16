<?php

namespace App\Models\Monitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringTalent extends Model
{
    use HasFactory;

    protected $table = 'monitoring_talents';

    protected $fillable = [
        'no',
        'employee_id',
        'employee_name',
        'posisi_asal',
        'talent_to',
        'unit',
        'region',
        'mentor_lapangan',
        'mentor_frlc',
        'status',
        'keterangan',
        'periode_penugasan_lama',
        'alasan',
        'periode_penugasan_baru',
        'lokasi_penugasan_baru',
        'catatan_tambahan',
        'kategori',
    ];

    /**
     * Scope filter by category.
     */
    public function scopeCategory($query, $category)
    {
        if (!empty($category) && $category !== 'all') {
            return $query->where('kategori', $category);
        }
        return $query;
    }

    /**
     * Scope filter by region.
     */
    public function scopeRegion($query, $region)
    {
        if (!empty($region) && $region !== 'all') {
            return $query->where('region', $region);
        }
        return $query;
    }

    /**
     * Scope filter by status.
     */
    public function scopeStatus($query, $status)
    {
        if (!empty($status) && $status !== 'all') {
            return $query->where('status', 'like', "%{$status}%");
        }
        return $query;
    }
}
