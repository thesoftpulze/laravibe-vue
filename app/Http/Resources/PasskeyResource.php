<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Support\CarbonImmutable;
use Illuminate\Http\Request;
use Laravel\Passkeys\Passkey;
use Override;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;

/**
 * @mixin Passkey
 */
final class PasskeyResource extends AppResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    #[Override]
    public function toArray(Request $request): array
    {
        return [
            $this->id(),

            $this->attribute('name'),
            $this->attribute('authenticator'),

            $this->attribute('last_used_at', resolver: fn (?CarbonImmutable $lastUsedAt): ?string => $lastUsedAt?->diffForHumans()),
            $this->attribute('created_at', resolver: fn (CarbonImmutable $createdAt): string => $createdAt->toStringDate()),
        ];
    }
}
