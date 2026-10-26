<?php

namespace App\Models\Talent;

use App\Services\RegionHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TalentPool extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Talent Pool';

    protected $table = 'talent_pools';

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
        'lokasi_penempatan',
        'tanggal_penempatan',
        'kategori',
    ];

    protected $casts = [
        'tanggal_penempatan' => 'date',
    ];

    /**
     * Scope filter by category.
     */
    public function scopeCategory($query, $category)
    {
        if (! empty($category) && $category !== 'all') {
            return $query->where('kategori', $category);
        }

        return $query;
    }

    /**
     * Scope filter by region keyword.
     */
    public function scopeRegion($query, $region)
    {
        return RegionHelper::applyQueryFilter($query, 'region', $region);
    }

    /**
     * Scope filter by status ('Sudah penempatan' atau 'Belum Penempatan').
     */
    public function scopeStatus($query, $status)
    {
        if (! empty($status) && $status !== 'all') {
            return $query->where('status', 'like', "%{$status}%");
        }

        return $query;
    }
}
