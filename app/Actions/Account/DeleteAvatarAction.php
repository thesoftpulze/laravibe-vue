<?php

declare(strict_types=1);

namespace App\Actions\Account;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

final readonly class DeleteAvatarAction
{
    /**
     * Remove the user's profile picture.
     */
    public function handle(User $user): void
    {
        if ($user->avatar_path === null) {
            return;
        }

        Storage::disk('public')->delete($user->avatar_path);

        $user->avatar_path = null;
        $user->save();
    }
}
