<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    public function weeklyReport()
    {
        return $this->belongsTo(WeeklyReport::class);
    }

    public function sessionParticipants()
    {
        return $this->hasMany(SessionParticipant::class);
    }

    public function participants()
    {
        return $this->hasMany(SessionParticipant::class);
    }

    public function evaluation()
    {
        return $this->hasOne(SessionEvaluation::class);
    }

    public function evaluations()
    {
        return $this->hasMany(SessionEvaluation::class);
    }

    public function photos()
    {
        return $this->hasMany(SessionPhoto::class);
    }

    public function ojtPlacements()
    {
        return $this->hasMany(OjtPlacement::class);
    }
}
