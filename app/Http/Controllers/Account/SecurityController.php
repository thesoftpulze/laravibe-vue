<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\DTOs\PageMeta;
use App\Http\Requests\Account\PasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

final class SecurityController
{
    public function edit(): Response
    {
        return vue('account/Security', metaProps: new PageMeta(title: 'Security'));
    }

    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        $password = $request->string('password')->value();

        authUser()->update(['password' => $password]);

        Auth::logoutOtherDevices($password);

        toastSuccess('Password updated successfully.');

        return back();
    }
}
