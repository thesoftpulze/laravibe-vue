<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Date;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('account', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});

test('user login is rate limited after multiple failed attempts', function () {
    Date::setTestNow(now());
    $user = User::factory()->create();

    $uri = '/login';
    $data = [
        'email' => $user->email,
        'password' => 'wrong-password',
    ];

    foreach (range(1, 5) as $attempt) {
        $this->post($uri, $data)
            ->assertInvalid('email');
    }
    $response = $this->post($uri, $data);

    $response->assertInvalid(['email' => trans('auth.throttle', ['seconds' => 60])]);
});
