<?php

namespace App\Models\Monitoring;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringAfdeling extends Model
{
    use HasFactory;

    protected $table = 'monitoring_afdeling';
    protected $primaryKey = 'afeliding_id';

    protected $fillable = [
        'region',
        'total',
    ];

    public function toFrontendArray(): array
    {
        return [
            'afeliding_id' => $this->afeliding_id,
            'Region' => $this->region,
            'Total' => (int) $this->total,
        ];
    }
}
