<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PortalProgram extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_reguler' => 'boolean',
        'is_active' => 'boolean',
        'curriculum_pillars' => 'array',
        'objectives' => 'array',
        'learning_method' => 'array',
        'order' => 'integer',
    ];

    /**
     * Resolves the proper public URL for the program image / poster.
     */
    public function getResolvedImageUrl(): string
    {
        if (empty($this->image)) {
            return asset('images/programs/fat_estate.jpg');
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'portal_programs/')) {
            return Storage::disk('public')->url($this->image);
        }

        return asset(ltrim($this->image, '/'));
    }

    /**
     * Resolves the proper public URL for the hero background image.
     */
    public function getResolvedHeroImageUrl(): string
    {
        if (empty($this->hero_image)) {
            return $this->getResolvedImageUrl();
        }

        if (str_starts_with($this->hero_image, 'http://') || str_starts_with($this->hero_image, 'https://')) {
            return $this->hero_image;
        }

        if (str_starts_with($this->hero_image, 'portal_programs/')) {
            return Storage::disk('public')->url($this->hero_image);
        }

        return asset(ltrim($this->hero_image, '/'));
    }
}
