<?php

declare(strict_types=1);

namespace Tests\Unit\Enum;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum BackedMetaEnum: string
{
    use HasEnumMetadata;

    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
}
