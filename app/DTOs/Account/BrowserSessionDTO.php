<?php

declare(strict_types=1);

namespace App\DTOs\Account;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class BrowserSessionDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public string $platform,
        public string $browser,
        public bool $isDesktop,
        public bool $isCurrentDevice,
        public string $lastActive,
        public ?string $ipAddress = null,
    ) {}
}
