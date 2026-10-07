<?php

namespace App\Models\Monitoring;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringEmployee extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Monitoring Alumni';

    protected $table = 'monitoring_employees';

    protected $primaryKey = 'id_monitoring';

    protected $fillable = [
        'nik',
        'nama',
        'join_date',
        'jabatan',
        'level_jabatan',
        'angkatan_fr_academy',
        'kategori',
        'alumni',
        'suku',
        'agama',
        'lokasi_terakhir',
        'region_terakhir',
        'status',
        'date_terminate',
        'bulan_terminate',
        'tahun_terminate',
        'lama_bekerja_bulan',
        'lama_bekerja_tahun',
        'lama_bekerja',
        'alasan_resign',
        'detail',
        'lokasi_awal_penempatan',
        'region_awal_penempatan',
        'gm',
        'mk',
    ];

    /**
     * Map model attributes to uppercase keys expected by frontend components if needed.
     */
    public function toFrontendArray(): array
    {
        return [
            'ID_MONITORING' => $this->id_monitoring,
            'NIK' => $this->nik,
            'NAMA' => $this->nama,
            'JOIN DATE' => $this->join_date,
            'JABATAN' => $this->jabatan,
            'LEVEL JABATAN' => $this->level_jabatan,
            'ANGKATAN FR ACADEMY' => $this->angkatan_fr_academy,
            'KATEGORI' => $this->kategori,
            'ALUMNI' => $this->alumni,
            'SUKU' => $this->suku,
            'AGAMA' => $this->agama,
            'LOKASI TERAKHIR' => $this->lokasi_terakhir,
            'REGION TERAKHIR' => $this->region_terakhir,
            'STATUS' => $this->status,
            'DATE TERMINATE' => $this->date_terminate,
            'BULAN TERMINATE' => $this->bulan_terminate,
            'TAHUN TERMINATE' => $this->tahun_terminate,
            'LAMA BEKERJA (BULAN)' => $this->lama_bekerja_bulan,
            'LAMA BEKERJA (TAHUN)' => $this->lama_bekerja_tahun,
            'LAMA BEKERJA' => $this->lama_bekerja,
            'Alasan Resign' => $this->alasan_resign,
            'Detail' => $this->detail,
            'LOKASI AWAL PENEMPATAN' => $this->lokasi_awal_penempatan,
            'REGION AWAL PENEMPATAN' => $this->region_awal_penempatan,
            'GM' => $this->gm,
            'MK' => $this->mk,
        ];
    }
}
