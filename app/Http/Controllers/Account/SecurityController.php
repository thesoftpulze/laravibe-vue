<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Actions\Account\ListBrowserSessionsAction;
use App\Actions\Account\LogoutOtherBrowserSessionsAction;
use App\DTOs\PageMeta;
use App\Http\Requests\Account\LogoutOtherSessionsRequest;
use App\Http\Requests\Account\PasswordUpdateRequest;
use App\Http\Resources\PasskeyResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class SecurityController
{
    public function edit(Request $request, ListBrowserSessionsAction $listBrowserSessions): Response
    {
        $user = authUser();

        return vue('account/Security', [
            'twoFactor' => [
                'enabled' => $user->hasEnabledTwoFactorAuthentication(),
                'pendingConfirmation' => $user->two_factor_secret !== null && $user->two_factor_confirmed_at === null,
            ],
            'passkeys' => PasskeyResource::collection($user->passkeys()->latest()->get())->toInertia(),
            'sessions' => $listBrowserSessions->handle($user, $request->session()->getId()),
        ], new PageMeta(title: 'Security'));
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $password = $request->string('password')->value();

        authUser()->update(['password' => $password]);

        Auth::logoutOtherDevices($password);

        toastSuccess('Password updated successfully.');

        return back();
    }

    public function destroyOtherSessions(LogoutOtherSessionsRequest $request, LogoutOtherBrowserSessionsAction $logoutOtherBrowserSessions): RedirectResponse
    {
        $logoutOtherBrowserSessions->handle(
            authUser(),
            $request->string('password')->value(),
            $request->session()->getId(),
        );

        toastSuccess('Logged out of your other browser sessions.');

        return back();
    }
}
