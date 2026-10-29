<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration\Settings;

use App\Actions\Administration\SendTestEmailAction;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class TestEmailController
{
    /**
     * Send a test email to the signed-in administrator.
     */
    public function store(SendTestEmailAction $action): RedirectResponse
    {
        $user = authUser();

        try {
            $action->handle($user);
        } catch (TransportExceptionInterface $exception) {
            toastError('The test email could not be sent.')->description($exception->getMessage());

            return back();
        }

        toastSuccess("Test email sent to {$user->email}.");

        return back();
    }
}
