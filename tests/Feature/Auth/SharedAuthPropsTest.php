<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Gate;

describe('shared auth props', function () {
    describe('guest', function () {
        test('auth.user is null', function () {
            $response = $this->get('/');

            $response->assertInertia(
                fn ($page) => $page
                    ->where('auth.user', null)
            );
        });

        test('auth.can map is present with all abilities false', function () {
            $response = $this->get('/');

            $response->assertInertia(
                fn ($page) => $page
                    ->has('auth.can')
                    ->where('auth.can.updateProfile', false)
                    ->where('auth.can.deleteAccount', false)
            );
        });
    });

    describe('authenticated', function () {
        test('auth.user contains expected fields', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('/');

            $response->assertInertia(
                fn ($page) => $page
                    ->has('auth.user')
                    ->where('auth.user.id', $user->id)
                    ->where('auth.user.name', $user->name)
                    ->where('auth.user.email', $user->email)
            );
        });

        test('auth.user does not expose remember_token', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('/');

            $response->assertInertia(
                fn ($page) => $page->missing('auth.user.remember_token')
            );
        });

        test('auth.user does not expose password', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('/');

            $response->assertInertia(
                fn ($page) => $page->missing('auth.user.password')
            );
        });

        test('auth.can reflects a denied gate', function () {
            Gate::define('deleteAccount', fn (User $user): bool => false);
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('/');

            $response->assertInertia(
                fn ($page) => $page
                    ->where('auth.can.updateProfile', true)
                    ->where('auth.can.deleteAccount', false)
            );
        });

        test('auth.can map has all abilities enabled', function () {
            $user = User::factory()->create();

            $response = $this->actingAs($user)->get('/');

            $response->assertInertia(
                fn ($page) => $page
                    ->where('auth.can.updateProfile', true)
                    ->where('auth.can.deleteAccount', true)
            );
        });
    });
});

test('quote is split into message and author', function () {
    $response = $this->get('/');

    $response->assertInertia(
        fn ($page) => $page->where(
            'quote',
            fn ($quote): bool => Inspiring::quotes()->contains($quote['message'] . ' - ' . $quote['author']),
        )
    );
});
