<?php

namespace App\Models\Monitoring;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringAngkatan extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Monitoring Angkatan';

    protected $table = 'monitoring_angkatan';

    protected $primaryKey = 'id_angkatan';

    protected $fillable = [
        'angkatan',
        'jumlah_awal',
        'lulus',
        'resign_in_class',
        'resign_ojt',
        'tidak_lulus',
    ];

    public function culleds()
    {
        return $this->hasMany(MonitoringCulled::class, 'id_angkatan', 'id_angkatan');
    }

    public function toFrontendArray(): array
    {
        $jumlahAwal = (int) $this->jumlah_awal;
        $resignInClass = (int) $this->resign_in_class;
        $resignOjt = (int) $this->resign_ojt;
        $tidakLulus = (int) $this->tidak_lulus;
        $lulus = max($jumlahAwal - $resignInClass - $resignOjt - $tidakLulus, 0);

        return [
            'id_angkatan' => $this->id_angkatan,
            'angkatan' => $this->angkatan,
            'Jumlah Awal' => $jumlahAwal,
            'lulus' => $this->lulus ?: $lulus,
            'Resign_In_Class' => $resignInClass,
            'Resign_OJT' => $resignOjt,
            'tidak_lulus' => $tidakLulus,
            'lulus_calculated' => $lulus,
            'culled_calculated' => $resignInClass + $resignOjt,
            'tidak_lulus_calculated' => $tidakLulus,
            'resign_in_class_calculated' => $resignInClass,
            'resign_ojt_calculated' => $resignOjt,
        ];
    }
}
