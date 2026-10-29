<?php

declare(strict_types=1);

namespace App\Settings;

/**
 * How the application presents itself.
 */
final class GeneralSettings extends Settings implements OverridesConfig
{
    /**
     * The name shown in the header, page titles and emails. Null keeps `APP_NAME`.
     */
    public ?string $siteName = null;

    /**
     * A short line describing the application.
     */
    public ?string $tagline = null;

    /**
     * Where people can reach the team behind the application.
     */
    public ?string $supportEmail = null;

    public static function group(): string
    {
        return 'general';
    }

    public function config(): array
    {
        return ['app.name' => $this->siteName];
    }
}
