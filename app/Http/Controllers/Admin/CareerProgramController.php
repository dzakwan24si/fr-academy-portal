<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerProgramController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.page_settings.index', ['tab' => 'karir']);
    }

    public function edit(CareerProgram $career_program)
    {
        return view('admin.career_programs.edit', compact('career_program'));
    }

    public function update(Request $request, CareerProgram $career_program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:career_programs,code,'.$career_program->id,
            'description' => 'required|string',
            'qualifications' => 'required|string',
            'gform_url' => 'required|url|max:255',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            // Delete old custom image if exists
            if ($career_program->image && Storage::disk('public')->exists($career_program->image)) {
                Storage::disk('public')->delete($career_program->image);
            }
            $validated['image'] = $request->file('image')->store('career_programs', 'public');
        }

        $career_program->update($validated);

        return redirect()->route('admin.page_settings.index', ['tab' => 'karir'])
            ->with('success', 'Program karir berhasil diperbarui.');
    }

    public function create()
    {
        return view('admin.career_programs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:career_programs,code',
            'description' => 'required|string',
            'qualifications' => 'required|string',
            'gform_url' => 'required|url|max:255',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('career_programs', 'public');
        }

        CareerProgram::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'qualifications' => $request->qualifications,
            'gform_url' => $request->gform_url,
            'is_active' => $request->has('is_active'),
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.page_settings.index', ['tab' => 'karir'])->with('success', 'Program karir berhasil ditambahkan.');
    }

    public function destroy(CareerProgram $careerProgram)
    {
        if ($careerProgram->image && Storage::disk('public')->exists($careerProgram->image)) {
            Storage::disk('public')->delete($careerProgram->image);
        }

        $careerProgram->delete();

        return redirect()->route('admin.page_settings.index', ['tab' => 'karir'])->with('success', 'Program karir berhasil dihapus.');
    }
}
