<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\DTOs\Administration\AuthenticationSettingsDTO;
use App\Settings\AuthenticationSettings;
use App\Settings\SettingsRepository;

final readonly class UpdateAuthenticationSettingsAction
{
    public function __construct(
        private SettingsRepository $settings,
        private AuthenticationSettings $authentication,
    ) {}

    /**
     * Save the authentication settings.
     */
    public function handle(AuthenticationSettingsDTO $data): void
    {
        $this->settings->save((clone $this->authentication)->fill($data->toArray()));
    }
}
