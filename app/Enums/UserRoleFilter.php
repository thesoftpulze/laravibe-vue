<?php

declare(strict_types=1);

namespace App\Enums;

use TheSoftPulze\LaravibeStandards\Enums\Concerns\HasEnumMetadata;

enum UserRoleFilter: string
{
    use HasEnumMetadata;

    case ADMINISTRATOR = 'admin';
    case MEMBER = 'user';
}
