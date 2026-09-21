<?php

namespace App\Models\Program;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class RealisasiVsBatch extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'Realisasi vs Batch';
    protected $table = 'realisasi_vs_batch';
    protected $primaryKey = 'realisasi_id';
    protected $guarded = [];
}
