<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'week_start_date' => 'date',
        'week_end_date'   => 'date',
        'strip_photos'    => 'array',
    ];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function trainingSessions()
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function quizScores()
    {
        return $this->hasMany(QuizScore::class);
    }

    public function rankings()
    {
        return $this->hasMany(Ranking::class);
    }

    public function resignations()
    {
        return $this->hasMany(Resignation::class);
    }

    public function parentReport()
    {
        return $this->belongsTo(WeeklyReport::class, 'parent_report_id');
    }

    public function mergedFrom()
    {
        return $this->hasMany(WeeklyReport::class, 'parent_report_id');
    }
}
