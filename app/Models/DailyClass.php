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
        'end_date',
        'start_time',
        'end_time',
        'room',
        'provider',
        'region',
        'trainer_name',
        'is_evaluation_open',
        'certificate_link',
        'qr_code_token',
        'material_url',
        'mess_url',
        'cert_signer_role',
        'cert_signer_name',
        'cert_description',
        'cert_date',
    ];
    
    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
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

