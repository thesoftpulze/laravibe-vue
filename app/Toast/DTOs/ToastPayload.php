<?php

declare(strict_types=1);

namespace App\Toast\DTOs;

use App\Enums\ToastType;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use InvalidArgumentException;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * @phpstan-type ToastItemsShape array<int, array<string, mixed>>
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class ToastPayload implements Arrayable, Jsonable
{
    use AsDTO;

    /**
     * Create a new class instance.
     */
    public function __construct(
        public ToastType $type,
        public string $message,
        /** @var array<int, ToastActionPayload>|null */
        public ?array $actions = null,
        public ?int $duration = null,
        public ?bool $dismissible = null,
    ) {
        // ...
    }

    public static function error(string $message, ToastActionPayload ...$actions): void
    {
        toast()->append(new self(ToastType::ERROR, $message, self::ensureActionsOrNull($actions)));
    }

    public static function success(string $message, ToastActionPayload ...$actions): void
    {
        toast()->append(new self(ToastType::SUCCESS, $message, self::ensureActionsOrNull($actions)));
    }

    public static function warning(string $message, ToastActionPayload ...$actions): void
    {
        toast()->append(new self(ToastType::WARNING, $message, self::ensureActionsOrNull($actions)));
    }

    public static function info(string $message, ToastActionPayload ...$actions): void
    {
        toast()->append(new self(ToastType::INFO, $message, self::ensureActionsOrNull($actions)));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): static
    {
        if (self::shouldThrowOnUnknownKeys()) {
            $unknownKeys = array_diff_key($data, ['type' => true, 'message' => true, 'actions' => true, 'duration' => true, 'dismissible' => true]);

            if ($unknownKeys !== []) {
                $keys = implode(', ', array_keys($unknownKeys));

                throw new InvalidArgumentException('Unknown properties for ' . self::class . ': ' . $keys . '.');
            }
        }

        if (! array_key_exists('type', $data)) {
            throw new InvalidArgumentException('Missing required property [type] for ' . self::class . '.');
        }

        if (! array_key_exists('message', $data)) {
            throw new InvalidArgumentException('Missing required property [message] for ' . self::class . '.');
        }

        $type = match (true) {
            $data['type'] instanceof ToastType => $data['type'],
            is_string($data['type']) => ToastType::fromValueOrFail($data['type']),
            default => throw new InvalidArgumentException('Invalid type for property [type] on ' . self::class . '. Expected [' . ToastType::class . '|string], got [' . gettype($data['type']) . '].'),
        };

        $message = match (true) {
            is_string($data['message']) => $data['message'],
            is_int($data['message']), is_float($data['message']), is_bool($data['message']) => (string) $data['message'],
            default => throw new InvalidArgumentException('Invalid type for property [message] on ' . self::class . '. Expected [string], got [' . gettype($data['message']) . '].'),
        };

        /** @var array<int, ToastActionPayload>|null $actions */
        $actions = null;

        if (array_key_exists('actions', $data) && $data['actions'] !== null) {
            if (! is_array($data['actions'])) {
                throw new InvalidArgumentException('Invalid type for property [actions] on ' . self::class . '. Expected [array], got [' . gettype($data['actions']) . '].');
            }

            $hydratedActions = [];

            foreach ($data['actions'] as $action) {
                if ($action instanceof ToastActionPayload) {
                    $hydratedActions[] = $action;

                    continue;
                }

                if (! is_array($action)) {
                    throw new InvalidArgumentException('Invalid action payload for [' . self::class . '].');
                }

                /** @var array<string, mixed> $action */
                $hydratedActions[] = ToastActionPayload::fromArray($action);
            }

            $actions = self::ensureActionsOrNull($hydratedActions);
        }

        $duration = null;

        if (array_key_exists('duration', $data) && $data['duration'] !== null) {
            if (is_int($data['duration'])) {
                $duration = $data['duration'];
            } elseif (is_string($data['duration']) && filter_var($data['duration'], FILTER_VALIDATE_INT) !== false) {
                $duration = (int) $data['duration'];
            } else {
                throw new InvalidArgumentException('Invalid type for property [duration] on ' . self::class . '. Expected [int], got [' . gettype($data['duration']) . '].');
            }
        }

        $dismissible = null;

        if (array_key_exists('dismissible', $data) && $data['dismissible'] !== null) {
            if (is_bool($data['dismissible'])) {
                $dismissible = $data['dismissible'];
            } elseif (is_string($data['dismissible']) || is_int($data['dismissible'])) {
                $bool = filter_var($data['dismissible'], FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

                if ($bool === null) {
                    throw new InvalidArgumentException('Invalid type for property [dismissible] on ' . self::class . '. Expected [bool], got [' . gettype($data['dismissible']) . '].');
                }

                $dismissible = $bool;
            } else {
                throw new InvalidArgumentException('Invalid type for property [dismissible] on ' . self::class . '. Expected [bool], got [' . gettype($data['dismissible']) . '].');
            }
        }

        return new self($type, $message, $actions, $duration, $dismissible);
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $toastArray = [
            'type' => $this->type->value,
            'message' => $this->message,
        ];

        if (is_array($this->actions) && count($this->actions) > 0) {
            $toastArray['actions'] = array_map(fn (ToastActionPayload $action): array => $action->toArray(), $this->actions);
        }

        if ($this->duration !== null) {
            $toastArray['duration'] = $this->duration;
        }

        if ($this->dismissible !== null) {
            $toastArray['dismissible'] = $this->dismissible;
        }

        return $toastArray;
    }

    protected static function shouldThrowOnUnknownKeys(): bool
    {
        return true;
    }

    /**
     * @param  array<int|string, ToastActionPayload>  $actions
     * @return array<int, ToastActionPayload>|null
     */
    private static function ensureActionsOrNull(array $actions): ?array
    {
        return count($actions) !== 0 ? array_values($actions) : null;
    }
}
