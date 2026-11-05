<?php

namespace App\Models\Talent;

use App\Models\Monitoring\MonitoringTalent;
use App\Services\RegionHelper;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProfilTalent extends Model
{
    use HasFactory, LogsActivity;

    protected static $logModuleName = 'Profil Talent';

    protected $table = 'profil_talents';

    protected $guarded = ['id'];

    public static bool $isSyncDeleting = false;
    public static bool $isSyncUpdating = false;

    public static array $monthNames = [
        'januari' => 'Januari',
        'februari' => 'Februari',
        'maret' => 'Maret',
        'april' => 'April',
        'mei' => 'Mei',
        'juni' => 'Juni',
        'juli' => 'Juli',
        'agustus' => 'Agustus',
        'september' => 'September',
        'oktober' => 'Oktober',
        'november' => 'November',
        'desember' => 'Desember',
    ];

    public static array $shortMonthNames = [
        'januari' => 'Jan',
        'februari' => 'Feb',
        'maret' => 'Mar',
        'april' => 'Apr',
        'mei' => 'Mei',
        'juni' => 'Jun',
        'juli' => 'Jul',
        'agustus' => 'Agu',
        'september' => 'Sep',
        'oktober' => 'Okt',
        'november' => 'Nov',
        'desember' => 'Des',
    ];

    protected static function booted()
    {
        static::saving(function ($talent) {
            // Normalize IDP progress percentages to 0 - 100 scale
            foreach (['refreshment', 'softskill', 'ojt'] as $pField) {
                if ($talent->$pField !== null && $talent->$pField !== '') {
                    $clean = (float) str_replace(['%', ' '], '', (string) $talent->$pField);
                    if ($clean > 0 && $clean <= 1.0) {
                        $clean = round($clean * 100, 2);
                    }
                    $talent->$pField = $clean;
                }
            }

            if ($talent->overall_progress !== null && $talent->overall_progress !== '') {
                $ovpVal = (float) str_replace(['%', ' '], '', (string) $talent->overall_progress);
                if ($ovpVal > 0 && $ovpVal <= 1.0) {
                    $ovpVal = round($ovpVal * 100, 2);
                }
                $talent->overall_progress = $ovpVal;
            } elseif ($talent->refreshment !== null || $talent->softskill !== null || $talent->ojt !== null) {
                $validProgs = array_filter([$talent->refreshment, $talent->softskill, $talent->ojt], fn ($v) => $v !== null && $v !== '');
                if (!empty($validProgs)) {
                    $talent->overall_progress = round(array_sum($validProgs) / count($validProgs), 1);
                }
            }

            $hasOjt = $talent->nilai_ojt !== null && $talent->nilai_ojt !== '' && is_numeric($talent->nilai_ojt);
            $hasPanelis = $talent->nilai_evaluasi_panelis !== null && $talent->nilai_evaluasi_panelis !== '' && is_numeric($talent->nilai_evaluasi_panelis);

            if ($hasOjt || $hasPanelis) {
                $ojtVal = $hasOjt ? (float) $talent->nilai_ojt : 0;
                $panelisVal = $hasPanelis ? (float) $talent->nilai_evaluasi_panelis : 0;
                $talent->nilai_akhir = round(($ojtVal * 0.40) + ($panelisVal * 0.60), 2);

                $grade = self::getGradeInfo($talent->nilai_akhir);
                if ($grade) {
                    $talent->keterangan_evaluasi = $grade['title'] . ' (Grade ' . $grade['grade'] . ')';
                }
            } elseif ($talent->nilai_akhir !== null && is_numeric($talent->nilai_akhir)) {
                $grade = self::getGradeInfo((float) $talent->nilai_akhir);
                if ($grade && empty($talent->keterangan_evaluasi)) {
                    $talent->keterangan_evaluasi = $grade['title'] . ' (Grade ' . $grade['grade'] . ')';
                }
            }
        });

        // Clean up stored photo on delete
        static::deleting(function ($talent) {
            if (!empty($talent->foto_profil) && str_starts_with($talent->foto_profil, 'storage/profil-talents/')) {
                Storage::disk('public')->delete(str_replace('storage/', '', $talent->foto_profil));
            }
        });

        // Two-way sync: Update status in Monitoring Talent when status_ojt is updated
        static::updated(function ($talent) {
            if (static::$isSyncUpdating) {
                return;
            }

            if ($talent->wasChanged('status_ojt')) {
                $nik = trim((string) $talent->employee_id);
                $newStatus = $talent->status_ojt;
                if (!empty($nik) && $newStatus !== null) {
                    try {
                        MonitoringTalent::$isSyncUpdating = true;
                        MonitoringTalent::where('employee_id', $nik)->get()->each(function ($mt) use ($newStatus) {
                            $mt->update(['status' => $newStatus]);
                        });

                        // Clear monitoring talent dashboard cache so KPI metrics update
                        Cache::put('dreal_cache_version', time());
                        Cache::put('dashboard_cache_version', time());
                    } finally {
                        MonitoringTalent::$isSyncUpdating = false;
                    }
                }
            }
        });

        // Two-way sync: Delete corresponding record in Monitoring Talent by employee_id (NIK)
        static::deleted(function ($talent) {
            if (static::$isSyncDeleting) {
                return;
            }

            $nik = trim((string) $talent->employee_id);
            if (!empty($nik)) {
                try {
                    MonitoringTalent::$isSyncDeleting = true;
                    MonitoringTalent::where('employee_id', $nik)->get()->each(function ($mt) {
                        $mt->delete();
                    });

                    // Clear monitoring talent dashboard cache
                    Cache::put('dreal_cache_version', time());
                    Cache::put('dashboard_cache_version', time());
                } finally {
                    MonitoringTalent::$isSyncDeleting = false;
                }
            }
        });
    }

    /**
     * Map numeric score to Grade (A/B/C/D), Title, and criteria bullets.
     */
    public static function getGradeInfo(?float $score): ?array
    {
        if ($score === null || $score <= 0) {
            return null;
        }

        if ($score > 80) {
            return [
                'grade' => 'A',
                'title' => 'Excellent',
                'range' => 'Nilai > 80',
                'bg_badge' => 'bg-blue-600',
                'border_badge' => 'border-blue-500',
                'text_badge' => 'text-white',
                'card_bg' => 'bg-blue-50/70',
                'border_card' => 'border-blue-200',
                'text_title' => 'text-blue-900',
                'text_sub' => 'text-blue-700',
                'text_number' => 'text-blue-950',
                'bullet_dot' => 'bg-blue-600',
                'bullets' => [
                    'Ready',
                    'Placement / Talent Pool',
                ],
            ];
        } elseif ($score >= 70) {
            return [
                'grade' => 'B',
                'title' => 'Good',
                'range' => 'Nilai 70 - 79',
                'bg_badge' => 'bg-emerald-600',
                'border_badge' => 'border-emerald-500',
                'text_badge' => 'text-white',
                'card_bg' => 'bg-emerald-50/70',
                'border_card' => 'border-emerald-200',
                'text_title' => 'text-emerald-900',
                'text_sub' => 'text-emerald-700',
                'text_number' => 'text-emerald-950',
                'bullet_dot' => 'bg-emerald-600',
                'bullets' => [
                    'Ready',
                    'Placement / Talent Pool',
                ],
            ];
        } elseif ($score >= 60) {
            return [
                'grade' => 'C',
                'title' => 'Need Improvement',
                'range' => 'Nilai 60 - 69',
                'bg_badge' => 'bg-amber-500',
                'border_badge' => 'border-amber-400',
                'text_badge' => 'text-white',
                'card_bg' => 'bg-amber-50/70',
                'border_card' => 'border-amber-200',
                'text_title' => 'text-amber-900',
                'text_sub' => 'text-amber-700',
                'text_number' => 'text-amber-950',
                'bullet_dot' => 'bg-amber-500',
                'bullets' => [
                    'Potential',
                    'Next Program',
                ],
            ];
        } else {
            return [
                'grade' => 'D',
                'title' => 'Unsatisfactory',
                'range' => 'Nilai < 60',
                'bg_badge' => 'bg-rose-600',
                'border_badge' => 'border-rose-500',
                'text_badge' => 'text-white',
                'card_bg' => 'bg-rose-50/70',
                'border_card' => 'border-rose-200',
                'text_title' => 'text-rose-900',
                'text_sub' => 'text-rose-700',
                'text_number' => 'text-rose-950',
                'bullet_dot' => 'bg-rose-600',
                'bullets' => [
                    'Not Fit',
                    'Not Proper',
                    'Not Ready',
                ],
            ];
        }
    }

    /**
     * Accessor to get structured evaluation grade data for the current talent.
     */
    public function getEvaluationGradeAttribute(): ?array
    {
        return self::getGradeInfo($this->nilai_akhir !== null ? (float) $this->nilai_akhir : null);
    }

    /**
     * Get previewable photo URL, automatically converting Google Drive URLs if present.
     */
    public function getPhotoUrlAttribute(): string
    {
        $raw = trim((string) $this->foto_profil);
        if (empty($raw)) {
            return '';
        }

        // Check if Google Drive link
        if (str_contains($raw, 'drive.google.com')) {
            if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $raw, $matches)) {
                return "https://lh3.googleusercontent.com/d/{$matches[1]}";
            }
            if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $raw, $matches)) {
                return "https://lh3.googleusercontent.com/d/{$matches[1]}";
            }
        }

        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            return $raw;
        }

        if (str_starts_with($raw, 'storage/') || str_starts_with($raw, '/storage/')) {
            return asset(ltrim($raw, '/'));
        }

        return asset('storage/' . ltrim($raw, '/'));
    }

    /**
     * Get initials for avatar fallback (e.g. DARMA YUDHI -> DY)
     */
    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->nama));
        $initials = '';
        foreach ($words as $w) {
            if (!empty($w)) {
                $initials .= strtoupper($w[0]);
                if (strlen($initials) >= 2) {
                    break;
                }
            }
        }
        return $initials ?: 'TL';
    }

    /**
     * Return list of months that have non-empty notes or suggestions.
     * Used for the month filter dropdown in "Catatan & Rekomendasi Mentor".
     */
    public function getMonthsWithNotesAttribute(): array
    {
        $available = [];
        foreach (self::$monthNames as $key => $label) {
            $catatan = trim((string) $this->{"catatan_mentor_{$key}"});
            $suggestion = trim((string) $this->{"dev_suggestion_{$key}"});
            if ($catatan !== '' || $suggestion !== '') {
                $available[$key] = $label;
            }
        }
        return $available;
    }

    /**
     * Return list of months with non-null scores.
     * Used for the "Nilai Mentor" chart.
     */
    public function getMonthsWithScoresAttribute(): array
    {
        $scores = [];
        foreach (self::$monthNames as $key => $label) {
            $val = $this->{"nilai_{$key}"};
            if ($val !== null && $val !== '' && is_numeric($val)) {
                $scores[$key] = [
                    'label' => $label,
                    'short' => self::$shortMonthNames[$key],
                    'score' => (float) $val,
                ];
            }
        }
        return $scores;
    }

    /**
     * Scope filter by region.
     */
    public function scopeRegion($query, $region)
    {
        return RegionHelper::applyQueryFilter($query, 'region', $region);
    }

    /**
     * Scope filter by talent / category.
     */
    public function scopeTalentCategory($query, $talent)
    {
        if (!empty($talent) && $talent !== 'all') {
            return $query->where('talent', $talent);
        }
        return $query;
    }
}
