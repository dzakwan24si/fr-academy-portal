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

    /**
     * Total Penggunaan Cuti dari rincian bulanan Jan - Des
     */
    public function getTotalBulananAttribute(): int
    {
        return (int) (
            $this->jan + $this->feb + $this->mar + $this->apr +
            $this->mei + $this->jun + $this->jul + $this->agu +
            $this->sep + $this->okt + $this->nov + $this->des
        );
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
     * Cari histori sisa cuti dari 2 tahun sebelumnya
     */
    public static function getHistoricalCarryover(string $nik, int $currentYear): int
    {
        // Tahun 2 periode lalu adalah (currentYear - 2)
        $pastRecord = self::where('nik', $nik)
            ->where('tahun', $currentYear - 2)
            ->first();

        if ($pastRecord) {
            return $pastRecord->sisa_cuti;
        }

        return 0;
    }
}
