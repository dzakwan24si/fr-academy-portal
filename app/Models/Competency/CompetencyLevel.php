<?php

namespace App\Models\Competency;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetencyLevel extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Level Kompetensi';

    protected $table = 'competency_levels';

    protected $fillable = [
        'competency_id',
        'position_id',
        'level',
        'description',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function competency(): BelongsTo
    {
        return $this->belongsTo(Competency::class, 'competency_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(CompetencyPosition::class, 'position_id');
    }

    /**
     * Tidak punya kolom `name`, jadi label dibuat eksplisit:
     * "{jabatan} - {L1..L4} pada kompetensi No. N {nama}".
     */
    public function getActivityLabel(): string
    {
        $levelLabel = CompetencyDictionary::LEVEL_LABELS[$this->level] ?? "L{$this->level}";
        $position = $this->position?->name ?? "Jabatan #{$this->position_id}";
        $competency = $this->competency;
        $competencyLabel = $competency ? "No. {$competency->sort_order} {$competency->name}" : "Kompetensi #{$this->competency_id}";

        return "{$position} - {$levelLabel} pada {$competencyLabel}";
    }
}
