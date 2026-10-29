<?php

declare(strict_types=1);

namespace App\DTOs\Administration;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * The general settings an administrator submits. A null value clears the setting.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class GeneralSettingsDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public ?string $siteName = null,
        public ?string $tagline = null,
        public ?string $supportEmail = null,
    ) {}
}
