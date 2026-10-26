<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertStatus(200);
});

test('email verification screen can not be rendered if email is already verified', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('verification.notice'));

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('email verification notification is successfully sent', function () {
    $user = User::factory()->unverified()->create();

    Notification::fake();

    $this->actingAs($user);

    $this->post(route('verification.send'))
        ->assertRedirect()
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'A new verification link has been sent to your email address.']]);

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('email validation notification is not sent if email is already verified', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post('/email/verification-notification')
        ->assertRedirect(route('dashboard', absolute: false));
});

test('email can be verified', function () {
    $user = User::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')]
    );

    $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('email verification link redirects to dashboard if email is already verified', function () {
    $user = User::factory()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('dashboard', absolute: false) . '?verified=1');
});
