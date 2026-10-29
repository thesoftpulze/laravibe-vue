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
                ->hasAll(['id', 'name', 'email', 'avatar', 'is_admin', 'email_verified_at', 'created_at', 'can'])
            )
            ->where('users.meta.total', 21)
            ->where('filters', ['search' => null, 'role' => null, 'verification' => null, 'sort' => null, 'direction' => null])
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

test('administrators can filter users by role and verification', function () {
    $admin = User::factory()->admin()->create(['name' => 'Admin']);
    User::factory()->unverified()->create(['name' => 'Unverified Member']);
    User::factory()->create(['name' => 'Verified Member']);

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['role' => 'admin']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.name', 'Admin')
            ->where('filters.role', 'admin')
        );

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['role' => 'user', 'verification' => 'unverified']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.name', 'Unverified Member')
        );

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['verification' => 'verified']))
        ->assertInertia(fn (Assert $page) => $page->where('users.meta.total', 2));
});

test('administrators can sort users by name', function () {
    $admin = User::factory()->admin()->create(['name' => 'Bob']);
    User::factory()->create(['name' => 'Alice']);
    User::factory()->create(['name' => 'Carol']);

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['sort' => 'name', 'direction' => 'asc']))
        ->assertInertia(fn (Assert $page) => $page
            ->where('users.data.0.name', 'Alice')
            ->where('users.data.2.name', 'Carol')
            ->where('filters.sort', 'name')
            ->where('filters.direction', 'asc')
        );

    $this->actingAs($admin)
        ->get(route('administration.users.index', ['sort' => 'name', 'direction' => 'desc']))
        ->assertInertia(fn (Assert $page) => $page->where('users.data.0.name', 'Carol'));
});

test('the list rejects filter and sort values it does not support', function (string $key, string $value) {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('administration.users.index'))
        ->get(route('administration.users.index', [$key => $value]))
        ->assertRedirect(route('administration.users.index'))
        ->assertSessionHasErrors($key);
})->with([
    'sort by a column that is not offered' => ['sort', 'password'],
    'sort by an injected expression' => ['sort', 'name; drop table users'],
    'unknown direction' => ['direction', 'sideways'],
    'unknown role' => ['role', 'owner'],
    'unknown verification' => ['verification', 'pending'],
]);

test('each user carries what the administrator may do to them', function () {
    $admin = User::factory()->admin()->create(['created_at' => now()]);
    User::factory()->unverified()->create(['created_at' => now()->subDay()]);

    $this->actingAs($admin)
        ->get(route('administration.users.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('users.data.0.can', ['updateRole' => false, 'resendVerification' => false, 'delete' => false])
            ->where('users.data.1.can', ['updateRole' => true, 'resendVerification' => true, 'delete' => true])
        );
});
