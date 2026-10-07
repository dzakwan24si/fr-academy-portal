<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = HomepageEvent::latest()->paginate(10);

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? true : false;

        // Jika event ini diaktifkan, matikan semua event lain
        if ($isActive) {
            HomepageEvent::where('is_active', true)->update(['is_active' => false]);
        }

        // Upload gambar
        $imagePath = $request->file('image')->store('events', 'public');

        HomepageEvent::create([
            'name' => $validated['name'],
            'image_path' => 'storage/'.$imagePath,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Tema event berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomepageEvent $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HomepageEvent $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->has('is_active') ? true : false;

        // Jika event ini diaktifkan, matikan semua event lain
        if ($isActive && ! $event->is_active) {
            HomepageEvent::where('is_active', true)->update(['is_active' => false]);
        }

        $dataToUpdate = [
            'name' => $validated['name'],
            'is_active' => $isActive,
        ];

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($event->image_path) {
                // Konversi path "storage/events/xxx" menjadi "events/xxx" untuk dihapus dari storage disk public
                $oldPath = str_replace('storage/', '', $event->image_path);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $imagePath = $request->file('image')->store('events', 'public');
            $dataToUpdate['image_path'] = 'storage/'.$imagePath;
        }

        $event->update($dataToUpdate);

        return redirect()->route('admin.events.index')->with('success', 'Tema event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomepageEvent $event)
    {
        if ($event->image_path) {
            $oldPath = str_replace('storage/', '', $event->image_path);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Tema event berhasil dihapus.');
    }
}
