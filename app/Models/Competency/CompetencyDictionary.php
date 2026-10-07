<?php

namespace App\Models\Competency;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetencyDictionary extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Kamus Kompetensi';

    protected $table = 'competency_dictionaries';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    /** Label level proficiency (L1-L4). */
    public const LEVEL_LABELS = [
        1 => 'L1 - Basic',
        2 => 'L2 - Mandiri',
        3 => 'L3 - Analitis',
        4 => 'L4 - Expert',
    ];

    /** Label tipe butir (knowledge / skill / development). */
    public const ITEM_TYPE_LABELS = [
        CompetencyItem::TYPE_KNOWLEDGE => 'Pengetahuan Wajib',
        CompetencyItem::TYPE_SKILL => 'Keterampilan Wajib',
        CompetencyItem::TYPE_DEVELOPMENT => 'Pilihan Program Development',
    ];

    protected $fillable = [
        'code',
        'name',
        'year',
        'description',
        'status',
        'published_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'published_at' => 'datetime',
    ];

    public function positions(): HasMany
    {
        return $this->hasMany(CompetencyPosition::class, 'dictionary_id')->orderBy('sort_order')->orderBy('id');
    }

    public function competencies(): HasMany
    {
        return $this->hasMany(Competency::class, 'dictionary_id')->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function getActivityLabel(): string
    {
        return "{$this->name} {$this->year}";
    }
}
