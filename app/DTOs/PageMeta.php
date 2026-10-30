<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Collection;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class PageMeta implements Arrayable, Jsonable
{
    use AsDTO;

    /**
     * Create a new class instance.
     *
     * @param  Collection<int, BreadCrumb>|null  $breadcrumbs
     */
    public function __construct(
        public ?string $heading = null,
        public ?string $subheading = null,
        public ?string $title = null,
        public ?Collection $breadcrumbs = null,
    ) {}

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'heading' => $this->heading,
            'subheading' => $this->subheading,

            'title' => $this->title,

            'breadcrumbs' => $this->breadcrumbs?->toArray(),
        ]);
    }
}
