<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Support\LaraTweaks;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Override;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LaraTweaks::default();

        $this->configureGates();
    }

    /**
     * Define the gates behind the abilities shared with the frontend through `auth.can`.
     */
    private function configureGates(): void
    {
        Gate::define('updateProfile', fn (User $user): bool => true);
        Gate::define('deleteAccount', fn (User $user): bool => true);
    }
}
