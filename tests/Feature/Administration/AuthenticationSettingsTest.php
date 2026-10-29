<?php

declare(strict_types=1);

use App\Models\User;
use App\Settings\AuthenticationSettings;
use App\Settings\SettingsRepository;

function saveAuthenticationSettings(array $values): void
{
    app(SettingsRepository::class)->save(new AuthenticationSettings()->fill($values));
}

test('administrators can view the authentication settings', function () {
    saveAuthenticationSettings(['allowRegistration' => false]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('administration.settings.authentication.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('administration/settings/Authentication')
            ->where('settings', ['allowRegistration' => false, 'requireEmailVerification' => true]));
});

test('administrators can update the authentication settings', function (array $input, array $expected) {
    $this->actingAs(User::factory()->admin()->create())
        ->from(route('administration.settings.authentication.edit'))
        ->put(route('administration.settings.authentication.update'), $input)
        ->assertRedirect(route('administration.settings.authentication.edit'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Settings saved.']]);

    expect(app(AuthenticationSettings::class)->toArray())->toBe($expected);
})->with([
    'unchecked boxes turn both off' => [[], ['allowRegistration' => false, 'requireEmailVerification' => false]],
    'checked boxes turn both on' => [
        ['allowRegistration' => '1', 'requireEmailVerification' => '1'],
        ['allowRegistration' => true, 'requireEmailVerification' => true],
    ],
]);

test('only administrators can manage the authentication settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('administration.settings.authentication.edit'))->assertForbidden();
    $this->actingAs($user)->put(route('administration.settings.authentication.update'), [])->assertForbidden();

    expect(app(AuthenticationSettings::class)->allowRegistration)->toBeTrue();
});

test('closing registration hides the registration routes', function () {
    saveAuthenticationSettings(['allowRegistration' => false]);

    $this->get('/register')->assertNotFound();
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertGuest();
    expect(User::query()->count())->toBe(0);
});

test('pages know whether registration is open', function () {
    $this->get(route('login'))->assertInertia(fn ($page) => $page->where('canRegister', true));

    saveAuthenticationSettings(['allowRegistration' => false]);

    $this->get(route('login'))->assertInertia(fn ($page) => $page->where('canRegister', false));
});

test('verified routes ask for a verified email while verification is required', function () {
    $this->actingAs(User::factory()->admin()->unverified()->create())
        ->get(route('administration.dashboard'))
        ->assertRedirect(route('verification.notice'));
});

test('verified routes let unverified users in while verification is not required', function () {
    saveAuthenticationSettings(['requireEmailVerification' => false]);

    $this->actingAs(User::factory()->admin()->unverified()->create())
        ->get(route('administration.dashboard'))
        ->assertOk();
});

test('the profile only asks for verification while it is required', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get(route('account.profile'))->assertInertia(fn ($page) => $page->where('mustVerifyEmail', true));

    saveAuthenticationSettings(['requireEmailVerification' => false]);

    $this->actingAs($user)->get(route('account.profile'))->assertInertia(fn ($page) => $page->where('mustVerifyEmail', false));
});
