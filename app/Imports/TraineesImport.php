<?php

namespace App\Imports;

use App\Models\Trainee;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

        if ($extension === 'csv') {
            $this->importCsv($file->getRealPath());
        } else {
            $this->importSpreadsheet($file->getRealPath());
        }
    }

    private function importCsv(string $path): void
    {
        $handle = fopen($path, 'r');
        // Read BOM if exists
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $headers = fgetcsv($handle); // skip header row
        $headers = array_map('strtolower', array_map('trim', $headers));

        $row = 2;
        while (($data = fgetcsv($handle)) !== false) {
            $mapped = array_combine($headers, $data);
            $this->processRow($mapped, $row);
            $row++;
        }
        fclose($handle);
    }

    private function importSpreadsheet(string $path): void
    {
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // First row is headers
        $headers = array_map('strtolower', array_map('trim', $rows[0]));
        unset($rows[0]);

        foreach ($rows as $index => $row) {
            $mapped = array_combine($headers, $row);
            $this->processRow($mapped, $index + 1);
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
