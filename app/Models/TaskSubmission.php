<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'submitted_at' => 'datetime',
        'file_path' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
