<?php

namespace App\Models\Monitoring;

use App\Models\Talent\ProfilTalent;
use App\Services\RegionHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MonitoringTalent extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Monitoring Talent';

    protected $table = 'monitoring_talents';

    public static bool $isSyncDeleting = false;
    public static bool $isSyncUpdating = false;

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

    protected static function booted()
    {
        // Two-way sync: Sync status (status penugasan) changes to Profil Talent (status_ojt)
        static::updated(function ($talent) {
            if (static::$isSyncUpdating) {
                return;
            }

            if ($talent->wasChanged('status')) {
                $nik = trim((string) $talent->employee_id);
                $newStatus = $talent->status;
                if (!empty($nik) && $newStatus !== null) {
                    try {
                        ProfilTalent::$isSyncUpdating = true;
                        ProfilTalent::where('employee_id', $nik)->get()->each(function ($pt) use ($newStatus) {
                            $pt->update(['status_ojt' => $newStatus]);
                        });
                    } finally {
                        ProfilTalent::$isSyncUpdating = false;
                    }
                }
            }
        });

        // Two-way sync: Delete corresponding record in Profil Talent by employee_id (NIK)
        static::deleted(function ($talent) {
            // Invalidate monitoring dashboard cache whenever a talent is deleted
            Cache::put('dreal_cache_version', time());
            Cache::put('dashboard_cache_version', time());

            if (static::$isSyncDeleting) {
                return;
            }

            $nik = trim((string) $talent->employee_id);
            if (!empty($nik)) {
                try {
                    ProfilTalent::$isSyncDeleting = true;
                    ProfilTalent::where('employee_id', $nik)->get()->each(function ($pt) {
                        $pt->delete();
                    });
                } finally {
                    ProfilTalent::$isSyncDeleting = false;
                }
            }
        });
    }

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
     * Scope filter by status.
     */
    public function scopeStatus($query, $status)
    {
        if (! empty($status) && $status !== 'all') {
            return $query->where('status', 'like', "%{$status}%");
        }

        return $query;
    }
}
