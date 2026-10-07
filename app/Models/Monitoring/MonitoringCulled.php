<?php

namespace App\Models\Monitoring;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringCulled extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Monitoring Culled';

    protected $table = 'monitoring_culled';

    protected $primaryKey = 'id_culled';

    protected $fillable = [
        'id_angkatan',
        'nama',
        'alasan',
        'kategori_status',
        'jenis_culled',
    ];

    public function angkatan()
    {
        return $this->belongsTo(MonitoringAngkatan::class, 'id_angkatan', 'id_angkatan');
    }

    public function toFrontendArray(): array
    {
        return [
            'id_culled' => $this->id_culled,
            'id_angkatan' => $this->id_angkatan,
            'nama' => $this->nama,
            'alasan' => $this->alasan,
            'kategori_status' => $this->kategori_status,
            'Jenis_culled' => $this->jenis_culled,
            'angkatan' => $this->angkatan ? $this->angkatan->toFrontendArray() : null,
        ];
    }
}
