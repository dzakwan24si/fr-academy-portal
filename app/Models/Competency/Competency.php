<?php

namespace App\Models\Competency;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Competency extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Kompetensi';

    protected $table = 'competencies';

    protected $fillable = [
        'dictionary_id',
        'sort_order',
        'name',
        'definition',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function dictionary(): BelongsTo
    {
        return $this->belongsTo(CompetencyDictionary::class, 'dictionary_id');
    }

    public function levels(): HasMany
    {
        return $this->hasMany(CompetencyLevel::class, 'competency_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CompetencyItem::class, 'competency_id')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Peta deskripsi level: key "{position_id}-{level}" => description.
     * Memakai relasi `levels` yang sudah di-eager-load agar tidak N+1.
     *
     * @return Collection<string, string|null>
     */
    public function levelMap(): Collection
    {
        return $this->levels->mapWithKeys(
            fn (CompetencyLevel $level) => ["{$level->position_id}-{$level->level}" => $level->description]
        );
    }

    /**
     * Butir bertipe tertentu (knowledge / skill / development), urut sort_order.
     * Memakai relasi `items` yang sudah di-eager-load.
     *
     * @return Collection<int, CompetencyItem>
     */
    public function itemsOfType(string $type): Collection
    {
        return $this->items->where('type', $type)->values();
    }

    public function getActivityLabel(): string
    {
        return "No. {$this->sort_order} - {$this->name}";
    }
}
