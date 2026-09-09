<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function quizScores()
    {
        return $this->hasMany(QuizScore::class);
    }

    public function reportParticipants()
    {
        return $this->hasMany(ReportParticipant::class);
    }
}
