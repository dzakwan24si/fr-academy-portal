<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportParticipant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function weeklyReport()
    {
        return $this->belongsTo(WeeklyReport::class);
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
