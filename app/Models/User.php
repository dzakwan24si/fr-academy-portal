<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\LogsActivity;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'nik', 'is_active', 'last_login_at', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable;

    protected static $logModuleName = 'Pengguna';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Menentukan URL default (landing page) pengguna berdasarkan role dan permission.
     * Jika tidak memiliki hak akses dashboard, cek hak akses halaman lain yang dimiliki.
     */
    public function getDefaultLandingUrl(): string
    {
        // 1. Superadmin & Top Management selalu memiliki akses ke Dashboard Utama
        if ($this->hasRole('Superadmin') || $this->hasRole('Top Management')) {
            return route('administrasi.dashboard-realisasi.index');
        }

        // 2. Jika pengguna memiliki hak akses melihat Dashboard
        if ($this->can('view_dashboard') || $this->can('edit_dashboard')) {
            return route('administrasi.dashboard-realisasi.index');
        }

        // 3. Modul Talent (Role Evaluator atau memiliki permission view_talent / edit_talent)
        if ($this->hasAnyRole(['evaluator', 'Evaluator', 'EVALUATOR']) || $this->can('view_talent') || $this->can('edit_talent')) {
            return route('administrasi.profil-talent.index');
        }

        // 4. Modul Program Training
        if ($this->can('view_program') || $this->can('edit_program')) {
            return route('administrasi.program-batch.index');
        }

        // 5. Modul Monitoring
        if ($this->can('view_monitoring') || $this->can('edit_monitoring')) {
            return route('administrasi.monitoring-summarized.index');
        }

        // 6. Modul Administrasi Umum
        if ($this->can('view_administrasi') || $this->can('edit_administrasi') || $this->can('access_administrasi')) {
            return route('administrasi.profil-trainee.index');
        }

        // 7. Modul Cost & Finansial
        if ($this->can('view_cost') || $this->can('edit_cost')) {
            return route('administrasi.cost.index');
        }

        // 8. Modul Data Aset (Inventaris)
        if ($this->can('view_data_aset') || $this->can('edit_data_aset')) {
            return route('administrasi.inventaris.dashboard');
        }

        // 9. Modul Learning Activity / Trainer
        if ($this->can('access_learning_activity') || $this->hasRole('Trainer')) {
            return route('training-non-reguler.dashboard');
        }

        // 10. Modul Standar Kompetensi
        if ($this->can('manage_competency')) {
            return route('admin.competency.index');
        }

        // 11. Manajemen Artikel / E-Information
        if ($this->can('manage_articles')) {
            return route('admin.artikel.index');
        }

        // 12. Manajemen Pengguna & Peran
        if ($this->can('manage_users')) {
            return route('admin.users.index');
        }
        if ($this->can('manage_roles')) {
            return route('admin.roles.index');
        }

        // 13. Event & Tema
        if ($this->can('manage_events')) {
            return route('admin.events.index');
        }

        // 14. Program Karir
        if ($this->can('manage_career')) {
            return route('admin.career_programs.index');
        }

        // 15. TV Display
        if ($this->can('manage_tv_display')) {
            return route('admin.tv.index');
        }

        // Fallback jika tidak ada modul operasional khusus
        return route('admin.profile.edit');
    }
}
