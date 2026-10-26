<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Laravel\Passkeys\Http\Responses\PasskeyRegistrationResponse as BasePasskeyRegistrationResponse;
use Override;
use Symfony\Component\HttpFoundation\Response;

/**
 * Queue a toast, then respond as the passkeys package does.
 */
final class PasskeyRegistrationResponse extends BasePasskeyRegistrationResponse
{
    #[Override]
    public function toResponse($request): Response
    {
        toastSuccess(__('Passkey added.'));

        return parent::toResponse($request);
    }
}
