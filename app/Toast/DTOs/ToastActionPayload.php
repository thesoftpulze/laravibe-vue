<?php

declare(strict_types=1);

namespace App\Toast\DTOs;

use App\Enums\ToastActionType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @implements Arrayable<string, mixed>
 */
final readonly class ToastActionPayload implements Arrayable, Jsonable
{
    use AsDTO;

    /**
     * Create a new class instance.
     */
    public function __construct(
        public ToastActionType $type,
        public string $payload,
        public ?string $label = null,
    ) {}

    public static function copy(string $payloadToCopy, ?string $label = null): self
    {
        return new self(ToastActionType::COPY, $payloadToCopy, $label);
    }

    /**
     * Open the URL in a new tab.
     */
    public static function redirect(string $redirectURL, ?string $label = null): self
    {
        return new self(ToastActionType::REDIRECT, $redirectURL, $label);
    }

    /**
     * Visit the URL in the same tab with an Inertia visit.
     */
    public static function visit(string $url, ?string $label = null): self
    {
        return new self(ToastActionType::VISIT, $url, $label);
    }

    protected static function shouldThrowOnUnknownKeys(): bool
    {
        return true;
    }
}
