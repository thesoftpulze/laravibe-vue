<?php

declare(strict_types=1);

namespace App\Settings;

/**
 * Who the application's emails come from.
 */
final class MailSettings extends Settings implements OverridesConfig
{
    /**
     * The sender name. Null keeps `MAIL_FROM_NAME`.
     */
    public ?string $fromName = null;

    /**
     * The sender address. Null keeps `MAIL_FROM_ADDRESS`.
     */
    public ?string $fromAddress = null;

    public static function group(): string
    {
        return 'mail';
    }

    public function config(): array
    {
        return [
            'mail.from.name' => $this->fromName,
            'mail.from.address' => $this->fromAddress,
        ];
    }
}
