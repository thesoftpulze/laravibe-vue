<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passkeys\Contracts\PasskeyRegistrationResponse;
use Laravel\Passkeys\Passkey;

function createPasskey(User $user, string $name = 'MacBook Pro'): Passkey
{
    return $user->passkeys()->create([
        'name' => $name,
        'credential_id' => fake()->unique()->uuid(),
        'credential' => ['aaguid' => '00000000-0000-0000-0000-000000000000'],
    ]);
}

test('security page shares only the user\'s own passkeys', function () {
    $user = User::factory()->create();
    $passkey = createPasskey($user);
    createPasskey(User::factory()->create(), 'Someone else');

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get(route('account.security'))
        ->assertInertia(fn ($page) => $page
            ->has('passkeys', 1)
            ->where('passkeys.0.id', $passkey->id)
            ->where('passkeys.0.name', 'MacBook Pro')
            ->where('passkeys.0.last_used_at', null)
            ->missing('passkeys.0.credential')
            ->missing('passkeys.0.credential_id'));
});

test('a passkey can be removed', function () {
    $user = User::factory()->create();
    $passkey = createPasskey($user);

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->from(route('account.security'))
        ->delete(route('passkey.destroy', $passkey))
        ->assertRedirect(route('account.security'))
        ->assertInertiaFlash('toasts', [['type' => 'success', 'message' => 'Passkey removed.']]);

    expect($passkey->fresh())->toBeNull();
});

test('another user\'s passkey cannot be removed', function () {
    $user = User::factory()->create();
    $passkey = createPasskey(User::factory()->create());

    $this->actingAs($user)
        ->withSession(['auth.password_confirmed_at' => time()])
        ->delete(route('passkey.destroy', $passkey))
        ->assertForbidden();

    expect($passkey->fresh())->not->toBeNull();
});

test('registering a passkey queues a toast and keeps the JSON response', function () {
    $passkey = createPasskey(User::factory()->create());
    $request = Request::create('/user/passkeys', 'POST', server: ['HTTP_ACCEPT' => 'application/json']);

    $response = app(PasskeyRegistrationResponse::class)->withPasskey($passkey)->toResponse($request);

    $toasts = toast()->pull();

    expect($response->getStatusCode())->toBe(200)
        ->and($toasts)->toHaveCount(1)
        ->and($toasts[0])->toHaveKey('message', 'Passkey added.');
});

test('confirm password page tells whether the user has passkeys', function (bool $hasPasskey) {
    $user = User::factory()->create();

    if ($hasPasskey) {
        createPasskey($user);
    }

    $this->actingAs($user)
        ->get(route('password.confirm'))
        ->assertInertia(fn ($page) => $page
            ->component('auth/ConfirmPassword')
            ->where('hasPasskeys', $hasPasskey));
})->with([true, false]);
