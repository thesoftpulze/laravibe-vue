<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum ToastType: string
{
    use HasEnumMetadata;

    case ERROR = 'error';
    case SUCCESS = 'success';
    case WARNING = 'warning';
    case INFO = 'info';
}
