<?php

namespace App\Models\Competency;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetencyPosition extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Jabatan Kompetensi';

    protected $table = 'competency_positions';

    protected $fillable = [
        'dictionary_id',
        'name',
        'sort_order',
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
        return $this->hasMany(CompetencyLevel::class, 'position_id');
    }

    public function getActivityLabel(): string
    {
        $dictionary = $this->dictionary;

        return $dictionary
            ? "{$this->name} - {$dictionary->name} {$dictionary->year}"
            : (string) $this->name;
    }
}
