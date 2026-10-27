<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $this->get(route('administration.users.index'))
        ->assertRedirect(route('login'));
});

test('non-administrators are forbidden', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('administration.users.index'))
        ->assertForbidden();
});

test('administrators see a paginated list of users', function () {
    $admin = User::factory()->admin()->create();
    User::factory(20)->create();

    $this->actingAs($admin)
        ->get(route('administration.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('administration/users/Index')
            ->has('users.data', 15)
            ->has('users.data.0', fn (Assert $user) => $user
                ->hasAll(['id', 'name', 'email', 'is_admin', 'email_verified_at', 'created_at'])
            )
            ->where('users.meta.total', 21)
            ->where('filters.search', '')
        );
});

test('the list does not expose sensitive user fields', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('administration.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->missing('users.data.0.password')
            ->missing('users.data.0.remember_token')
        );
});

test('administrators can search users by name or email', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@example.com']);
    User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);
    User::factory()->create(['name' => 'John Smith', 'email' => 'jsmith@example.org']);

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['search' => 'JANE']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.name', 'Jane Doe')
            ->where('filters.search', 'JANE')
        );

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['search' => 'example.org']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.email', 'jsmith@example.org')
        );
});

test('pagination links keep the search query', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin']);
    User::factory(20)->create(['name' => 'Matching User']);

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['search' => 'matching']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('users.meta.total', 20)
            ->where('users.links.next', fn (string $url) => str_contains($url, 'search=matching'))
        );
});
