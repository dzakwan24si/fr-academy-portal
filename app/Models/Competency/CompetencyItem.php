<?php

namespace App\Models\Competency;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CompetencyItem extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Butir Kompetensi';

    protected $table = 'competency_items';

    public const TYPE_KNOWLEDGE = 'knowledge';

    public const TYPE_SKILL = 'skill';

    public const TYPE_DEVELOPMENT = 'development';

    public const TYPES = [
        self::TYPE_KNOWLEDGE,
        self::TYPE_SKILL,
        self::TYPE_DEVELOPMENT,
    ];

    protected $fillable = [
        'competency_id',
        'type',
        'body',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class, 'competency_id');
    }

    /**
     * Tidak punya kolom `name`, jadi label dibuat eksplisit:
     * "{tipe}: {cuplikan isi} pada kompetensi No. N {nama}".
     */
    public function getActivityLabel(): string
    {
        $typeLabel = CompetencyDictionary::ITEM_TYPE_LABELS[$this->type] ?? $this->type;
        $snippet = Str::limit((string) $this->body, 60);
        $competency = $this->competency;
        $competencyLabel = $competency ? "No. {$competency->sort_order} {$competency->name}" : "Kompetensi #{$this->competency_id}";

        return "{$typeLabel}: \"{$snippet}\" pada {$competencyLabel}";
    }
}
