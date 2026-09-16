<?php

namespace Tests\Feature;

use App\Models\Administrasi\Cuti;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CutiImportTest extends TestCase
{
    public function test_cuti_csv_import_upserts_null_values_as_zero(): void
    {
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
            'cuti_diambil' => 0,
            'jan' => 1,
            'mar' => 0,
        ]);

        Cuti::where('nik', $nik)->delete();
    }
}
