<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);

        return $setting !== null ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );
    }

    /**
     * Get image URL with fallback to default asset.
     */
    public static function getImageUrl(string $key, string $default): string
    {
        $val = static::get($key);
        if (! $val) {
            return str_starts_with($default, 'http://') || str_starts_with($default, 'https://')
                ? $default
                : asset($default);
        }
        if (str_starts_with($val, 'http://') || str_starts_with($val, 'https://')) {
            return $val;
        }

        return Storage::url($val);
    }

    /**
     * Format a slide path to full URL.
     */
    public static function formatSlideUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        if (str_starts_with($path, 'images/')) {
            return asset($path);
        }

        return Storage::url($path);
    }

    /**
     * Get hero slides array (full URLs for display).
     * Supports unlimited dynamic slides.
     */
    public static function getHeroSlides(): array
    {
        $raw = static::getRawHeroSlides();

        return array_map([static::class, 'formatSlideUrl'], $raw);
    }

    /**
     * Get raw hero slide paths.
     */
    public static function getRawHeroSlides(): array
    {
        $val = static::get('home_hero_slides');
        if ($val) {
            $decoded = json_decode($val, true);
            if (is_array($decoded) && ! empty($decoded)) {
                return array_values($decoded);
            }
        }

        // Backward compatibility if home_hero_slide_1..4 were set
        $legacy = [];
        for ($i = 1; $i <= 4; $i++) {
            $path = static::get("home_hero_slide_{$i}");
            if ($path) {
                $legacy[] = $path;
            }
        }

        if (! empty($legacy)) {
            return $legacy;
        }

        // Default 4 slides
        return [
            'images/welcomefr.jpg',
            'images/slide2.png',
            'images/slide3.png',
            'images/slide4.png',
        ];
    }
}
