<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum UserSortColumn: string
{
    use HasEnumMetadata;

    case NAME = 'name';
    case CREATED_AT = 'created_at';
}
