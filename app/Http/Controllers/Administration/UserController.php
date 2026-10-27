<?php

declare(strict_types=1);

namespace App\Http\Controllers\Administration;

use App\DTOs\PageMeta;
use App\Http\Resources\Administration\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Response;

final class UserController
{
    /**
     * List the users, optionally filtered by a name or email search.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->limit(100, '')->toString();

        $users = User::query()
            ->select(['id', 'name', 'email', 'is_admin', 'email_verified_at', 'created_at'])
            ->when($search !== '', fn (Builder $query): Builder => $query->whereAny(['name', 'email'], 'like', "%{$search}%"))
            ->latest()
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return vue('administration/users/Index', [
            'users' => UserResource::collection($users)->toInertia(),
            'filters' => ['search' => $search],
        ], new PageMeta(
            title: 'Users',
        ));
    }
}
