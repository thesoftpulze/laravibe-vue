<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum UserVerificationFilter: string
{
    use HasEnumMetadata;

    case VERIFIED = 'verified';
    case UNVERIFIED = 'unverified';
}
