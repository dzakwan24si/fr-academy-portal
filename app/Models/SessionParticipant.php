<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionParticipant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function trainingSession()
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
