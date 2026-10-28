<?php

declare(strict_types=1);

namespace App\Toast;

use App\Enums\ToastType;
use App\Toast\DTOs\ToastActionPayload;
use App\Toast\DTOs\ToastPayload;

/**
 * Builds a toast step by step and queues it when the builder goes out of scope,
 * like Laravel's PendingDispatch. Call send() to queue it straight away.
 */
final class PendingToast
{
    private ?string $description = null;

    /** @var array<int, ToastActionPayload> */
    private array $actions = [];

    private ?int $duration = null;

    private ?bool $dismissible = null;

    private bool $isSent = false;

    public function __construct(
        private readonly Toast $toast,
        private readonly ToastType $type,
        private readonly string $message,
    ) {}

    public function __destruct()
    {
        $this->send();
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function action(ToastActionPayload ...$actions): self
    {
        $this->actions = [...$this->actions, ...array_values($actions)];

        return $this;
    }

    /**
     * Set how long the toast stays open, in milliseconds.
     */
    public function duration(int $milliseconds): self
    {
        $this->duration = max(0, $milliseconds);

        return $this;
    }

    /**
     * Keep the toast open until it is closed.
     */
    public function persistent(): self
    {
        return $this->duration(0);
    }

    /**
     * Choose whether the user can close the toast before it times out.
     */
    public function dismissible(bool $dismissible = true): self
    {
        $this->dismissible = $dismissible;

        return $this;
    }

    public function toPayload(): ToastPayload
    {
        return new ToastPayload(
            type: $this->type,
            message: $this->message,
            actions: $this->actions !== [] ? $this->actions : null,
            duration: $this->duration,
            dismissible: $this->dismissible,
            description: $this->description,
        );
    }

    /**
     * Queue the toast now. Later calls do nothing.
     */
    public function send(): void
    {
        if ($this->isSent) {
            return;
        }

        $this->isSent = true;
        $this->toast->append($this->toPayload());
    }
}
