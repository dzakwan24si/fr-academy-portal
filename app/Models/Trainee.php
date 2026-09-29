<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Notifications\Notifiable;

class Trainee extends Model
{
    use HasApiTokens, HasFactory, HasPushSubscriptions, Notifiable;

    protected $guarded = ['id'];
    
    protected $appends = ['ui_data'];

    public function getUiDataAttribute()
    {
        return [
            'name' => $this->name,
            'program' => $this->trainingProgram->name ?? 'Program belum diset',
            'region' => $this->origin ?? $this->ojt_region,
            'unit' => $this->unit,
            'talent_to' => $this->talent_to,
            'university' => $this->university,
            'major' => $this->major,
            'status' => ucfirst($this->status),
            'initials' => strtoupper(substr($this->name, 0, 2))
        ];
    }

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }


    public function quizScores()
    {
        return $this->hasMany(QuizScore::class);
    }

    public function reportParticipants()
    {
        return $this->hasMany(ReportParticipant::class);
    }

    public function sessionParticipants()
    {
        return $this->hasMany(SessionParticipant::class);
    }

    public function rankings()
    {
        return $this->hasMany(Ranking::class);
    }

    public function ojtPlacements()
    {
        return $this->hasMany(OjtPlacement::class);
    }

    public function resignation()
    {
        return $this->hasOne(Resignation::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function taskSubmissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function evaluations()
    {
        return $this->hasMany(TraineeEvaluation::class);
    }
}
