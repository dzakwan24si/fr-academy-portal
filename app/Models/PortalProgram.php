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
     * Resolves the menu key (reguler, managerial, estate, mill, traksi, downstream, administrasi, talent)
     */
    public function getMenuKeyAttribute(): string
    {
        if ($this->is_reguler || in_array($this->category_key, ['trainee', 'reguler'])) {
            return 'reguler';
        }
        if ($this->category_key === 'estate' || str_contains($this->slug, 'estate') || in_array($this->slug, ['klinik-kerja-mandor', 'sistem-pengancakan-panen'])) {
            return 'estate';
        }
        if ($this->category_key === 'mill' || str_contains($this->slug, 'mill') || in_array($this->slug, ['pengelolaan-water-treatment', 'manajemen-perawatan-pompa'])) {
            return 'mill';
        }
        if ($this->category_key === 'traksi' || str_contains($this->slug, 'traksi') || in_array($this->slug, ['refreshment-perawatan-dump-truck', 'defensive-driving', 'defensive-driving-perkebunan'])) {
            return 'traksi';
        }
        if ($this->category_key === 'downstream' || str_contains($this->slug, 'downstream') || in_array($this->slug, ['super-olein-training'])) {
            return 'downstream';
        }
        if ($this->category_key === 'administrasi' || str_contains($this->slug, 'administrasi') || str_contains($this->slug, 'damkar') || str_contains($this->slug, 'k3')) {
            return 'administrasi';
        }
        if ($this->category_key === 'managerial' || str_contains($this->slug, 'managerial') || in_array($this->slug, ['high-impact-communication', 'continuous-improvement-program'])) {
            return 'managerial';
        }
        if ($this->category_key === 'talent' || str_contains($this->slug, 'talent')) {
            return 'talent';
        }
        return $this->category_key ?: 'lainnya';
    }

    /**
     * Resolves human-friendly menu display name
     */
    public function getMenuNameAttribute(): string
    {
        $key = $this->menu_key;
        $map = [
            'reguler' => 'Program Reguler',
            'managerial' => 'Managerial & Leadership',
            'estate' => 'Estate',
            'mill' => 'Mill',
            'traksi' => 'Traksi',
            'downstream' => 'Downstream',
            'administrasi' => 'Administrasi',
            'talent' => 'Talent Management',
        ];
        return $map[$key] ?? ($this->category_name ?: ucfirst($key));
    }

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
