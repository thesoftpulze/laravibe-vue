<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
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
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    #[Override]
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

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
        /** @var string $quote */
        $quote = Inspiring::quotes()->random();
        [$message, $author] = str($quote)->explode('-');

        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => mb_trim((string) $message), 'author' => mb_trim((string) $author)],
            'auth' => [
                'user' => $user?->toResource(),
                'can' => $this->resolveAbilities($user),
            ],
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
            ];
        }

        return [
            'updateProfile' => true,
            'deleteAccount' => true,
        ];
    }
}
