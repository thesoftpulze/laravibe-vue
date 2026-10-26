<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/account');

    $response->assertOk();
});

test('profile information can be updated', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/account', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Profile updated successfully.']])
        ->assertRedirect('/account');

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/account', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/account');

    expect($user->refresh()->email_verified_at)->not->toBeNull();
    Notification::assertNothingSent();
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/account', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/account')
        ->delete('/account', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/account');

    expect($user->fresh())->not->toBeNull();
});

test('profile cannot be updated when the updateProfile gate denies it', function () {
    Gate::define('updateProfile', fn (User $user): bool => false);
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/account', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response->assertForbidden();
    expect($user->refresh()->name)->not->toBe('Test User');
});

test('account cannot be deleted when the deleteAccount gate denies it', function () {
    Gate::define('deleteAccount', fn (User $user): bool => false);
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/account', [
            'password' => 'password',
        ]);

    $response->assertForbidden();
    expect($user->fresh())->not->toBeNull();
});
