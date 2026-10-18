<?php

namespace App\Models\Program;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class ProgramReguler extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'Program Reguler';

    protected $table = 'program_reguler';

    protected $primaryKey = 'Program_reguler_id';

    protected $guarded = [];

    protected $casts = [
        'tanggal_pelaksanaan' => 'array',
        'Mulai_program' => 'date:Y-m-d',
        'Selesai_program' => 'date:Y-m-d',
        'kelas' => 'integer',
        'resign' => 'integer',
        'staff' => 'integer',
        'lulus' => 'integer',
    ];

    public function setStaffAttribute($value): void
    {
        $intVal = ($value !== null && $value !== '') ? (int) $value : null;
        $this->attributes['staff'] = $intVal;
        if (! isset($this->attributes['lulus']) || $this->attributes['lulus'] === null) {
            $this->attributes['lulus'] = $intVal;
        }
    }

    public function setLulusAttribute($value): void
    {
        $intVal = ($value !== null && $value !== '') ? (int) $value : null;
        $this->attributes['lulus'] = $intVal;
        if (! isset($this->attributes['staff']) || $this->attributes['staff'] === null) {
            $this->attributes['staff'] = $intVal;
        }
    }

    public function trainingRecords()
    {
        return $this->hasMany(TrainingRecord::class, 'program_id', 'Program_reguler_id')
            ->where('program_type', 'reguler');
    }

    public function isMandor(): bool
    {
        $prog = strtolower(trim(($this->jenis_program_reguler ?? '').' '.($this->Training_Program ?? '')));

        return str_contains($prog, 'mandor') || str_starts_with($prog, 'pmt') || str_starts_with($prog, 'pkt');
    }

    public function scopeStaf($query)
    {
        return $query->where(function ($q) {
            $q->where('jenis_program_reguler', 'like', '%staf%')
                ->orWhere(function ($sub) {
                    $sub->where('jenis_program_reguler', 'not like', '%mandor%')
                        ->where('Training_Program', 'not like', 'PMT%')
                        ->where('Training_Program', 'not like', 'PKT%');
                });
        });
    }

    public function scopeMandor($query)
    {
        return $query->where(function ($q) {
            $q->where('jenis_program_reguler', 'like', '%mandor%')
                ->orWhere('Training_Program', 'like', 'PMT%')
                ->orWhere('Training_Program', 'like', 'PKT%');
        });
    }
}
