<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Actions\Account\DeleteAvatarAction;
use App\Actions\Account\UpdateAvatarAction;
use App\Http\Requests\Account\AvatarUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;

final class AvatarController
{
    /**
     * Replace the user's profile picture.
     */
    public function update(AvatarUpdateRequest $request, UpdateAvatarAction $action): RedirectResponse
    {
        /** @var UploadedFile $avatar */
        $avatar = $request->file('avatar');

        $action->handle(authUser(), $avatar);

        toastSuccess('Profile picture updated successfully.');

        return to_route('account.profile');
    }

    /**
     * Remove the user's profile picture.
     */
    public function destroy(DeleteAvatarAction $action): RedirectResponse
    {
        Gate::authorize('updateProfile');

        $action->handle(authUser());

        toastSuccess('Profile picture removed.');

        return to_route('account.profile');
    }
}
