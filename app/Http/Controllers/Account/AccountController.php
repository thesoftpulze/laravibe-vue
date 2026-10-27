<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\DTOs\PageMeta;
use App\Http\Requests\Account\ProfileDeleteRequest;
use App\Http\Requests\Account\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class AccountController
{
    /**
     * Show an overview of the user's account.
     */
    public function show(): Response
    {
        $user = authUser();

        return vue('account/Overview', [
            'twoFactorEnabled' => $user->hasEnabledTwoFactorAuthentication(),
            'passkeyCount' => $user->passkeys()->count(),
        ], new PageMeta(
            title: 'Account',
        ));
    }

    public function edit(Request $request): Response
    {
        return vue('account/Profile', [
            // @phpstan-ignore-next-line instanceof.alwaysTrue
            'mustVerifyEmail' => authUser() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
        ], new PageMeta(
            title: 'Profile',
        ));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $authUser = authUser();
        $authUser->fill($request->validated());

        $emailChanged = $authUser->isDirty('email');

        if ($emailChanged) {
            $authUser->email_verified_at = null;
        }

        $authUser->save();

        if ($emailChanged) {
            $authUser->sendEmailVerificationNotification();
        }

        toastSuccess('Profile updated successfully.');

        return to_route('account.profile');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = authUser();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        toastSuccess('Account deleted successfully.');

        return redirect('/');
    }
}
