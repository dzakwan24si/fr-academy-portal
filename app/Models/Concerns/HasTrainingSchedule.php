<?php

namespace App\Models\Concerns;

trait HasTrainingSchedule
{
    public function scheduleDates(): array
    {
        if (is_array($this->tanggal_pelaksanaan) && $this->tanggal_pelaksanaan) {
            return $this->tanggal_pelaksanaan;
        }

        return array_values(array_filter([
            $this->start_date?->format('Y-m-d'),
            $this->end_date?->format('Y-m-d'),
        ]));
    }
}
