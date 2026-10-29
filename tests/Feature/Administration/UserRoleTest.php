<?php

declare(strict_types=1);

use App\Models\User;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('administrators can make another user an administrator', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['name' => 'Jane Doe']);

    $this->actingAs($admin)
        ->from(route('administration.users.index'))
        ->put(route('administration.users.role.update', $user), ['is_admin' => true])
        ->assertRedirect(route('administration.users.index'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Jane Doe is now an administrator.']]);

    expect($user->refresh()->is_admin)->toBeTrue();
});

test('administrators can revoke another administrator', function () {
    $admin = User::factory()->admin()->create();
    $other = User::factory()->admin()->create(['name' => 'Jane Doe']);

    $this->actingAs($admin)
        ->put(route('administration.users.role.update', $other), ['is_admin' => false])
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Jane Doe is no longer an administrator.']]);

    expect($other->refresh()->is_admin)->toBeFalse();
});

test('administrators cannot change their own role', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('administration.users.role.update', $admin), ['is_admin' => false])
        ->assertForbidden();

    expect($admin->refresh()->is_admin)->toBeTrue();
});

test('non-administrators cannot change roles', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('administration.users.role.update', User::factory()->create()), ['is_admin' => true])
        ->assertForbidden();
});

test('the role must be given as a boolean', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('administration.users.role.update', $user), ['is_admin' => 'owner'])
        ->assertSessionHasErrors(['is_admin' => 'The is admin field must be true or false.']);

    expect($user->refresh()->is_admin)->toBeFalse();
});
