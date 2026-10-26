<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\TwoFactorConfirmedResponse as TwoFactorConfirmedResponseContract;
use Laravel\Fortify\Fortify;
use Override;

/**
 * Confirm the change with a toast as well as Fortify's session status.
 */
final class TwoFactorConfirmedResponse implements TwoFactorConfirmedResponseContract
{
    /**
     * @param  Request  $request
     */
    #[Override]
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 200);
        }

        toastSuccess(__('Two-factor authentication enabled.'));

        return back()->with('status', Fortify::TWO_FACTOR_AUTHENTICATION_CONFIRMED);
    }
}
