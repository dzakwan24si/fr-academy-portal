<?php

namespace App\Models\Program;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class ProgramNonReguler extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'Program Non-Reguler';
    protected $table = 'program_non_reguler';
    protected $primaryKey = 'id_nonreguler';
    protected $guarded = [];
    protected $casts = [
        'tanggal_pelaksanaan' => 'array',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
    ];

    public function trainingRecords()
    {
        return $this->hasMany(TrainingRecord::class, 'program_id', 'id_nonreguler')
            ->where('program_type', 'non_reguler');
    }

    public function isSoftSkill(): bool
    {
        $kat = strtolower(trim(($this->kategori_pelatihan ?? '') . ' ' . ($this->sub_kategori ?? '') . ' ' . ($this->jenis_pelatihan ?? '')));
        return str_contains($kat, 'managerial') || str_contains($kat, 'leadership') || str_contains($kat, 'soft');
    }

    public function scopeSoftSkill($query)
    {
        return $query->where(function ($q) {
            $q->where('kategori_pelatihan', 'like', '%managerial%')
              ->orWhere('kategori_pelatihan', 'like', '%leadership%')
              ->orWhere('kategori_pelatihan', 'like', '%soft%');
        });
    }

    public function scopeTeknikal($query)
    {
        return $query->where(function ($q) {
            $q->where('kategori_pelatihan', 'not like', '%managerial%')
              ->orWhere('kategori_pelatihan', 'not like', '%leadership%')
              ->orWhere('kategori_pelatihan', 'not like', '%soft%');
        });
    }
}
