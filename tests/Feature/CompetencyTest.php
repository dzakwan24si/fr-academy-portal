<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Competency\Competency;
use App\Models\Competency\CompetencyDictionary;
use App\Models\Competency\CompetencyItem;
use App\Models\Competency\CompetencyLevel;
use App\Models\Competency\CompetencyPosition;
use App\Models\User;
use Database\Seeders\CompetencySeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CompetencyTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // GUARD: test ini menulis data. Tolak keras bila bukan database *_test.
        $database = DB::getDatabaseName();
        if (! str_ends_with((string) $database, '_test')) {
            throw new RuntimeException(
                "ABORT: CompetencyTest hanya boleh jalan di database berakhiran '_test', terdeteksi '{$database}'. Periksa override env di phpunit.xml."
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    // ---------------------------------------------------------------------
    // Seeder (Fase 1)
    // ---------------------------------------------------------------------

    private function countsFor(string $code, int $year): array
    {
        $dictionary = CompetencyDictionary::where('code', $code)->where('year', $year)->firstOrFail();
        $competencyIds = Competency::where('dictionary_id', $dictionary->id)->pluck('id');

        return [
            'positions' => CompetencyPosition::where('dictionary_id', $dictionary->id)->count(),
            'competencies' => $competencyIds->count(),
            'levels' => CompetencyLevel::whereIn('competency_id', $competencyIds)->count(),
            'items' => CompetencyItem::whereIn('competency_id', $competencyIds)->count(),
        ];
    }

    public function test_seeder_produces_exact_counts_for_both_dictionaries(): void
    {
        $this->seed(CompetencySeeder::class);

        $this->assertSame(
            ['positions' => 4, 'competencies' => 17, 'levels' => 272, 'items' => 282],
            $this->countsFor('estate', 2026)
        );
        $this->assertSame(
            ['positions' => 7, 'competencies' => 17, 'levels' => 476, 'items' => 272],
            $this->countsFor('mill', 2027)
        );
    }

    public function test_seeder_uses_json_status_and_sets_published_at(): void
    {
        $this->seed(CompetencySeeder::class);

        foreach ([['estate', 2026], ['mill', 2027]] as [$code, $year]) {
            $dictionary = CompetencyDictionary::where('code', $code)->where('year', $year)->firstOrFail();
            $this->assertSame('published', $dictionary->status);
            $this->assertNotNull($dictionary->published_at);
        }
    }

    public function test_seeder_stores_complete_level_matrix_with_no_empty_cells(): void
    {
        $this->seed(CompetencySeeder::class);

        $this->assertSame(0, CompetencyLevel::whereNull('description')->count());

        // Setiap kompetensi punya level utk semua jabatan x 4 level.
        foreach (CompetencyDictionary::with(['positions', 'competencies'])->get() as $dictionary) {
            $expectedPerCompetency = $dictionary->positions->count() * 4;
            foreach ($dictionary->competencies as $competency) {
                $this->assertSame(
                    $expectedPerCompetency,
                    CompetencyLevel::where('competency_id', $competency->id)->count(),
                    "Kompetensi {$competency->name} tidak lengkap."
                );
            }
        }
    }

    public function test_seeder_writes_one_summary_log_per_dictionary_not_per_row(): void
    {
        $before = ActivityLog::count();

        $this->seed(CompetencySeeder::class);

        $this->assertSame($before + 2, ActivityLog::count(), 'Harus tepat 2 entri ringkasan, bukan ratusan baris.');

        $imports = ActivityLog::where('action', 'Import')->where('module', 'Kamus Kompetensi')->get();
        $this->assertCount(2, $imports);
        $this->assertTrue($imports->contains(fn ($l) => str_contains($l->description, 'Impor Kamus Kompetensi') && str_contains($l->description, '2026')));
        $this->assertTrue($imports->contains(fn ($l) => str_contains($l->description, '2027')));
        $this->assertSame('Sistem', $imports->first()->user_name, 'Seeder berjalan tanpa user login.');
    }

    public function test_seeding_twice_changes_nothing_and_does_not_overwrite_manual_edits(): void
    {
        $this->seed(CompetencySeeder::class);

        $estate = CompetencyDictionary::where('code', 'estate')->firstOrFail();
        $level = CompetencyLevel::whereIn('competency_id', Competency::where('dictionary_id', $estate->id)->pluck('id'))->firstOrFail();
        $level->update(['description' => 'DIEDIT MANUAL LEWAT CRUD']);
        $estate->update(['name' => 'Nama Diedit Admin']);
        $item = CompetencyItem::firstOrFail();
        $item->delete();

        $countsBefore = [
            $this->countsFor('estate', 2026),
            $this->countsFor('mill', 2027),
        ];
        $importLogsBefore = ActivityLog::where('action', 'Import')->count();

        $this->seed(CompetencySeeder::class); // kedua kali

        $this->assertSame($countsBefore, [$this->countsFor('estate', 2026), $this->countsFor('mill', 2027)]);
        $this->assertSame('DIEDIT MANUAL LEWAT CRUD', $level->fresh()->description);
        $this->assertSame('Nama Diedit Admin', $estate->fresh()->name);
        $this->assertNull(CompetencyItem::find($item->id), 'Butir yang dihapus admin tidak boleh dikembalikan seeder.');
        $this->assertSame(2, CompetencyDictionary::count());
        $this->assertSame($importLogsBefore, ActivityLog::where('action', 'Import')->count(), 'Dictionary yang dilewati tidak boleh membuat log impor lagi.');
    }

    public function test_import_command_wraps_the_same_seeder_and_is_idempotent(): void
    {
        $this->assertSame(0, Artisan::call('competency:import'));
        $this->assertSame(2, CompetencyDictionary::count());
        $this->assertStringContainsString('Diimpor', Artisan::output());

        $this->assertSame(0, Artisan::call('competency:import'));
        $this->assertSame(2, CompetencyDictionary::count());
        $this->assertStringContainsString('Dilewati', Artisan::output());
        $this->assertSame(['positions' => 4, 'competencies' => 17, 'levels' => 272, 'items' => 282], $this->countsFor('estate', 2026));
    }

    // ---------------------------------------------------------------------
    // Model & LogsActivity (Fase 1)
    // ---------------------------------------------------------------------

    public function test_level_and_item_models_have_informative_activity_labels(): void
    {
        $dictionary = CompetencyDictionary::create([
            'code' => 'estate', 'name' => 'Uji Kamus', 'year' => 2099, 'status' => 'draft',
        ]);
        $position = CompetencyPosition::create(['dictionary_id' => $dictionary->id, 'name' => 'Asisten Uji', 'sort_order' => 1]);
        $competency = Competency::create(['dictionary_id' => $dictionary->id, 'sort_order' => 1, 'name' => 'Replanting Uji']);

        CompetencyLevel::create(['competency_id' => $competency->id, 'position_id' => $position->id, 'level' => 2, 'description' => 'Mandiri']);
        CompetencyItem::create(['competency_id' => $competency->id, 'type' => 'skill', 'body' => 'Taksasi panen', 'sort_order' => 1]);

        $levelLog = ActivityLog::where('module', 'Level Kompetensi')->latest('id')->firstOrFail();
        $this->assertSame('Tambah', $levelLog->action);
        $this->assertStringContainsString('Asisten Uji', $levelLog->description);
        $this->assertStringContainsString('L2 - Mandiri', $levelLog->description);
        $this->assertStringContainsString('Replanting Uji', $levelLog->description);

        $itemLog = ActivityLog::where('module', 'Butir Kompetensi')->latest('id')->firstOrFail();
        $this->assertStringContainsString('Keterampilan Wajib', $itemLog->description);
        $this->assertStringContainsString('Taksasi panen', $itemLog->description);
    }

    public function test_only_real_changes_are_logged_on_update(): void
    {
        $dictionary = CompetencyDictionary::create([
            'code' => 'mill', 'name' => 'Uji Kamus', 'year' => 2098, 'status' => 'draft',
        ]);
        $count = ActivityLog::count();

        $dictionary->update(['name' => 'Uji Kamus']); // tidak berubah
        $this->assertSame($count, ActivityLog::count());

        $dictionary->update(['name' => 'Uji Kamus Baru']);
        $this->assertSame($count + 1, ActivityLog::count());
    }

    public function test_published_scope_and_unique_code_year(): void
    {
        CompetencyDictionary::create(['code' => 'estate', 'name' => 'A', 'year' => 2090, 'status' => 'published']);
        CompetencyDictionary::create(['code' => 'estate', 'name' => 'B', 'year' => 2091, 'status' => 'draft']);

        $this->assertSame(['A'], CompetencyDictionary::published()->where('year', '>=', 2090)->pluck('name')->all());

        $this->expectException(QueryException::class);
        CompetencyDictionary::create(['code' => 'estate', 'name' => 'Duplikat', 'year' => 2090, 'status' => 'draft']);
    }

    public function test_deleting_dictionary_cascades_to_all_children(): void
    {
        $this->seed(CompetencySeeder::class);

        CompetencyDictionary::where('code', 'mill')->firstOrFail()->delete();

        $this->assertSame(0, CompetencyDictionary::where('code', 'mill')->count());
        $this->assertSame(['positions' => 4, 'competencies' => 17, 'levels' => 272, 'items' => 282], $this->countsFor('estate', 2026));
        $this->assertSame(4, CompetencyPosition::count());
        $this->assertSame(17, Competency::count());
        $this->assertSame(272, CompetencyLevel::count());
        $this->assertSame(282, CompetencyItem::count());
    }

    // ---------------------------------------------------------------------
    // Migration permission (Fase 1)
    // ---------------------------------------------------------------------

    private function permissionMigration(): object
    {
        return require database_path('migrations/2026_10_07_120100_add_manage_competency_permission.php');
    }

    public function test_permission_migration_is_idempotent_and_does_not_touch_other_roles(): void
    {
        $administrasi = Role::findOrCreate('Administrasi', 'web');
        $superadmin = Role::findOrCreate('Superadmin', 'web');
        $staff = Role::findOrCreate('Staff', 'web');
        $adminInfo = Role::findOrCreate('Admin E-Information', 'web');

        foreach (['access_administrasi', 'view_cost', 'edit_cost', 'manage_articles'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $administrasi->syncPermissions(['access_administrasi', 'view_cost', 'edit_cost']);
        $staff->syncPermissions(['access_administrasi', 'view_cost']);
        $adminInfo->syncPermissions(['manage_articles']);

        $migration = $this->permissionMigration();
        $migration->up();
        $migration->up(); // dijalankan dua kali tanpa error

        $this->assertSame(1, Permission::where('name', 'manage_competency')->count());

        $administrasi = $administrasi->fresh();
        $this->assertTrue($administrasi->hasPermissionTo('manage_competency'));
        $this->assertTrue($superadmin->fresh()->hasPermissionTo('manage_competency'));

        // Permission lama Administrasi tidak hilang (givePermissionTo, bukan sync).
        foreach (['access_administrasi', 'view_cost', 'edit_cost'] as $name) {
            $this->assertTrue($administrasi->hasPermissionTo($name), "Administrasi kehilangan {$name}");
        }

        // Role lain tidak ikut mendapat permission & tidak kehilangan permission miliknya.
        $this->assertFalse($staff->fresh()->hasPermissionTo('manage_competency'));
        $this->assertFalse($adminInfo->fresh()->hasPermissionTo('manage_competency'));
        $this->assertSame(['access_administrasi', 'view_cost'], $staff->fresh()->permissions->pluck('name')->sort()->values()->all());
        $this->assertSame(['manage_articles'], $adminInfo->fresh()->permissions->pluck('name')->all());
    }

    public function test_permission_migration_runs_safely_when_roles_do_not_exist(): void
    {
        Role::whereIn('name', ['Administrasi', 'Superadmin'])->delete();

        $migration = $this->permissionMigration();
        $migration->up();
        $migration->up();

        $this->assertSame(1, Permission::where('name', 'manage_competency')->count());
    }

    public function test_permission_migration_does_not_reapply_to_role_that_already_has_it(): void
    {
        $administrasi = Role::findOrCreate('Administrasi', 'web');

        $migration = $this->permissionMigration();
        $migration->up();

        $permissionId = Permission::where('name', 'manage_competency')->value('id');
        $this->assertSame(1, DB::table('role_has_permissions')
            ->where('role_id', $administrasi->id)->where('permission_id', $permissionId)->count());

        $migration->up();

        $this->assertSame(1, DB::table('role_has_permissions')
            ->where('role_id', $administrasi->id)->where('permission_id', $permissionId)->count());
    }

    public function test_role_seeder_is_untouched_by_competency_permission(): void
    {
        $this->assertStringNotContainsString(
            'manage_competency',
            (string) file_get_contents(database_path('seeders/RoleSeeder.php'))
        );
    }

    // ---------------------------------------------------------------------
    // Admin CRUD & Auth Tests (Fase 3)
    // ---------------------------------------------------------------------

    private function createEditor(): User
    {
        $role = Role::findOrCreate('Administrasi', 'web');
        $permission = Permission::findOrCreate('manage_competency', 'web');
        if (! $role->hasPermissionTo($permission)) {
            $role->givePermissionTo($permission);
        }

        $user = User::create([
            'name' => 'Test Editor',
            'email' => 'editor.'.uniqid().'@domain.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function createUnauthorizedUser(): User
    {
        return User::create([
            'name' => 'Normal User',
            'email' => 'normal.'.uniqid().'@domain.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_guest_cannot_access_admin_competency_routes(): void
    {
        $this->get('/admin/kompetensi')->assertRedirect('/login');
    }

    public function test_unauthorized_user_without_permission_receives_403(): void
    {
        $user = $this->createUnauthorizedUser();
        $this->actingAs($user);

        $this->get('/admin/kompetensi')->assertForbidden();
    }

    public function test_authorized_user_can_create_dictionary_and_toggle_publish(): void
    {
        $editor = $this->createEditor();
        $this->actingAs($editor);

        $response = $this->post('/admin/kompetensi', [
            'code' => 'estate',
            'name' => 'Estate Test Dictionary',
            'year' => 2035,
            'description' => 'Draft deskripsi',
        ]);

        $dictionary = CompetencyDictionary::where('code', 'estate')->where('year', 2035)->firstOrFail();
        $response->assertRedirect(route('admin.competency.show', $dictionary));
        $this->assertSame('draft', $dictionary->status);

        // Toggle publish -> published
        $this->patch(route('admin.competency.toggle_publish', $dictionary))->assertRedirect();
        $this->assertTrue($dictionary->fresh()->isPublished());
        $this->assertNotNull($dictionary->fresh()->published_at);

        // Toggle publish -> draft
        $this->patch(route('admin.competency.toggle_publish', $dictionary))->assertRedirect();
        $this->assertFalse($dictionary->fresh()->isPublished());
    }

    public function test_cannot_delete_published_dictionary_but_can_delete_draft(): void
    {
        $editor = $this->createEditor();
        $this->actingAs($editor);

        $dict = CompetencyDictionary::create([
            'code' => 'mill',
            'name' => 'Mill Dictionary Test',
            'year' => 2036,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->delete(route('admin.competency.destroy', $dict))->assertRedirect();
        $this->assertNotNull(CompetencyDictionary::find($dict->id), 'Dictionary published tidak boleh terhapus.');

        $dict->update(['status' => 'draft', 'published_at' => null]);
        $this->delete(route('admin.competency.destroy', $dict))->assertRedirect(route('admin.competency.index'));
        $this->assertNull(CompetencyDictionary::find($dict->id), 'Draft dictionary harus terhapus.');
    }

    public function test_copy_dictionary_to_new_year_creates_complete_draft_copy(): void
    {
        $this->seed(CompetencySeeder::class);
        $editor = $this->createEditor();
        $this->actingAs($editor);

        $source = CompetencyDictionary::where('code', 'estate')->where('year', 2026)->firstOrFail();

        $response = $this->post(route('admin.competency.copy', $source), [
            'target_year' => 2029,
        ]);

        $copied = CompetencyDictionary::where('code', 'estate')->where('year', 2029)->firstOrFail();
        $response->assertRedirect(route('admin.competency.show', $copied));

        $this->assertSame('draft', $copied->status);
        $this->assertSame(
            ['positions' => 4, 'competencies' => 17, 'levels' => 272, 'items' => 282],
            $this->countsFor('estate', 2029)
        );
    }

    public function test_positions_crud_and_reorder(): void
    {
        $editor = $this->createEditor();
        $this->actingAs($editor);

        $dict = CompetencyDictionary::create(['code' => 'estate', 'name' => 'Uji', 'year' => 2040, 'status' => 'draft']);

        // Add positions
        $this->post(route('admin.competency.positions.store', $dict), ['name' => 'Asisten A'])->assertRedirect();
        $this->post(route('admin.competency.positions.store', $dict), ['name' => 'Asisten B'])->assertRedirect();

        $posA = CompetencyPosition::where('dictionary_id', $dict->id)->where('name', 'Asisten A')->firstOrFail();
        $posB = CompetencyPosition::where('dictionary_id', $dict->id)->where('name', 'Asisten B')->firstOrFail();

        // Update name
        $this->put(route('admin.competency.positions.update', $posA), ['name' => 'Asisten Utama'])->assertRedirect();
        $this->assertSame('Asisten Utama', $posA->fresh()->name);

        // Reorder
        $this->post(route('admin.competency.positions.reorder', $dict), [
            'position_ids' => [$posB->id, $posA->id],
        ])->assertRedirect();

        $this->assertSame(1, $posB->fresh()->sort_order);
        $this->assertSame(2, $posA->fresh()->sort_order);

        // Delete
        $this->delete(route('admin.competency.positions.destroy', $posA))->assertRedirect();
        $this->assertNull(CompetencyPosition::find($posA->id));
    }

    public function test_update_competency_uses_diff_save_and_keeps_stable_ids(): void
    {
        $this->seed(CompetencySeeder::class);
        $editor = $this->createEditor();
        $this->actingAs($editor);

        $estate = CompetencyDictionary::where('code', 'estate')->firstOrFail();
        $pos = $estate->positions()->firstOrFail();
        $comp = $estate->competencies()->firstOrFail();

        $levelBefore = CompetencyLevel::where('competency_id', $comp->id)->where('position_id', $pos->id)->where('level', 1)->firstOrFail();
        $itemBefore = $comp->items()->where('type', 'knowledge')->firstOrFail();

        $levelBeforeId = $levelBefore->id;
        $itemBeforeId = $itemBefore->id;

        // Form update competency
        $response = $this->put(route('admin.competency.competencies.update', $comp), [
            'name' => $comp->name.' (Revisi)',
            'definition' => $comp->definition,
            'levels' => [
                $pos->id => [
                    1 => 'Deskripsi Level 1 Baru yang Diedit Admin',
                ],
            ],
            'items' => [
                'knowledge' => [
                    'Taksasi Panen Baru',
                    'Sistem Garis Panen',
                ],
                'skill' => ['Evaluasi Kerapatan'],
                'development' => ['In-House Training'],
            ],
        ]);

        $response->assertRedirect(route('admin.competency.show', $estate));

        $levelAfter = CompetencyLevel::find($levelBeforeId);
        $this->assertNotNull($levelAfter, 'ID Level harus stabil setelah diff-save.');
        $this->assertSame('Deskripsi Level 1 Baru yang Diedit Admin', $levelAfter->description);

        $itemAfter = CompetencyItem::find($itemBeforeId);
        $this->assertNotNull($itemAfter, 'ID Item pertama harus tetap stabil.');
        $this->assertSame('Taksasi Panen Baru', $itemAfter->body);

        $activityCount = ActivityLog::where('module', 'Kompetensi')->count();
        $this->assertGreaterThan(0, $activityCount, 'Setiap perubahan CRUD menghasilkan entri di activity_logs.');
    }

    public function test_can_create_dictionary_with_custom_category_code(): void
    {
        $editor = $this->createEditor();
        $this->actingAs($editor);

        $response = $this->post(route('admin.competency.store'), [
            'code' => 'head_office',
            'name' => 'Head Office Competency Standard',
            'year' => 2028,
            'description' => 'Kategori HO custom',
        ]);

        $dict = CompetencyDictionary::where('code', 'head_office')->where('year', 2028)->first();
        $this->assertNotNull($dict);
        $response->assertRedirect(route('admin.competency.show', $dict));
    }
}
