<?php

declare(strict_types=1);

namespace Tests\Unit\DTO;

use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

final readonly class AsDTOStrictDTO
{
    use AsDTO;

    public function __construct(
        public string $title,
    ) {}

    protected static function shouldThrowOnUnknownKeys(): bool
    {
        return true;
    }
}
