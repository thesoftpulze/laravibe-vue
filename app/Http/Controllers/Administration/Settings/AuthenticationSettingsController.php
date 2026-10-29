<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration\Settings;

use App\Actions\Administration\UpdateAuthenticationSettingsAction;
use App\DTOs\Administration\AuthenticationSettingsDTO;
use App\DTOs\PageMeta;
use App\Http\Requests\Administration\Settings\AuthenticationSettingsUpdateRequest;
use App\Settings\AuthenticationSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class AuthenticationSettingsController
{
    /**
     * Show the authentication settings form.
     */
    public function edit(AuthenticationSettings $settings): Response
    {
        return vue('administration/settings/Authentication', [
            'settings' => $settings->toArray(),
        ], new PageMeta(
            title: 'Authentication settings',
        ));
    }

    /**
     * Save the authentication settings.
     */
    public function update(AuthenticationSettingsUpdateRequest $request, UpdateAuthenticationSettingsAction $action): RedirectResponse
    {
        $action->handle(AuthenticationSettingsDTO::from($request));

        toastSuccess('Settings saved.');

        return back();
    }
}
