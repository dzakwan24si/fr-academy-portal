<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Http\Request;

class NonRegulerTrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::latest()->paginate(10);
        $total = Trainer::count();

        return view('admin.non-reguler.trainers.index', compact('trainers', 'total'));
    }

    public function create()
    {
        $users = User::all();

        return view('admin.non-reguler.trainers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);
        Trainer::create($validated);

        return redirect()->route('training-non-reguler.trainers.index')->with('success', 'Trainer berhasil ditambahkan.');
    }

    public function edit(Trainer $trainer)
    {
        $users = User::all();

        return view('admin.non-reguler.trainers.edit', compact('trainer', 'users'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ]);
        $trainer->update($validated);

        return redirect()->route('training-non-reguler.trainers.index')->with('success', 'Trainer berhasil diperbarui.');
    }

    public function destroy(Trainer $trainer)
    {
        $trainer->delete();

        return redirect()->route('training-non-reguler.trainers.index')->with('success', 'Trainer berhasil dihapus.');
    }
}
