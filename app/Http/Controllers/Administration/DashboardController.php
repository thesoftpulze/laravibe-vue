<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration;

use App\Actions\Administration\GetUserStatisticsAction;
use App\DTOs\PageMeta;
use App\Http\Resources\Administration\UserResource;
use App\Models\User;
use Inertia\Response;

final class DashboardController
{
    /**
     * Show the user statistics and the latest sign-ups.
     */
    public function show(GetUserStatisticsAction $statistics): Response
    {
        return vue('administration/Dashboard', [
            'statistics' => deferProp(fn (): array => $statistics->handle()->toArray()),
            'recentUsers' => deferProp(fn (): array => UserResource::collection(
                User::query()->latest()->latest('id')->limit(5)->get(),
            )->toInertia()),
        ], new PageMeta(
            title: 'Dashboard',
        ));
    }
}
