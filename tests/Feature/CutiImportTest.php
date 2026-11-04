<?php

namespace Tests\Feature;

use App\Models\Administrasi\Cuti;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CutiImportTest extends TestCase
{
    public function test_cuti_csv_import_upserts_null_values_as_zero(): void
    {
        $this->authenticateUser();
        $nik = 'TEST-IMPORT-CUTI';
        $csv = implode("\n", [
            'nik,nama,jabatan,tmk,tahun,hak_cuti,sisa_hak_cuti_carryover,cuti_diambil,jan,feb,mar,apr,mei,jun,jul,agu,sep,okt,nov,des,keterangan',
            "{$nik},Pegawai Import,Staff,2020-01-01,2026,12,null,null,1,0,null,0,0,0,0,0,0,0,0,0,Import",
        ]);

        $response = $this->post('/administrasi/cuti/import', [
            'file' => UploadedFile::fake()->createWithContent('cuti.csv', $csv),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cuti', [
            'nik' => $nik,
            'tahun' => 2026,
            'sisa_hak_cuti_carryover' => 0,
            'cuti_diambil' => 1,
            'jan' => 1,
            'mar' => 0,
        ]);

        Cuti::where('nik', $nik)->delete();
    }

    public function test_can_download_cuti_template()
    {
        $this->authenticateUser();
        $response = $this->get('/administrasi/cuti/template/download');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_import_cuti_without_nik_auto_generates_nik()
    {
        $this->authenticateUser();
        $csv = implode("\n", [
            'nama,jabatan,jenis_jabatan,tmk,hak_cuti,tahun,jan,feb,mar,apr,mei,jun,jul,agu,sep,okt,nov,des',
            'Karyawan Tanpa NIK,Staff Kebun,Staff,2022-01-01,12,2026,0,0,0,0,0,0,0,0,0,0,0,0',
        ]);

        $response = $this->post('/administrasi/cuti/import', [
            'file' => UploadedFile::fake()->createWithContent('cuti_noid.csv', $csv),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cuti', [
            'nama' => 'Karyawan Tanpa NIK',
            'tahun' => 2026,
        ]);

        $item = Cuti::where('nama', 'Karyawan Tanpa NIK')->first();
        $this->assertNotNull($item->nik);
        $this->assertStringStartsWith('FRA-', $item->nik);
        $this->assertNotNull($item->cuti_id);
        $this->assertGreaterThan(0, $item->cuti_id);

        Cuti::where('nama', 'Karyawan Tanpa NIK')->delete();
    }
}
