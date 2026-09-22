<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_program_id',
        'title',
        'date',
        'trainer_name',
        'is_evaluation_open',
        'qr_code_token',
    ];
    
    protected $casts = [
        'date' => 'date',
        'is_evaluation_open' => 'boolean',
    ];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function traineeEvaluations()
    {
        return $this->hasMany(TraineeEvaluation::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
