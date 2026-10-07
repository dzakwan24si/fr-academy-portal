<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Inventaris\Barang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_log_created_automatically_on_model_events(): void
    {
        $user = $this->authenticateUser();

        // 1. CREATE Event
        $barang = Barang::create([
            'nama_asset' => 'Laptop ThinkPad',
            'satuan' => 'Unit',
            'jenis_lokasi' => 'Kantor',
            'lokasi' => 'Ruang IT',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Tambah',
            'module' => 'Inventaris Barang',
            'user_name' => $user->name,
        ]);

        // 2. UPDATE Event
        $barang->update([
            'nama_asset' => 'Laptop ThinkPad X1',
        ]);

        $editLog = ActivityLog::where('action', 'Edit')->latest()->first();
        $this->assertNotNull($editLog);
        $this->assertEquals('Inventaris Barang', $editLog->module);
        $this->assertArrayHasKey('old', $editLog->properties);
        $this->assertArrayHasKey('new', $editLog->properties);
        $this->assertEquals('Laptop ThinkPad', $editLog->properties['old']['nama_asset']);
        $this->assertEquals('Laptop ThinkPad X1', $editLog->properties['new']['nama_asset']);

        // 3. DELETE Event
        $barang->delete();

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Hapus',
            'module' => 'Inventaris Barang',
        ]);
    }

    public function test_activity_log_page_accessible_by_authenticated_users(): void
    {
        $this->authenticateUser();

        ActivityLog::log('Tambah', 'Test Modul', 'Testing description');

        $response = $this->get(route('administrasi.activity-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Histori Aktivitas');
        $response->assertSee('Test Modul');
    }

    public function test_activity_log_pruning_command(): void
    {
        // Old log (130 days old) inserted via DB table to bypass Eloquent timestamp setting
        $oldId = DB::table('activity_logs')->insertGetId([
            'action' => 'Tambah',
            'module' => 'Old Module',
            'description' => 'Old description',
            'created_at' => now()->subDays(130),
            'updated_at' => now()->subDays(130),
        ]);

        // Recent log (5 days old)
        $newId = DB::table('activity_logs')->insertGetId([
            'action' => 'Tambah',
            'module' => 'New Module',
            'description' => 'New description',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        $this->artisan('activity-log:clean --days=120')
            ->assertExitCode(0);

        $this->assertDatabaseMissing('activity_logs', ['id' => $oldId]);
        $this->assertDatabaseHas('activity_logs', ['id' => $newId]);
    }

    public function test_activity_log_created_on_csv_import(): void
    {
        $this->authenticateUser();

        $file = UploadedFile::fake()->createWithContent(
            'test_barang.csv',
            "NAMA ASSET,SATUAN,LOKASI,KETERANGAN\nKursi Kerja,Unit,Kantor,Bagus\n"
        );

        $response = $this->post(route('administrasi.inventaris.barang.import'), [
            'file' => $file,
            'default_lokasi' => 'Kantor',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'Import',
            'module' => 'Inventaris Barang',
        ]);
    }
}
