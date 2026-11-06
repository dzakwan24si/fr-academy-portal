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
        'featured_image', 'status', 'published_at', 'author_id',
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

    /**
     * Get rendered content with video embeds properly converted to iframe.
     */
    public function getRenderedContentAttribute(): string
    {
        $content = $this->content ?? '';

        // Convert any <video><source src="...youtube..."></video> into proper responsive iframe
        return preg_replace_callback(
            '/<video[^>]*>\s*<source[^>]*src="([^"]*(?:youtube\.com|youtu\.be)[^"]*)"[^>]*>\s*<\/video>/i',
            function ($matches) {
                $url = $matches[1];
                preg_match('/(?:youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*)/i', $url, $vidMatch);
                $videoId = $vidMatch[1] ?? '';
                $embedUrl = $videoId ? "https://www.youtube.com/embed/{$videoId}" : $url;

                return '<div class="video-container my-6 aspect-video w-full max-w-3xl mx-auto rounded-xl overflow-hidden shadow-md"><iframe width="100%" height="100%" src="'.htmlspecialchars($embedUrl).'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="w-full h-full aspect-video border-0"></iframe></div>';
            },
            $content
        );
    }
}
