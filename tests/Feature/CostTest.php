<?php

namespace Tests\Feature;

use App\Models\Cost\BiayaRekruit;
use App\Models\Cost\CapexRab;
use App\Models\Cost\CapexRealisasi;
use App\Models\Cost\Cost;
use App\Models\Cost\Vra;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CostTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
        Cost::create([
            'tahun'          => 2026,
            'GL_ACCOUNT'     => '135.80.00001',
            'jenis_cost'     => 'BIAYA KARYAWAN',
            'account_name'   => 'STAFF',
            'budget'         => 500000000,
            'realisasi'      => 400000000,
            'budget_tahunan' => 6000000000,
        ]);
        Cost::create([
            'tahun'          => 2026,
            'GL_ACCOUNT'     => '135.80.00001',
            'jenis_cost'     => 'BIAYA KARYAWAN',
            'account_name'   => 'NON STAFF',
            'budget'         => 50000000,
            'realisasi'      => 40000000,
            'budget_tahunan' => 600000000,
        ]);
    }

    public function test_can_view_cost_index_page()
    {
        $response = $this->get('/administrasi/cost');
        $response->assertStatus(200);
        $response->assertSee('Cost Management');
        $response->assertSee('BIAYA KARYAWAN');
        $response->assertSee('STAFF');
        $response->assertSee('NON STAFF');
    }

    public function test_can_create_cost_item()
    {
        $payload = [
            'tahun'          => 2026,
            'bulan'          => 6,
            'GL_ACCOUNT'     => '135.80.99999',
            'jenis_cost'     => 'BIAYA OPERASIONAL',
            'account_name'   => 'INTERNET & BANDWIDTH',
            'budget'         => 5000000,
            'realisasi'      => 4500000,
            'budget_tahunan' => 60000000,
        ];

        $response = $this->post('/administrasi/cost', $payload);
        $response->assertRedirect('/administrasi/cost?year=2026');

        $this->assertDatabaseHas('costs', [
            'account_name' => 'INTERNET & BANDWIDTH',
            'jenis_cost'   => 'BIAYA OPERASIONAL',
            'budget'       => 5000000,
        ]);
    }

    public function test_can_update_cost_item()
    {
        $cost = Cost::first();
        $this->assertNotNull($cost);

        $payload = [
            'tahun'          => $cost->tahun,
            'bulan'          => $cost->bulan,
            'GL_ACCOUNT'     => $cost->GL_ACCOUNT,
            'jenis_cost'     => $cost->jenis_cost,
            'account_name'   => $cost->account_name . ' (Updated)',
            'budget'         => 999999,
            'realisasi'      => 888888,
            'budget_tahunan' => 12000000,
        ];

        $response = $this->put("/administrasi/cost/{$cost->cost_id}", $payload);
        $response->assertRedirect("/administrasi/cost?year={$cost->tahun}");

        $this->assertDatabaseHas('costs', [
            'cost_id'      => $cost->cost_id,
            'account_name' => $cost->account_name . ' (Updated)',
            'budget'       => 999999,
        ]);
    }

    public function test_can_delete_cost_item()
    {
        $cost = Cost::create([
            'tahun'        => 2026,
            'jenis_cost'   => 'TEST DELETE',
            'account_name' => 'WILL BE DELETED',
        ]);

        $response = $this->delete("/administrasi/cost/{$cost->cost_id}");
        $response->assertRedirect('/administrasi/cost?year=2026');

        $this->assertDatabaseMissing('costs', [
            'cost_id' => $cost->cost_id,
        ]);
    }

    public function test_can_download_cost_template()
    {
        $response = $this->get('/administrasi/cost/template/download');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'Template_Import_Cost'));
    }

    public function test_can_import_cost_csv()
    {
        $csvContent = "cost_id,GL_ACCOUNT,jenis_cost,account_name,bulan,budget,realisasi,budget_tahunan\n101,135.80.00099,BIAYA ATK,KERTAS F4,,1000000,850000,12000000\n102,135.80.00099,BIAYA ATK,TINTA PRINTER,,2000000,1900000,24000000\n";
        $file = UploadedFile::fake()->createWithContent('import_cost.csv', $csvContent);

        $response = $this->post('/administrasi/cost/import', [
            'csv_file'    => $file,
            'import_mode' => 'append',
            'tahun'       => 2026,
        ]);

        $response->assertRedirect('/administrasi/cost?year=2026&month=all');
        $this->assertDatabaseHas('costs', [
            'account_name' => 'KERTAS F4',
            'jenis_cost'   => 'BIAYA ATK',
        ]);
        $this->assertDatabaseHas('costs', [
            'account_name' => 'TINTA PRINTER',
            'jenis_cost'   => 'BIAYA ATK',
        ]);
    }

    public function test_can_import_cost_csv_with_target_month()
    {
        $csvContent = "GL_ACCOUNT,jenis_cost,account_name,budget,realisasi\n135.80.00099,BIAYA KONSUMSI,MAKAN SIANG KARYAWAN,3500000,3200000\n";
        $file = UploadedFile::fake()->createWithContent('import_cost_month.csv', $csvContent);

        $response = $this->post('/administrasi/cost/import', [
            'csv_file'    => $file,
            'import_mode' => 'append',
            'tahun'       => 2026,
            'bulan'       => 7,
        ]);

        $response->assertRedirect('/administrasi/cost?year=2026&month=7');
        $this->assertDatabaseHas('costs', [
            'account_name' => 'MAKAN SIANG KARYAWAN',
            'bulan'        => 7,
            'realisasi'    => 3200000,
        ]);
    }

    public function test_can_sync_auto_aggregation_from_sources()
    {
        // 1. Seed VRA
        Vra::create([
            'tahun' => 2026,
            'bulan' => 5,
            'nomor_polisi' => 'BM 1111 AA',
            'kendaraan' => 'Bus Operasional',
            'total_biaya_bulan' => 8191473.50,
            'KM' => 1000,
        ]);

        // 2. Seed Capex RAB & Realisasi
        CapexRab::create([
            'tahun'              => 2026,
            'lokasi'             => 'kantor',
            'kel_barang_kapital' => 'AC Split Inverter',
            'jumlah_unit'        => 5,
            'harga_satuan'       => 30000000,
            'total_rp'           => 150000000,
        ]);
        CapexRealisasi::create([
            'tahun'              => 2026,
            'lokasi'             => 'kantor',
            'kel_barang_kapital' => 'AC Split Inverter',
            'nama_barang'        => 'Beli AC Split',
            'tanggal'            => '2026-05-10',
            'debit'              => 25000000,
            'kredit'             => 0,
            'total_rp'           => 25000000,
        ]);

        // 3. Seed Biaya Rekruit
        BiayaRekruit::create([
            'tahun'         => 2026,
            'tanggal'       => '2026-05-15',
            'nama'          => 'Iklan Job Portal & Assessment',
            'jenis_rekruit' => 'Eksternal',
            'total_biaya'   => 1750000,
        ]);

        // Call sync
        $response = $this->post('/administrasi/cost/sync', [
            'year' => 2026,
        ]);
        $response->assertRedirect('/administrasi/cost?year=2026');

        // Assert VRA is aggregated
        $busCost = Cost::where('account_name', 'like', '%Bus%')->first();
        $this->assertNotNull($busCost);
        $this->assertGreaterThan(0, $busCost->realisasi);

        // Assert Capex Kantor is aggregated
        $expectedRabKantor = (float) CapexRab::where('tahun', 2026)->where('lokasi', 'kantor')->sum('total_rp');
        $expectedRealKantor = (float) CapexRealisasi::where('tahun', 2026)->where('lokasi', 'kantor')->sum('total_rp');
        $this->assertDatabaseHas('costs', [
            'account_name'   => 'Capex Kantor',
            'budget_tahunan' => $expectedRabKantor,
            'realisasi'      => $expectedRealKantor,
        ]);

        // Assert Rekruit is aggregated
        $expectedRekruit = (float) BiayaRekruit::where('tahun', 2026)->sum('total_biaya');
        $this->assertDatabaseHas('costs', [
            'account_name' => 'REKRUT',
            'realisasi'    => $expectedRekruit,
        ]);
    }

    public function test_all_months_view_aggregates_sum_budget_and_realisasi()
    {
        // Create 2 monthly entries for the same account
        Cost::create([
            'tahun'        => 2026,
            'bulan'        => 1,
            'GL_ACCOUNT'   => '135.80.00777',
            'jenis_cost'   => 'BIAYA OPERASIONAL',
            'account_name' => 'KONSUMSI HARIAN',
            'budget'       => 1000000,
            'realisasi'    => 800000,
        ]);

        Cost::create([
            'tahun'        => 2026,
            'bulan'        => 2,
            'GL_ACCOUNT'   => '135.80.00777',
            'jenis_cost'   => 'BIAYA OPERASIONAL',
            'account_name' => 'KONSUMSI HARIAN',
            'budget'       => 1500000,
            'realisasi'    => 1200000,
        ]);

        $response = $this->get('/administrasi/cost?year=2026&month=all');
        $response->assertStatus(200);

        // In the aggregated 'all' view, budget should be SUM (1,000,000 + 1,500,000 = 2,500,000)
        // and realisasi should be SUM (800,000 + 1,200,000 = 2,000,000)
        $response->assertSee('2.500.000');
        $response->assertSee('2.000.000');
    }
}
