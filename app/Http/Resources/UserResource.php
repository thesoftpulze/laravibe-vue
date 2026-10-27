<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            $this->optionalAttribute(
                'avatar_path',
                resolver: fn (?string $path): ?string => $path === null ? null : Storage::disk('public')->url($path),
                alias: 'avatar',
            ),

            $this->optionalDateTimeAttributes('email_verified_at'),
        ];
    }
}
