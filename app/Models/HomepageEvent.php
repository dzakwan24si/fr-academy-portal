<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageEvent extends Model
{
    protected $fillable = ['name', 'image_path', 'is_active'];
}
