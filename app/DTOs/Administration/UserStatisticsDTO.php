<?php

declare(strict_types=1);

namespace App\DTOs\Administration;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class UserStatisticsDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public int $total,
        public int $administrators,
        public int $unverified,
        public int $joinedThisWeek,
    ) {}
}
