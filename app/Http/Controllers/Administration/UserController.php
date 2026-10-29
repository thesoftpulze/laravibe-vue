<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration;

use App\Actions\Administration\DeleteUserAction;
use App\Actions\Administration\ListUsersAction;
use App\DTOs\Administration\UserFiltersDTO;
use App\DTOs\PageMeta;
use App\Http\Requests\Administration\UserIndexRequest;
use App\Http\Resources\Administration\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

final class UserController
{
    /**
     * List the users, optionally filtered and sorted.
     */
    public function index(UserIndexRequest $request, ListUsersAction $action): Response
    {
        $filters = UserFiltersDTO::from($request);

        return vue('administration/users/Index', [
            'users' => UserResource::collection($action->handle($filters))->toInertia(),
            'filters' => $filters->toArray(),
        ], new PageMeta(
            title: 'Users',
        ));
    }

    /**
     * Delete the user.
     */
    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        Gate::authorize('deleteUser', $user);

        $action->handle($user);

        toastSuccess("{$user->name} was deleted.");

        return back();
    }
}
