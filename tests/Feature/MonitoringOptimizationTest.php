<?php

namespace Tests\Feature;

use App\Models\Monitoring\MonitoringTalent;
use Tests\TestCase;

class MonitoringOptimizationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }

    public function test_main_dashboard_and_dashboard_realisasi_loads_successfully()
    {
        $mainResponse = $this->get('/');
        $mainResponse->assertStatus(200);

        $realisasiResponse = $this->get('/administrasi/dashboard-realisasi');
        $realisasiResponse->assertStatus(200);

        $realisasiFiltered = $this->get('/administrasi/dashboard-realisasi?region=Riau&program_type=non_reguler');
        $realisasiFiltered->assertStatus(200);
    }

    public function test_dashboard_monitoring_tahunan_loads_successfully()
    {
        $response = $this->get('/administrasi/dashboard-monitoring-tahunan');
        $response->assertStatus(200);

        $responseReguler = $this->get('/administrasi/dashboard-monitoring-tahunan?tab=reguler&month=FEB');
        $responseReguler->assertStatus(200);
    }

    public function test_monitoring_talent_crud_and_exports()
    {
        $response = $this->get('/administrasi/monitoring-talent');
        $response->assertStatus(200);

        $talentData = [
            'employee_id' => 'TEST999',
            'employee_name' => 'John Doe Test',
            'posisi_asal' => 'Asisten Kebun',
            'talent_to' => 'Askep',
            'unit' => 'EST1',
            'region' => 'Riau',
            'kategori' => 'TALENT ESTATE TAHUN 2026',
        ];

        $postResponse = $this->post('/administrasi/monitoring-talent/store', $talentData);
        $postResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('monitoring_talents', [
            'employee_id' => 'TEST999',
            'employee_name' => 'John Doe Test',
        ]);

        $talent = MonitoringTalent::where('employee_id', 'TEST999')->first();
        $this->assertNotNull($talent);

        $talentData['employee_name'] = 'John Doe Updated';
        $putResponse = $this->put('/administrasi/monitoring-talent/'.$talent->id, $talentData);
        $putResponse->assertSessionHasNoErrors();
        $this->assertDatabaseHas('monitoring_talents', [
            'id' => $talent->id,
            'employee_name' => 'John Doe Updated',
        ]);

        $templateResponse = $this->get('/administrasi/monitoring-talent/template');
        $templateResponse->assertStatus(200);

        $exportResponse = $this->get('/administrasi/monitoring-talent/export');
        $exportResponse->assertStatus(200);

        $delResponse = $this->delete('/administrasi/monitoring-talent/'.$talent->id);
        $delResponse->assertSessionHasNoErrors();
        $this->assertDatabaseMissing('monitoring_talents', ['id' => $talent->id]);
    }

    public function test_monitoring_dashboard_apis()
    {
        $empRes = $this->getJson('/administrasi/monitoring/api/employees');
        $empRes->assertStatus(200)->assertJson(['status' => 'success']);

        $storeEmp = $this->postJson('/administrasi/monitoring/api/employees', [
            'NIK' => 'NIK999',
            'NAMA' => 'Karyawan Test Monitoring',
            'JABATAN' => 'Staff IT',
            'STATUS' => 'Aktif',
        ]);
        $storeEmp->assertStatus(201)->assertJson(['status' => 'success']);
        $createdEmpId = $storeEmp->json('data.ID_MONITORING');

        $updateEmp = $this->putJson('/administrasi/monitoring/api/employees/'.$createdEmpId, [
            'NAMA' => 'Karyawan Test Monitoring Edit',
        ]);
        $updateEmp->assertStatus(200)->assertJson(['status' => 'success']);

        $delEmp = $this->deleteJson('/administrasi/monitoring/api/employees/'.$createdEmpId);
        $delEmp->assertStatus(200)->assertJson(['status' => 'success']);

        $angkatanRes = $this->getJson('/administrasi/monitoring/api/angkatan');
        $angkatanRes->assertStatus(200)->assertJson(['status' => 'success']);

        $culledRes = $this->getJson('/administrasi/monitoring/api/culled');
        $culledRes->assertStatus(200)->assertJson(['status' => 'success']);

        $afdelingRes = $this->getJson('/administrasi/monitoring/api/afdeling');
        $afdelingRes->assertStatus(200)->assertJson(['status' => 'success']);
    }

    public function test_monitoring_summarized_and_alumni_endpoints()
    {
        $summarizedView = $this->get('/administrasi/monitoring-summarized');
        $summarizedView->assertStatus(200);

        $alumniView = $this->get('/administrasi/monitoring-alumni');
        $alumniView->assertStatus(200);

        $dataRes = $this->getJson('/administrasi/monitoring-summarized/api/data?view_mode=aggregated');
        $dataRes->assertStatus(200)->assertJson(['status' => 'success']);

        $drilldownRes = $this->getJson('/administrasi/monitoring-summarized/api/drilldown?program=FAT');
        $drilldownRes->assertStatus(200)->assertJson(['status' => 'success']);
    }
}
