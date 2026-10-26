<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('password update page can be renderd', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->withSession(['auth.password_confirmed_at' => time()])->get('/account/security');

    $response->assertStatus(200);
});

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/account/security')
        ->put('/account/security/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/account/security');

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/account/security')
        ->put('/account/security/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect('/account/security');
});

test('updating the password keeps the current session signed in', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->from('/account/security')
        ->put('/account/security/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors();

    $this->get('/account')->assertOk();
});

test('sessions signed in with an old password are logged out', function () {
    $user = User::factory()->create();
    $staleHash = Auth::guard('web')->hashPasswordForCookie($user->password);

    $user->update(['password' => 'new-password']);

    $this
        ->actingAs($user)
        ->withSession(['password_hash_web' => $staleHash])
        ->get('/account')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
