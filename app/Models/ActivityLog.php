<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Batas maksimal data histori aktivitas yang disimpan (FIFO).
     */
    public const MAX_LOGS = 1000;

    protected static function booted(): void
    {
        static::created(function () {
            static::pruneOldLogs(self::MAX_LOGS);
        });
    }

    /**
     * Memangkas log lama secara otomatis jika melebihi batas maksimal (FIFO).
     */
    public static function pruneOldLogs(int $maxLogs = self::MAX_LOGS): int
    {
        if ($maxLogs <= 0) {
            return 0;
        }

        $cutoffId = static::query()
            ->orderByDesc('id')
            ->skip($maxLogs)
            ->value('id');

        if ($cutoffId) {
            return static::where('id', '<=', $cutoffId)->delete();
        }

        return 0;
    }

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Dapatkan daftar modul yang diizinkan untuk dilihat oleh pengguna berdasarkan Role & Permission.
     * Mengembalikan null jika pengguna adalah Superadmin / Top Management (artinya akses ke semua modul tanpa batasan).
     * Mengembalikan array kosong [] jika pengguna tidak memiliki izin ke modul mana pun.
     */
    public static function getAllowedModulesForUser($user): ?array
    {
        if (!$user) {
            return [];
        }

        // Superadmin & Top Management memiliki akses penuh ke seluruh log tanpa batasan
        if ($user->hasRole('Superadmin') || $user->hasRole('Top Management')) {
            return null;
        }

        $allowed = [];

        // 1. Modul Talent
        if ($user->can('view_talent') || $user->can('edit_talent') || $user->hasAnyRole(['evaluator', 'Evaluator', 'EVALUATOR'])) {
            $allowed = array_merge($allowed, ['Profil Talent', 'Talent Pool', 'Talent', 'Monitoring Talent']);
        }

        // 2. Modul Program Training
        if ($user->can('view_program') || $user->can('edit_program') || $user->hasRole('Trainer')) {
            $allowed = array_merge($allowed, [
                'Program Batch',
                'Program Reguler',
                'Program Non-Reguler',
                'Realisasi Vs Target',
                'Training Record',
                'Dashboard Realisasi',
            ]);
        }

        // 3. Modul Monitoring
        if ($user->can('view_monitoring') || $user->can('edit_monitoring')) {
            $allowed = array_merge($allowed, ['Monitoring', 'Monitoring Asisten', 'Monitoring Talent', 'Monitoring Dashboard']);
        }

        // 4. Modul Administrasi Umum
        if ($user->can('view_administrasi') || $user->can('edit_administrasi') || $user->can('access_administrasi')) {
            $allowed = array_merge($allowed, ['Profil Trainee', 'Rekrutmen', 'Cuti', 'MPP']);
        }

        // 5. Modul Cost & Finansial
        if ($user->can('view_cost') || $user->can('edit_cost')) {
            $allowed = array_merge($allowed, ['Cost', 'Capex', 'VRA', 'Biaya Rekrut', 'Biaya Rekruit']);
        }

        // 6. Modul Data Aset (Inventaris)
        if ($user->can('view_data_aset') || $user->can('edit_data_aset')) {
            $allowed = array_merge($allowed, ['Inventaris']);
        }

        // 7. Modul Standar Kompetensi
        if ($user->can('manage_competency')) {
            $allowed = array_merge($allowed, ['Standar Kompetensi']);
        }

        // 8. Modul Pengguna & Peran
        if ($user->can('manage_users') || $user->can('manage_roles')) {
            $allowed = array_merge($allowed, ['Pengguna', 'Peran']);
        }

        // 9. Modul Artikel, Event, Display TV, Karir
        if ($user->can('manage_articles') || $user->can('manage_events') || $user->can('manage_tv_display') || $user->can('manage_career')) {
            $allowed = array_merge($allowed, ['Artikel', 'Event', 'Display TV', 'Karir']);
        }

        // 10. Dashboard Realisasi
        if ($user->can('view_dashboard') || $user->can('edit_dashboard')) {
            $allowed = array_merge($allowed, ['Dashboard Realisasi']);
        }

        return array_values(array_unique($allowed));
    }

    /**
     * Helper to log an activity explicitly
     */
    public static function log(string $action, string $module, string $description, ?array $properties = null): self
    {
        $user = Auth::user();

        $roleName = '-';
        if ($user) {
            $roleName = (method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null) ?? $user->role ?? 'User';
        }

        return static::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Sistem',
            'user_role' => $roleName,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'properties' => $properties,
        ]);
    }
}
