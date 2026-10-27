<?php

declare(strict_types=1);

namespace App\Http\Resources\Administration;

use App\Models\User;
use Illuminate\Http\Request;
use Override;
use TheSoftPulze\LaravibeStandards\Resources\AppResource;

/**
 * @mixin User
 */
final class UserResource extends AppResource
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
            $this->attribute('email'),
            $this->attribute('is_admin'),

            $this->optionalDateTimeAttributes('email_verified_at'),
            $this->createdAt(),
        ];
    }
}
