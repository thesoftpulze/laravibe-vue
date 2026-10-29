<?php

declare(strict_types=1);

namespace App\DTOs\Administration;

use App\Enums\SortDirection;
use App\Enums\UserRoleFilter;
use App\Enums\UserSortColumn;
use App\Enums\UserVerificationFilter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use TheSoftPulze\LaravibeStandards\DTOs\Concerns\AsDTO;

/**
 * The filters and sort order of the administration users list. A null value means the default.
 *
 * @implements Arrayable<string, mixed>
 */
final readonly class UserFiltersDTO implements Arrayable, Jsonable
{
    use AsDTO;

    public function __construct(
        public ?string $search = null,
        public ?UserRoleFilter $role = null,
        public ?UserVerificationFilter $verification = null,
        public ?UserSortColumn $sort = null,
        public ?SortDirection $direction = null,
    ) {}
}
