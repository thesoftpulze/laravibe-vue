<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\DTOs\Administration\MailSettingsDTO;
use App\Settings\MailSettings;
use App\Settings\SettingsRepository;

final readonly class UpdateMailSettingsAction
{
    public function __construct(
        private SettingsRepository $settings,
        private MailSettings $mail,
    ) {}

    /**
     * Save the mail settings.
     */
    public function handle(MailSettingsDTO $data): void
    {
        $this->settings->save((clone $this->mail)->fill($data->toArray()));
    }
}
