<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Notification::fake();
});

test('administrators can resend the verification email to an unverified user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->unverified()->create(['email' => 'jane@example.com']);

    $this->actingAs($admin)
        ->from(route('administration.users.index'))
        ->post(route('administration.users.verification.store', $user))
        ->assertRedirect(route('administration.users.index'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Verification email sent to jane@example.com.']]);

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('verified users are not sent another verification email', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('administration.users.verification.store', $user))
        ->assertForbidden();

    Notification::assertNothingSent();
});

test('non-administrators cannot resend verification emails', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('administration.users.verification.store', User::factory()->unverified()->create()))
        ->assertForbidden();

    Notification::assertNothingSent();
});
