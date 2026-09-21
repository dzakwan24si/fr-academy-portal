<?php

namespace Tests\Feature;

use App\Models\Program\ProgramReguler;
use App\Models\Program\ProgramNonReguler;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProgramBatchImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }

    public function test_regular_csv_accepts_null_numeric_values(): void
    {
        $csv = implode("\n", [
            'Program_id,Kategori_Program,Training_Program,Batch,Lokasi_training,Mulai_program,Selesai_program,jumlah_bulan_training,jumlah_hari,Total_training_days,Promosi_mandor,Fresh_Graduate,target_peserta,Uang_saku,jenis_program_reguler,Region,tanggal_pelaksanaan',
            '999999,REGULER,Test Program,I-2026,Jakarta,2026-01-01,2026-01-02,null,null,null,0,10,10,null,Training Reguler - Staf,Riau,null',
        ]);

        $response = $this->post('/administrasi/program-batch/import', [
            'file' => UploadedFile::fake()->createWithContent('program_batch_Reguler.csv', $csv),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('program_reguler', [
            'Program_reguler_id' => 999999,
            'jumlah_bulan_training' => 0,
            'jumlah_hari' => 0,
            'Total_training_days' => 0,
            'Uang_saku' => 0,
        ]);
        $this->assertSame(1, ProgramReguler::where('Program_reguler_id', 999999)->count());
        ProgramReguler::where('Program_reguler_id', 999999)->delete();
    }

    public function test_non_regular_csv_accepts_json_dates(): void
    {
        $csv = implode("\n", [
            'id_nonreguler,kategori_pelatihan,jenis_pelatihan,region,trainer,type_training,total_jam,total_hari,peserta_non_staf,peserta_ast,peserta_askep,peserta_mgr,peserta_gm,peserta_head,target_staf,target_non_staf,target_total,training_staf,training_non_staf,training_total,td_staf,td_non_staf,td_total,start_date,end_date,total_batch,lokasi_training,deskripsi,sub_kategori,tanggal_pelaksanaan,TARGET_BATCH,TARGET_PESERTA',
            '999999,Teknis,Test Non Reguler,Riau,Internal,Workshop,8,1,0,0,0,0,0,5,5,0,5,0,0,0,5,0,5,2026-01-01,2026-01-01,1,Jakarta,,Administrasi,"[""2026-01-01""]",1,5',
        ]);

        $response = $this->post('/administrasi/program-batch/import', [
            'file' => UploadedFile::fake()->createWithContent('batch_nonreguler.csv', $csv),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('program_non_reguler', [
            'id_nonreguler' => 999999,
            'jenis_pelatihan' => 'Test Non Reguler',
        ]);
        $this->assertSame(['2026-01-01'], ProgramNonReguler::find(999999)->tanggal_pelaksanaan);
        ProgramNonReguler::where('id_nonreguler', 999999)->delete();
    }

    public function test_regular_csv_with_alternate_headers_and_multiline(): void
    {
        $csv = implode("\n", [
            'Program_reguler_id,Nama_Program_reguler,Batch,Lokasi_training,Mulai_program,Selesai_program,jumlah_bulan_training,jumlah_hari,Total_training_days,Promosi_mandor,Fresh_Graduate,Total_orang,Uang_saku,jenis_program_reguler,Region,tanggal_pelaksanaan',
            '888888,"FAT Multi-Line' . "\n" . 'Program",I-2026,FRLC Kubang,2026-01-01,2026-02-27,6,180,1584,2,31,33,155000000,Training Reguler - Staf,Riau,"[""2026-01-01""]"',
        ]);

        $response = $this->post('/administrasi/program-batch/import', [
            'file' => UploadedFile::fake()->createWithContent('semua-data-program-training.csv', $csv),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('program_reguler', [
            'Program_reguler_id' => 888888,
            'Training_Program' => "FAT Multi-Line\nProgram",
            'target_peserta' => 33,
            'Region' => 'Riau',
        ]);
        ProgramReguler::where('Program_reguler_id', 888888)->delete();
    }
}
