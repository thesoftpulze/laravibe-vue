<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\Models\User;

final readonly class UpdateUserRoleAction
{
    /**
     * Grant or revoke the user's administrator role.
     */
    public function handle(User $user, bool $isAdmin): User
    {
        $user->is_admin = $isAdmin;
        $user->save();

        return $user;
    }
}
