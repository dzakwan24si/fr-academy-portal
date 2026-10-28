<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortalProgramController extends Controller
{
    /**
     * Redirect to the CMS Page Settings with Program tab.
     */
    public function index()
    {
        return redirect()->route('admin.page_settings.index', ['tab' => 'program']);
    }

    /**
     * Show the form for creating a new program.
     */
    public function create()
    {
        return view('admin.portal_programs.create');
    }

    /**
     * Store a newly created program in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:portal_programs,slug',
            'code' => 'nullable|string|max:50',
            'subtitle' => 'nullable|string|max:255',
            'category_key' => 'nullable|string|max:50',
            'category_name' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:100',
            'is_reguler' => 'nullable',
            'overview' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',

            // Reguler specific
            'curriculum_pillars_text' => 'nullable|string',
            'objectives_text' => 'nullable|string',
            'method_ratio' => 'nullable|string|max:50',
            'method_desc' => 'nullable|string|max:255',
        ]);

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (PortalProgram::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $isReguler = $request->boolean('is_reguler');

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('portal_programs', 'public');
        }

        $heroImagePath = null;
        if ($request->hasFile('hero_image_file')) {
            $heroImagePath = $request->file('hero_image_file')->store('portal_programs', 'public');
        }

        // Parse curriculum pillars (tersedia untuk reguler maupun non-reguler)
        $curriculum = null;
        if (!empty($validated['curriculum_pillars_text'])) {
            $lines = preg_split('/\r\n|\r|\n/', $validated['curriculum_pillars_text']);
            $curriculum = array_values(array_filter(array_map('trim', $lines)));
        }

        // Parse objectives / benefit (tersedia untuk reguler maupun non-reguler)
        $objectives = null;
        if (!empty($validated['objectives_text'])) {
            $lines = preg_split('/\r\n|\r|\n/', $validated['objectives_text']);
            $objectives = array_values(array_filter(array_map('trim', $lines)));
        }

        // Learning method (hanya aktif untuk program reguler trainee)
        $learningMethod = null;
        if ($isReguler) {
            $learningMethod = [
                'ratio' => !empty($validated['method_ratio']) ? trim($validated['method_ratio']) : '70 : 20 : 10',
                'desc' => !empty($validated['method_desc']) ? trim($validated['method_desc']) : '70% experiential learning, 20% social learning, dan 10% formal learning',
            ];
        }

        $categoryName = !empty($validated['category_name']) ? trim($validated['category_name']) : 'Program Pelatihan';
        $categoryKey = !empty($validated['category_key']) ? Str::slug($validated['category_key']) : Str::slug($categoryName);

        PortalProgram::create([
            'slug' => $slug,
            'code' => !empty($validated['code']) ? strtoupper(trim($validated['code'])) : strtoupper($slug),
            'title' => trim($validated['title']),
            'subtitle' => $validated['subtitle'] ?? null,
            'category_key' => $categoryKey,
            'category_name' => $categoryName,
            'badge' => $validated['badge'] ?? null,
            'is_reguler' => $isReguler,
            'overview' => trim($validated['overview']),
            'image' => $imagePath,
            'hero_image' => $heroImagePath,
            'curriculum_pillars' => $curriculum,
            'objectives' => $objectives,
            'learning_method' => $learningMethod,
            'order' => (int) ($validated['order'] ?? 0),
            'is_active' => $request->has('is_active') ? true : true,
        ]);

        return redirect()->route('admin.page_settings.index', ['tab' => 'program'])
            ->with('success', 'Program pelatihan "' . $validated['title'] . '" berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified program.
     */
    public function edit(PortalProgram $portal_program)
    {
        return view('admin.portal_programs.edit', compact('portal_program'));
    }

    /**
     * Update the specified program in storage.
     */
    public function update(Request $request, PortalProgram $portal_program)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:portal_programs,slug,' . $portal_program->id,
            'code' => 'nullable|string|max:50',
            'subtitle' => 'nullable|string|max:255',
            'category_key' => 'nullable|string|max:50',
            'category_name' => 'nullable|string|max:100',
            'badge' => 'nullable|string|max:100',
            'is_reguler' => 'nullable',
            'overview' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'hero_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',

            // Reguler specific
            'curriculum_pillars_text' => 'nullable|string',
            'objectives_text' => 'nullable|string',
            'method_ratio' => 'nullable|string|max:50',
            'method_desc' => 'nullable|string|max:255',
        ]);

        $isReguler = $request->boolean('is_reguler');
        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : ($portal_program->slug ?? Str::slug($validated['title']));

        $imagePath = $portal_program->image;
        if ($request->hasFile('image_file')) {
            if ($portal_program->image && str_starts_with($portal_program->image, 'portal_programs/') && Storage::disk('public')->exists($portal_program->image)) {
                Storage::disk('public')->delete($portal_program->image);
            }
            $imagePath = $request->file('image_file')->store('portal_programs', 'public');
        }

        $heroImagePath = $portal_program->hero_image;
        if ($request->hasFile('hero_image_file')) {
            if ($portal_program->hero_image && str_starts_with($portal_program->hero_image, 'portal_programs/') && Storage::disk('public')->exists($portal_program->hero_image)) {
                Storage::disk('public')->delete($portal_program->hero_image);
            }
            $heroImagePath = $request->file('hero_image_file')->store('portal_programs', 'public');
        }

        // Parse curriculum pillars (tersedia untuk reguler maupun non-reguler)
        $curriculum = null;
        if (!empty($validated['curriculum_pillars_text'])) {
            $lines = preg_split('/\r\n|\r|\n/', $validated['curriculum_pillars_text']);
            $curriculum = array_values(array_filter(array_map('trim', $lines)));
        }

        // Parse objectives / benefit (tersedia untuk reguler maupun non-reguler)
        $objectives = null;
        if (!empty($validated['objectives_text'])) {
            $lines = preg_split('/\r\n|\r|\n/', $validated['objectives_text']);
            $objectives = array_values(array_filter(array_map('trim', $lines)));
        }

        // Learning method (hanya aktif untuk program reguler trainee)
        $learningMethod = null;
        if ($isReguler) {
            $learningMethod = [
                'ratio' => !empty($validated['method_ratio']) ? trim($validated['method_ratio']) : '70 : 20 : 10',
                'desc' => !empty($validated['method_desc']) ? trim($validated['method_desc']) : '70% experiential learning, 20% social learning, dan 10% formal learning',
            ];
        }

        $portal_program->update([
            'slug' => $slug,
            'code' => !empty($validated['code']) ? strtoupper(trim($validated['code'])) : ($portal_program->code ?? strtoupper($slug)),
            'title' => trim($validated['title']),
            'subtitle' => $validated['subtitle'] ?? null,
            'category_key' => !empty($validated['category_name']) ? Str::slug($validated['category_name']) : ($portal_program->category_key ?? 'program'),
            'category_name' => !empty($validated['category_name']) ? trim($validated['category_name']) : ($portal_program->category_name ?? 'Program Pelatihan'),
            'badge' => $validated['badge'] ?? $portal_program->badge ?? null,
            'is_reguler' => $isReguler,
            'overview' => trim($validated['overview']),
            'image' => $imagePath,
            'hero_image' => $heroImagePath,
            'curriculum_pillars' => $curriculum,
            'objectives' => $objectives,
            'learning_method' => $learningMethod,
            'order' => isset($validated['order']) ? (int) $validated['order'] : ($portal_program->order ?? 0),
            'is_active' => $request->has('is_active') ? true : ($portal_program->is_active ?? true),
        ]);

        return redirect()->route('admin.page_settings.index', ['tab' => 'program'])
            ->with('success', 'Program pelatihan "' . $portal_program->title . '" berhasil diperbarui.');
    }

    /**
     * Remove the specified program from storage.
     */
    public function destroy(PortalProgram $portal_program)
    {
        if ($portal_program->image && str_starts_with($portal_program->image, 'portal_programs/') && Storage::disk('public')->exists($portal_program->image)) {
            Storage::disk('public')->delete($portal_program->image);
        }

        if ($portal_program->hero_image && str_starts_with($portal_program->hero_image, 'portal_programs/') && Storage::disk('public')->exists($portal_program->hero_image)) {
            Storage::disk('public')->delete($portal_program->hero_image);
        }

        $title = $portal_program->title;
        $portal_program->delete();

        return redirect()->route('admin.page_settings.index', ['tab' => 'program'])
            ->with('success', 'Program "' . $title . '" berhasil dihapus.');
    }

    /**
     * Toggle active status of a program.
     */
    public function toggle(PortalProgram $portal_program)
    {
        $portal_program->is_active = !$portal_program->is_active;
        $portal_program->save();

        $status = $portal_program->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.page_settings.index', ['tab' => 'program'])
            ->with('success', 'Status program "' . $portal_program->title . '" berhasil ' . $status . '.');
    }

    /**
     * Synchronize default catalog programs into DB (update or create).
     */
    public function syncDefaultPrograms()
    {
        $defaults = \App\Services\ProgramCatalogService::getStaticFallbackPrograms();
        $count = 0;

        foreach ($defaults as $slug => $data) {
            $isReguler = in_array($slug, ['fat', 'mat', 'tat', 'aat', 'dmt', 'pmt', 'management-associate']);
            
            \App\Models\PortalProgram::updateOrCreate(
                ['slug' => $slug],
                [
                    'code' => $data['code'] ?? strtoupper($slug),
                    'title' => $data['title'],
                    'subtitle' => $data['subtitle'] ?? null,
                    'category_key' => $data['category_key'] ?? ($isReguler ? 'trainee' : 'teknikal'),
                    'category_name' => $data['category_name'] ?? ($isReguler ? 'Program Trainee' : 'Program Pelatihan'),
                    'target_role' => $data['target_role'] ?? null,
                    'duration' => $data['duration'] ?? null,
                    'format' => $data['format'] ?? null,
                    'badge' => $data['badge'] ?? null,
                    'is_reguler' => $isReguler,
                    'overview' => $data['overview'] ?? 'Deskripsi materi pelatihan First Resources.',
                    'image' => $data['image'] ?? null,
                    'hero_image' => $data['hero_image'] ?? null,
                    'curriculum_pillars' => $data['curriculum_pillars'] ?? null,
                    'objectives' => $data['objectives'] ?? null,
                    'learning_method' => $isReguler ? ($data['learning_method'] ?? [
                        'ratio' => '70 : 20 : 10',
                        'desc' => '70% experiential learning, 20% social learning, dan 10% formal learning',
                    ]) : null,
                    'order' => $count * 10,
                    'is_active' => true,
                ]
            );
            $count++;
        }

        return redirect()->route('admin.page_settings.index', ['tab' => 'program'])
            ->with('success', "Berhasil menyinkronkan {$count} program ke database.");
    }
}
