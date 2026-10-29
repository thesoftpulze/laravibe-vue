<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration\Settings;

use App\Actions\Administration\UpdateMailSettingsAction;
use App\DTOs\Administration\MailSettingsDTO;
use App\DTOs\PageMeta;
use App\Http\Requests\Administration\Settings\MailSettingsUpdateRequest;
use App\Settings\MailSettings;
use App\Settings\SettingsRepository;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class MailSettingsController
{
    /**
     * Show the mail settings form, with the `.env` values that apply while a field is empty.
     */
    public function edit(MailSettings $settings, SettingsRepository $repository): Response
    {
        return vue('administration/settings/Mail', [
            'settings' => $settings->toArray(),
            'defaults' => [
                'fromName' => $repository->originalConfig('mail.from.name'),
                'fromAddress' => $repository->originalConfig('mail.from.address'),
            ],
            'mailer' => config()->string('mail.default'),
        ], new PageMeta(
            title: 'Mail settings',
        ));
    }

    /**
     * Save the mail settings.
     */
    public function update(MailSettingsUpdateRequest $request, UpdateMailSettingsAction $action): RedirectResponse
    {
        $action->handle(MailSettingsDTO::from($request));

        toastSuccess('Settings saved.');

        return back();
    }
}
