<?php

declare(strict_types=1);

namespace App\Toast;

use App\Toast\DTOs\ToastPayload;
use Inertia\Support\SessionKey;

/**
 * Queues toasts as Inertia flash data, delivered once with the next response.
 */
final class Toast
{
    public const string KEY = 'toasts';

    private const int MAX_QUEUE_ITEMS = 5;

    /**
     * Retrieve the queued toasts and remove them from the flash data.
     *
     * @return array<int, array<string, mixed>>
     */
    public function pull(): array
    {
        $flashed = $this->flashed();
        $toasts = $this->queue($flashed);

        unset($flashed[self::KEY]);
        session()->flash(SessionKey::FLASH_DATA, $flashed);

        return $toasts;
    }

    public function append(ToastPayload $newToast): void
    {
        $flashed = $this->flashed();
        $toasts = $this->queue($flashed);

        $serializedToast = $newToast->toArray();
        $latestToast = $toasts[count($toasts) - 1] ?? null;

        if ($latestToast === $serializedToast) {
            return;
        }

        $toasts[] = $serializedToast;

        if (count($toasts) > self::MAX_QUEUE_ITEMS) {
            $toasts = array_slice($toasts, -self::MAX_QUEUE_ITEMS);
        }

        session()->flash(SessionKey::FLASH_DATA, [...$flashed, self::KEY => $toasts]);
    }

    /**
     * Read the pending Inertia flash data from the session.
     *
     * @return array<string, mixed>
     */
    private function flashed(): array
    {
        /** @var array<string, mixed> $flashed */
        $flashed = session()->get(SessionKey::FLASH_DATA, []);

        return $flashed;
    }

    /**
     * @param  array<string, mixed>  $flashed
     * @return array<int, array<string, mixed>>
     */
    private function queue(array $flashed): array
    {
        /** @var array<int, array<string, mixed>> $toasts */
        $toasts = $flashed[self::KEY] ?? [];

        return array_values($toasts);
    }
}
