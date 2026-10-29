<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum SortDirection: string
{
    use HasEnumMetadata;

    case ASC = 'asc';
    case DESC = 'desc';
}
