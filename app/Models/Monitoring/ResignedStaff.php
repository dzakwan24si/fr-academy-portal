<?php

namespace App\Models\Monitoring;

use Illuminate\Database\Eloquent\Model;

class ResignedStaff extends Model
{
    protected $table = 'resigned_staff';

    protected $fillable = [
        'employee_id',
        'nama',
        'join_date',
        'resign_date',
        'jabatan',
        'kebun',
        'deskripsi_resign',
        'jenis_resign',
        'cluster_resign',
        'alumni',
        'keterangan',
        'region',
    ];
}
