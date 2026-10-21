<?php

namespace App\Imports;

use App\Models\Trainee;
use App\Services\SpreadsheetReader;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TraineesImport
{
    protected int $programId;

    protected array $errors = [];

    protected int $imported = 0;

    public function __construct(int $programId)
    {
        $this->programId = $programId;
    }

    /**
     * Import trainees from an uploaded Excel/CSV file.
     */
    public function import(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $rows = SpreadsheetReader::read($file->getRealPath(), $extension);
        if (empty($rows)) {
            return;
        }

        $headers = array_shift($rows);
        $headers = array_map('strtolower', array_map('trim', $headers));

        foreach ($rows as $index => $row) {
            if (count($row) < count($headers)) {
                $row = array_pad($row, count($headers), null);
            } elseif (count($row) > count($headers)) {
                $row = array_slice($row, 0, count($headers));
            }
            $mapped = array_combine($headers, $row);
            $this->processRow($mapped, $index + 2);
        }
    }

    private function processRow(array $row, int $rowNum): void
    {
        $name = trim($row['nama'] ?? '');
        if (empty($name)) {
            return; // skip empty rows
        }

        // Resolve birth_date
        $birthDate = null;
        $rawDate = trim($row['tgl_lahir'] ?? '');
        if (! empty($rawDate)) {
            // Try to parse common date formats
            $timestamp = strtotime($rawDate);
            if ($timestamp !== false) {
                $birthDate = date('Y-m-d', $timestamp);
            } elseif (is_numeric($rawDate)) {
                // Excel serial date
                $birthDate = Date::excelToDateTimeObject((float) $rawDate)->format('Y-m-d');
            }
        }

        $status = strtolower(trim($row['status'] ?? 'aktif'));
        if (! in_array($status, ['aktif', 'pasif', 'resign'])) {
            $status = 'aktif';
        }

        try {
            Trainee::create([
                'training_program_id' => $this->programId,
                'nik' => trim($row['nik'] ?? '') ?: null,
                'name' => $name,
                'birth_place' => trim($row['tempat_lahir'] ?? '') ?: null,
                'birth_date' => $birthDate,
                'origin' => trim($row['asal_daerah'] ?? '') ?: null,
                'university' => trim($row['universitas'] ?? '') ?: null,
                'major' => trim($row['jurusan'] ?? '') ?: null,
                'status' => $status,
            ]);
            $this->imported++;
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$rowNum} ({$name}): ".$e->getMessage();
        }
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
