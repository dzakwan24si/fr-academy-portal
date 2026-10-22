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
     * Helper to log an activity explicitly
     */
    public static function log(string $action, string $module, string $description, ?array $properties = null): self
    {
        $user = Auth::user();

        $roleName = '-';
        if ($user) {
            $roleName = $user->getRoleNames()->first() ?? $user->role ?? 'User';
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
