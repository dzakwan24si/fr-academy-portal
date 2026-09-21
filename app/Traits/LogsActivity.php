<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Str;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            $module = static::getActivityModuleName();
            $label = static::getActivityModelLabel($model);

            ActivityLog::log(
                action: 'Tambah',
                module: $module,
                description: "Menambahkan data {$module} ({$label})",
                properties: [
                    'new' => static::filterActivityAttributes($model->getAttributes()),
                ]
            );
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $ignored = static::getIgnoredActivityAttributes();

            // Filter out ignored attributes & timestamps
            $changedKeys = array_filter(array_keys($changes), function ($key) use ($ignored) {
                return !in_array($key, $ignored);
            });

            if (empty($changedKeys)) {
                return;
            }

            $module = static::getActivityModuleName();
            $label = static::getActivityModelLabel($model);

            $oldValues = [];
            $newValues = [];

            foreach ($changedKeys as $key) {
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $changes[$key];
            }

            ActivityLog::log(
                action: 'Edit',
                module: $module,
                description: "Mengubah data {$module} ({$label})",
                properties: [
                    'old' => static::filterActivityAttributes($oldValues),
                    'new' => static::filterActivityAttributes($newValues),
                ]
            );
        });

        static::deleted(function ($model) {
            $module = static::getActivityModuleName();
            $label = static::getActivityModelLabel($model);

            ActivityLog::log(
                action: 'Hapus',
                module: $module,
                description: "Menghapus data {$module} ({$label})",
                properties: [
                    'old' => static::filterActivityAttributes($model->getAttributes()),
                ]
            );
        });
    }

    protected static function getActivityModuleName(): string
    {
        if (property_exists(static::class, 'logModuleName') && !empty(static::$logModuleName)) {
            return static::$logModuleName;
        }

        return Str::headline(class_basename(static::class));
    }

    protected static function getActivityModelLabel($model): string
    {
        if (method_exists($model, 'getActivityLabel')) {
            return $model->getActivityLabel();
        }

        return $model->name ?? $model->nama ?? $model->title ?? $model->kode ?? "ID: {$model->getKey()}";
    }

    protected static function getIgnoredActivityAttributes(): array
    {
        $defaultIgnored = ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];

        if (property_exists(static::class, 'dontLogAttributes') && is_array(static::$dontLogAttributes)) {
            return array_merge($defaultIgnored, static::$dontLogAttributes);
        }

        return $defaultIgnored;
    }

    protected static function filterActivityAttributes(array $attributes): array
    {
        $ignored = static::getIgnoredActivityAttributes();

        foreach ($ignored as $key) {
            unset($attributes[$key]);
        }

        return $attributes;
    }
}
