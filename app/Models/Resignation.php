<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
