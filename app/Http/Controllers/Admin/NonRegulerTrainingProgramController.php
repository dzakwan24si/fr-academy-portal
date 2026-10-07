<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use Illuminate\Http\Request;

class NonRegulerTrainingProgramController extends Controller
{
    public function index()
    {
        $programs = TrainingProgram::withCount(['dailyClasses', 'trainees'])->latest()->paginate(10);
        $total = TrainingProgram::count();
        $totalFat = TrainingProgram::where('type', 'FAT')->count();
        $totalTat = TrainingProgram::where('type', 'TAT')->count();
        $totalMat = TrainingProgram::where('type', 'MAT')->count();
        $totalAat = TrainingProgram::where('type', 'AAT')->count();

        return view('admin.non-reguler.training_programs.index', compact('programs', 'total', 'totalFat', 'totalTat', 'totalMat', 'totalAat'));
    }

    public function create()
    {
        return view('admin.non-reguler.training_programs.create');
    }

    public function store(Request $request)
    {
        // Auto-fill type based on name if not provided
        if (! $request->has('type') || empty($request->type)) {
            $request->merge(['type' => explode(' ', $request->name)[0] ?? '']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'year' => 'required|numeric',
            'description' => 'nullable|string',
            'quote' => 'nullable|string',
        ]);
        TrainingProgram::create($validated);

        return redirect()->route('training-non-reguler.training-programs.index')->with('success', 'Program Training berhasil ditambahkan.');
    }

    public function edit(TrainingProgram $trainingProgram)
    {
        return view('admin.non-reguler.training_programs.edit', compact('trainingProgram'));
    }

    public function update(Request $request, TrainingProgram $trainingProgram)
    {
        // Auto-fill type based on name if not provided
        if (! $request->has('type') || empty($request->type)) {
            $request->merge(['type' => explode(' ', $request->name)[0] ?? '']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'year' => 'required|numeric',
            'description' => 'nullable|string',
            'quote' => 'nullable|string',
        ]);
        $trainingProgram->update($validated);

        return redirect()->route('training-non-reguler.training-programs.index')->with('success', 'Program Training berhasil diperbarui.');
    }

    public function destroy(TrainingProgram $trainingProgram)
    {
        $trainingProgram->delete();

        return redirect()->route('training-non-reguler.training-programs.index')->with('success', 'Program Training berhasil dihapus.');
    }
}
