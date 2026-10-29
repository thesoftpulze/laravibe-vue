<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('administration.dashboard'));
    $response->assertRedirect(route('login'));
});

test('administrators can visit the dashboard', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $response = $this->get(route('administration.dashboard'));
    $response->assertOk();
});

test('non-administrators are forbidden from the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('administration.dashboard'));
    $response->assertForbidden();
});

test('the dashboard defers the user statistics and the latest sign-ups', function () {
    $this->freezeTime();

    $admin = User::factory()->admin()->create(['created_at' => now()->subMonth()]);
    User::factory()->unverified()->create(['created_at' => now()->subDays(8)]);
    User::factory()->create(['created_at' => now()->subDays(6)]);
    $newest = User::factory()->unverified()->create(['name' => 'Newest User', 'created_at' => now()]);
    User::factory(3)->create(['created_at' => now()->subYear()]);

    $this->actingAs($admin)
        ->get(route('administration.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('administration/Dashboard')
            ->missing('statistics')
            ->missing('recentUsers')
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->where('statistics', [
                    'total' => 7,
                    'administrators' => 1,
                    'unverified' => 2,
                    'joinedThisWeek' => 2,
                ])
                ->has('recentUsers', 5)
                ->where('recentUsers.0.id', $newest->id)
                ->where('recentUsers.0.name', 'Newest User')
            )
        );
});
