<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Moa extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
