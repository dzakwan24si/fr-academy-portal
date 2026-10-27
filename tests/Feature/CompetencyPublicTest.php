<?php

namespace Tests\Feature;

use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyDictionary;
use App\Models\Competency\CompetencyLevel;
use Database\Seeders\CompetencySeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * Fase 2: halaman publik Standar Kompetensi (tanpa login).
 */
class CompetencyPublicTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // GUARD: test ini menulis data. Tolak keras bila bukan database *_test.
        $database = DB::getDatabaseName();
        if (! str_ends_with((string) $database, '_test')) {
            throw new RuntimeException(
                "ABORT: CompetencyPublicTest hanya boleh jalan di database berakhiran '_test', terdeteksi '{$database}'."
            );
        }

        // Layout memanggil @vite; test ini tidak bergantung pada hasil build aset.
        $this->withoutVite();
    }

    private function seedBoth(): void
    {
        $this->seed(CompetencySeeder::class);
    }

    private function makeDraft(string $code, int $year): CompetencyDictionary
    {
        $dictionary = CompetencyDictionary::where('code', $code)->where('year', $year)->firstOrFail();
        $dictionary->update(['status' => 'draft', 'published_at' => null]);

        return $dictionary;
    }

    public function test_guest_can_open_published_estate_page(): void
    {
        $this->seedBoth();
        $estate = CompetencyDictionary::where('code', 'estate')->firstOrFail();
        $first = Competency::where('dictionary_id', $estate->id)->orderBy('sort_order')->firstOrFail();

        $this->assertGuest();
        $response = $this->get('/standar-kompetensi/estate');

        $response->assertOk();
        $response->assertSee('Standar Kompetensi Teknis');
        $response->assertSee($estate->name);
        $response->assertSee($first->name);
        $response->assertSee('L1 - Basic', false);
        $response->assertSee('L4 - Expert', false);
    }

    public function test_default_route_shows_first_published_dictionary(): void
    {
        $this->seedBoth();

        $this->get('/standar-kompetensi')->assertOk()->assertSee('Estate Technical Competency Dictionary');
    }

    public function test_default_route_falls_back_to_mill_when_estate_is_draft(): void
    {
        $this->seedBoth();
        $this->makeDraft('estate', 2026);

        $response = $this->get('/standar-kompetensi');

        $response->assertOk()->assertSee('FR Mill / PKS Technical Competency Dictionary');
        $response->assertDontSee('competency-tab-estate', false);
    }

    public function test_draft_dictionary_returns_404_and_is_not_listed(): void
    {
        $this->seedBoth();
        $draft = $this->makeDraft('estate', 2026);

        $this->get('/standar-kompetensi/estate')->assertNotFound();
        $this->get('/standar-kompetensi/estate?tahun=2026')->assertNotFound();

        // Tab Estate & tahun draft tidak muncul di halaman lain yang published.
        $mill = $this->get('/standar-kompetensi/mill');
        $mill->assertOk();
        $mill->assertDontSee('competency-tab-estate', false);
        $mill->assertDontSee($draft->name);
    }

    public function test_draft_year_never_leaks_through_query_or_selector(): void
    {
        $this->seedBoth();
        $estate = CompetencyDictionary::where('code', 'estate')->where('year', 2026)->firstOrFail();

        // Tahun 2027 estate = draft, berisi teks rahasia.
        $draft = CompetencyDictionary::create([
            'code' => 'estate', 'name' => 'RAHASIA DRAFT ESTATE', 'year' => 2027, 'status' => 'draft',
            'description' => 'Deskripsi rahasia draft',
        ]);
        Competency::create(['dictionary_id' => $draft->id, 'sort_order' => 1, 'name' => 'Kompetensi Rahasia', 'definition' => 'x']);

        $this->get('/standar-kompetensi/estate?tahun=2027')->assertNotFound();

        $page = $this->get('/standar-kompetensi/estate');
        $page->assertOk();
        $page->assertSee($estate->name);
        $page->assertDontSee('RAHASIA DRAFT ESTATE');
        $page->assertDontSee('Kompetensi Rahasia');
        $page->assertDontSee('2027'); // tahun draft tidak muncul di selector / judul
        $page->assertDontSee('id="competency-year"', false); // hanya 1 tahun published => tanpa selector
    }

    public function test_year_selector_lists_only_published_years_and_selects_requested_year(): void
    {
        $this->seedBoth();
        $old = CompetencyDictionary::create([
            'code' => 'estate', 'name' => 'Estate Edisi Lama', 'year' => 2025, 'status' => 'published', 'published_at' => now(),
        ]);
        Competency::create(['dictionary_id' => $old->id, 'sort_order' => 1, 'name' => 'Kompetensi Lama', 'definition' => 'lama']);
        CompetencyDictionary::create(['code' => 'estate', 'name' => 'Draft Estate', 'year' => 2030, 'status' => 'draft']);

        $default = $this->get('/standar-kompetensi/estate');
        $default->assertOk()->assertSee('id="competency-year"', false);
        $default->assertSee('tahun=2025', false);
        $default->assertDontSee('tahun=2030', false);
        $default->assertSee('Estate Technical Competency Dictionary'); // tahun terbaru (2026) jadi default

        $this->get('/standar-kompetensi/estate?tahun=2025')
            ->assertOk()
            ->assertSee('Estate Edisi Lama')
            ->assertSee('Kompetensi Lama');
    }

    public function test_unknown_code_and_invalid_params_are_handled(): void
    {
        $this->seedBoth();

        $this->get('/standar-kompetensi/foo')->assertNotFound();
        $this->get('/standar-kompetensi/estate?tahun=1999')->assertNotFound();
        $this->get('/standar-kompetensi/estate?tahun=abc')->assertNotFound();
        $this->get('/standar-kompetensi/estate?tahun[]=2026')->assertNotFound();

        // k tidak valid jatuh ke kompetensi pertama (bukan error).
        $this->get('/standar-kompetensi/estate?k=999')->assertOk()->assertSee('Kompetensi 1 dari 17');
        $this->get('/standar-kompetensi/estate?k=0')->assertOk()->assertSee('Kompetensi 1 dari 17');
        $this->get('/standar-kompetensi/estate?k=abc')->assertOk()->assertSee('Kompetensi 1 dari 17');
        $this->get('/standar-kompetensi/estate?k[]=3')->assertOk()->assertSee('Kompetensi 1 dari 17');
    }

    public function test_competency_param_selects_by_display_order(): void
    {
        $this->seedBoth();
        $estate = CompetencyDictionary::where('code', 'estate')->firstOrFail();
        $third = Competency::where('dictionary_id', $estate->id)->orderBy('sort_order')->orderBy('id')->skip(2)->firstOrFail();

        $this->get('/standar-kompetensi/estate?k=3')
            ->assertOk()
            ->assertSee('Kompetensi 3 dari 17')
            ->assertSee('3. '.$third->name);
    }

    public function test_matrix_shows_every_position_and_dash_for_empty_cells(): void
    {
        $this->seedBoth();
        $estate = CompetencyDictionary::where('code', 'estate')->firstOrFail();
        $first = Competency::where('dictionary_id', $estate->id)->orderBy('sort_order')->orderBy('id')->firstOrFail();
        $position = $estate->positions()->firstOrFail();

        CompetencyLevel::where('competency_id', $first->id)
            ->where('position_id', $position->id)->where('level', 2)
            ->update(['description' => null]);

        $response = $this->get('/standar-kompetensi/estate?k=1');

        $response->assertOk();
        foreach ($estate->positions as $p) {
            $response->assertSee($p->name);
        }
        $response->assertSee('–'); // sel kosong tampil sebagai en dash
        $response->assertSee('id="competency-position-filter"', false);
        $response->assertSee('x-model="position"', false);
    }

    public function test_public_page_has_bounded_query_count(): void
    {
        $this->seedBoth();

        DB::enableQueryLog();
        $this->get('/standar-kompetensi/mill?k=5')->assertOk();
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Tidak boleh tumbuh sebanding jumlah jabatan/kompetensi/level (N+1).
        $this->assertLessThan(15, $count, "Terlalu banyak query ({$count}); kemungkinan N+1.");
    }

    public function test_empty_state_when_nothing_is_published(): void
    {
        $this->seedBoth();
        CompetencyDictionary::query()->update(['status' => 'draft', 'published_at' => null]);

        $this->get('/standar-kompetensi')->assertOk()->assertSee('Belum ada standar kompetensi yang dipublikasikan');
        $this->get('/standar-kompetensi/estate')->assertNotFound();
        $this->get('/standar-kompetensi/mill')->assertNotFound();
    }

    public function test_navbar_menu_exists_on_desktop_and_mobile_and_footer(): void
    {
        $url = route('competency.public');

        $html = $this->get('/program')->assertOk()->getContent();

        // desktop + mobile + footer
        $this->assertGreaterThanOrEqual(3, substr_count($html, 'href="'.$url.'"'));
        // Menu berdiri sendiri sebagai item top-level dengan nama KOMPETENSI
        $this->assertStringContainsString('>KOMPETENSI<', $html);
        // Desktop & Mobile: link KOMPETENSI berdiri sendiri setelah PROGRAM
        $this->assertMatchesRegularExpression('/>PROGRAM<\/a>\s*<a href="'.preg_quote($url, '/').'"[^>]*>KOMPETENSI<\/a>/', $html);
    }

    public function test_sitemap_lists_only_published_dictionaries(): void
    {
        $this->seedBoth();
        $this->makeDraft('mill', 2027);

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('standar-kompetensi/estate', $xml);
        $this->assertStringNotContainsString('standar-kompetensi/mill', $xml);
    }

    public function test_public_page_is_not_behind_auth_group(): void
    {
        $route = app('router')->getRoutes()->getByName('competency.public');

        $this->assertNotNull($route);
        $this->assertNotContains('auth', $route->gatherMiddleware());
        $this->assertSame('standar-kompetensi/{code?}', $route->uri());
    }

    public function test_guest_can_print_all_competencies_mode(): void
    {
        $this->seedBoth();
        $estate = CompetencyDictionary::where('code', 'estate')->firstOrFail();

        $response = $this->get('/standar-kompetensi/estate?all=1&print=1');

        $response->assertOk();
        $response->assertSee('Mode Cetak Seluruh Kamus');
        $response->assertSee($estate->name);
        $response->assertSee('print-page-break');
    }
}
