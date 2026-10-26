<?php

declare(strict_types=1);

namespace Tests\Unit\DTO;

use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

final readonly class AsDTOUserDTO
{
    use AsDTO;

    public function __construct(
        public string $name,
        public int $age,
        public bool $active = true,
        public ?AsDTOAddressDTO $address = null,
    ) {}
}
