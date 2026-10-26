<?php

declare(strict_types=1);

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

test('security page requires password confirmation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('account.security'))
        ->assertRedirect(route('password.confirm'));
});

test('security page shows two-factor as disabled for new users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('account.security'))
        ->assertInertia(fn ($page) => $page
            ->component('account/Security')
            ->where('twoFactor', ['enabled' => false, 'pendingConfirmation' => false]));
});

test('enabling two-factor leaves it pending until it is confirmed', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post(route('two-factor.enable'));

    $this->get(route('account.security'))
        ->assertInertia(fn ($page) => $page->where('twoFactor', ['enabled' => false, 'pendingConfirmation' => true]));
});

test('two-factor can be confirmed with a valid code', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post(route('two-factor.enable'));

    $code = app(Google2FA::class)->getCurrentOtp(decrypt($user->refresh()->two_factor_secret));

    $this->post(route('two-factor.confirm'), ['code' => $code])
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Two-factor authentication enabled.']]);

    $this->get(route('account.security'))
        ->assertInertia(fn ($page) => $page->where('twoFactor', ['enabled' => true, 'pendingConfirmation' => false]));
});

test('two-factor is not confirmed with an invalid code', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->post(route('two-factor.enable'));

    $this->post(route('two-factor.confirm'), ['code' => '000000'])
        ->assertSessionHasErrorsIn('confirmTwoFactorAuthentication', 'code');

    expect($user->refresh()->two_factor_confirmed_at)->toBeNull();
});

test('two-factor can be disabled', function () {
    $user = User::factory()->withTwoFactor()->create();

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->delete(route('two-factor.disable'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Two-factor authentication disabled.']]);

    expect($user->refresh())
        ->two_factor_secret->toBeNull()
        ->two_factor_confirmed_at->toBeNull();
});
