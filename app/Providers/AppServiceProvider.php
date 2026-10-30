<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            // Superadmin has universal access
            if ($user->hasRole('Superadmin')) {
                return true;
            }

            // Auto-grant Read access if user already has Edit permission for the module
            if (str_starts_with($ability, 'view_')) {
                $editAbility = 'edit_' . substr($ability, 5);
                try {
                    if ($user->hasPermissionTo($editAbility)) {
                        return true;
                    }
                } catch (\Throwable $e) {
                    // Not a registered Spatie permission
                }
            }

            return null;
        });
    }
}
