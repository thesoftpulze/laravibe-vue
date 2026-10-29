<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\DTOs\Administration\GeneralSettingsDTO;
use App\Settings\GeneralSettings;
use App\Settings\SettingsRepository;

final readonly class UpdateGeneralSettingsAction
{
    public function __construct(
        private SettingsRepository $settings,
        private GeneralSettings $general,
    ) {}

    /**
     * Save the general settings.
     */
    public function handle(GeneralSettingsDTO $data): void
    {
        $this->settings->save((clone $this->general)->fill($data->toArray()));
    }
}
