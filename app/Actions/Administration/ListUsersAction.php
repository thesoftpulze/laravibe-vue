<?php

declare(strict_types=1);

namespace App\Actions\Administration;

use App\DTOs\Administration\UserFiltersDTO;
use App\Enums\SortDirection;
use App\Enums\UserRoleFilter;
use App\Enums\UserSortColumn;
use App\Enums\UserVerificationFilter;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class ListUsersAction
{
    /**
     * List the users matching the filters, newest first unless another order is asked for.
     *
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(UserFiltersDTO $filters): LengthAwarePaginator
    {
        $sort = $filters->sort ?? UserSortColumn::CREATED_AT;
        $direction = $filters->direction ?? SortDirection::DESC;

        return User::query()
            ->select(['id', 'name', 'email', 'avatar_path', 'is_admin', 'email_verified_at', 'created_at'])
            ->when($filters->search, fn (Builder $query, string $search): Builder => $query->where(
                fn (Builder $query): Builder => $query->whereLike('name', "%{$search}%")->orWhereLike('email', "%{$search}%"),
            ))
            ->when($filters->role, fn (Builder $query, UserRoleFilter $role): Builder => $query->where(
                'is_admin', $role === UserRoleFilter::ADMINISTRATOR,
            ))
            ->when($filters->verification, fn (Builder $query, UserVerificationFilter $verification): Builder => $verification === UserVerificationFilter::VERIFIED
                ? $query->whereNotNull('email_verified_at')
                : $query->whereNull('email_verified_at'))
            ->orderBy($sort->value, $direction->value)
            ->orderBy('id', $direction->value)
            ->paginate(15)
            ->withQueryString();
    }
}
