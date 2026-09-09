<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainees()
    {
        return $this->hasMany(Trainee::class);
    }

    public function weeklyReports()
    {
        return $this->hasMany(WeeklyReport::class);
    }

    public function moas()
    {
        return $this->hasMany(Moa::class);
    }
}
