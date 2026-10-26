<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class BreadCrumb implements Arrayable, Jsonable
{
    use AsDTO;

    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $label,
        public ?string $href = null,
    ) {
        // ...
    }
}
