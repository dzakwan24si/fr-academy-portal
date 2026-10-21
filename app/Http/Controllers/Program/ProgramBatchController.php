<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Program\ProgramNonReguler;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use App\Services\ExcelExporter;
use App\Services\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProgramBatchController extends Controller
{
    public function programIndex(Request $request)
    {
        $yearInput = $request->input('year');
        $year = ($yearInput !== null && $yearInput !== '' && $yearInput !== 'all') ? (int) $yearInput : null;
        $type = $request->input('type', 'all');
        $search = strtolower(trim((string) $request->input('search')));
        $region = strtolower(trim((string) $request->input('region')));
        $groups = $this->programGroups($year, $type, $region)->filter(function ($group) use ($search) {
            if ($search !== '' && ! str_contains(strtolower($group['search_text']), $search)) {
                return false;
            }

            return true;
        })->values();
        $regions = $this->programGroups(null, 'all')->pluck('region')->filter()->unique()->sort()->values();
        $availableYears = $this->allProgramYears();

        return view('Administrasi.Pages.Program.program', compact('groups', 'year', 'type', 'search', 'region', 'regions', 'availableYears'));
    }

    public function programShow(Request $request, string $type, string $name)
    {
        $yearInput = $request->input('year');
        $year = ($yearInput !== null && $yearInput !== '' && $yearInput !== 'all') ? (int) $yearInput : null;
        $group = $this->programGroups($year, $type, trim((string) $request->input('region', '')))->first(fn ($item) => $item['name'] === $name);
        abort_unless($group, 404);
        $availableYears = $this->allProgramYears();

        return view('Administrasi.Pages.Program.program_detail', ['group' => $group, 'year' => $year, 'availableYears' => $availableYears]);
    }

    public function index()
    {
        return view('Administrasi.Pages.Program.program_batch');
    }

    public function data()
    {
        $records = TrainingRecord::where('status', 'valid')->get()->groupBy(fn ($record) => $record->program_type.':'.$record->program_id);

        $nonReguler = ProgramNonReguler::query()->latest('start_date')->get()->map(fn ($item) => $this->normalizeNonReguler($item, $records->get('non_reguler:'.$item->id_nonreguler, collect())));
        $reguler = ProgramReguler::query()->latest('Mulai_program')->get()->map(fn ($item) => $this->normalizeReguler($item, $records->get('reguler:'.$item->Program_reguler_id, collect())));

        return response()->json($nonReguler->concat($reguler)->values());
    }

    private function programGroups(?int $year, string $type, string $region = '')
    {
        $cleanStr = fn ($str) => strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string) $str)));
        $isSoft = function ($item) use ($cleanStr) {
            $kat = $cleanStr(($item->kategori_pelatihan ?? '').' '.($item->sub_kategori ?? '').' '.($item->jenis_pelatihan ?? ''));

            return str_contains($kat, 'managerial') || str_contains($kat, 'leadership') || str_contains($kat, 'soft');
        };

        $batches = collect();

        // 1. Non Reguler Programs
        if ($type !== 'reguler') {
            $nonRegQuery = ProgramNonReguler::all();
            if (in_array($type, ['non_reguler_soft_skill', 'soft_skill'], true)) {
                $nonRegQuery = $nonRegQuery->filter($isSoft);
            } elseif (in_array($type, ['non_reguler_teknikal', 'teknikal'], true)) {
                $nonRegQuery = $nonRegQuery->reject($isSoft);
            }

            $batches = $batches->concat($nonRegQuery->map(function ($item) use ($isSoft) {
                $soft = $isSoft($item);

                return [
                    'type' => 'non_reguler',
                    'sub_type' => $soft ? 'soft_skill' : 'teknikal',
                    'type_label' => $soft ? 'Non Reguler - Soft Skill' : 'Non Reguler - Teknikal',
                    'id' => $item->getKey(),
                    'name' => trim((string) $item->jenis_pelatihan),
                    'batch' => (int) ($item->TARGET_BATCH ?? $item->total_batch ?? 0),
                    'target' => (int) ($item->TARGET_PESERTA ?? $item->target_total ?? 0),
                    'region' => trim((string) $item->region),
                    'location' => $item->lokasi_training,
                    'start' => $item->start_date,
                    'end' => $item->end_date,
                    'model' => $item,
                ];
            }));
        }

        // 2. Reguler Programs
        if (in_array($type, ['all', 'reguler'], true)) {
            $batches = $batches->concat(ProgramReguler::all()->map(fn ($item) => [
                'type' => 'reguler',
                'sub_type' => 'reguler',
                'type_label' => 'Reguler',
                'id' => $item->getKey(),
                'name' => trim((string) $item->Training_Program),
                'batch' => 1,
                'target' => $item->target_peserta,
                'region' => trim((string) $item->Region),
                'location' => $item->Lokasi_training,
                'start' => $item->Mulai_program,
                'end' => $item->Selesai_program,
                'model' => $item,
            ]));
        }

        if ($region !== '') {
            $batches = $batches->filter(fn ($batch) => $this->normalizeRegion($batch['region']) === $this->normalizeRegion($region));
        }
        $batches = $batches->filter(fn ($batch) => $this->batchInYear($batch, $year));
        $records = TrainingRecord::where('status', 'valid')->get()->groupBy(fn ($record) => $record->program_type.':'.$record->program_id);

        return $batches->groupBy(fn ($batch) => $batch['type'].':'.$batch['name'])->map(function ($items) use ($records, $year) {
            $first = $items->first();
            $related = $items->map(function ($batch) use ($records, $year) {
                $key = $batch['type'].':'.$batch['id'];
                $actualRecords = $records->get($key, collect())->filter(fn ($record) => $this->recordInYear($record, $year));
                $actualParticipants = $actualRecords->count();
                if ($batch['type'] === 'reguler') {
                    $realizedBatch = $actualRecords->isNotEmpty() ? 1 : 0;
                } else {
                    $explicitBatch = (int) ($batch['model']->realisasi_batch ?? $batch['model']->total_batch ?? 0);
                    $realizedBatch = $explicitBatch > 0 ? $explicitBatch : ($actualRecords->isNotEmpty() ? 1 : 0);
                }

                return $batch + ['actual_participants' => $actualParticipants, 'realized_batch' => $realizedBatch, 'period' => $this->period($batch)];
            });
            $targetBatch = $items->sum('batch');
            $targetParticipants = $items->sum('target');
            $realizedBatch = $related->sum('realized_batch');
            $realizedParticipants = $related->sum('actual_participants');
            $regions = $items->pluck('region')->filter()->map(fn ($value) => trim((string) $value))->unique()->values()->all();
            $searchText = $items->flatMap(fn ($item) => [$item['name'], $item['region'], $item['location'], $item['model']->Batch ?? null])->filter()->implode(' ');

            return [
                'key' => $first['type'].'|'.$first['name'],
                'type' => $first['type'],
                'sub_type' => $first['sub_type'] ?? 'reguler',
                'type_label' => $first['type_label'] ?? ($first['type'] === 'reguler' ? 'Reguler' : 'Non Reguler'),
                'name' => $first['name'],
                'region' => $regions[0] ?? '',
                'regions' => $regions,
                'search_text' => $searchText,
                'total_batch' => $targetBatch,
                'target_participants' => $targetParticipants,
                'realized_batch' => $realizedBatch,
                'realized_participants' => $realizedParticipants,
                'batch_percent' => $this->percent($realizedBatch, $targetBatch),
                'participant_percent' => $this->percent($realizedParticipants, $targetParticipants),
                'batches' => $related->values(),
            ];
        })->values();
    }

    private function extractYearFromField($val): ?int
    {
        if (empty($val)) {
            return null;
        }
        if ($val instanceof \DateTimeInterface) {
            return (int) $val->format('Y');
        }
        if (is_numeric($val) && (int) $val >= 2000 && (int) $val <= 2100) {
            return (int) $val;
        }
        if (is_array($val)) {
            foreach ($val as $item) {
                if ($y = $this->extractYearFromField($item)) {
                    return $y;
                }
            }

            return null;
        }
        if (is_string($val)) {
            $trimmed = trim($val);
            if (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) {
                if ($decoded = json_decode($trimmed, true)) {
                    if ($y = $this->extractYearFromField($decoded)) {
                        return $y;
                    }
                }
            }
            if (preg_match('/\b(20\d{2})\b/', $trimmed, $m)) {
                return (int) $m[1];
            }
            if (($time = strtotime($trimmed)) !== false && $time > 0) {
                return (int) date('Y', $time);
            }
        }

        return null;
    }

    private function getBatchYear(array $batch): ?int
    {
        $model = $batch['model'] ?? null;
        if ($batch['type'] === 'reguler') {
            if (! empty($batch['start'])) {
                return (int) date('Y', strtotime($batch['start']));
            }
            if ($model && ! empty($model->tahun)) {
                return (int) $model->tahun;
            }
            if ($model && ! empty($model->tanggal_pelaksanaan) && ($y = $this->extractYearFromField($model->tanggal_pelaksanaan))) {
                return $y;
            }
            if (! empty($batch['end'])) {
                return (int) date('Y', strtotime($batch['end']));
            }
        } else {
            if ($model && ! empty($model->tahun)) {
                return (int) $model->tahun;
            }
            if (! empty($batch['start'])) {
                return (int) date('Y', strtotime($batch['start']));
            }
            if ($model && ! empty($model->tanggal_pelaksanaan) && ($y = $this->extractYearFromField($model->tanggal_pelaksanaan))) {
                return $y;
            }
            if (! empty($batch['end'])) {
                return (int) date('Y', strtotime($batch['end']));
            }
        }

        return null;
    }

    private function batchInYear(array $batch, ?int $year): bool
    {
        if ($year === null) {
            return true;
        }
        $bYear = $this->getBatchYear($batch);
        if ($bYear !== null) {
            return $bYear === $year;
        }

        return collect([$batch['start'], $batch['end']])->contains(fn ($date) => $date && (int) date('Y', strtotime($date)) === $year);
    }

    private function recordInYear(TrainingRecord $record, ?int $year): bool
    {
        if ($year === null) {
            return true;
        }

        return collect($record->activityDates())->contains(fn ($date) => $date && (int) date('Y', strtotime($date)) === $year);
    }

    private function batchMonths(array $batch): array
    {
        return collect([$batch['start'], $batch['end']])->filter()->map(fn ($date) => (int) date('n', strtotime($date)))->unique()->values()->all();
    }

    private function period(array $batch): string
    {
        return ($batch['start'] ? date('d M Y', strtotime($batch['start'])) : '-').' - '.($batch['end'] ? date('d M Y', strtotime($batch['end'])) : '-');
    }

    public function allProgramYears(): array
    {
        $regYears = ProgramReguler::all()->map(function ($item) {
            if ($item->Mulai_program) {
                return (int) $item->Mulai_program->format('Y');
            }
            if ($item->tahun) {
                return (int) $item->tahun;
            }
            if (! empty($item->tanggal_pelaksanaan) && ($y = $this->extractYearFromField($item->tanggal_pelaksanaan))) {
                return $y;
            }
            if ($item->Selesai_program) {
                return (int) $item->Selesai_program->format('Y');
            }

            return null;
        });

        $nonRegYears = ProgramNonReguler::all()->map(function ($item) {
            if ($item->tahun) {
                return (int) $item->tahun;
            }
            if ($item->start_date) {
                return (int) $item->start_date->format('Y');
            }
            if (! empty($item->tanggal_pelaksanaan) && ($y = $this->extractYearFromField($item->tanggal_pelaksanaan))) {
                return $y;
            }
            if ($item->end_date) {
                return (int) $item->end_date->format('Y');
            }

            return null;
        });

        return collect($regYears->all())
            ->merge($nonRegYears->all())
            ->merge([(int) date('Y')])
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();
    }

    private function percent(int $value, int $total): float
    {
        return $total > 0 ? round($value / $total * 100, 2) : 0;
    }

    private function normalizeRegion($value): string
    {
        return strtolower(trim(str_replace(' fr', '', (string) $value)));
    }

    private function clearDashboardCache(): void
    {
        Cache::put('dreal_cache_version', time());
        Cache::put('dashboard_cache_version', time());
        Cache::put('rvb_cache_version', time());
        Cache::forget('dreal_filter_options');
        Cache::forget('portal_dashboard_years');
    }

    public function store(Request $request, string $type)
    {
        abort_unless(in_array($type, ['reguler', 'non_reguler'], true), 404);
        $model = $type === 'reguler' ? new ProgramReguler : new ProgramNonReguler;
        $model->newQuery()->create($this->preparePayload($request->all(), $type));
        $this->clearDashboardCache();

        return response()->json(['message' => 'Data berhasil disimpan.'], 201);
    }

    public function update(Request $request, string $type, int $id)
    {
        abort_unless(in_array($type, ['reguler', 'non_reguler'], true), 404);
        $model = $type === 'reguler' ? new ProgramReguler : new ProgramNonReguler;
        $instance = $model->newQuery()->findOrFail($id);
        $instance->update($this->preparePayload($request->all(), $type));

        $this->syncTrainingRecordsForProgram($type, $instance);
        $this->clearDashboardCache();

        return response()->json(['message' => 'Data berhasil diperbarui.']);
    }

    private function syncTrainingRecordsForProgram(string $type, $program): void
    {
        $id = $type === 'reguler' ? $program->Program_reguler_id : $program->id_nonreguler;
        $title = $type === 'reguler' ? $program->Training_Program : $program->jenis_pelatihan;
        $year = $program->tahun;
        $startDate = $type === 'reguler' ? $program->Mulai_program : $program->start_date;
        $dates = is_array($program->tanggal_pelaksanaan) ? $program->tanggal_pelaksanaan : json_decode($program->tanggal_pelaksanaan ?? '[]', true);
        if (empty($startDate) && ! empty($dates) && is_array($dates)) {
            $startDate = $dates[0] ?? null;
        }

        $records = TrainingRecord::where('program_type', $type)->where('program_id', $id)->get();
        if ($records->isEmpty()) {
            return;
        }

        $updateData = [];
        if ($year) {
            $updateData['tahun'] = (int) $year;
        }
        if ($title) {
            $updateData['notes'] = $title;
            $updateData['nama_pelatihan'] = $title;
        }

        if ($startDate) {
            $monthNum = (int) date('n', strtotime($startDate));
            $monthNames = [
                1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR',
                5 => 'MEI', 6 => 'JUN', 7 => 'JUL', 8 => 'AGU',
                9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES',
            ];
            if (isset($monthNames[$monthNum])) {
                $updateData['bulan'] = $monthNames[$monthNum];
            }
        }

        if (! empty($updateData)) {
            TrainingRecord::where('program_type', $type)
                ->where('program_id', $id)
                ->update($updateData);
        }
    }

    public function destroy(string $type, int $id)
    {
        abort_unless(in_array($type, ['reguler', 'non_reguler'], true), 404);
        $model = $type === 'reguler' ? new ProgramReguler : new ProgramNonReguler;
        $model->newQuery()->findOrFail($id)->delete();
        $this->clearDashboardCache();

        return response()->json(['message' => 'Data berhasil dihapus.']);
    }

    public function destroyAll(Request $request)
    {
        $type = $request->input('type'); // 'reguler', 'non_reguler', or null/all
        if ($type === 'reguler') {
            ProgramReguler::truncate();
            ActivityLog::log('Hapus', 'Program Batch', 'Menghapus seluruh data Program Reguler');
            $this->clearDashboardCache();

            return response()->json(['message' => 'Seluruh data Program Reguler berhasil dihapus.']);
        } elseif ($type === 'non_reguler') {
            ProgramNonReguler::truncate();
            ActivityLog::log('Hapus', 'Program Batch', 'Menghapus seluruh data Program Non Reguler');
            $this->clearDashboardCache();

            return response()->json(['message' => 'Seluruh data Program Non Reguler berhasil dihapus.']);
        } else {
            ProgramReguler::truncate();
            ProgramNonReguler::truncate();
            ActivityLog::log('Hapus', 'Program Batch', 'Menghapus seluruh data Program Batch (Reguler & Non Reguler)');
            $this->clearDashboardCache();

            return response()->json(['message' => 'Seluruh data Program Batch (Reguler & Non Reguler) berhasil dihapus.']);
        }
    }

    public function template(Request $request): StreamedResponse
    {
        $type = $request->input('type', 'reguler');

        if ($type === 'non_reguler') {
            $headers = [
                'Jenis Pelatihan',
                'Kategori Pelatihan',
                'Sub Kategori',
                'Target Batch',
                'Type Training',
                'Region',
                'Lokasi Training',
                'Trainer',
                'Tanggal Mulai Pelaksanaan',
                'Tanggal Selesai Pelaksanaan',
                'Total Hari',
                'Total Jam',
                'Peserta Non Staf',
                'Peserta AST',
                'Peserta Askep',
                'Peserta Mgr',
                'Peserta GM',
                'Peserta Head',
                'Target Peserta',
                'Tahun',
            ];

            $sampleRow = [
                'Supervisory Management Development',
                'Managerial and Leadership',
                'Softskill',
                1,
                'In-House',
                'Riau',
                'Head Office Pekanbaru',
                'Internal Trainer',
                '2026-03-10',
                '2026-03-12',
                3,
                24,
                0,
                10,
                5,
                2,
                0,
                0,
                17,
                2026,
            ];

            return ExcelExporter::download('Template_Program_Batch_Non_Reguler.xlsx', $headers, [$sampleRow], 'Template Batch Non Reguler');
        }

        $headers = [
            'Training Program',
            'Batch',
            'Jenis Program Reguler',
            'Region',
            'Lokasi Training',
            'Tanggal Mulai Pelaksanaan',
            'Tanggal Selesai Pelaksanaan',
            'Jumlah Bulan Training',
            'Jumlah Hari',
            'Total Training Days',
            'Promosi Mandor',
            'Fresh Graduate',
            'Target Peserta',
            'Uang Saku',
            'Tahun',
            'Kelas',
            'Resign',
            'Lulus',
        ];

        $sampleRow = [
            'Field Assistant Training (FAT)',
            'Batch 1',
            'Training Reguler - Staf',
            'Riau',
            'Training Center Riau',
            '2026-02-01',
            '2026-04-01',
            2,
            60,
            60,
            5,
            15,
            20,
            1500000,
            2026,
            20,
            0,
            20,
        ];

        return ExcelExporter::download('Template_Program_Batch_Reguler.xlsx', $headers, [$sampleRow], 'Template Batch Reguler');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt,xlsx|max:20480']);
        $file = $request->file('file');
        $rows = SpreadsheetReader::read($file->getRealPath(), $file->getClientOriginalExtension());
        if (count($rows) < 2) {
            if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
                return response()->json(['message' => 'File tidak memiliki data.'], 422);
            }

            return back()->withErrors(['file' => 'File tidak memiliki data.']);
        }

        $rawHeaders = array_shift($rows);
        $cleanHeader = fn ($str) => SpreadsheetReader::cleanString($str);
        $normalizedHeaders = array_map($cleanHeader, $rawHeaders);

        // Detect if non-reguler or reguler
        $isNonReg = false;
        $isReg = false;

        foreach ($normalizedHeaders as $h) {
            $clean = str_replace(['_', ' ', '-'], '', $h);
            if (in_array($clean, ['jenispelatihan', 'idnonreguler', 'pesertaast', 'pesertanonstaf', 'typetraining', 'tdtotal'])) {
                $isNonReg = true;
            }
            if (in_array($clean, ['trainingprogram', 'namaprogramreguler', 'programregulerid', 'freshgraduate', 'promosimandor', 'jenisprogramreguler', 'jumlahbulantraining', 'totaltrainingdays', 'uangsaku'])) {
                $isReg = true;
            }
        }

        $table = null;
        if ($isReg) {
            $table = 'reg';
        } elseif ($isNonReg) {
            $table = 'non';
        } else {
            // Fallback check
            if (in_array('jenis_pelatihan', $rawHeaders, true)) {
                $table = 'non';
            } elseif (in_array('Training_Program', $rawHeaders, true) || in_array('Nama_Program_reguler', $rawHeaders, true)) {
                $table = 'reg';
            }
        }

        if (! $table) {
            if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
                return response()->json(['message' => 'Header tidak dikenali. Gunakan template batch non-reguler atau reguler.'], 422);
            }

            return back()->withErrors(['file' => 'Header tidak dikenali. Gunakan template batch non-reguler atau reguler.']);
        }

        $imported = 0;
        $seenRegIds = [];
        $seenNonRegIds = [];
        DB::transaction(function () use ($rows, $normalizedHeaders, $table, &$imported, &$seenRegIds, &$seenNonRegIds) {
            foreach ($rows as $row) {
                if (empty(array_filter($row, fn ($val) => trim((string) $val) !== ''))) {
                    continue;
                }
                $item = [];
                foreach ($normalizedHeaders as $idx => $h) {
                    $clean = str_replace(['_', ' ', '-'], '', $h);
                    $item[$clean] = trim((string) ($row[$idx] ?? ''));
                }
                $stored = $this->storeRowMapped($item, $table, $seenRegIds, $seenNonRegIds);
                if ($stored) {
                    $imported++;
                }
            }
        });

        ActivityLog::log(
            'Import',
            'Program Batch',
            "Mengimport {$imported} data program batch dari file ".$file->getClientOriginalName(),
            ['filename' => $file->getClientOriginalName(), 'count' => $imported]
        );

        $this->clearDashboardCache();

        $message = "{$imported} data program batch berhasil diimpor.";
        if ($request->wantsJson() || $request->ajax() || $request->isJson()) {
            return response()->json(['message' => $message, 'count' => $imported], 200);
        }

        return back()->with('success', $message);
    }

    private function storeRowMapped(array $item, string $table, array &$seenRegIds = [], array &$seenNonRegIds = []): bool
    {
        if ($table === 'reg') {
            $trainingProgram = $item['trainingprogram'] ?? $item['namaprogramreguler'] ?? $item['namaprogram'] ?? $item['pelatihan'] ?? $item['namapelatihan'] ?? '';
            if ($trainingProgram === '') {
                return false;
            }

            $mulai = $this->dateValue(
                $item['tanggalmulaipelaksanaan'] ?? $item['tanggalmulai'] ?? $item['mulaiprogram'] ?? $item['tglmulaipelaksanaan']
                ?? $item['mulaipelaksanaan'] ?? $item['startdate'] ?? $item['tglmulai'] ?? $item['mulai'] ?? $item['start']
                ?? $item['tanggalawal'] ?? $item['tglawal'] ?? null
            );
            $selesai = $this->dateValue(
                $item['tanggalselesaipelaksanaan'] ?? $item['tanggalselesai'] ?? $item['selesaiprogram'] ?? $item['tglselesaipelaksanaan']
                ?? $item['selesaipelaksanaan'] ?? $item['enddate'] ?? $item['tglselesai'] ?? $item['selesai'] ?? $item['end']
                ?? $item['tanggalberakhir'] ?? $item['tglberakhir'] ?? $item['tanggalakhir'] ?? $item['tglakhir'] ?? null
            );
            $tglPelaksanaan = $this->jsonValue($item['tanggalpelaksanaan'] ?? null);
            if (empty($tglPelaksanaan) && $mulai && $selesai) {
                $tglPelaksanaan = $this->generateDateRange($mulai, $selesai);
            }
            if (! $mulai && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
                $mulai = reset($tglPelaksanaan);
            }
            if (! $selesai && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
                $selesai = end($tglPelaksanaan);
            }

            $record = [
                'Kategori_Program' => ! empty($item['kategoriprogram']) ? $item['kategoriprogram'] : (! empty($item['kategori']) ? $item['kategori'] : 'REGULER'),
                'Training_Program' => $trainingProgram,
                'Batch' => $item['batch'] ?? $item['angkatan'] ?? null,
                'Lokasi_training' => $item['lokasitraining'] ?? $item['lokasi'] ?? null,
                'Mulai_program' => $mulai,
                'Selesai_program' => $selesai,
                'jumlah_bulan_training' => is_numeric($item['jumlahbulantraining'] ?? null) ? (int) $item['jumlahbulantraining'] : (is_numeric($item['durasi'] ?? null) ? (int) $item['durasi'] : 0),
                'jumlah_hari' => is_numeric($item['jumlahhari'] ?? null) ? (int) $item['jumlahhari'] : 0,
                'Total_training_days' => is_numeric($item['totaltrainingdays'] ?? null) ? (int) $item['totaltrainingdays'] : (is_numeric($item['totalhari'] ?? null) ? (int) $item['totalhari'] : 0),
                'Promosi_mandor' => is_numeric($item['promosimandor'] ?? null) ? (int) $item['promosimandor'] : 0,
                'Fresh_Graduate' => is_numeric($item['freshgraduate'] ?? null) ? (int) $item['freshgraduate'] : 0,
                'target_peserta' => is_numeric($item['targetpeserta'] ?? null) ? (int) $item['targetpeserta'] : (is_numeric($item['totalorang'] ?? null) ? (int) $item['totalorang'] : 0),
                'Uang_saku' => is_numeric($item['uangsaku'] ?? null) ? (float) $item['uangsaku'] : 0,
                'jenis_program_reguler' => $item['jenisprogramreguler'] ?? $item['kategorireguler'] ?? $item['subkategori'] ?? null,
                'Region' => $item['region'] ?? $item['wilayah'] ?? null,
                'tanggal_pelaksanaan' => $tglPelaksanaan,
                'tahun' => is_numeric($item['tahun'] ?? null) ? (int) $item['tahun'] : null,
                'kelas' => is_numeric($item['kelas'] ?? null) ? (int) $item['kelas'] : null,
                'resign' => is_numeric($item['resign'] ?? null) ? (int) $item['resign'] : null,
                'staff' => is_numeric($item['staff'] ?? ($item['lulus'] ?? null)) ? (int) ($item['staff'] ?? $item['lulus']) : null,
                'lulus' => is_numeric($item['lulus'] ?? ($item['staff'] ?? null)) ? (int) ($item['lulus'] ?? $item['staff']) : null,
            ];

            // Normalize nulls/empty strings
            $record = array_map(fn ($v) => is_array($v) ? $v : ($v === '' || strtolower(trim((string) $v)) === 'null' ? null : $v), $record);

            $id = $item['programregulerid'] ?? $item['programid'] ?? $item['id'] ?? null;
            if (is_numeric($id) && (int) $id > 0) {
                $intId = (int) $id;
                if (! in_array($intId, $seenRegIds, true)) {
                    $seenRegIds[] = $intId;
                    $record['Program_reguler_id'] = $intId;
                    ProgramReguler::query()->updateOrCreate(['Program_reguler_id' => $intId], $record);
                } else {
                    ProgramReguler::query()->create($record);
                }
            } else {
                ProgramReguler::query()->create($record);
            }

            return true;
        } else {
            $jenisPelatihan = $item['jenispelatihan'] ?? $item['namapelatihan'] ?? $item['pelatihan'] ?? $item['trainingprogram'] ?? '';
            if ($jenisPelatihan === '') {
                return false;
            }

            $start = $this->dateValue(
                $item['tanggalmulaipelaksanaan'] ?? $item['tanggalmulai'] ?? $item['startdate'] ?? $item['mulaiprogram']
                ?? $item['tglmulaipelaksanaan'] ?? $item['mulaipelaksanaan'] ?? $item['tglmulai'] ?? $item['mulai']
                ?? $item['start'] ?? $item['tanggalawal'] ?? $item['tglawal'] ?? null
            );
            $end = $this->dateValue(
                $item['tanggalselesaipelaksanaan'] ?? $item['tanggalselesai'] ?? $item['enddate'] ?? $item['selesaiprogram']
                ?? $item['tglselesaipelaksanaan'] ?? $item['selesaipelaksanaan'] ?? $item['tglselesai'] ?? $item['selesai']
                ?? $item['end'] ?? $item['tanggalberakhir'] ?? $item['tglberakhir'] ?? $item['tanggalakhir'] ?? $item['tglakhir'] ?? null
            );
            $tglPelaksanaan = $this->jsonValue($item['tanggalpelaksanaan'] ?? null);
            if (empty($tglPelaksanaan) && $start && $end) {
                $tglPelaksanaan = $this->generateDateRange($start, $end);
            }
            if (! $start && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
                $start = reset($tglPelaksanaan);
            }
            if (! $end && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
                $end = end($tglPelaksanaan);
            }

            $record = [
                'kategori_pelatihan' => $item['kategoripelatihan'] ?? $item['kategoriprogram'] ?? $item['kategori'] ?? null,
                'jenis_pelatihan' => $jenisPelatihan,
                'region' => $item['region'] ?? $item['wilayah'] ?? null,
                'trainer' => $item['trainer'] ?? $item['instruktur'] ?? null,
                'type_training' => $item['typetraining'] ?? $item['metode'] ?? null,
                'total_jam' => is_numeric($item['totaljam'] ?? null) ? (int) $item['totaljam'] : 0,
                'total_hari' => is_numeric($item['totalhari'] ?? null) ? (int) $item['totalhari'] : 0,
                'peserta_non_staf' => is_numeric($item['pesertanonstaf'] ?? null) ? (int) $item['pesertanonstaf'] : 0,
                'peserta_ast' => is_numeric($item['pesertaast'] ?? null) ? (int) $item['pesertaast'] : 0,
                'peserta_askep' => is_numeric($item['pesertaaskep'] ?? null) ? (int) $item['pesertaaskep'] : 0,
                'peserta_mgr' => is_numeric($item['pesertamgr'] ?? null) ? (int) $item['pesertamgr'] : 0,
                'peserta_gm' => is_numeric($item['pesertagm'] ?? null) ? (int) $item['pesertagm'] : 0,
                'peserta_head' => is_numeric($item['pesertahead'] ?? null) ? (int) $item['pesertahead'] : 0,
                'target_staf' => is_numeric($item['targetstaf'] ?? null) ? (int) $item['targetstaf'] : 0,
                'target_non_staf' => is_numeric($item['targetnonstaf'] ?? null) ? (int) $item['targetnonstaf'] : 0,
                'target_total' => is_numeric($item['targettotal'] ?? null) ? (int) $item['targettotal'] : 0,
                'training_staf' => is_numeric($item['trainingstaf'] ?? null) ? (int) $item['trainingstaf'] : 0,
                'training_non_staf' => is_numeric($item['trainingnonstaf'] ?? null) ? (int) $item['trainingnonstaf'] : 0,
                'training_total' => is_numeric($item['trainingtotal'] ?? null) ? (int) $item['trainingtotal'] : 0,
                'td_staf' => is_numeric($item['tdstaf'] ?? null) ? (int) $item['tdstaf'] : 0,
                'td_non_staf' => is_numeric($item['tdnonstaf'] ?? null) ? (int) $item['tdnonstaf'] : 0,
                'td_total' => is_numeric($item['tdtotal'] ?? null) ? (int) $item['tdtotal'] : 0,
                'start_date' => $start,
                'end_date' => $end,
                'realisasi_batch' => is_numeric($item['realisasibatch'] ?? null) ? (int) $item['realisasibatch'] : (is_numeric($item['batch'] ?? null) ? (int) $item['batch'] : (is_numeric($item['totalbatch'] ?? null) ? (int) $item['totalbatch'] : 0)),
                'lokasi_training' => $item['lokasitraining'] ?? $item['lokasi'] ?? null,
                'deskripsi' => $item['deskripsi'] ?? $item['keterangan'] ?? null,
                'sub_kategori' => $item['subkategori'] ?? null,
                'tanggal_pelaksanaan' => $tglPelaksanaan,
                'TARGET_BATCH' => is_numeric($item['targetbatch'] ?? null) ? (int) $item['targetbatch'] : 0,
                'TARGET_PESERTA' => is_numeric($item['targetpeserta'] ?? null) ? (int) $item['targetpeserta'] : (is_numeric($item['targettotal'] ?? null) ? (int) $item['targettotal'] : 0),
                'tahun' => is_numeric($item['tahun'] ?? null) ? (int) $item['tahun'] : null,
            ];

            // Normalize nulls/empty strings
            $record = array_map(fn ($v) => is_array($v) ? $v : ($v === '' || strtolower(trim((string) $v)) === 'null' ? null : $v), $record);

            $id = $item['idnonreguler'] ?? $item['programid'] ?? $item['id'] ?? null;
            if (is_numeric($id) && (int) $id > 0) {
                $intId = (int) $id;
                if (! in_array($intId, $seenNonRegIds, true)) {
                    $seenNonRegIds[] = $intId;
                    $record['id_nonreguler'] = $intId;
                    ProgramNonReguler::query()->updateOrCreate(['id_nonreguler' => $intId], $record);
                } else {
                    ProgramNonReguler::query()->create($record);
                }
            } else {
                ProgramNonReguler::query()->create($record);
            }

            return true;
        }
    }

    private function preparePayload(array $payload, string $type): array
    {
        if ($type === 'reguler') {
            if (array_key_exists('Nama_Program_reguler', $payload) && ! empty($payload['Nama_Program_reguler'])) {
                $payload['Training_Program'] = $payload['Nama_Program_reguler'];
            }
            if (array_key_exists('Total_orang', $payload)) {
                $payload['target_peserta'] = $payload['Total_orang'];
            }
        } else {
            if (array_key_exists('total_batch', $payload) && ! array_key_exists('realisasi_batch', $payload)) {
                $payload['realisasi_batch'] = $payload['total_batch'];
            }
        }

        $dateFields = $type === 'reguler' ? ['Mulai_program', 'Selesai_program'] : ['start_date', 'end_date'];
        foreach ($dateFields as $field) {
            $payload[$field] = $this->dateValue($payload[$field] ?? null);
        }
        if (array_key_exists('tanggal_pelaksanaan', $payload)) {
            $payload['tanggal_pelaksanaan'] = $this->jsonValue($payload['tanggal_pelaksanaan']);
        }

        $numericFields = $type === 'reguler'
            ? ['jumlah_bulan_training', 'jumlah_hari', 'Total_training_days', 'Promosi_mandor', 'Fresh_Graduate', 'target_peserta', 'tahun', 'kelas', 'resign', 'staff', 'lulus']
            : ['total_jam', 'total_hari', 'peserta_non_staf', 'peserta_ast', 'peserta_askep', 'peserta_mgr', 'peserta_gm', 'peserta_head', 'target_staf', 'target_non_staf', 'target_total', 'training_staf', 'training_non_staf', 'training_total', 'td_staf', 'td_non_staf', 'td_total', 'realisasi_batch', 'total_batch', 'TARGET_BATCH', 'TARGET_PESERTA', 'tahun'];
        foreach ($numericFields as $field) {
            if (array_key_exists($field, $payload)) {
                $payload[$field] = is_numeric($payload[$field]) ? (int) $payload[$field] : null;
            }
        }
        if ($type === 'reguler') {
            if (array_key_exists('staff', $payload) && ! array_key_exists('lulus', $payload)) {
                $payload['lulus'] = $payload['staff'];
            } elseif (array_key_exists('lulus', $payload) && ! array_key_exists('staff', $payload)) {
                $payload['staff'] = $payload['lulus'];
            }
        }
        if ($type === 'reguler' && array_key_exists('Uang_saku', $payload)) {
            $payload['Uang_saku'] = is_numeric($payload['Uang_saku']) ? (float) $payload['Uang_saku'] : 0;
        }

        $allowedColumns = $type === 'reguler'
            ? ['Kategori_Program', 'Training_Program', 'Batch', 'Lokasi_training', 'Mulai_program', 'Selesai_program', 'jumlah_bulan_training', 'jumlah_hari', 'Total_training_days', 'Promosi_mandor', 'Fresh_Graduate', 'target_peserta', 'Uang_saku', 'jenis_program_reguler', 'Region', 'tanggal_pelaksanaan', 'tahun', 'kelas', 'resign', 'staff', 'lulus']
            : ['kategori_pelatihan', 'jenis_pelatihan', 'region', 'trainer', 'type_training', 'total_jam', 'total_hari', 'peserta_non_staf', 'peserta_ast', 'peserta_askep', 'peserta_mgr', 'peserta_gm', 'peserta_head', 'target_staf', 'target_non_staf', 'target_total', 'training_staf', 'training_non_staf', 'training_total', 'td_staf', 'td_non_staf', 'td_total', 'start_date', 'end_date', 'realisasi_batch', 'total_batch', 'lokasi_training', 'deskripsi', 'sub_kategori', 'tanggal_pelaksanaan', 'TARGET_BATCH', 'TARGET_PESERTA', 'tahun'];

        return array_filter($payload, fn ($value, $key) => in_array($key, $allowedColumns, true), ARRAY_FILTER_USE_BOTH);
    }

    private function normalizeNonReguler(ProgramNonReguler $item, $actualRecords = null): array
    {
        $actualRecords = $actualRecords ?? collect();
        $orig = $item->toArray();

        // 1. Hitung berdasarkan Jabatan (Position)
        $orig['peserta_ast'] = $actualRecords->filter(fn ($r) => str_contains(strtolower($r->position), 'asisten') && ! str_contains(strtolower($r->position), 'kepala'))->count();
        $orig['peserta_askep'] = $actualRecords->filter(fn ($r) => str_contains(strtolower($r->position), 'askep') || (str_contains(strtolower($r->position), 'asisten') && str_contains(strtolower($r->position), 'kepala')))->count();
        $orig['peserta_mgr'] = $actualRecords->filter(fn ($r) => str_contains(strtolower($r->position), 'manager') && ! str_contains(strtolower($r->position), 'general'))->count();
        $orig['peserta_gm'] = $actualRecords->filter(fn ($r) => str_contains(strtolower($r->position), 'general manager') || str_contains(strtolower($r->position), 'gm'))->count();
        $orig['peserta_head'] = $actualRecords->filter(fn ($r) => str_contains(strtolower($r->position), 'head'))->count();

        // 2. Hitung berdasarkan Golongan (Staf vs Non-Staf)
        $orig['training_staf'] = $actualRecords->filter(fn ($r) => strtolower(trim($r->gol)) === 'staf')->count();
        $orig['training_non_staf'] = $actualRecords->filter(fn ($r) => strtolower(trim($r->gol)) !== 'staf')->count();
        $orig['training_total'] = $actualRecords->count();

        // 3. Hitung Training Days (TD)
        $totalDays = (int) $item->total_hari;
        $orig['td_staf'] = $orig['training_staf'] * $totalDays;
        $orig['td_non_staf'] = $orig['training_non_staf'] * $totalDays;
        $orig['td_total'] = $orig['training_total'] * $totalDays;

        $batchCount = (int) ($item->TARGET_BATCH ?? $item->total_batch ?? 0);
        $participants = (int) ($item->TARGET_PESERTA ?? $item->target_total ?? 0);
        $year = $item->tahun ?: ($item->start_date ? (int) $item->start_date->format('Y') : ($item->end_date ? (int) $item->end_date->format('Y') : null));

        $startDate = $item->start_date;
        $endDate = $item->end_date;
        $tglPelaksanaan = $item->tanggal_pelaksanaan;
        if (! $startDate && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
            $startDate = reset($tglPelaksanaan);
        }
        if (! $endDate && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
            $endDate = end($tglPelaksanaan);
        }

        return [
            'id' => 'non-reg-'.$item->id_nonreguler,
            'title' => $item->jenis_pelatihan,
            'start' => $startDate,
            'end' => $endDate,
            'tahun' => $year,
            'tanggal_pelaksanaan' => $tglPelaksanaan ?: array_values(array_filter([$item->start_date?->format('Y-m-d'), $item->end_date?->format('Y-m-d')])),
            'type' => 'Non-Reguler',
            'location' => $item->lokasi_training,
            'region' => $item->region,
            'subCategory' => $item->sub_kategori ?: '-',
            'batchCount' => $batchCount,
            'participants' => $participants,
            'trainer' => $item->trainer,
            'originalData' => $orig,
        ];
    }

    private function normalizeReguler(ProgramReguler $item, $actualRecords = null): array
    {
        $actualRecords = $actualRecords ?? collect();
        $orig = $item->toArray();

        // 1. Hitung berdasarkan Golongan
        $orig['training_staf'] = $actualRecords->filter(fn ($r) => strtolower(trim($r->gol)) === 'staf')->count();
        $orig['training_non_staf'] = $actualRecords->filter(fn ($r) => strtolower(trim($r->gol)) !== 'staf')->count();
        $orig['training_total'] = $actualRecords->count();

        // 2. Hitung Training Days (TD)
        $totalDays = (int) ($item->jumlah_hari ?? $item->Total_training_days ?? 0);
        $orig['td_staf'] = $orig['training_staf'] * $totalDays;
        $orig['td_non_staf'] = $orig['training_non_staf'] * $totalDays;
        $orig['td_total'] = $orig['training_total'] * $totalDays;

        $orig['Nama_Program_reguler'] = $item->Training_Program;
        $orig['Total_orang'] = $item->target_peserta;
        $year = $item->tahun ?: ($item->Mulai_program ? (int) $item->Mulai_program->format('Y') : ($item->Selesai_program ? (int) $item->Selesai_program->format('Y') : null));

        $startDate = $item->Mulai_program;
        $endDate = $item->Selesai_program;
        $tglPelaksanaan = $item->tanggal_pelaksanaan;
        if (! $startDate && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
            $startDate = reset($tglPelaksanaan);
        }
        if (! $endDate && is_array($tglPelaksanaan) && ! empty($tglPelaksanaan)) {
            $endDate = end($tglPelaksanaan);
        }

        return [
            'id' => 'reg-'.$item->Program_reguler_id,
            'title' => $item->Training_Program,
            'start' => $startDate,
            'end' => $endDate,
            'tahun' => $year,
            'tanggal_pelaksanaan' => $tglPelaksanaan ?: array_values(array_filter([$item->Mulai_program?->format('Y-m-d'), $item->Selesai_program?->format('Y-m-d')])),
            'type' => 'Reguler',
            'location' => $item->Lokasi_training,
            'region' => $item->Region,
            'subCategory' => $item->jenis_program_reguler ?: '-',
            'batchCount' => 1,
            'participants' => $item->target_peserta,
            'trainer' => 'Internal',
            'originalData' => $orig,
        ];
    }

    private function dateValue($value): ?string
    {
        return SpreadsheetReader::parseDate($value);
    }

    private function jsonValue($value): ?array
    {
        if (is_array($value)) {
            return $value;
        }
        if ($value === null || trim((string) $value) === '' || trim((string) $value) === 'null') {
            return null;
        }
        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function generateDateRange(?string $startDate, ?string $endDate): ?array
    {
        if (! $startDate || ! $endDate) {
            return array_values(array_filter([$startDate, $endDate]));
        }

        try {
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);

            if ($start > $end) {
                return [$startDate, $endDate];
            }

            $diff = $start->diff($end)->days;
            if ($diff > 366) {
                return [$startDate, $endDate];
            }

            $dates = [];
            $curr = clone $start;
            while ($curr <= $end) {
                if ((int) $curr->format('w') !== 0) { // Exclude Sunday
                    $dates[] = $curr->format('Y-m-d');
                }
                $curr->modify('+1 day');
            }

            return ! empty($dates) ? $dates : [$startDate, $endDate];
        } catch (\Throwable $e) {
            return array_values(array_filter([$startDate, $endDate]));
        }
    }
}
