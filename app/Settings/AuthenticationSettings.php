<?php

declare(strict_types=1);

namespace App\Settings;

/**
 * How people sign up and get access.
 */
final class AuthenticationSettings extends Settings
{
    /**
     * Whether visitors can create an account. Existing users can always log in.
     */
    public bool $allowRegistration = true;

    /**
     * Whether routes behind the `verified` middleware need a verified email address.
     */
    public bool $requireEmailVerification = true;

    public static function group(): string
    {
        return 'authentication';
    }
}
