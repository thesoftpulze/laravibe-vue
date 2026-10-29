<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Settings\AuthenticationSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Middleware;
use Override;

final class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    #[Override]
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user?->toResource(),
                'can' => $this->resolveAbilities($user),
            ],
            'canRegister' => app(AuthenticationSettings::class)->allowRegistration,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Resolve the UI-relevant abilities for the given user.
     *
     * Add ability keys here as the application grows. Each key maps directly
     * to a `can()` call in Vue components (e.g. `can('updateProfile')`).
     *
     * @return array<string, bool>
     */
    private function resolveAbilities(?User $user): array
    {
        if (! $user instanceof User) {
            return [
                'updateProfile' => false,
                'deleteAccount' => false,
                'accessAdministration' => false,
                'manageSettings' => false,
            ];
        }

        $gate = Gate::forUser($user);

        return [
            'updateProfile' => $gate->allows('updateProfile'),
            'deleteAccount' => $gate->allows('deleteAccount'),
            'accessAdministration' => $gate->allows('accessAdministration'),
            'manageSettings' => $gate->allows('manageSettings'),
        ];
    }
}
