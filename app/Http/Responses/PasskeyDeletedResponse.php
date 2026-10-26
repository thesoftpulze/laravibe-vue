<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Laravel\Passkeys\Http\Responses\PasskeyDeletedResponse as BasePasskeyDeletedResponse;
use Override;
use Symfony\Component\HttpFoundation\Response;

/**
 * Queue a toast, then respond as the passkeys package does.
 */
final class PasskeyDeletedResponse extends BasePasskeyDeletedResponse
{
    #[Override]
    public function toResponse($request): Response
    {
        toastSuccess(__('Passkey removed.'));

        return parent::toResponse($request);
    }
}
