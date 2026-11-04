<?php

namespace Tests\Feature;

use App\Models\Administrasi\ProfilTrainee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilTraineeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authenticateUser();
    }

    public function test_profil_trainee_page_loads_successfully()
    {
        ProfilTrainee::create([
            'trainee_id' => 'TRN-001',
            'nama_trainee' => 'Budi Santoso',
            'nama_jabatan' => 'Estate Assistant',
            'tahun_lulus' => 2023,
            'asal_universitas' => 'Institut Pertanian Bogor',
            'jurusan' => 'Agronomi',
            'status' => 'Aktif',
        ]);

        $response = $this->get(route('administrasi.profil-trainee.index'));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Estate Assistant');
        $response->assertSee('Semua Jabatan');
        $response->assertSee('Semua Tahun Lulusan');

        // Check KPIs
        $response->assertSee('Total Trainee');
        $response->assertSee('Rata-rata IPK');
        $response->assertSee('Rata-rata Usia');
        $response->assertDontSee('Distribusi Gender');
        $response->assertDontSee('Asal Kampus');

        // Check Demography Charts
        $response->assertSee('chartUniversitas');
        $response->assertSee('chartPekerjaanOrtu');
        $response->assertSee('chartSuku');
        $response->assertSee('chartProvinsi');
        $response->assertViewHas('chartUniversitas');
        $response->assertViewHas('chartPekerjaanOrtu');
        $response->assertViewHas('chartSuku');
        $response->assertViewHas('chartProvinsi');
    }

    public function test_filter_by_nama_jabatan_and_tahun_lulusan()
    {
        ProfilTrainee::create([
            'trainee_id' => 'TRN-001',
            'nama_trainee' => 'Budi Santoso',
            'nama_jabatan' => 'Estate Assistant',
            'tahun_lulus' => 2023,
            'status' => 'Aktif',
        ]);

        ProfilTrainee::create([
            'trainee_id' => 'TRN-002',
            'nama_trainee' => 'Siti Aminah',
            'nama_jabatan' => 'Mill Assistant',
            'tahun_lulus' => 2022,
            'status' => 'Aktif',
        ]);

        // Filter by jabatan
        $responseJabatan = $this->get(route('administrasi.profil-trainee.index', ['nama_jabatan' => 'Estate Assistant']));
        $responseJabatan->assertStatus(200);
        $traineesJabatan = $responseJabatan->viewData('trainees')->pluck('nama_trainee')->toArray();
        $this->assertContains('Budi Santoso', $traineesJabatan);
        $this->assertNotContains('Siti Aminah', $traineesJabatan);
        $statsJabatan = $responseJabatan->viewData('stats');
        $this->assertEquals(1, $statsJabatan['total_trainee']);

        // Filter by tahun lulusan
        $responseTahun = $this->get(route('administrasi.profil-trainee.index', ['tahun_lulusan' => '2022']));
        $responseTahun->assertStatus(200);
        $traineesTahun = $responseTahun->viewData('trainees')->pluck('nama_trainee')->toArray();
        $this->assertContains('Siti Aminah', $traineesTahun);
        $this->assertNotContains('Budi Santoso', $traineesTahun);
        $statsTahun = $responseTahun->viewData('stats');
        $this->assertEquals(1, $statsTahun['total_trainee']);
    }

    public function test_demographic_charts_aggregate_top_and_respond_to_filters(): void
    {
        ProfilTrainee::create([
            'trainee_id' => 'TRN-101',
            'nama_trainee' => 'Alpha',
            'nama_jabatan' => 'Estate Assistant',
            'asal_universitas' => 'IPB',
            'pekerjaan_orangtua' => 'Petani',
            'suku' => 'Jawa',
            'kota_provinsi_asal' => 'Riau',
            'status' => 'Aktif',
        ]);
        ProfilTrainee::create([
            'trainee_id' => 'TRN-102',
            'nama_trainee' => 'Beta',
            'nama_jabatan' => 'Estate Assistant',
            'asal_universitas' => 'IPB',
            'pekerjaan_orangtua' => 'PNS',
            'suku' => 'Batak',
            'kota_provinsi_asal' => 'Sumatera Utara',
            'status' => 'Aktif',
        ]);
        ProfilTrainee::create([
            'trainee_id' => 'TRN-103',
            'nama_trainee' => 'Gamma',
            'nama_jabatan' => 'Mill Assistant',
            'asal_universitas' => 'UGM',
            'pekerjaan_orangtua' => 'Wiraswasta',
            'suku' => 'Jawa',
            'kota_provinsi_asal' => 'Jawa Tengah',
            'status' => 'Aktif',
        ]);

        // When filtering by Estate Assistant
        $response = $this->get(route('administrasi.profil-trainee.index', ['nama_jabatan' => 'Estate Assistant']));
        $response->assertStatus(200);

        $chartUniv = $response->viewData('chartUniversitas');
        $this->assertContains('IPB', $chartUniv['labels']);
        $this->assertNotContains('UGM', $chartUniv['labels']);
        $ipkIndex = array_search('IPB', $chartUniv['labels']);
        $this->assertEquals(2, $chartUniv['data'][$ipkIndex]);

        $chartOrtu = $response->viewData('chartPekerjaanOrtu');
        $this->assertContains('Petani', $chartOrtu['labels']);
        $this->assertContains('PNS', $chartOrtu['labels']);
        $this->assertNotContains('Wiraswasta', $chartOrtu['labels']);

        $chartSuku = $response->viewData('chartSuku');
        $this->assertContains('Jawa', $chartSuku['labels']);
        $this->assertContains('Batak', $chartSuku['labels']);
    }

    public function test_can_download_template_without_id()
    {
        $response = $this->get(route('administrasi.profil-trainee.template'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_import_csv_without_id_auto_generates_trainee_id()
    {
        $csv = implode("\n", [
            'Nama Trainee,Nama Jabatan,Asal Universitas,Jurusan,Jenis Kelamin,Usia',
            'Trainee Auto,Asisten Tanaman,Universitas Andalas,Agro,Laki-laki,23',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('trainees.csv', $csv);

        $response = $this->post(route('administrasi.profil-trainee.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('profil_trainees', [
            'nama_trainee' => 'Trainee Auto',
        ]);

        $t = ProfilTrainee::where('nama_trainee', 'Trainee Auto')->first();
        $this->assertNotNull($t->trainee_id);
        $this->assertStringStartsWith('TRN-', $t->trainee_id);
    }
}
