<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration;

use App\Actions\Administration\UpdateUserRoleAction;
use App\Http\Requests\Administration\UserRoleUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final class UserRoleController
{
    /**
     * Grant or revoke the user's administrator role.
     */
    public function update(UserRoleUpdateRequest $request, User $user, UpdateUserRoleAction $action): RedirectResponse
    {
        $action->handle($user, $request->safe()->boolean('is_admin'));

        toastSuccess($user->is_admin ? "{$user->name} is now an administrator." : "{$user->name} is no longer an administrator.");

        return back();
    }
}
