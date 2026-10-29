<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\Mail\TestEmail;
use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;

final readonly class SendTestEmailAction
{
    public function __construct(
        private Mailer $mailer,
    ) {}

    /**
     * Send a test email straight away, so a delivery failure surfaces to the sender.
     */
    public function handle(User $recipient): void
    {
        $this->mailer->to($recipient)->send(new TestEmail());
    }
}
