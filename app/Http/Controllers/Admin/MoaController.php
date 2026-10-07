<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Moa;
use App\Models\Trainer;
use App\Models\TrainingProgram;
use Illuminate\Http\Request;

class MoaController extends Controller
{
    public function index()
    {
        $moas = Moa::with(['trainingProgram', 'trainer'])->latest()->paginate(10);
        $total = Moa::count();
        $draft = Moa::where('status', 'draft')->count();
        $submitted = Moa::where('status', 'submitted')->count();

        return view('admin.moas.index', compact('moas', 'total', 'draft', 'submitted'));
    }

    public function create()
    {
        $programs = TrainingProgram::all();
        $trainers = Trainer::all();

        return view('admin.moas.create', compact('programs', 'trainers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'trainer_id' => 'required|exists:trainers,id',
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'notes' => 'nullable|string',
            'status' => 'required|string',
        ]);
        Moa::create($validated);

        return redirect()->route('training-reguler.moas.index')->with('success', 'MOA berhasil ditambahkan.');
    }

    public function edit(Moa $moa)
    {
        $programs = TrainingProgram::all();
        $trainers = Trainer::all();

        return view('admin.moas.edit', compact('moa', 'programs', 'trainers'));
    }

    public function update(Request $request, Moa $moa)
    {
        $validated = $request->validate([
            'training_program_id' => 'required|exists:training_programs,id',
            'trainer_id' => 'required|exists:trainers,id',
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date|after_or_equal:week_start_date',
            'notes' => 'nullable|string',
            'status' => 'required|string',
        ]);
        $moa->update($validated);

        return redirect()->route('training-reguler.moas.index')->with('success', 'MOA berhasil diperbarui.');
    }

    public function destroy(Moa $moa)
    {
        $moa->delete();

        return redirect()->route('training-reguler.moas.index')->with('success', 'MOA berhasil dihapus.');
    }
}
