<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum ToastActionType: string
{
    use HasEnumMetadata;

    case COPY = 'copy';
    case REDIRECT = 'redirect';
}
