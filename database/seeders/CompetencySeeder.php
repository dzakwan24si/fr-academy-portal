<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyDictionary;
use App\Models\Competency\CompetencyItem;
use App\Models\Competency\CompetencyLevel;
use App\Models\Competency\CompetencyPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Mengimpor Kamus Kompetensi (Estate 2026 & Mill 2027) dari JSON.
 *
 * Aturan penting:
 * - TIDAK menimpa data yang sudah ada. Dictionary dengan (code, year) yang sudah ada dilewati utuh,
 *   supaya edit lewat CRUD tidak pernah dikembalikan ke versi awal.
 * - Semua file divalidasi dulu; satu error membatalkan seluruh impor.
 * - Seluruh impor dalam satu transaksi, tanpa event model (LogsActivity tidak membanjiri
 *   Histori Aktivitas); cukup satu entri ringkasan per dictionary.
 */
class CompetencySeeder extends Seeder
{
    /** @var list<string> */
    public const FILES = [
        'competency_estate_2026.json',
        'competency_mill_2027.json',
    ];

    public function run(): void
    {
        // Tahap 1: baca & validasi SEMUA file sebelum menyentuh database.
        $payloads = [];
        foreach (self::FILES as $file) {
            $payloads[$file] = $this->loadAndValidate(database_path('seeders/data/'.$file), $file);
        }

        // Tahap 2: impor semua dalam satu transaksi.
        DB::transaction(function () use ($payloads) {
            Model::withoutEvents(function () use ($payloads) {
                foreach ($payloads as $data) {
                    $this->importDictionary($data);
                }
            });
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function loadAndValidate(string $path, string $label): array
    {
        if (! is_file($path)) {
            throw new RuntimeException("[{$label}] File data tidak ditemukan: {$path}");
        }

        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data)) {
            throw new RuntimeException("[{$label}] JSON tidak valid: ".json_last_error_msg());
        }

        foreach (['code', 'name', 'year', 'status', 'positions', 'competencies'] as $key) {
            if (! array_key_exists($key, $data)) {
                throw new RuntimeException("[{$label}] Field '{$key}' tidak ada.");
            }
        }

        if (! in_array($data['status'], [CompetencyDictionary::STATUS_DRAFT, CompetencyDictionary::STATUS_PUBLISHED], true)) {
            throw new RuntimeException("[{$label}] Status '{$data['status']}' tidak valid.");
        }

        $positions = $data['positions'];
        if (! is_array($positions) || $positions === [] || count($positions) !== count(array_unique($positions))) {
            throw new RuntimeException("[{$label}] Daftar jabatan kosong atau ada nama ganda.");
        }

        foreach ($data['competencies'] as $index => $competency) {
            $no = $competency['order'] ?? ($index + 1);
            $name = $competency['name'] ?? '(tanpa nama)';

            if (empty($competency['name'])) {
                throw new RuntimeException("[{$label}] Kompetensi #{$no} tidak punya nama.");
            }

            $levels = $competency['levels'] ?? [];

            // Setiap kompetensi wajib punya level untuk SEMUA jabatan x 4 level.
            if (array_diff($positions, array_keys($levels)) !== [] || array_diff(array_keys($levels), $positions) !== []) {
                throw new RuntimeException("[{$label}] Kompetensi #{$no} '{$name}': jabatan pada levels tidak sama dengan daftar positions.");
            }

            foreach ($positions as $position) {
                if (! is_array($levels[$position]) || count($levels[$position]) !== 4) {
                    throw new RuntimeException("[{$label}] Kompetensi #{$no} '{$name}': jabatan '{$position}' harus punya tepat 4 level (L1-L4).");
                }
            }

            foreach (array_keys($competency['items'] ?? []) as $type) {
                if (! in_array($type, CompetencyItem::TYPES, true)) {
                    throw new RuntimeException("[{$label}] Kompetensi #{$no} '{$name}': tipe butir '{$type}' tidak dikenal.");
                }
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function importDictionary(array $data): void
    {
        $exists = CompetencyDictionary::where('code', $data['code'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            $this->command?->warn("Dilewati: {$data['name']} {$data['year']} (code={$data['code']}) sudah ada, data tidak ditimpa.");

            return;
        }

        $isPublished = $data['status'] === CompetencyDictionary::STATUS_PUBLISHED;

        $dictionary = CompetencyDictionary::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'year' => $data['year'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'], // sesuai nilai JSON, tidak diubah diam-diam
            'published_at' => $isPublished ? now() : null,
        ]);

        $positionIds = [];
        foreach (array_values($data['positions']) as $i => $positionName) {
            $positionIds[$positionName] = CompetencyPosition::create([
                'dictionary_id' => $dictionary->id,
                'name' => $positionName,
                'sort_order' => $i + 1,
            ])->id;
        }

        $levelCount = 0;
        $itemCount = 0;

        foreach ($data['competencies'] as $index => $row) {
            $competency = Competency::create([
                'dictionary_id' => $dictionary->id,
                'sort_order' => $row['order'] ?? ($index + 1),
                'name' => $row['name'],
                'definition' => $row['definition'] ?? null,
            ]);

            foreach ($row['levels'] as $positionName => $texts) {
                foreach (array_values($texts) as $i => $text) {
                    CompetencyLevel::create([
                        'competency_id' => $competency->id,
                        'position_id' => $positionIds[$positionName],
                        'level' => $i + 1,
                        'description' => ($text === null || trim((string) $text) === '') ? null : $text,
                    ]);
                    $levelCount++;
                }
            }

            foreach (($row['items'] ?? []) as $type => $bodies) {
                foreach (array_values($bodies) as $i => $body) {
                    CompetencyItem::create([
                        'competency_id' => $competency->id,
                        'type' => $type,
                        'body' => $body,
                        'sort_order' => $i + 1,
                    ]);
                    $itemCount++;
                }
            }
        }

        $competencyCount = count($data['competencies']);
        $positionCount = count($positionIds);

        // Satu entri ringkasan saja (Model::withoutEvents menekan LogsActivity per baris).
        ActivityLog::log(
            action: 'Import',
            module: 'Kamus Kompetensi',
            description: "Impor Kamus Kompetensi {$dictionary->name} {$dictionary->year}",
            properties: [
                'code' => $dictionary->code,
                'year' => $dictionary->year,
                'status' => $dictionary->status,
                'positions' => $positionCount,
                'competencies' => $competencyCount,
                'levels' => $levelCount,
                'items' => $itemCount,
            ],
        );

        $this->command?->info("Diimpor: {$dictionary->name} {$dictionary->year} - {$positionCount} jabatan, {$competencyCount} kompetensi, {$levelCount} level, {$itemCount} butir.");
    }
}
