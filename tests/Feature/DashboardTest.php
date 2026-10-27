<?php

declare(strict_types=1);

use App\Models\User;

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
