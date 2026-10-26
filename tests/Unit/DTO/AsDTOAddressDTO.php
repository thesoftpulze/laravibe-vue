<?php

declare(strict_types=1);

namespace Tests\Unit\DTO;

use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

final readonly class AsDTOAddressDTO
{
    use AsDTO;

    public function __construct(
        public string $lineOne,
        public string $city,
    ) {}
}
