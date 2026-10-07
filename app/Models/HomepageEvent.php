<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class HomepageEvent extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'Homepage Event';

    protected $fillable = ['name', 'image_path', 'is_active'];
}
