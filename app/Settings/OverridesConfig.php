<?php

declare(strict_types=1);

namespace App\Settings;

/**
 * A settings group whose values replace config values, so code that reads config follows the settings.
 */
interface OverridesConfig
{
    /**
     * The config values this group overrides, keyed by config key. A null value keeps the value from config.
     *
     * @return array<string, mixed>
     */
    public function config(): array;
}
