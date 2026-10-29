<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Settings\GeneralSettings;
use App\Support\LaraTweaks;
use Illuminate\Http\RedirectResponse;
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
     * Define the gates behind the abilities shared with the frontend through `auth.can`,
     * and the administration's per-user abilities, which the users list sends with each user.
     */
    private function configureGates(): void
    {
        Gate::define('updateProfile', fn (User $user): bool => true);
        Gate::define('deleteAccount', fn (User $user): bool => true);
        Gate::define('accessAdministration', fn (User $user): bool => $user->is_admin);
        Gate::define('manageSettings', fn (User $user): bool => $user->is_admin);

        // Administrators never change their own role or delete themselves, so at least one administrator remains.
        Gate::define('updateUserRole', fn (User $user, User $target): bool => $user->is_admin && ! $user->is($target));
        Gate::define('deleteUser', fn (User $user, User $target): bool => $user->is_admin && ! $user->is($target));
        Gate::define('resendUserVerification', fn (User $user, User $target): bool => $user->is_admin && ! $target->hasVerifiedEmail());
    }

    /**
     * Render HTTP errors as Inertia pages. Server errors keep Laravel's debug page while debugging,
     * and an expired form submission goes back to the form instead of showing the error at its URL.
     */
    private function configureErrorPages(): void
    {
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response): ExceptionResponse|RedirectResponse|null {
            $status = $response->statusCode();

            if ($response->request->expectsJson() || $response->request->is('api/*') || ! in_array($status, [403, 404, 419, 500, 503], true)) {
                return null;
            }

            if ($status === 419 && ! $response->request->isMethod('GET')) {
                toastWarning(__('The page expired. Please try again.'));

                return back();
            }

            if ($status >= 500 && config()->boolean('app.debug')) {
                return null;
            }

            return $response->render('Error', [
                'status' => $status,
                'supportEmail' => app(GeneralSettings::class)->supportEmail,
            ])->withSharedData();
        });
    }
}
