<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\TraineesImport;
use App\Models\Trainee;
use App\Models\TrainingProgram;
use Illuminate\Http\Request;

class NonRegulerTraineeController extends Controller
{
    public function index()
    {
        $trainees = Trainee::with(['trainingProgram', 'resignation'])->latest()->paginate(10);
        $total = Trainee::count();
        $aktif = Trainee::where('status', 'aktif')->count();
        $pasif = Trainee::where('status', 'pasif')->count();
        $resign = Trainee::where('status', 'resign')->count();
        $programs = TrainingProgram::all();

        return view('admin.non-reguler.trainees.index', compact('trainees', 'total', 'aktif', 'pasif', 'resign', 'programs'));
    }

    public function create()
    {
        $programs = TrainingProgram::all();

        return view('admin.non-reguler.trainees.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:255|unique:trainees,nik',
            'name' => 'required|string|max:255',
            'origin' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'status' => 'required|string',
            'training_program_id' => 'required|exists:training_programs,id',
            // OJT fields
            'ojt_company_name' => 'nullable|string|max:255',
            'ojt_region' => 'nullable|string|max:255',
            'ojt_location_code' => 'nullable|string|max:255',
            // Resignation fields
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'resign_reason' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'avg_inclass_score' => 'nullable|numeric',
            'inclass_rank' => 'nullable|integer',
            'avg_practice_score' => 'nullable|numeric',
            'practice_rank' => 'nullable|integer',
        ]);

        $traineeData = collect($validated)->only([
            'nik', 'name', 'origin', 'university', 'major', 'status', 'training_program_id',
            'ojt_company_name', 'ojt_region', 'ojt_location_code',
        ])->toArray();
        $trainee = Trainee::create($traineeData);

        if ($request->status === 'resign') {
            $resignData = [
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'reason' => $request->resign_reason,
                'avg_inclass_score' => $request->avg_inclass_score ?? 0,
                'inclass_rank' => $request->inclass_rank ?? 0,
                'avg_practice_score' => $request->avg_practice_score ?? 0,
                'practice_rank' => $request->practice_rank ?? 0,
            ];
            if ($request->hasFile('photo')) {
                $resignData['photo_path'] = $request->file('photo')->store('trainee_photos', 'public');
            }
            $trainee->resignation()->create($resignData);
        }

        return redirect()->route('training-non-reguler.trainees.index')->with('success', 'Trainee berhasil ditambahkan.');
    }

    public function edit(Trainee $trainee)
    {
        $programs = TrainingProgram::all();

        return view('admin.non-reguler.trainees.edit', compact('trainee', 'programs'));
    }

    public function update(Request $request, Trainee $trainee)
    {
        $validated = $request->validate([
            'nik' => 'required|string|max:255|unique:trainees,nik,'.$trainee->id,
            'name' => 'required|string|max:255',
            'origin' => 'nullable|string|max:255',
            'university' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'status' => 'required|string',
            'training_program_id' => 'required|exists:training_programs,id',
            // OJT fields
            'ojt_company_name' => 'nullable|string|max:255',
            'ojt_region' => 'nullable|string|max:255',
            'ojt_location_code' => 'nullable|string|max:255',
            // Resignation fields
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'resign_reason' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'avg_inclass_score' => 'nullable|numeric',
            'inclass_rank' => 'nullable|integer',
            'avg_practice_score' => 'nullable|numeric',
            'practice_rank' => 'nullable|integer',
        ]);

        $traineeData = collect($validated)->only([
            'nik', 'name', 'origin', 'university', 'major', 'status', 'training_program_id',
            'ojt_company_name', 'ojt_region', 'ojt_location_code',
        ])->toArray();
        $trainee->update($traineeData);

        if ($request->status === 'resign') {
            $resignData = [
                'birth_place' => $request->birth_place,
                'birth_date' => $request->birth_date,
                'reason' => $request->resign_reason,
                'avg_inclass_score' => $request->avg_inclass_score ?? 0,
                'inclass_rank' => $request->inclass_rank ?? 0,
                'avg_practice_score' => $request->avg_practice_score ?? 0,
                'practice_rank' => $request->practice_rank ?? 0,
            ];
            if ($request->hasFile('photo')) {
                $resignData['photo_path'] = $request->file('photo')->store('trainee_photos', 'public');
            }
            $trainee->resignation()->updateOrCreate(['trainee_id' => $trainee->id], $resignData);
        } else {
            // If status changed to non-resign, maybe delete resignation record? Or keep it?
            // Usually we keep it but it won't be shown. Let's leave it.
        }

        return redirect()->route('training-non-reguler.trainees.index')->with('success', 'Trainee berhasil diperbarui.');
    }

    public function destroy(Trainee $trainee)
    {
        $trainee->delete();

        return redirect()->route('training-non-reguler.trainees.index')->with('success', 'Trainee berhasil dihapus.');
    }

    /**
     * Import trainees from Excel/CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
            'training_program_id' => 'required|exists:training_programs,id',
        ]);

        try {
            $importer = new TraineesImport((int) $request->training_program_id);
            $importer->import($request->file('file'));

            $count = $importer->getImportedCount();
            $errors = $importer->getErrors();

            if (! empty($errors)) {
                return redirect()->route('training-non-reguler.trainees.index')
                    ->with('import_errors', $errors)
                    ->with('error', "{$count} trainee berhasil diimport, namun ada ".count($errors).' baris yang gagal.');
            }

            return redirect()->route('training-non-reguler.trainees.index')
                ->with('success', "{$count} trainee berhasil diimport dari file Excel/CSV!");
        } catch (\Exception $e) {
            return redirect()->route('training-non-reguler.trainees.index')
                ->with('error', 'Terjadi kesalahan saat import: '.$e->getMessage());
        }
    }

    /**
     * Download Excel template for import
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_trainee.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            // BOM for UTF-8 compatibility with Excel
            fwrite($file, "\xEF\xBB\xBF");
            // Header row - these are the heading keys the import expects
            fputcsv($file, ['nik', 'nama', 'tempat_lahir', 'tgl_lahir', 'asal_daerah', 'universitas', 'jurusan', 'status']);
            // Example data rows
            fputcsv($file, ['100123', 'Muhammad Fikri', 'Medan', '2001-05-15', 'Sumatera Utara', 'Universitas Sumatera Utara', 'Teknik Pertanian', 'aktif']);
            fputcsv($file, ['100124', 'Aldi Zulkarnain', 'Pekanbaru', '2002-03-20', 'Riau', 'Universitas Riau', 'Agribisnis', 'aktif']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
