<?php

namespace Tests\Feature;

use App\Models\Cost\CapexRab;
use App\Models\Cost\CapexRealisasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CapexTest extends TestCase
{
    use DatabaseTransactions;
    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
        CapexRealisasi::query()->delete();
        CapexRab::query()->delete();

        CapexRab::create([
            'rab_id' => 1,
            'lokasi' => 'kantor',
            'kel_barang_kapital' => 'TV',
            'type' => 'Maxhub C8630',
            'satuan' => 'Unit',
            'jumlah_unit' => 1,
            'harga_satuan' => 159998730,
            'total_rp' => 159998730,
            'tahun' => 2026,
        ]);
        CapexRab::create([
            'rab_id' => 8,
            'lokasi' => 'mess',
            'kel_barang_kapital' => 'AC',
            'type' => 'Daikin',
            'satuan' => 'Unit',
            'jumlah_unit' => 4,
            'harga_satuan' => 4400000,
            'total_rp' => 17600000,
            'tahun' => 2026,
        ]);
        CapexRealisasi::create([
            'realisasi_id' => 1,
            'rab_id' => 1,
            'lokasi' => 'kantor',
            'kel_barang_kapital' => 'TV',
            'nama_barang' => 'TV Maxhub',
            'type' => 'Maxhub',
            'tanggal' => '2026-03-31',
            'debit' => 159998730,
            'kredit' => 0,
            'total_rp' => 159998730,
            'tahun' => 2026,
        ]);
        CapexRealisasi::create([
            'realisasi_id' => 2,
            'rab_id' => 8,
            'lokasi' => 'mess',
            'kel_barang_kapital' => 'AC',
            'nama_barang' => 'AC Mess',
            'type' => 'Daikin',
            'tanggal' => '2026-05-31',
            'debit' => 17600000,
            'kredit' => 0,
            'total_rp' => 17600000,
            'tahun' => 2026,
        ]);
    }

    public function test_can_view_capex_page()
    {
        $response = $this->get('/administrasi/capex?year=2026');
        $response->assertStatus(200);
        $response->assertSee('CAPEX');
        $response->assertSee('RAB Kantor');
        $response->assertSee('Realisasi Kantor');
        $response->assertSee('RAB Mess');
        $response->assertSee('Realisasi Mess');
        $response->assertSee('Total RAB Anggaran');
        $response->assertSee('Total Realisasi Aktual');
    }

    public function test_can_filter_by_location()
    {
        $response = $this->get('/administrasi/capex?year=2026&location=kantor');
        $response->assertStatus(200);
        $response->assertSee('CAPEX KANTOR (RAB VS REALISASI)');

        $responseMess = $this->get('/administrasi/capex?year=2026&location=mess');
        $responseMess->assertStatus(200);
        $responseMess->assertSee('CAPEX MESS (RAB VS REALISASI)');
    }

    public function test_can_create_rab_record()
    {
        $payload = [
            'tahun'              => 2026,
            'lokasi'             => 'kantor',
            'kel_barang_kapital' => 'Server & Network Rack',
            'type'               => 'Dell PowerEdge R750',
            'satuan'             => 'Unit',
            'jumlah_unit'        => 2,
            'harga_satuan'       => 75000000,
        ];

        $response = $this->post('/administrasi/capex/rab', $payload);
        $response->assertRedirect('/administrasi/capex?year=2026&location=kantor');

        $this->assertDatabaseHas('capex_rab', [
            'kel_barang_kapital' => 'Server & Network Rack',
            'type'               => 'Dell PowerEdge R750',
            'total_rp'           => 150000000,
        ]);
    }

    public function test_can_update_rab_record()
    {
        $rab = CapexRab::first();
        $this->assertNotNull($rab);

        $payload = [
            'tahun'              => $rab->tahun,
            'lokasi'             => $rab->lokasi,
            'kel_barang_kapital' => 'Updated Item Name',
            'type'               => 'Updated Type',
            'satuan'             => 'Unit',
            'jumlah_unit'        => 3,
            'harga_satuan'       => 10000000,
        ];

        $response = $this->put('/administrasi/capex/rab/' . $rab->rab_id, $payload);
        $response->assertRedirect('/administrasi/capex?year=' . $rab->tahun . '&location=' . $rab->lokasi);

        $this->assertDatabaseHas('capex_rab', [
            'rab_id'             => $rab->rab_id,
            'kel_barang_kapital' => 'Updated Item Name',
            'total_rp'           => 30000000,
        ]);
    }

    public function test_can_delete_rab_record()
    {
        $rab = CapexRab::create([
            'tahun'              => 2026,
            'lokasi'             => 'mess',
            'kel_barang_kapital' => 'Item to Delete',
            'jumlah_unit'        => 1,
            'harga_satuan'       => 1000000,
            'total_rp'           => 1000000,
        ]);

        $response = $this->delete('/administrasi/capex/rab/' . $rab->rab_id);
        $response->assertRedirect('/administrasi/capex?year=2026&location=mess');

        $this->assertDatabaseMissing('capex_rab', [
            'rab_id' => $rab->rab_id,
        ]);
    }

    public function test_can_create_realisasi_record()
    {
        $payload = [
            'tahun'              => 2026,
            'lokasi'             => 'kantor',
            'kel_barang_kapital' => 'Komputer & Laptop',
            'nama_barang'        => 'Lenovo ThinkPad P16',
            'type'               => 'ThinkPad',
            'tanggal'            => '2026-04-12',
            'debit'              => 32000000,
            'kredit'             => 0,
        ];

        $response = $this->post('/administrasi/capex/realisasi', $payload);
        $response->assertRedirect('/administrasi/capex?year=2026&location=kantor');

        $this->assertDatabaseHas('capex_realisasi', [
            'nama_barang' => 'Lenovo ThinkPad P16',
            'total_rp'    => 32000000,
        ]);
    }

    public function test_can_update_realisasi_record()
    {
        $realisasi = CapexRealisasi::first();
        $this->assertNotNull($realisasi);

        $payload = [
            'tahun'              => $realisasi->tahun,
            'lokasi'             => $realisasi->lokasi,
            'kel_barang_kapital' => $realisasi->kel_barang_kapital,
            'nama_barang'        => 'Updated Realisasi Name',
            'type'               => 'Updated Model',
            'tanggal'            => '2026-05-20',
            'debit'              => 25000000,
            'kredit'             => 5000000,
        ];

        $response = $this->put('/administrasi/capex/realisasi/' . $realisasi->realisasi_id, $payload);
        $response->assertRedirect('/administrasi/capex?year=' . $realisasi->tahun . '&location=' . $realisasi->lokasi);

        $this->assertDatabaseHas('capex_realisasi', [
            'realisasi_id' => $realisasi->realisasi_id,
            'nama_barang'  => 'Updated Realisasi Name',
            'total_rp'     => 20000000,
        ]);
    }

    public function test_can_delete_realisasi_record()
    {
        $realisasi = CapexRealisasi::create([
            'tahun'              => 2026,
            'lokasi'             => 'mess',
            'kel_barang_kapital' => 'Mess Asset',
            'nama_barang'        => 'Item to Delete',
            'total_rp'           => 5000000,
        ]);

        $response = $this->delete('/administrasi/capex/realisasi/' . $realisasi->realisasi_id);
        $response->assertRedirect('/administrasi/capex?year=2026&location=mess');

        $this->assertDatabaseMissing('capex_realisasi', [
            'realisasi_id' => $realisasi->realisasi_id,
        ]);
    }

    public function test_can_download_templates()
    {
        $responseRab = $this->get('/administrasi/capex/template/download?type=rab');
        $responseRab->assertStatus(200);
        $responseRab->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $responseRealisasi = $this->get('/administrasi/capex/template/download?type=realisasi');
        $responseRealisasi->assertStatus(200);
        $responseRealisasi->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_export_excel()
    {
        $response = $this->get('/administrasi/capex/export/report?year=2026&type=all');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_filter_by_month()
    {
        $response = $this->get('/administrasi/capex?year=2026&month=3');
        $response->assertStatus(200);
        $response->assertSee('TV Maxhub');
        $response->assertDontSee('AC Mess');
    }

    public function test_can_import_rab_csv()
    {
        $csvContent = "rab_id,Lokasi,Kel_barang_kapital,Foto_barang,type,satuan,jumlah_unit,harga_satuan,total_rp\n";
        $csvContent .= "999,kantor,Testing Import CSV,,Type X,Unit,5,1000000,5000000\n";

        $file = UploadedFile::fake()->createWithContent('import_rab.csv', $csvContent);

        $response = $this->post('/administrasi/capex/import', [
            'csv_file'    => $file,
            'import_type' => 'auto',
            'import_mode' => 'append',
            'tahun'       => 2026,
        ]);

        $response->assertRedirect('/administrasi/capex?year=2026');
        $this->assertDatabaseHas('capex_rab', [
            'rab_id'             => 999,
            'kel_barang_kapital' => 'Testing Import CSV',
            'total_rp'           => 5000000,
        ]);
    }

    public function test_can_import_realisasi_csv()
    {
        $csvContent = "realisasi_id,rab_id,Lokasi,Kel_barang_kapital,Foto_barang,nama_barang,type,tanggal,debit,kredit,total_rp\n";
        $csvContent .= "888,,mess,Testing Rel Import,,Barang Rel Test,Type Y,2026-06-01,12000000,2000000,10000000\n";

        $file = UploadedFile::fake()->createWithContent('import_realisasi.csv', $csvContent);

        $response = $this->post('/administrasi/capex/import', [
            'csv_file'    => $file,
            'import_type' => 'auto',
            'import_mode' => 'append',
            'tahun'       => 2026,
        ]);

        $response->assertRedirect('/administrasi/capex?year=2026');
        $this->assertDatabaseHas('capex_realisasi', [
            'realisasi_id' => 888,
            'nama_barang'  => 'Barang Rel Test',
            'total_rp'     => 10000000,
        ]);
    }
}
