<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerProgram;
use Illuminate\Http\Request;

class CareerProgramController extends Controller
{
    public function index()
    {
        $programs = CareerProgram::latest()->get();
        return view('admin.career_programs.index', compact('programs'));
    }

    public function edit(CareerProgram $career_program)
    {
        return view('admin.career_programs.edit', compact('career_program'));
    }

    public function update(Request $request, CareerProgram $career_program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:career_programs,code,' . $career_program->id,
            'description' => 'required|string',
            'qualifications' => 'required|string',
            'gform_url' => 'required|url|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $career_program->update($validated);

        return redirect()->route('admin.career_programs.index')
                         ->with('success', 'Program karir berhasil diperbarui.');
    }

    public function create()
    {
        return view('admin.career_programs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:career_programs,code',
            'description' => 'required|string',
            'qualifications' => 'required|string',
            'gform_url' => 'required|url|max:255',
            'is_active' => 'boolean'
        ]);

        CareerProgram::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'qualifications' => $request->qualifications,
            'gform_url' => $request->gform_url,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.career_programs.index')->with('success', 'Program karir berhasil ditambahkan.');
    }

    public function destroy(CareerProgram $careerProgram)
    {
        $careerProgram->delete();
        return redirect()->route('admin.career_programs.index')->with('success', 'Program karir berhasil dihapus.');
    }
}