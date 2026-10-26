<?php

declare(strict_types=1);

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

test('users with two-factor enabled are sent to the challenge instead of being logged in', function () {
    $user = User::factory()->withTwoFactor()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('two-factor challenge screen can be rendered', function () {
    $user = User::factory()->withTwoFactor()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password']);

    $this->get(route('two-factor.login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('auth/TwoFactorChallenge'));
});

test('users can complete the challenge with an authentication code', function () {
    $user = User::factory()->withTwoFactor()->create();
    $code = app(Google2FA::class)->getCurrentOtp(decrypt($user->two_factor_secret));

    $this->post('/login', ['email' => $user->email, 'password' => 'password']);
    $response = $this->post(route('two-factor.login.store'), ['code' => $code]);

    $response->assertRedirect(route('account', absolute: false));
    $this->assertAuthenticatedAs($user);
});

test('users can complete the challenge with a recovery code once', function () {
    $user = User::factory()->withTwoFactor()->create();
    $recoveryCode = $user->recoveryCodes()[0];

    $this->post('/login', ['email' => $user->email, 'password' => 'password']);
    $this->post(route('two-factor.login.store'), ['recovery_code' => $recoveryCode]);

    $this->assertAuthenticatedAs($user);
    expect($user->refresh()->recoveryCodes())->not->toContain($recoveryCode);
});

test('users can not complete the challenge with an invalid code', function () {
    $user = User::factory()->withTwoFactor()->create();

    $this->post('/login', ['email' => $user->email, 'password' => 'password']);
    $response = $this->post(route('two-factor.login.store'), ['code' => '000000']);

    $response->assertSessionHasErrors('code');
    $this->assertGuest();
});
