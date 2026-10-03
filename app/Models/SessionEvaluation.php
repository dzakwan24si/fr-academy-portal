<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionEvaluation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class);
    }
}
