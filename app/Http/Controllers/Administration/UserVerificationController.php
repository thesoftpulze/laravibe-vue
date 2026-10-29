<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class UserVerificationController
{
    /**
     * Send the user a new email verification link.
     */
    public function store(User $user): RedirectResponse
    {
        Gate::authorize('resendUserVerification', $user);

        $user->sendEmailVerificationNotification();

        toastSuccess("Verification email sent to {$user->email}.");

        return back();
    }
}
