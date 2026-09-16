<?php

namespace App\Models\Administrasi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';
    protected $primaryKey = 'cuti_id';

    protected $fillable = [
        'nik',
        'nama',
        'jabatan',
        'jenis_jabatan',
        'tmk',
        'tahun',
        'hak_cuti',
        'sisa_hak_cuti_carryover',
        'cuti_diambil',
        'jan',
        'feb',
        'mar',
        'apr',
        'mei',
        'jun',
        'jul',
        'agu',
        'sep',
        'okt',
        'nov',
        'des',
        'keterangan',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'hak_cuti' => 'integer',
        'sisa_hak_cuti_carryover' => 'integer',
        'cuti_diambil' => 'integer',
        'jan' => 'integer',
        'feb' => 'integer',
        'mar' => 'integer',
        'apr' => 'integer',
        'mei' => 'integer',
        'jun' => 'integer',
        'jul' => 'integer',
        'agu' => 'integer',
        'sep' => 'integer',
        'okt' => 'integer',
        'nov' => 'integer',
        'des' => 'integer',
        'tmk' => 'date',
    ];

    public const MONTHS = ['jan', 'feb', 'mar', 'apr', 'mei', 'jun', 'jul', 'agu', 'sep', 'okt', 'nov', 'des'];

    /**
     * Scope to order query by employee category and position hierarchy
     */
    public function scopeOrderedHierarchy($query)
    {
        $customOrderRaw = "CASE 
            WHEN jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%' THEN 1 
            WHEN jenis_jabatan LIKE '%PBT%' THEN 2 
            WHEN jenis_jabatan LIKE '%KHT%' THEN 3 
            ELSE 4 
        END ASC,
        CASE 
            -- 1. Staff Specific Position Hierarchy
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND jabatan LIKE '%Academy Director%' THEN 1
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND (jabatan LIKE '%Sr Manager L & D%' OR jabatan LIKE '%Sr Manager L&D%') THEN 2
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND jabatan LIKE '%Training Manager Downstream%' THEN 3
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND (jabatan LIKE '%L & D Manager%' OR jabatan LIKE '%L&D Manager%') THEN 4
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND jabatan LIKE '%Trainer%' THEN 5
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND (jabatan LIKE '%L&D Officer%' OR jabatan LIKE '%L & D Officer%') THEN 6
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND (jabatan LIKE '%Staf E-Learning%' OR jabatan LIKE '%E-Learning%') THEN 7
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND jabatan LIKE '%KTU%' THEN 8
            WHEN (jenis_jabatan LIKE '%Staf%' OR jenis_jabatan LIKE '%Staff%') AND (jabatan LIKE '%Recruitment%' OR jabatan LIKE '%Rekruitmen%') THEN 9

            -- 2. Karyawan PBT Specific Position Hierarchy
            WHEN jenis_jabatan LIKE '%PBT%' AND jabatan LIKE '%Driver Pool%' THEN 1
            WHEN jenis_jabatan LIKE '%PBT%' AND jabatan LIKE '%Koordinator Mess%' THEN 2
            WHEN jenis_jabatan LIKE '%PBT%' AND (jabatan LIKE '%Mandor Komp & Infrastruktur%' OR jabatan LIKE '%Mandor%') THEN 3

            -- 3. Karyawan KHT Specific Hierarchy
            WHEN jenis_jabatan LIKE '%KHT%' AND jabatan LIKE '%Pertamanan%' THEN 1
            WHEN jenis_jabatan LIKE '%KHT%' AND jabatan LIKE '%Krani Pembukuan%' THEN 2
            WHEN jenis_jabatan LIKE '%KHT%' AND jabatan LIKE '%Petugas Taman%' THEN 3
            WHEN jenis_jabatan LIKE '%KHT%' AND jabatan LIKE '%Krani Development%' THEN 4
            WHEN jenis_jabatan LIKE '%KHT%' AND jabatan LIKE '%Training Fasilitator%' THEN 5
            WHEN jenis_jabatan LIKE '%KHT%' AND jabatan LIKE '%Petugas Mess%' THEN 6
            WHEN jenis_jabatan LIKE '%KHT%' AND (jabatan LIKE '%Office Boy%' OR jabatan LIKE '%Office Girl%') THEN 7
            WHEN jenis_jabatan LIKE '%KHT%' AND jabatan LIKE '%Supir%' THEN 8

            ELSE 99
        END ASC,
        nama ASC";

        return $query->orderByRaw($customOrderRaw);
    }

    /**
     * Total Penggunaan Cuti dari rincian bulanan Jan - Des
     */
    public function getTotalBulananAttribute(): int
    {
        return (int) array_sum(array_map(fn($m) => (int) $this->$m, self::MONTHS));
    }

    /**
     * Hitung Sisa Cuti:
     * Sisa Cuti = Hak Cuti + Sisa Hak Cuti Carryover - Cuti yang Diambil
     */
    public function getSisaCutiAttribute(): int
    {
        return (int) ($this->hak_cuti + $this->sisa_hak_cuti_carryover - $this->cuti_diambil);
    }

    /**
     * Periode Hak Cuti (Tahun - 1)
     */
    public function getPeriodeHakCutiAttribute(): int
    {
        return (int) ($this->tahun - 1);
    }

    /**
     * Label Periode Sisa Hak Cuti (contoh: untuk tahun 2026 -> 2023-2024)
     */
    public function getPeriodeSisaCarryoverLabelAttribute(): string
    {
        $start = $this->tahun - 3;
        $end = $this->tahun - 2;
        return "{$start}–{$end}";
    }

    /**
     * Cari histori sisa cuti dari 2 tahun sebelumnya (misal untuk 2026: sisa cuti 2023 + sisa cuti 2024)
     */
    public static function getHistoricalCarryover(string $nik, int $currentYear): int
    {
        $year1 = $currentYear - 3;
        $year2 = $currentYear - 2;

        $record1 = self::where('nik', $nik)->where('tahun', $year1)->first();
        $record2 = self::where('nik', $nik)->where('tahun', $year2)->first();

        $sisa1 = $record1 ? $record1->sisa_cuti : 0;
        $sisa2 = $record2 ? $record2->sisa_cuti : 0;

        return (int) ($sisa1 + $sisa2);
    }
}
