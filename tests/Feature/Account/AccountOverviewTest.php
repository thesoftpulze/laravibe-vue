<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $this->get(route('account'))
        ->assertRedirect(route('login'));
});

test('the overview shows the account security status', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('account'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('account/Overview')
            ->where('twoFactorEnabled', false)
            ->where('passkeyCount', 0)
        );
});

test('the overview reflects enabled two-factor authentication and the user\'s own passkeys', function () {
    $user = User::factory()->withTwoFactor()->create();
    $user->passkeys()->create([
        'name' => 'MacBook Pro',
        'credential_id' => fake()->unique()->uuid(),
        'credential' => ['aaguid' => '00000000-0000-0000-0000-000000000000'],
    ]);
    User::factory()->create()->passkeys()->create([
        'name' => 'Someone else',
        'credential_id' => fake()->unique()->uuid(),
        'credential' => ['aaguid' => '00000000-0000-0000-0000-000000000000'],
    ]);

    $this->actingAs($user)
        ->get(route('account'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('twoFactorEnabled', true)
            ->where('passkeyCount', 1)
        );
});
