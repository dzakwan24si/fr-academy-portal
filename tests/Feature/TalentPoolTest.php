<?php

namespace Tests\Feature;

use App\Models\Talent\TalentPool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TalentPoolTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }

    public function test_talent_pool_page_renders_and_calculates_kpis(): void
    {
        TalentPool::create([
            'no' => 1,
            'employee_name' => 'Kandidat Belum 1',
            'status' => 'Belum Penempatan',
            'kategori' => 'ESTATE TAHUN 2026',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);
        TalentPool::create([
            'no' => 2,
            'employee_name' => 'Kandidat Belum 2',
            'status' => 'Belum Penempatan',
            'kategori' => 'ESTATE TAHUN 2026',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);
        TalentPool::create([
            'no' => 3,
            'employee_name' => 'Kandidat Sudah 1',
            'status' => 'Sudah penempatan',
            'kategori' => 'ESTATE TAHUN 2026',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);

        $response = $this->get(route('administrasi.talent-pool.index'));
        $response->assertStatus(200);

        // Verify only 2 KPI cards exist and "Tingkat Penempatan" is removed
        $response->assertSee('Total Talent Pool');
        $response->assertSee('Sudah Penempatan');
        $response->assertDontSee('Tingkat Penempatan');

        // Total Talent Pool should count only Belum Penempatan (= 2)
        // Sudah Penempatan should count = 1
        $response->assertSee('2'); // countBelum
        $response->assertSee('1'); // countSudah

        // Verify chart and dropdown filters exist
        $response->assertSee('chartTargetPosisi');
        $response->assertSee('name="year"', false);
        $response->assertSee('name="jenis_kategori"', false);
        $response->assertViewHas('chartLabels');
        $response->assertViewHas('chartTotals');
    }

    public function test_talent_pool_filters_by_jenis_kategori(): void
    {
        TalentPool::create([
            'no' => 1,
            'employee_name' => 'Budi Estate 2025',
            'status' => 'Belum Penempatan',
            'tahun' => 2025,
            'jenis_kategori' => 'Estate',
        ]);
        TalentPool::create([
            'no' => 2,
            'employee_name' => 'Siti Mill 2025',
            'status' => 'Belum Penempatan',
            'tahun' => 2025,
            'jenis_kategori' => 'Mill',
        ]);

        // When filtering by Estate
        $response = $this->get(route('administrasi.talent-pool.index', [
            'jenis_kategori' => 'Estate',
        ]));
        $response->assertStatus(200);
        $response->assertSee('Jenis Kategori');
        $response->assertSee('Budi Estate 2025');
        $response->assertDontSee('Siti Mill 2025');
    }

    public function test_store_and_update_talent_pool_with_tahun_and_jenis_kategori(): void
    {
        $response = $this->post(route('administrasi.talent-pool.store'), [
            'employee_name' => 'Joko Widodo',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Mill',
            'kategori' => 'Mill tahun 2026',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('talent_pools', [
            'employee_name' => 'Joko Widodo',
            'tahun' => 2026,
            'jenis_kategori' => 'Mill',
            'kategori' => 'Mill tahun 2026',
        ]);
    }

    public function test_target_posisi_chart_aggregates_properly(): void
    {
        TalentPool::create([
            'no' => 1,
            'employee_name' => 'Kandidat A',
            'talent_to' => 'Field Assistant',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
            'kategori' => 'ESTATE 2026',
        ]);
        TalentPool::create([
            'no' => 2,
            'employee_name' => 'Kandidat B',
            'talent_to' => 'Field Assistant',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
            'kategori' => 'ESTATE 2026',
        ]);
        TalentPool::create([
            'no' => 3,
            'employee_name' => 'Kandidat C',
            'talent_to' => 'Mill Assistant',
            'status' => 'Sudah penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Mill',
            'kategori' => 'MILL 2026',
        ]);

        $response = $this->get(route('administrasi.talent-pool.index'));
        $response->assertStatus(200);

        $chartLabels = $response->viewData('chartLabels');
        $chartTotals = $response->viewData('chartTotals');

        $this->assertContains('Field Assistant', $chartLabels);
        $this->assertContains('Mill Assistant', $chartLabels);

        $fieldIndex = array_search('Field Assistant', $chartLabels);
        $this->assertEquals(2, $chartTotals[$fieldIndex]);

        $millIndex = array_search('Mill Assistant', $chartLabels);
        $this->assertEquals(1, $chartTotals[$millIndex]);
    }

    public function test_target_posisi_dropdown_is_scoped_by_status(): void
    {
        // Manager only exists in 'Sudah penempatan'
        TalentPool::create([
            'no' => 1,
            'employee_name' => 'Manager X',
            'talent_to' => 'Mill Manager',
            'status' => 'Sudah penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Mill',
        ]);

        // Assistant exists in 'Belum Penempatan'
        TalentPool::create([
            'no' => 2,
            'employee_name' => 'Assistant Y',
            'talent_to' => 'Field Assistant',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);

        // When status is Belum Penempatan:
        $responseBelum = $this->get(route('administrasi.talent-pool.index', ['status' => 'Belum Penempatan']));
        $responseBelum->assertStatus(200);

        $talentToListBelum = $responseBelum->viewData('talentToList')->toArray();
        $this->assertContains('Field Assistant', $talentToListBelum);
        $this->assertNotContains('Mill Manager', $talentToListBelum);

        // When status is Sudah penempatan:
        $responseSudah = $this->get(route('administrasi.talent-pool.index', ['status' => 'Sudah penempatan']));
        $responseSudah->assertStatus(200);

        $talentToListSudah = $responseSudah->viewData('talentToList')->toArray();
        $this->assertContains('Mill Manager', $talentToListSudah);
        $this->assertNotContains('Field Assistant', $talentToListSudah);
    }

    public function test_filter_by_region_does_not_throw_error(): void
    {
        TalentPool::create([
            'no' => 1,
            'employee_name' => 'Karyawan Riau',
            'region' => 'Riau',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);

        $response = $this->get(route('administrasi.talent-pool.index', ['region' => 'Riau']));
        $response->assertStatus(200);
        $this->assertEquals('Riau', $response->viewData('region'));
    }

    public function test_status_dropdown_is_scoped_by_target_posisi(): void
    {
        // Manager only has 'Sudah penempatan'
        TalentPool::create([
            'no' => 1,
            'employee_name' => 'Manager Mill',
            'talent_to' => 'Mill Manager',
            'status' => 'Sudah penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Mill',
        ]);

        // Field Assistant only has 'Belum Penempatan'
        TalentPool::create([
            'no' => 2,
            'employee_name' => 'Field Assistant 1',
            'talent_to' => 'Field Assistant',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);

        // When filtering by talent_to = 'Mill Manager', statusList should only contain 'Sudah penempatan'
        $responseManager = $this->get(route('administrasi.talent-pool.index', ['talent_to' => 'Mill Manager']));
        $responseManager->assertStatus(200);
        $statusListManager = $responseManager->viewData('statusList')->toArray();
        $this->assertContains('Sudah penempatan', $statusListManager);
        $this->assertNotContains('Belum Penempatan', $statusListManager);

        // When filtering by talent_to = 'Field Assistant', statusList should only contain 'Belum Penempatan'
        $responseAssistant = $this->get(route('administrasi.talent-pool.index', ['talent_to' => 'Field Assistant']));
        $responseAssistant->assertStatus(200);
        $statusListAssistant = $responseAssistant->viewData('statusList')->toArray();
        $this->assertContains('Belum Penempatan', $statusListAssistant);
        $this->assertNotContains('Sudah penempatan', $statusListAssistant);
    }

    public function test_chart_respects_both_status_and_target_posisi_filters(): void
    {
        TalentPool::create([
            'no' => 1,
            'employee_name' => 'Askep 1',
            'talent_to' => 'Askep',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);
        TalentPool::create([
            'no' => 2,
            'employee_name' => 'Field Assistant 1',
            'talent_to' => 'Field Assistant',
            'status' => 'Belum Penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);
        TalentPool::create([
            'no' => 3,
            'employee_name' => 'Askep 2',
            'talent_to' => 'Askep',
            'status' => 'Sudah penempatan',
            'tahun' => 2026,
            'jenis_kategori' => 'Estate',
        ]);

        // Filter both status='Belum Penempatan' AND talent_to='Askep'
        $response = $this->get(route('administrasi.talent-pool.index', [
            'status' => 'Belum Penempatan',
            'talent_to' => 'Askep',
        ]));
        $response->assertStatus(200);

        $chartLabels = $response->viewData('chartLabels');
        $chartTotals = $response->viewData('chartTotals');

        // Only Askep should be in the chart, not Field Assistant
        $this->assertContains('Askep', $chartLabels);
        $this->assertNotContains('Field Assistant', $chartLabels);
        $this->assertEquals([1], $chartTotals);
    }
}
