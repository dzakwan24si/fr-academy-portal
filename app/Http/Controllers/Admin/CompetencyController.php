<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyDictionary;
use App\Models\Competency\CompetencyItem;
use App\Models\Competency\CompetencyLevel;
use App\Models\Competency\CompetencyPosition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompetencyController extends Controller
{
    public function index()
    {
        $dictionaries = CompetencyDictionary::withCount(['competencies', 'positions'])
            ->orderBy('code')
            ->orderByDesc('year')
            ->get();

        return view('admin.competency.index', compact('dictionaries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:30', 'regex:/^[a-z0-9_-]+$/'],
            'name' => 'required|string|max:255',
            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
                Rule::unique('competency_dictionaries')->where('code', $request->code),
            ],
            'description' => 'nullable|string',
        ], [
            'code.regex' => 'Jenis (Code) hanya boleh berisi huruf kecil, angka, garis hubung (-), dan garis bawah (_). Contoh: estate, mill, ho, logistik.',
            'year.unique' => 'Kamus kompetensi untuk jenis dan tahun tersebut sudah ada.',
        ]);

        $validated['status'] = CompetencyDictionary::STATUS_DRAFT;

        $dictionary = CompetencyDictionary::create($validated);

        return redirect()->route('admin.competency.show', $dictionary)
            ->with('success', "Kamus kompetensi {$dictionary->name} ({$dictionary->year}) berhasil dibuat.");
    }

    public function show(CompetencyDictionary $dictionary)
    {
        $dictionary->load([
            'positions' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'competencies' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')->withCount(['levels', 'items']),
        ]);

        return view('admin.competency.show', compact('dictionary'));
    }

    public function update(Request $request, CompetencyDictionary $dictionary): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
                Rule::unique('competency_dictionaries')
                    ->where('code', $dictionary->code)
                    ->ignore($dictionary->id),
            ],
            'description' => 'nullable|string',
        ], [
            'year.unique' => 'Kamus kompetensi lain dengan jenis dan tahun tersebut sudah ada.',
        ]);

        $dictionary->update($validated);

        return redirect()->route('admin.competency.show', $dictionary)
            ->with('success', 'Informasi kamus kompetensi berhasil diperbarui.');
    }

    public function destroy(CompetencyDictionary $dictionary): RedirectResponse
    {
        if ($dictionary->isPublished()) {
            return redirect()->back()->with('error', 'Kamus yang berstatus Published tidak boleh dihapus. Silakan unpublish terlebih dahulu.');
        }

        $name = "{$dictionary->name} {$dictionary->year}";
        $dictionary->delete();

        return redirect()->route('admin.competency.index')
            ->with('success', "Kamus kompetensi {$name} berhasil dihapus.");
    }

    public function togglePublish(CompetencyDictionary $dictionary): RedirectResponse
    {
        if ($dictionary->isPublished()) {
            $dictionary->update([
                'status' => CompetencyDictionary::STATUS_DRAFT,
                'published_at' => null,
            ]);
            $message = "Kamus {$dictionary->name} {$dictionary->year} diubah menjadi Draft.";
        } else {
            $dictionary->update([
                'status' => CompetencyDictionary::STATUS_PUBLISHED,
                'published_at' => now(),
            ]);
            $message = "Kamus {$dictionary->name} {$dictionary->year} berhasil Dipublikasikan.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function copy(Request $request, CompetencyDictionary $dictionary): RedirectResponse
    {
        $request->validate([
            'target_year' => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
                Rule::unique('competency_dictionaries', 'year')->where('code', $dictionary->code),
            ],
        ], [
            'target_year.unique' => "Kamus kompetensi {$dictionary->code} untuk tahun tersebut sudah ada.",
        ]);

        $targetYear = (int) $request->target_year;

        $newDict = DB::transaction(function () use ($dictionary, $targetYear) {
            return Model::withoutEvents(function () use ($dictionary, $targetYear) {
                $newDict = CompetencyDictionary::create([
                    'code' => $dictionary->code,
                    'name' => $dictionary->name,
                    'year' => $targetYear,
                    'description' => $dictionary->description,
                    'status' => CompetencyDictionary::STATUS_DRAFT,
                    'published_at' => null,
                ]);

                $positionMap = [];
                foreach ($dictionary->positions()->orderBy('sort_order')->orderBy('id')->get() as $pos) {
                    $newPos = CompetencyPosition::create([
                        'dictionary_id' => $newDict->id,
                        'name' => $pos->name,
                        'sort_order' => $pos->sort_order,
                    ]);
                    $positionMap[$pos->id] = $newPos->id;
                }

                foreach ($dictionary->competencies()->orderBy('sort_order')->orderBy('id')->with(['levels', 'items'])->get() as $comp) {
                    $newComp = Competency::create([
                        'dictionary_id' => $newDict->id,
                        'sort_order' => $comp->sort_order,
                        'name' => $comp->name,
                        'definition' => $comp->definition,
                    ]);

                    foreach ($comp->levels as $level) {
                        if (isset($positionMap[$level->position_id])) {
                            CompetencyLevel::create([
                                'competency_id' => $newComp->id,
                                'position_id' => $positionMap[$level->position_id],
                                'level' => $level->level,
                                'description' => $level->description,
                            ]);
                        }
                    }

                    foreach ($comp->items as $item) {
                        CompetencyItem::create([
                            'competency_id' => $newComp->id,
                            'type' => $item->type,
                            'body' => $item->body,
                            'sort_order' => $item->sort_order,
                        ]);
                    }
                }

                ActivityLog::log(
                    'Salin',
                    "Menyalin kamus kompetensi dari edisi {$dictionary->year} ke edisi {$targetYear}",
                    'Kamus Kompetensi'
                );

                return $newDict;
            });
        });

        return redirect()->route('admin.competency.show', $newDict)
            ->with('success', "Berhasil menyalin kamus kompetensi ke edisi {$targetYear}. Status awal: Draft.");
    }

    // ---------------------------------------------------------------------
    // Positions Management
    // ---------------------------------------------------------------------

    public function storePosition(Request $request, CompetencyDictionary $dictionary): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('competency_positions', 'name')->where('dictionary_id', $dictionary->id),
            ],
        ], [
            'name.unique' => 'Jabatan dengan nama tersebut sudah ada di kamus ini.',
        ]);

        $maxSort = (int) $dictionary->positions()->max('sort_order');
        $position = $dictionary->positions()->create([
            'name' => $validated['name'],
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->back()->with('success', "Jabatan {$position->name} berhasil ditambahkan.");
    }

    public function updatePosition(Request $request, CompetencyPosition $position): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('competency_positions', 'name')
                    ->where('dictionary_id', $position->dictionary_id)
                    ->ignore($position->id),
            ],
        ], [
            'name.unique' => 'Jabatan lain dengan nama tersebut sudah ada.',
        ]);

        $position->update(['name' => $validated['name']]);

        return redirect()->back()->with('success', 'Nama jabatan berhasil diperbarui.');
    }

    public function destroyPosition(CompetencyPosition $position): RedirectResponse
    {
        $name = $position->name;
        $position->delete();

        return redirect()->back()->with('success', "Jabatan {$name} dan seluruh data level terkait berhasil dihapus.");
    }

    public function reorderPositions(Request $request, CompetencyDictionary $dictionary): RedirectResponse
    {
        $request->validate([
            'position_ids' => 'required|array',
            'position_ids.*' => 'integer|exists:competency_positions,id',
        ]);

        DB::transaction(function () use ($request, $dictionary) {
            foreach ($request->position_ids as $index => $id) {
                CompetencyPosition::where('dictionary_id', $dictionary->id)
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return redirect()->back()->with('success', 'Urutan jabatan berhasil disimpan.');
    }

    // ---------------------------------------------------------------------
    // Competencies Management
    // ---------------------------------------------------------------------

    public function storeCompetency(Request $request, CompetencyDictionary $dictionary): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'definition' => 'nullable|string',
        ]);

        $maxSort = (int) $dictionary->competencies()->max('sort_order');
        $competency = $dictionary->competencies()->create([
            'name' => $validated['name'],
            'definition' => $validated['definition'] ?? null,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('admin.competency.competencies.edit', $competency)
            ->with('success', "Kompetensi {$competency->name} berhasil ditambahkan. Silakan lengkapi deskripsi level dan butir indikator.");
    }

    public function editCompetency(Competency $competency)
    {
        $competency->load([
            'dictionary.positions' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
            'levels',
            'items' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
        ]);

        $dictionary = $competency->dictionary;
        $levelMap = $competency->levelMap();

        return view('admin.competency.edit_competency', compact('competency', 'dictionary', 'levelMap'));
    }

    public function updateCompetency(Request $request, Competency $competency): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'definition' => 'nullable|string',
            'levels' => 'nullable|array',
            'levels.*.*' => 'nullable|string',
            'items' => 'nullable|array',
            'items.knowledge' => 'nullable|array',
            'items.skill' => 'nullable|array',
            'items.development' => 'nullable|array',
            'items.*.*' => 'nullable|string',
        ]);

        $dictionary = $competency->dictionary;
        $allowedPositionIds = $dictionary->positions()->pluck('id')->all();

        DB::transaction(function () use ($request, $competency, $validated, $allowedPositionIds) {
            // 1. Update nama & definisi kompetensi jika berubah
            $competency->update([
                'name' => $validated['name'],
                'definition' => $validated['definition'] ?? null,
            ]);

            // 2. Diff-save level
            $existingLevels = $competency->levels->keyBy(fn ($l) => "{$l->position_id}-{$l->level}");
            $inputLevels = $request->input('levels', []);

            foreach ($allowedPositionIds as $posId) {
                for ($lvl = 1; $lvl <= 4; $lvl++) {
                    $key = "{$posId}-{$lvl}";
                    $newDesc = trim((string) ($inputLevels[$posId][$lvl] ?? ''));
                    $newDesc = $newDesc === '' ? null : $newDesc;

                    $existing = $existingLevels->get($key);

                    if ($existing) {
                        if ($existing->description !== $newDesc) {
                            $existing->update(['description' => $newDesc]);
                        }
                    } else {
                        if ($newDesc !== null) {
                            CompetencyLevel::create([
                                'competency_id' => $competency->id,
                                'position_id' => $posId,
                                'level' => $lvl,
                                'description' => $newDesc,
                            ]);
                        }
                    }
                }
            }

            // 3. Diff-save items per type
            $inputItems = $request->input('items', []);

            foreach (CompetencyItem::TYPES as $type) {
                $rawList = $inputItems[$type] ?? [];
                $submittedBodies = collect($rawList)
                    ->map(fn ($b) => trim((string) $b))
                    ->filter(fn ($b) => $b !== '')
                    ->values();

                $existingTypeItems = $competency->items()
                    ->where('type', $type)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

                foreach ($submittedBodies as $idx => $body) {
                    $sortOrder = $idx + 1;
                    if ($existingTypeItems->has($idx)) {
                        $item = $existingTypeItems[$idx];
                        if ($item->body !== $body || $item->sort_order !== $sortOrder) {
                            $item->update([
                                'body' => $body,
                                'sort_order' => $sortOrder,
                            ]);
                        }
                    } else {
                        CompetencyItem::create([
                            'competency_id' => $competency->id,
                            'type' => $type,
                            'body' => $body,
                            'sort_order' => $sortOrder,
                        ]);
                    }
                }

                // Hapus item lama yang tidak lagi terpakai
                if ($existingTypeItems->count() > $submittedBodies->count()) {
                    for ($i = $submittedBodies->count(); $i < $existingTypeItems->count(); $i++) {
                        $existingTypeItems[$i]->delete();
                    }
                }
            }
        });

        return redirect()->route('admin.competency.show', $competency->dictionary_id)
            ->with('success', "Kompetensi {$competency->name} berhasil diperbarui.");
    }

    public function destroyCompetency(Competency $competency): RedirectResponse
    {
        $dictionaryId = $competency->dictionary_id;
        $name = $competency->name;

        $competency->delete();

        // Normalisasi sort_order sisa kompetensi di dictionary ini
        $remaining = Competency::where('dictionary_id', $dictionaryId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($remaining as $idx => $comp) {
            if ($comp->sort_order !== $idx + 1) {
                $comp->update(['sort_order' => $idx + 1]);
            }
        }

        return redirect()->route('admin.competency.show', $dictionaryId)
            ->with('success', "Kompetensi {$name} berhasil dihapus.");
    }

    public function reorderCompetencies(Request $request, CompetencyDictionary $dictionary): RedirectResponse
    {
        $request->validate([
            'competency_ids' => 'required|array',
            'competency_ids.*' => 'integer|exists:competencies,id',
        ]);

        DB::transaction(function () use ($request, $dictionary) {
            foreach ($request->competency_ids as $index => $id) {
                Competency::where('dictionary_id', $dictionary->id)
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return redirect()->back()->with('success', 'Urutan kompetensi berhasil disimpan.');
    }
}
