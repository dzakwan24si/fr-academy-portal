<?php

namespace App\Http\Controllers;

use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyDictionary;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Halaman publik (tanpa login) Standar Kompetensi.
 *
 * Hanya dictionary berstatus "published" yang boleh terbaca. Semua query di sini
 * wajib melewati scope published() agar draft tidak pernah bocor lewat parameter apa pun.
 */
class CompetencyPublicController extends Controller
{
    /** Urutan & label tab jenis dictionary bawaan. */
    public const CODE_LABELS = [
        'estate' => 'Estate',
        'mill' => 'Mill / PKS',
        'ho' => 'Head Office / HO',
    ];

    public static function getCodeLabel(string $code): string
    {
        return self::CODE_LABELS[$code] ?? strtoupper(str_replace(['_', '-'], ' ', $code));
    }

    public function show(Request $request, ?string $code = null)
    {
        // Peta code => daftar tahun published (terbaru dulu). Satu query untuk tab & selector tahun.
        $yearsByCode = $this->publishedYearsByCode();
        $codeLabels = $yearsByCode->keys()->mapWithKeys(fn ($c) => [$c => self::getCodeLabel($c)])->all();

        if ($code === null) {
            $code = $yearsByCode->keys()->first();

            // Belum ada satu pun dictionary published: tampilkan halaman kosong yang ramah.
            if ($code === null) {
                return view('competency.public', [
                    'empty' => true,
                    'codeLabels' => self::CODE_LABELS,
                    'yearsByCode' => $yearsByCode,
                    'isAll' => false,
                    'autoPrint' => false,
                    'allCompetencies' => collect(),
                ]);
            }
        }

        // Code valid tetapi tidak punya dictionary published => 404 (tidak membedakan draft/tidak ada).
        $years = $yearsByCode->get($code);
        abort_if($years === null, 404);

        $year = $this->resolveYear($request->query('tahun'), $years);

        $dictionary = CompetencyDictionary::published()
            ->where('code', $code)
            ->where('year', $year)
            ->with(['positions', 'competencies:id,dictionary_id,sort_order,name'])
            ->firstOrFail();

        /** @var Collection<int, Competency> $competencies */
        $competencies = $dictionary->competencies->values();

        $selectedIndex = $this->resolveCompetencyIndex($request->query('k'), $competencies->count());
        $selected = null;

        if ($selectedIndex !== null) {
            // Muat detail hanya untuk kompetensi terpilih (hindari N+1 dan muat data berlebihan).
            $selected = Competency::query()
                ->where('dictionary_id', $dictionary->id)
                ->with(['levels', 'items'])
                ->findOrFail($competencies[$selectedIndex - 1]->id);
        }

        $isAll = $request->boolean('all');
        $autoPrint = $request->boolean('print');

        $allCompetencies = collect();
        if ($isAll) {
            $allCompetencies = Competency::query()
                ->where('dictionary_id', $dictionary->id)
                ->orderBy('sort_order')
                ->with(['levels', 'items'])
                ->get();
        }

        return view('competency.public', [
            'empty' => false,
            'codeLabels' => $codeLabels,
            'yearsByCode' => $yearsByCode,
            'code' => $code,
            'year' => $year,
            'years' => $years,
            'dictionary' => $dictionary,
            'competencies' => $competencies,
            'selected' => $selected,
            'selectedIndex' => $selectedIndex,
            'isAll' => $isAll,
            'autoPrint' => $autoPrint,
            'allCompetencies' => $allCompetencies,
            'levelLabels' => CompetencyDictionary::LEVEL_LABELS,
            'itemTypeLabels' => CompetencyDictionary::ITEM_TYPE_LABELS,
        ]);
    }

    /**
     * @return Collection<string, array<int, int>> code => [tahun desc]
     */
    private function publishedYearsByCode(): Collection
    {
        $rows = CompetencyDictionary::published()
            ->orderByDesc('year')
            ->get(['code', 'year']);

        $grouped = $rows->groupBy('code')->map(fn ($group) => $group->pluck('year')->map(fn ($y) => (int) $y)->all());

        $preferredOrder = array_keys(self::CODE_LABELS);

        return $grouped->sortBy(function ($years, $code) use ($preferredOrder) {
            $index = array_search($code, $preferredOrder, true);

            return $index !== false ? $index : 999;
        });
    }

    /**
     * Tahun default = published terbaru. Tahun yang diminta harus termasuk tahun published, kalau tidak 404.
     *
     * @param  array<int, int>  $years
     */
    private function resolveYear(mixed $requested, array $years): int
    {
        if ($requested === null || $requested === '') {
            return $years[0];
        }

        abort_unless(is_string($requested) && ctype_digit($requested) && in_array((int) $requested, $years, true), 404);

        return (int) $requested;
    }

    /**
     * Nomor urut tampil 1..N (posisi dalam daftar, bukan nilai sort_order mentah). Nilai tak valid jatuh ke 1.
     */
    private function resolveCompetencyIndex(mixed $requested, int $total): ?int
    {
        if ($total === 0) {
            return null;
        }

        if (is_string($requested) && ctype_digit($requested)) {
            $n = (int) $requested;
            if ($n >= 1 && $n <= $total) {
                return $n;
            }
        }

        return 1;
    }
}
