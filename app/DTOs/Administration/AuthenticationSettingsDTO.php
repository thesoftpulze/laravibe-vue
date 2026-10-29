<?php

declare(strict_types=1);

namespace App\DTOs\Administration;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * The authentication settings an administrator submits.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class AuthenticationSettingsDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public bool $allowRegistration,
        public bool $requireEmailVerification,
    ) {}
}
