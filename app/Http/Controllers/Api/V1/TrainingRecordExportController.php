<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Program\TrainingRecord;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingRecordExportController extends Controller
{
    /**
     * Export training records for external system integration (e.g. Peta SDM)
     */
    public function export(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 100);
        if ($perPage < 1) {
            $perPage = 100;
        }
        if ($perPage > 1000) {
            $perPage = 1000;
        }

        $query = TrainingRecord::query()->with(['reguler', 'nonReguler']);

        // Filter by 'since' date parameter (if specified)
        if ($request->filled('since')) {
            try {
                $sinceDate = Carbon::parse($request->input('since'));
                $query->where(function ($q) use ($sinceDate) {
                    $q->where('updated_at', '>=', $sinceDate)
                        ->orWhere('created_at', '>=', $sinceDate);
                });
            } catch (\Throwable $e) {
                return response()->json([
                    'error' => 'Invalid since date format. Please use ISO date format (e.g. YYYY-MM-DD or YYYY-MM-DD HH:MM:SS).',
                ], 422);
            }
        }

        // Order deterministically
        $query->orderBy('id', 'asc');

        $paginated = $query->paginate($perPage);

        $transformedData = collect($paginated->items())->map(function (TrainingRecord $record) {
            $act = $record->activity();

            // Calculate jml_hari
            $rawJmlHari = $record->jml_hari ?: ($act?->jumlah_hari ?? $act?->total_hari ?? $act?->Total_training_days ?? 0);
            $jmlHari = is_numeric($rawJmlHari) ? (int) $rawJmlHari : 0;

            // Calculate jam_pelatihan (handles both Reguler & Non-Reguler)
            $rawTotalJam = $record->total_jam ?: ($act?->total_jam ?? $act?->Total_training_days ?? $record->activityHoursPerParticipant());
            $jamPelatihan = is_numeric($rawTotalJam) ? (float) $rawTotalJam : 0.0;

            // Resolve NIK as explicit string (prevents scientific notation issues in consumers)
            $nik = (string) ($record->displayNik() ?? $record->participant_identifier ?? '');

            return [
                'nik' => $nik,
                'nama' => (string) ($record->participant_name ?? ''),
                'jenis_kelamin' => (string) ($record->gender ?? ''),
                'nama_pt' => (string) ($record->company ?? ''),
                'department' => (string) ($record->department ?? ''),
                'lokasi_kerja' => (string) ($record->work_location ?? ''),
                'group' => (string) ($record->group_name ?? ''),
                'region' => (string) ($record->region ?? ''),
                'gol' => (string) ($record->gol ?? ''),
                'jabatan' => (string) ($record->position ?? ''),
                'bulan' => (string) ($record->bulan ?? ''),
                'tahun' => (string) ($record->tahun ?? ''),
                'jml_hari' => $jmlHari,
                'jam_pelatihan' => $jamPelatihan,
                'nama_pelatihan' => (string) ($record->activityName() ?? ''),
                'instruktur' => (string) ($record->instructor ?: ($record->activityTrainer() ?? '')),
                'tempat_pelatihan' => (string) ($record->program_type === 'reguler' ? ($act?->Lokasi_training ?? '') : ($act?->lokasi_training ?? '')),
                'reg_non_reg' => $record->program_type === 'reguler' ? 'Reguler' : 'Non Reguler',
                'internal_eksternal' => (string) ($record->internal_external ?? ''),
                'level' => (string) ($record->level ?? ''),
                'periode' => (string) ($record->periode ?? ''),
            ];
        });

        return response()->json([
            'data' => $transformedData,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
            ],
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
