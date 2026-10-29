<?php

declare(strict_types=1);

use App\Models\User;
use App\Settings\GeneralSettings;
use App\Settings\SettingsRepository;

test('administrators can view the general settings', function () {
    app(SettingsRepository::class)->save(new GeneralSettings()->fill(['tagline' => 'Build faster']));
    $appName = config('app.name');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('administration.settings.general.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('administration/settings/General')
            ->where('settings', ['siteName' => null, 'tagline' => 'Build faster', 'supportEmail' => null])
            ->where('defaultSiteName', $appName));
});

test('the settings section opens on the general settings', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/administration/settings')
        ->assertRedirect('/administration/settings/general');
});

test('administrators can update the general settings', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->from(route('administration.settings.general.edit'))
        ->put(route('administration.settings.general.update'), [
            'siteName' => 'Acme',
            'tagline' => 'Build faster',
            'supportEmail' => 'help@acme.test',
        ])
        ->assertRedirect(route('administration.settings.general.edit'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Settings saved.']]);

    app(SettingsRepository::class)->forget();

    expect(app(SettingsRepository::class)->load(GeneralSettings::class)->toArray())->toBe([
        'siteName' => 'Acme',
        'tagline' => 'Build faster',
        'supportEmail' => 'help@acme.test',
    ]);
});

test('clearing a general setting restores its default', function () {
    $appName = config('app.name');
    app(SettingsRepository::class)->save(new GeneralSettings()->fill(['siteName' => 'Acme', 'tagline' => 'Build faster']));

    $this->actingAs(User::factory()->admin()->create())
        ->put(route('administration.settings.general.update'), ['siteName' => '', 'tagline' => 'Build faster', 'supportEmail' => ''])
        ->assertSessionHasNoErrors();

    expect(app(GeneralSettings::class)->siteName)->toBeNull()
        ->and(config('app.name'))->toBe($appName);
});

test('the general settings are validated', function (array $input, string $field) {
    $this->actingAs(User::factory()->admin()->create())
        ->put(route('administration.settings.general.update'), $input)
        ->assertSessionHasErrors($field);

    expect(app(GeneralSettings::class)->toArray())->toBe(new GeneralSettings()->toArray());
})->with([
    'site name too long' => [['siteName' => str_repeat('a', 61)], 'siteName'],
    'tagline too long' => [['tagline' => str_repeat('a', 161)], 'tagline'],
    'invalid support email' => [['supportEmail' => 'not-an-email'], 'supportEmail'],
]);

test('only administrators can manage the settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('administration.settings.general.edit'))->assertForbidden();
    $this->actingAs($user)->put(route('administration.settings.general.update'), ['siteName' => 'Acme'])->assertForbidden();

    expect(app(GeneralSettings::class)->siteName)->toBeNull();
});

test('guests are sent to log in', function () {
    $this->get(route('administration.settings.general.edit'))->assertRedirect(route('login'));
});

test('the tagline is the meta description', function () {
    $this->get(route('home'))->assertDontSee('<meta name="description"', false);

    app(SettingsRepository::class)->save(new GeneralSettings()->fill(['tagline' => 'Build faster & ship']));

    $this->get(route('home'))->assertSee('<meta name="description" content="Build faster &amp; ship">', false);
});

test('error pages show the support email', function () {
    $this->get('/missing')->assertInertia(fn ($page) => $page->where('supportEmail', null));

    app(SettingsRepository::class)->save(new GeneralSettings()->fill(['supportEmail' => 'help@acme.test']));

    $this->get('/missing')->assertInertia(fn ($page) => $page->where('supportEmail', 'help@acme.test'));
});
