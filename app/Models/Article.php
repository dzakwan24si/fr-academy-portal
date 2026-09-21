<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'Artikel';
    protected $fillable = [
        'title', 'slug', 'content', 'category_id', 'tags',
        'featured_image', 'status', 'published_at', 'author_id'
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
