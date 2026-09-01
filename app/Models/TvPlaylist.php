<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvPlaylist extends Model
{
    protected $fillable = ['article_id', 'duration_seconds', 'order'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
