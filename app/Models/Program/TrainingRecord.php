<?php

namespace App\Models\Program;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
    use LogsActivity;

    protected static $logModuleName = 'Training Record';

    private static array $activityCache = [];

    protected $guarded = [];
    protected $casts = ['training_date' => 'date:Y-m-d'];

    public function reguler()
    {
        return $this->belongsTo(ProgramReguler::class, 'program_id', 'Program_reguler_id');
    }

    public function nonReguler()
    {
        return $this->belongsTo(ProgramNonReguler::class, 'program_id', 'id_nonreguler');
    }

    public function activity(): ?Model
    {
        $key = $this->program_type . ':' . $this->program_id;
        if (array_key_exists($key, self::$activityCache)) return self::$activityCache[$key];

        return self::$activityCache[$key] = match ($this->program_type) {
            'reguler' => $this->reguler,
            'non_reguler' => $this->nonReguler,
            default => null,
        };
    }

    public function activityDates(): array
    {
        $activity = $this->activity();
        if (!$activity) return [];

        $dates = $activity->tanggal_pelaksanaan ?: [];
        if ($dates) return array_values(array_filter($dates));

        return $this->program_type === 'reguler'
            ? array_values(array_filter([$activity->Mulai_program, $activity->Selesai_program]))
            : array_values(array_filter([$activity->start_date, $activity->end_date]));
    }

    public function activityName(): ?string
    {
        $activity = $this->activity();
        if (!$activity) return null;

        if ($this->program_type !== 'reguler') return $activity->jenis_pelatihan;

        $name = trim((string) $activity->Training_Program);
        $batch = trim((string) $activity->Batch);
        return $batch !== '' && !str_contains(strtolower($name), strtolower($batch))
            ? $name . ' - ' . $batch
            : $name;
    }

    public function activityTrainer(): ?string
    {
        $activity = $this->activity();
        if (!$activity) return null;

        return $this->program_type === 'reguler' ? ($activity->trainer ?: 'Internal') : $activity->trainer;
    }

    public function activityStartDate(): ?string
    {
        $activity = $this->activity();
        if (!$activity) return null;

        $date = $this->program_type === 'reguler' ? $activity->Mulai_program : $activity->start_date;
        return $date?->format('Y-m-d');
    }

    public function activityEndDate(): ?string
    {
        $activity = $this->activity();
        if (!$activity) return null;

        $date = $this->program_type === 'reguler' ? $activity->Selesai_program : $activity->end_date;
        return $date?->format('Y-m-d');
    }

    public function activityHoursPerParticipant(): ?float
    {
        $activity = $this->activity();
        if (!$activity) return null;

        $totalHours = $this->program_type === 'reguler' ? $activity->Total_training_days : $activity->total_jam;
        $totalParticipants = $this->program_type === 'reguler' ? $activity->target_peserta : $activity->target_total;
        if (!$totalHours || !$totalParticipants) return null;

        return round((float) $totalHours / (int) $totalParticipants, 2);
    }
}
