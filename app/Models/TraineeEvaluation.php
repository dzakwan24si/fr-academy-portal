<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TraineeEvaluation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'answers' => 'array',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function dailyClass()
    {
        return $this->belongsTo(DailyClass::class);
    }
}
