<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\DTOs\Administration\UserStatisticsDTO;
use App\Models\User;

final readonly class GetUserStatisticsAction
{
    /**
     * Count the users, administrators, unverified users and users who joined in the last 7 days.
     */
    public function handle(): UserStatisticsDTO
    {
        /** @var object{total: int|string, administrators: int|string|null, unverified: int|string|null, joined_this_week: int|string|null} $counts */
        $counts = User::query()->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when is_admin = ? then 1 else 0 end) as administrators', [true])
            ->selectRaw('sum(case when email_verified_at is null then 1 else 0 end) as unverified')
            ->selectRaw('sum(case when created_at >= ? then 1 else 0 end) as joined_this_week', [now()->subDays(7)])
            ->first();

        return new UserStatisticsDTO(
            total: (int) $counts->total,
            administrators: (int) $counts->administrators,
            unverified: (int) $counts->unverified,
            joinedThisWeek: (int) $counts->joined_this_week,
        );
    }
}
