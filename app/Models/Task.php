<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function dailyClass()
    {
        return $this->belongsTo(DailyClass::class);
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }
}
