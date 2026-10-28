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
        'tahun',
        'jenis_kategori',
    ];

    protected $casts = [
        'tanggal_penempatan' => 'date',
        'tahun' => 'integer',
    ];

    protected static function booted()
    {
        static::saving(function ($talent) {
            if (empty($talent->tahun)) {
                if (preg_match('/\b(20\d{2})\b/', (string) $talent->kategori, $m)) {
                    $talent->tahun = (int) $m[1];
                } elseif (! empty($talent->tanggal_penempatan)) {
                    $talent->tahun = (int) date('Y', strtotime($talent->tanggal_penempatan));
                } else {
                    $talent->tahun = (int) date('Y');
                }
            }
            if (empty($talent->jenis_kategori)) {
                $lowerCat = strtolower((string) $talent->kategori);
                if (str_contains($lowerCat, 'estate') || str_contains($lowerCat, 'kebun')) {
                    $talent->jenis_kategori = 'Estate';
                } elseif (str_contains($lowerCat, 'mill') || str_contains($lowerCat, 'mil') || str_contains($lowerCat, 'pks')) {
                    $talent->jenis_kategori = 'Mill';
                } elseif (str_contains($lowerCat, 'traksi')) {
                    $talent->jenis_kategori = 'Traksi';
                } elseif (str_contains($lowerCat, 'administrasi') || str_contains($lowerCat, 'admin')) {
                    $talent->jenis_kategori = 'Administrasi';
                } elseif (str_contains($lowerCat, 'downstream')) {
                    $talent->jenis_kategori = 'Downstream';
                } else {
                    $talent->jenis_kategori = 'Estate';
                }
            }
        });
    }

    /**
     * Scope filter by year.
     */
    public function scopeYear($query, $year)
    {
        if (! empty($year) && $year !== 'all') {
            return $query->where('tahun', (int) $year);
        }

        return $query;
    }

    /**
     * Scope filter by jenis kategori.
     */
    public function scopeJenisKategori($query, $jenisKategori)
    {
        if (! empty($jenisKategori) && $jenisKategori !== 'all') {
            return $query->where('jenis_kategori', $jenisKategori);
        }

        return $query;
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
