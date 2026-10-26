<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

it('adds security headers to every response', function (): void {
    $this->get(route('home'))
        ->assertOk()
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
        ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
        ->assertHeaderMissing('Strict-Transport-Security');
});

it('adds security headers to error responses', function (): void {
    $this->get('/missing')
        ->assertNotFound()
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('sends hsts only over https in production', function (string $environment, bool $secure, bool $expected): void {
    app()->detectEnvironment(fn (): string => $environment);

    $response = $this->get(($secure ? 'https://' : 'http://') . 'localhost/');

    $expected
        ? $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains')
        : $response->assertHeaderMissing('Strict-Transport-Security');
})->with([
    'https in production' => ['production', true, true],
    'http in production' => ['production', false, false],
    'https in local' => ['local', true, false],
]);

it('keeps headers the response already set', function (): void {
    Route::get('/test-frame', fn () => response('ok')->header('X-Frame-Options', 'DENY'));

    $this->get('/test-frame')->assertHeader('X-Frame-Options', 'DENY');
});
