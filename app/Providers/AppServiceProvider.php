<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Support\LaraTweaks;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Inertia\ExceptionResponse;
use Inertia\Inertia;
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
        $this->configureErrorPages();
    }

    /**
     * Define the gates behind the abilities shared with the frontend through `auth.can`.
     */
    private function configureGates(): void
    {
        Gate::define('updateProfile', fn (User $user): bool => true);
        Gate::define('deleteAccount', fn (User $user): bool => true);
    }

    /**
     * Render HTTP errors as Inertia pages. Server errors keep Laravel's debug page while debugging.
     */
    private function configureErrorPages(): void
    {
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response): ?ExceptionResponse {
            $status = $response->statusCode();

            if ($response->request->expectsJson() || $response->request->is('api/*') || ! in_array($status, [403, 404, 419, 500, 503], true)) {
                return null;
            }

            if ($status >= 500 && config()->boolean('app.debug')) {
                return null;
            }

            return $response->render('Error', ['status' => $status])->withSharedData();
        });
    }
}
